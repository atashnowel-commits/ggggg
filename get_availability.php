<?php
// get_availability.php
// Returns a doctor's availability for a given month (JSON).
// GET params: doctor_id (required), month (1-12), year (YYYY).

require 'db_connect.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

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
    $first_day = date('Y-m-01', strtotime("$year-$month-01"));
    $last_day = date('Y-m-t', strtotime("$year-$month-01"));

    // 1) Get doctor's unavailable/available overrides from doctor_availability
    $unavail_sql = "SELECT id, doctor_id, specific_date as date, start_time, end_time, availability_type, reason, is_all_day
                    FROM doctor_availability
                    WHERE doctor_id = ?
                      AND specific_date BETWEEN ? AND ?
                      AND active = 1
                    ORDER BY specific_date ASC, start_time ASC";
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
        if (!isset($row['start_time']) || $row['start_time'] === '00:00:00' || $row['is_all_day']) {
            $row['start_time'] = '00:00:00';
        }
        if (!isset($row['end_time']) || $row['end_time'] === '00:00:00' || $row['is_all_day']) {
            $row['end_time'] = '23:59:59';
        }
        $row['is_all_day'] = (int)$row['is_all_day'];
        // Only include UNAVAILABLE entries in the unavailable_dates array
        if ($row['availability_type'] === 'UNAVAILABLE') {
            $unavailable_dates[] = $row;
        }
    }
    mysqli_stmt_close($stmt);

    // 2) Get doctor's scheduled working days from doctor_schedules
    $working_days = [];
    $doctor_day_slots = [];

    // Check if doctor_schedules table exists
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'doctor_schedules'");
    if ($table_check && mysqli_num_rows($table_check) > 0) {
        $sched_sql = "SELECT DISTINCT day_of_week, start_time, end_time, slot_duration, active
                      FROM doctor_schedules
                      WHERE doctor_id = ? AND active = 1";
        $stmt = mysqli_prepare($conn, $sched_sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $doctor_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

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
        }
    }

    // Also check doctor_availability for RECURRING entries
    $recurring_sql = "SELECT day_of_week, start_time, end_time, slot_duration
                      FROM doctor_availability
                      WHERE doctor_id = ? AND availability_type = 'RECURRING' AND active = 1";
    $stmt = mysqli_prepare($conn, $recurring_sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $doctor_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['day_of_week']) {
                $day = strtoupper($row['day_of_week']);
                if (!in_array($day, $working_days)) {
                    $working_days[] = $day;
                }
                $doctor_day_slots[$day] = [
                    'start_time' => $row['start_time'],
                    'end_time' => $row['end_time'],
                    'slot_duration' => intval($row['slot_duration'] ?? 30)
                ];
            }
        }
        mysqli_stmt_close($stmt);
    }

    // 3) Derive clinic open days from clinic_settings.business_hours
    $clinic_open_days = [];
    $business_hours_query = "SELECT setting_value FROM clinic_settings WHERE setting_key = 'business_hours' LIMIT 1";
    $bh_result = mysqli_query($conn, $business_hours_query);
    if ($bh_result && ($bh_row = mysqli_fetch_assoc($bh_result))) {
        $bh_json = $bh_row['setting_value'];
        $bh = json_decode($bh_json, true);
        if (is_array($bh)) {
            foreach ($bh as $day => $times) {
                $open = isset($times['open']) ? $times['open'] : null;
                $close = isset($times['close']) ? $times['close'] : null;
                if ($open !== null && $open !== '' && $close !== null && $close !== '') {
                    $clinic_open_days[] = strtoupper($day);
                }
            }
        }
    }

    if (empty($clinic_open_days)) {
        $clinic_open_days = ['MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY'];
    }

    if (!empty($working_days)) {
        $filtered_working_days = array_values(array_intersect($working_days, $clinic_open_days));
    } else {
        $filtered_working_days = $clinic_open_days;
    }

    // 4) Check for holidays table
    $holidays = [];
    $res = mysqli_query($conn, "SHOW TABLES LIKE 'holidays'");
    if ($res && mysqli_num_rows($res) > 0) {
        $hol_sql = "SELECT id, date, name FROM holidays WHERE date BETWEEN ? AND ?";
        $stmt = mysqli_prepare($conn, $hol_sql);
        mysqli_stmt_bind_param($stmt, "ss", $first_day, $last_day);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $holidays[] = $row;
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

    // 5) Get appointments for the doctor in the month
    $appt_sql = "SELECT a.id, a.patient_id, a.appointment_date, a.appointment_time, a.status, a.type, a.duration,
                 p.first_name as patient_first_name, p.last_name as patient_last_name
                 FROM appointments a
                 LEFT JOIN patients p ON a.patient_id = p.id
                 WHERE a.doctor_id = ?
                   AND a.appointment_date BETWEEN ? AND ?
                   AND a.status NOT IN ('CANCELLED','NO_SHOW')
                 ORDER BY a.appointment_date ASC, a.appointment_time ASC";
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
