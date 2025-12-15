<?php
session_start(); // Start the session to access session variables

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Prevent page caching
header("Cache-Control: no-cache, must-revalidate");

// Redirect to the login page
// Make sure the path is correctly specified relative to this script's location
header('Location: ../../index.php');
exit;

?>