<?php
// manage_availability.php
require 'db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] !== 'DOCTOR' && $_SESSION['user_type'] !== 'DOCTOR_OWNER')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Validate CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        exit();
    }
}

$doctor_id = $_SESSION['user_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';

try {
    if ($action === 'set_unavailable') {
        $date = trim($_POST['date']);
        $reason = trim($_POST['reason']);
        $is_all_day = isset($_POST['is_all_day']) ? 1 : 0;
        
        // Check if already exists
        $check_query = "SELECT id FROM doctor_availability 
                       WHERE doctor_id = ? AND date = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "is", $doctor_id, $date);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            // Update existing
            $update_query = "UPDATE doctor_availability 
                           SET availability_type = 'UNAVAILABLE', 
                               reason = ?, 
                               is_all_day = ?
                           WHERE doctor_id = ? AND date = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "siis", $reason, $is_all_day, $doctor_id, $date);
        } else {
            // Insert new
            $insert_query = "INSERT INTO doctor_availability 
                           (doctor_id, date, start_time, end_time, availability_type, reason, is_all_day) 
                           VALUES (?, ?, '00:00:00', '00:00:00', 'UNAVAILABLE', ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "issi", $doctor_id, $date, $reason, $is_all_day);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Availability updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating availability']);
        }
        
    } else if ($action === 'remove_unavailable') {
        $date = trim($_POST['date']);
        
        $delete_query = "DELETE FROM doctor_availability 
                        WHERE doctor_id = ? AND date = ? AND availability_type = 'UNAVAILABLE'";
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, "is", $doctor_id, $date);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Availability removed']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error removing availability']);
        }
        
    } else if ($action === 'get_unavailable_dates') {
        $month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        
        $first_day = date('Y-m-01', strtotime("$year-$month-01"));
        $last_day = date('Y-m-t', strtotime("$year-$month-01"));
        
        $query = "SELECT date, reason, is_all_day 
                 FROM doctor_availability 
                 WHERE doctor_id = ? 
                 AND availability_type = 'UNAVAILABLE'
                 AND date BETWEEN ? AND ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iss", $doctor_id, $first_day, $last_day);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $unavailable_dates = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $unavailable_dates[] = $row;
        }
        
        echo json_encode(['success' => true, 'unavailable_dates' => $unavailable_dates]);
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Error managing availability: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
