<?php

require "../../includes/auth.php";
require "../../includes/security.php";
require "../../includes/flash_message.php";
require_once "../../config/db.php";
include "../../includes/header.php";

if (isset($_POST['submit'])) {

    $name = preg_replace(
        '/\s+/',
        ' ',
        sanitizeInput($_POST['name'])
    );

    $password = $_POST['password'];

    $role = sanitizeInput($_POST['role']);

    $status = filter_input(
        INPUT_POST,
        'status',
        FILTER_VALIDATE_INT
    );

    if (empty($name)) {

        setMessage("Username is required.");

        header("Location: add.php");
        exit;
    }

    if (strlen($name) > 100) {

        setMessage("Username must not exceed 100 characters.");

        header("Location: add.php");
        exit;
    }

    if (strlen($password) < 8) {

        setMessage("Password must be at least 8 characters.");

        header("Location: add.php");
        exit;
    }

    if (!in_array($role, ['admin', 'staff'])) {

        setMessage("Invalid role.");

        header("Location: add.php");
        exit;
    }

    if ($status === false) {

        setMessage("Invalid status.");

        header("Location: add.php");
        exit;
    }

    $check = $mysqli->prepare(
        "SELECT id
         FROM users
         WHERE name = ?"
    );

    $check->bind_param("s", $name);

    $check->execute();

    if ($check->get_result()->num_rows > 0) {

        $check->close();

        setMessage("Username already exists.");

        header("Location: add.php");
        exit;
    }

    $check->close();


    $password = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $stmt = $mysqli->prepare(
        "INSERT INTO users
        (
            name,
            password,
            role,
            status
        )
        VALUES
        (
            ?, ?, ?, ?
        )"
    );

    $stmt->bind_param(
        "sssi",
        $name,
        $password,
        $role,
        $status
    );

    if ($stmt->execute()) {

        $stmt->close();

        setMessage("User added successfully!");

        header("Location: index.php");
        exit;
    }

    $stmt->close();

    setMessage("Failed to add user.");

    header("Location: add.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Add User</title>

    <link
        rel="stylesheet"
        href="/pms/assets/css/style.css">

</head>

<body>

    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">

        <h2>Add User</h2>

        <?php showMessages(); ?>

        <form method="POST">

            <label>Username</label>

            <input
                type="text"
                name="name"
                maxlength="100"
                required>

            <br><br>

            <label>Password</label>

            <input
                type="password"
                name="password"
                minlength="8"
                required>

            <br><br>

            <label>Role</label>

            <select
                name="role"
                required>

                <option value="admin">

                    Admin

                </option>

                <option value="staff">

                    Staff

                </option>

            </select>

            <br><br>

            <label>Status</label>

            <select
                name="status"
                required>

                <option value="1">

                    Active

                </option>

                <option value="0">

                    Inactive

                </option>

            </select>

            <br><br>

            <button
                type="submit"
                name="submit">

                Add User

            </button>

        </form>

    </div>

</body>

</html>