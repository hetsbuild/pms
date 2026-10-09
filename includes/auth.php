<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id'])) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['name'] = $_COOKIE['user_name'];
        $_SESSION['role'] = $_COOKIE['user_role'];
    }
}
?>