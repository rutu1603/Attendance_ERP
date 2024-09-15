<?php
include "connection.php";

$sql = "SELECT  name FROM faculty";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$faculty = [];
while ($row = mysqli_fetch_assoc($result)) {
    $faculty[] = $row;
}

echo json_encode($faculty);
?>
