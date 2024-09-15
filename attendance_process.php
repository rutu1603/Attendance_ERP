<?php
include "connection.php";
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Retrieve POST data
$selectedDate = isset($_POST['selected_date']) ? $_POST['selected_date'] : '';
$branch = isset($_POST['branch']) ? $_POST['branch'] : '';
$semester = isset($_POST['semester']) ? $_POST['semester'] : '';
$division = isset($_POST['division']) ? $_POST['division'] : '';
$course = isset($_POST['course']) ? $_POST['course'] : '';
$attendanceData = isset($_POST['attendance']) ? $_POST['attendance'] : [];

// Check if attendance has already been marked for the same date and course
$checkQuery = "SELECT * FROM attendance WHERE date = ? AND courses = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ss", $selectedDate, $course);
$stmt->execute();
$result = $stmt->get_result();

// If attendance exists, show an alert message
if ($result->num_rows > 0) {
    echo "<script>alert('Attendance has already been marked for this course on this date.'); window.location.href = 'index.php';</script>";
    exit;
}

// Loop through attendance data and save it to the database
foreach ($attendanceData as $roll_no => $data) {
    $name = $data['name'];
    $status = $data['status'];  // '1' for Present, '0' for Absent

    // Insert or update attendance
    $query = "INSERT INTO attendance (roll_no, name, date, status, courses)
              VALUES (?, ?, ?, ?, ?)
              ON DUPLICATE KEY UPDATE status = VALUES(status)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $roll_no, $name, $selectedDate, $status, $course);
    if (!$stmt->execute()) {
        error_log("Error executing query: " . $stmt->error);
    }
}

// Redirect to the home page after saving
header("Location: index.php");
exit;

?>
