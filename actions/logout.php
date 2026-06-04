<?php

//php file for logging out the user by destroying the session and redirecting to login page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Unset all session variables
$_SESSION = array();
// Destroy the session
session_destroy();

// Redirect to login page after logout
header('Location: ../login.php');
exit();

?>