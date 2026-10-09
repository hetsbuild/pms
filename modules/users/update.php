<?php

require '../../includes/auth.php';
require '../../includes/security.php';
require '../../includes/flash_message.php';
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$id) {

        setMessage("Invalid User ID.");

        header("Location: index.php");
        exit;
    }

    $name = preg_replace(
        '/\s+/',
        ' ',
        sanitizeInput($_POST['name'])
    );

    $role = sanitizeInput($_POST['role']);

    $status = filter_input(
        INPUT_POST,
        'status',
        FILTER_VALIDATE_INT
    );

    if (empty($name)) {

        setMessage("Username is required.");

        header("Location: edit.php?id=" . $id);
        exit;
    }

    if (strlen($name) > 100) {

        setMessage("Username must not exceed 100 characters.");

        header("Location: edit.php?id=" . $id);
        exit;
    }

    if (!in_array($role, ['admin', 'staff'])) {

        setMessage("Invalid role.");

        header("Location: edit.php?id=" . $id);
        exit;
    }

    if ($status === false) {

        setMessage("Invalid status.");

        header("Location: edit.php?id=" . $id);
        exit;
    }

    $check = $mysqli->prepare(
        "SELECT id
         FROM users
         WHERE name = ?
         AND id != ?"
    );

    $check->bind_param(
        "si",
        $name,
        $id
    );

    $check->execute();

    if ($check->get_result()->num_rows > 0) {

        $check->close();

        setMessage("Username already exists.");

        header("Location: edit.php?id=" . $id);
        exit;
    }

    $check->close();

    $stmt = $mysqli->prepare(
        "UPDATE users
         SET
            name = ?,
            role = ?,
            status = ?
         WHERE id = ?"
    );

    $stmt->bind_param(
        "ssii",
        $name,
        $role,
        $status,
        $id
    );

    if ($stmt->execute()) {

        $stmt->close();

        setMessage("User updated successfully.");

        header("Location: index.php");
        exit;
    }

    $stmt->close();

    setMessage("Failed to update user.");

    header("Location: edit.php?id=" . $id);
    exit;
}
