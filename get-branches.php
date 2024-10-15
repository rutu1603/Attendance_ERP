<?php

session_start();
 
 header('Content-Type: application/json');
include "connection.php";

// Get the branch from the session
$user_branch = isset($_SESSION['branch']) ? $_SESSION['branch'] : '';

if ($user_branch) {
    echo json_encode(['branch' => $user_branch]);
    
} else {
    echo json_encode(['error' => 'Branch not found.']);
}
exit;
?>
