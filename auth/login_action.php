<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();
require '../config/db.php';

$name = trim($_POST['name']);
$password = trim($_POST['password']);
$role = trim($_POST['role']);


//Validation

if (empty($name) || empty($password) || empty($role)) {
    echo "<script>
alert('All fields are required.');
window.location.href='login.php';
</script>";
    exit;
}

if (strlen($password) < 4) {
    echo "<script>
alert('Password must contain at least 4 characters');
window.location.href='login.php';
</script>";
    exit;
}

//User Check

$stmt = $mysqli->prepare(
    "SELECT * FROM users
     WHERE name = ? AND role = ?"
);

$stmt->bind_param("ss", $name, $role);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "<script>
alert('Invalid Username or Role');
window.location.href='login.php';
</script>";
    exit;
}

$user = $result->fetch_assoc();

// Inactive User Check

if ($user['status'] == 0) {
    echo "<script>
alert('User not found');
window.location.href='login.php';
</script>";
    exit;
}

// Password Check

if (!password_verify($password, $user['password'])) {
    echo "<script>
alert('Invalid Password');
window.location.href='login.php';
</script>";
    exit;
}


// Login Success

$_SESSION['user_id'] = $user['id'];
$_SESSION['name'] = $user['name'];
$_SESSION['role'] = $user['role'];

if (isset($_POST['remember'])) {

    setcookie("user_id", $user['id'], time() + (86400 * 30), "/");
    setcookie("user_role", $user['role'], time() + (86400 * 30), "/");
    setcookie("user_name", $user['name'], time() + (86400 * 30), "/");
}


if ($user['role'] == 'admin' || $user['role'] == 'staff') {

    header("Location: ../dashboard.php");
    
} elseif ($user['role'] == 'customer') {

    header("Location: ../customer/dashboard.php");
exit;
}

exit;
exit;
