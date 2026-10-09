<?php

session_start();

// Clear all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Remove cookies
setcookie("user_id", "", time() - 3600, "/");
setcookie("user_name", "", time() - 3600, "/");
setcookie("user_role", "", time() - 3600, "/");

// Redirect to customer products page
header("Location: index.php");
exit;