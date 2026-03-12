<?php
// get_availability.php
// Ready-to-use endpoint that returns a doctor's availability for a given month.
// Response (JSON):
// {
//   "success": true,
//   "unavailable_dates": [{ "id": 1, "date":"2026-02-14", "reason":"Vacation", "is_all_day":1, "start_time":"00:00:00","end_time":"23:59:59","availability_type":"UNAVAILABLE" }, ...],
//   "working_days": ["MONDAY","WEDNESDAY", ...],             // doctor's active schedule days (after applying clinic open-days)
//   "clinic_open_days": ["MONDAY","TUESDAY", ...],          // clinic open days from clinic_settings (if present)
//   "appointment_counts": { "2026-02-04": 3, ... },
//   "appointments": [{ "id": 22, "patient_id":145, "appointment_date":"2026-02-04", "appointment_time":"10:00:00", "status":"SCHEDULED" , ...}, ...],
//   "month": 2, "year": 2026
// }
// Notes:
// - Expects GET params: doctor_id (required), month (1-12), year (YYYY).
// - Uses prepared statements and returns strict JSON (no HTML). If an error occurs, success=false and message is returned.
// - It will attempt to read clinic business hours from clinic_settings.setting_key = 'business_hours' (JSON). If found, the API will derive `clinic_open_days`.
// - If a `holidays` table exists (date, name), those dates between the requested month will be treated as unavailable and returned in unavailable_dates.

require 'db_connect.php';

header('Content-Type: application/json; charset=utf-8');

