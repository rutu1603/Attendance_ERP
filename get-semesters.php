<?php
include "connection.php";

$sql = "SELECT  name FROM semester";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$semesters = [];
while ($row = mysqli_fetch_assoc($result)) {
    $semesters[] = $row;
}

echo json_encode($semesters);
?>
