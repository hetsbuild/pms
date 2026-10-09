<?php

// Copy this file to config/db.php and set your own credentials.

$mysqli = new mysqli("localhost", "root", "", "pms", 3307);

if ($mysqli->connect_error) {
    die("failed");
}

?>