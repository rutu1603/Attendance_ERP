
<?php
session_start();
include "connection.php";

if (isset($_SESSION['role']) && isset($_SESSION['branch'])) {
    $role = $_SESSION['role'];
    $branch_name = $_SESSION['branch'];

    if ($role == 'faculty') {
        // Fetch courses for faculty based on their assigned courses
        if (isset($_SESSION['courses'])) {
            $courses = $_SESSION['courses'];
            echo json_encode($courses);
        } else {
            echo json_encode(['error' => 'No courses found for the faculty.']);
        }
    } 
    
    else {
        // Fetch courses based on the branch for non-faculty roles (admin/executive)
        $sql = "SELECT name FROM courses WHERE branch_name = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $branch_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $courses = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $courses[] = $row['name'];
        }

        if (!empty($courses)) {
            echo json_encode($courses);
        } else {
            echo json_encode(['error' => 'No courses found for the branch.']);
        }
    }
} else {
    echo json_encode(['error' => 'Invalid role or branch.']);
}


?>
