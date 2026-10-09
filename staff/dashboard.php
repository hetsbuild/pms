<?php

session_start();

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'staff'
) {
    header("Location: ../auth/login.php");
    exit;
}

echo "<h1>Staff Dashboard</h1>";
echo "Welcome " . $_SESSION['name'];

?>