<?php
include "connection.php";

$branch_name = isset($_GET['branch_name']) ? $_GET['branch_name'] : '';

$sql = "SELECT name FROM faculty WHERE branch_name = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $branch_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$faculties = [];
while ($row = mysqli_fetch_assoc($result)) {
    $faculties[] = $row;
}

echo json_encode($faculties);
?>