// Basic auth check: optional but recommended. If you don't want session requirement, remove this block.
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Helper to output error JSON and exit
function respond_error($message, $http_code = 200) {
    http_response_code($http_code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;
$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('n'));
$year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

if ($doctor_id <= 0) {
    respond_error('Invalid doctor_id');
}

if ($month < 1 || $month > 12 || $year < 1900) {
    respond_error('Invalid month or year');
}

try {
    // Compute first and last day strings
    $first_day = date('Y-m-01', strtotime("$year-$month-01"));
    $last_day = date('Y-m-t', strtotime("$year-$month-01"));

    // 1) Get doctor's unavailable/available overrides from doctor_availability
    $unavail_sql = "SELECT id, doctor_id, date, start_time, end_time, availability_type, reason, is_all_day
                    FROM doctor_availability
                    WHERE doctor_id = ?
                      AND date BETWEEN ? AND ?
                    ORDER BY date ASC, start_time ASC";
    $stmt = mysqli_prepare($conn, $unavail_sql);
    if (!$stmt) {
        error_log("prepare failed: " . mysqli_error($conn));
        respond_error('Server error preparing availability query');
    }
    mysqli_stmt_bind_param($stmt, "iss", $doctor_id, $first_day, $last_day);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $unavailable_dates = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // normalize times for all-day entries
        if (!isset($row['start_time']) || $row['start_time'] === '00:00:00' || $row['is_all_day']) {
            $row['start_time'] = '00:00:00';
        }
        if (!isset($row['end_time']) || $row['end_time'] === '00:00:00' || $row['is_all_day']) {
            $row['end_time'] = '23:59:59';
        }
        $row['is_all_day'] = (int)$row['is_all_day'];
        $unavailable_dates[] = $row;
    }
    mysqli_stmt_close($stmt);

    // 2) Get doctor's scheduled working days from doctor_schedules
    $sched_sql = "SELECT DISTINCT day_of_week, start_time, end_time, slot_duration, active
                  FROM doctor_schedules
                  WHERE doctor_id = ? AND active = 1";
    $stmt = mysqli_prepare($conn, $sched_sql);
    mysqli_stmt_bind_param($stmt, "i", $doctor_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $working_days = []; // e.g. ['MONDAY','TUESDAY']
    $doctor_day_slots = []; // map day_of_week -> {start_time,end_time,slot_duration}
    while ($row = mysqli_fetch_assoc($result)) {
        $day = strtoupper($row['day_of_week']);
        $working_days[] = $day;
        $doctor_day_slots[$day] = [
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
            'slot_duration' => intval($row['slot_duration'])
        ];
    }
    mysqli_stmt_close($stmt);

    // 3) Derive clinic open days from clinic_settings.business_hours if present
    $clinic_open_days = []; // e.g. ['MONDAY','TUESDAY',...]
    $business_hours_query = "SELECT setting_value FROM clinic_settings WHERE setting_key = 'business_hours' LIMIT 1";
    $bh_result = mysqli_query($conn, $business_hours_query);
    if ($bh_result && ($bh_row = mysqli_fetch_assoc($bh_result))) {
        $bh_json = $bh_row['setting_value'];
        $bh = json_decode($bh_json, true);
        if (is_array($bh)) {
            // mapping keys may be lowercase day names
            foreach ($bh as $day => $times) {
                $open = isset($times['open']) ? $times['open'] : null;
                $close = isset($times['close']) ? $times['close'] : null;
                if ($open !== null && $open !== '' && $close !== null && $close !== '') {
                    $clinic_open_days[] = strtoupper($day);
                }
            }
        }
    }

    // If clinic_open_days is empty, assume all weekdays (Mon-Fri) open
    if (empty($clinic_open_days)) {
        $clinic_open_days = ['MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY']; // include saturday default as business_hours may allow
    }

    // Intersect doctor's working days with clinic open days (if doctor schedule present)
    if (!empty($working_days)) {
        $filtered_working_days = array_values(array_intersect($working_days, $clinic_open_days));
    } else {
        // doctor has no explicit schedule -> fallback to clinic open days (doctors may be available any clinic open day until they mark unavailable)
        $filtered_working_days = $clinic_open_days;
    }

    // 4) Optional: check for a holidays table (global clinic holidays) and treat them as unavailable
    $holidays = [];
    $has_holidays_table = false;
    $res = mysqli_query($conn, "SHOW TABLES LIKE 'holidays'");
    if ($res && mysqli_num_rows($res) > 0) {
        $has_holidays_table = true;
    }
    if ($has_holidays_table) {
        $hol_sql = "SELECT id, date, name FROM holidays WHERE date BETWEEN ? AND ?";
        $stmt = mysqli_prepare($conn, $hol_sql);
        mysqli_stmt_bind_param($stmt, "ss", $first_day, $last_day);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $holidays[] = $row;
            // also append to unavailable_dates (all-day)
            $unavailable_dates[] = [
                'id' => isset($row['id']) ? $row['id'] : null,
                'doctor_id' => null,
                'date' => $row['date'],
                'start_time' => '00:00:00',
                'end_time' => '23:59:59',
                'availability_type' => 'UNAVAILABLE',
                'reason' => 'Holiday: ' . ($row['name'] ?? 'Holiday'),
                'is_all_day' => 1
            ];
        }
        mysqli_stmt_close($stmt);
    }

    // 5) Get appointments for the doctor in the month (exclude cancelled/no-show)
    $appt_sql = "SELECT id, patient_id, appointment_date, appointment_time, status, type, duration, created_at
                 FROM appointments
                 WHERE doctor_id = ?
                   AND appointment_date BETWEEN ? AND ?
                   AND status NOT IN ('CANCELLED','NO_SHOW')
                 ORDER BY appointment_date ASC, appointment_time ASC";
    $stmt = mysqli_prepare($conn, $appt_sql);
    mysqli_stmt_bind_param($stmt, "iss", $doctor_id, $first_day, $last_day);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $appointments = [];
    $appointment_counts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
        $d = $row['appointment_date'];
        if (!isset($appointment_counts[$d])) $appointment_counts[$d] = 0;
        $appointment_counts[$d]++;
    }
    mysqli_stmt_close($stmt);

    // 6) Build a list of all dates in month and compute derived availability boolean (optional)
    // We'll not return every date by default; the front-end calendar uses working_days, unavailable_dates, and appointments.

    // Final response
    echo json_encode([
        'success' => true,
        'doctor_id' => $doctor_id,
        'month' => $month,
        'year' => $year,
        'unavailable_dates' => $unavailable_dates,
        'working_days' => array_values($filtered_working_days),
        'clinic_open_days' => array_values($clinic_open_days),
        'appointment_counts' => $appointment_counts,
        'appointments' => $appointments
    ]);
    exit();

} catch (Throwable $e) {
    error_log("get_availability error: " . $e->getMessage());
    respond_error('An unexpected server error occurred');
}
?>