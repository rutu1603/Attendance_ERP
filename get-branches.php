<?php
include "connection.php";

$sql = "SELECT  name FROM branch";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$branches = [];
while ($row = mysqli_fetch_assoc($result)) {
    $branches[] = $row;
}

echo json_encode($branches);
?>
