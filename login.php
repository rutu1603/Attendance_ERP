<?php
session_start();
include "connection.php";

if (isset($_POST["login"])) {
    $role = $_POST["role"];
    $user_name = $_POST["user_name"];
    $password = $_POST["password"];

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
            // echo 'Branch found: ' . $row['branch_name']; // Check the branch fetched
            $_SESSION['branch'] = $row['branch_name'];
        } else {
            die("Branch not found for the user.");
        }
        


        // Fetch and store courses related to the faculty
        if ($role == 'faculty') {
            $courses_sql = "SELECT name FROM faculty_course WHERE username = ?";
            $courses_stmt = mysqli_prepare($conn, $courses_sql);
            mysqli_stmt_bind_param($courses_stmt, "s", $user_name);
            mysqli_stmt_execute($courses_stmt);
            $faculty_result = mysqli_stmt_get_result($courses_stmt);
        
            $courses = [];
            while ($row = mysqli_fetch_assoc($faculty_result)) {
                $courses[] = $row['name'];
            }
        
            if (!empty($courses)) {
                $_SESSION['courses'] = $courses; // Store courses in session for faculty
            } else {
                echo "No courses found for this faculty.";
            }
        }

        // Redirect based on role
        if ($role == 'admin') {
            header("Location: report.php");
            exit();
        } elseif ($role == 'faculty') {
            header("Location: faculty.php");
            exit();
        } elseif ($role == 'executive') {
            header("Location: executive.php");
            exit();
        }
    }
    mysqli_stmt_close($stmt);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 600px;
            margin: 100px auto;
            padding: 50px;
            background-color: white;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }
        .form-group {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="login.php" method="post">
            <h2 style="text-align:center">Login</h2>
            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" class="form-control" required>
                    <option value="admin">Admin</option>
                    <!-- <option value="executive">Executive</option> -->
                    <option value="faculty">Faculty</option>
                </select>
            </div>
            <div class="form-group">
                <label for="user_name">Username</label>
                <input type="text" placeholder="Enter user_name" name="user_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" placeholder="Enter Password" name="password" class="form-control" required>
            </div>
            <div class="form-btn">
                <input type="submit" value="Login" name="login" class="btn btn-primary">
            </div>
        </form>
    </div>
</body>
</html>
