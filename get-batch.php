<?php
include "connection.php";

$sql = "SELECT batch FROM batch";
$result = mysqli_query($conn, $sql);
$batches = [];

while ($row = mysqli_fetch_assoc($result)) {
    $batches[] = $row;
}
echo json_encode($batches);
?>
