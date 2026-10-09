<?php

require '../config/db.php';

$name = trim($_POST['name']);
$password = trim($_POST['password']);
$role = $_POST['role'];

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare(
    "INSERT INTO users(name,password,role)
     VALUES(?,?,?)"
);

$stmt->bind_param(
    "sss",
    $name,
    $hash,
    $role
);

$stmt->execute();

header("Location: login.php");
exit;

?>