<?php
session_start();
include "connection.php";  // Ensure your connection.php connects to the database

// Simulating login process for testing
$user_name = 'VIT10006';  // Replace with a valid username from your 'login' table
$password = 'sampal6';  // Replace with the correct password
$role = 'faculty';  // Role can be 'faculty', 'admin', etc.

// SQL query to verify user credentials
$sql = "SELECT * FROM login WHERE role = ? AND username = ? AND password = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $role, $user_name, $password);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user) {
    $_SESSION['logged_in'] = true;
    $_SESSION['role'] = $role;
    $_SESSION['user_name'] = $user_name;

    // Fetch branch for all roles
    $sql = "SELECT branch_name FROM login WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $user_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // echo  $row['branch_name'] . '<br>'; // Output the branch
        $_SESSION['branch'] = $row['branch_name'];  // Store in session
    } else {
        die("Branch not found for the user.");
    }

    // Fetch and store courses related to the faculty
    if ($role == 'faculty') {
        $courses_sql = "SELECT course FROM faculty_course WHERE username = ?";
        $courses_stmt = mysqli_prepare($conn, $courses_sql);
        mysqli_stmt_bind_param($courses_stmt, "s", $user_name);
        mysqli_stmt_execute($courses_stmt);
        $faculty_result = mysqli_stmt_get_result($courses_stmt);
    
        $courses = [];
        while ($row = mysqli_fetch_assoc($faculty_result)) {
            $courses[] = $row['course'];
        }
    
        // Debugging output: check if courses are fetched
        echo "<pre>";
        print_r($courses);  // Output fetched courses
        echo "</pre>";
    
        if (!empty($courses)) {
            $_SESSION['courses'] = $courses; // Store courses in session for faculty
        } else {
            echo "No courses found for this faculty.";
        }
    }
    

    // Print session variables to check if they're set correctly
    echo '<pre>';
    print_r($_SESSION);  // Check if session variables are set correctly
    echo '</pre>';
} else {
    echo "Invalid login credentials.";
}

mysqli_stmt_close($stmt);
?>
