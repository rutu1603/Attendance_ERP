<?php
include "connection.php";
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and decode attendance data from the form
    $attendanceData = isset($_POST['attendance_data']) ? json_decode($_POST['attendance_data'], true) : [];

    // Prepare SQL statement for inserting or updating attendance
    $sql = "INSERT INTO attendance (roll_no, name, status, date) VALUES (?, ?, ?, CURDATE()) 
            ON DUPLICATE KEY UPDATE status = VALUES(status)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error preparing the SQL statement: " . $conn->error;
        exit;
    }

    // Prepare statement to fetch student names
    $nameQuery = "SELECT name FROM students WHERE roll_no = ?";
    $nameStmt = $conn->prepare($nameQuery);

    if (!$nameStmt) {
        echo "Error preparing the name query: " . $conn->error;
        exit;
    }

    foreach ($attendanceData as $entry) {
        $rollNo = $entry['roll_no'];
        $status = $entry['status'];

        // Fetch the student's name
        $nameStmt->bind_param("i", $rollNo);
        $nameStmt->execute();
        $nameStmt->bind_result($name);
        $nameStmt->fetch();
        $nameStmt->free_result(); // Free result after fetching

        // Insert or update attendance
        $stmt->bind_param("sss", $rollNo, $name, $status);
        $stmt->execute();
    }

    // Close the prepared statements after the loop
    $nameStmt->close();
    $stmt->close();

    // Close the connection
    $conn->close();

    header("Location: attendance.php?success=1");
    exit;
}
?>
