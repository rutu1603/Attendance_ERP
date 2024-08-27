<?php
include "connection.php";

$semester_name = isset($_GET['semester_name']) ? $_GET['semester_name'] : '';
$branch_name = isset($_GET['branch_name']) ? $_GET['branch_name'] : '';

$sql = "SELECT name FROM courses WHERE semester_name = ? AND branch_name = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $semester_name, $branch_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$courses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $courses[] = $row;
}

echo json_encode($courses);
?>
