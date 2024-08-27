<?php
include "connection.php";

$sql = "SELECT  name FROM division";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$divisions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $divisions[] = $row;
}

echo json_encode($divisions);
?>
