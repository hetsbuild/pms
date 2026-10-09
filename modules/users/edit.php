<?php

require "../../includes/auth.php";
require "../../includes/security.php";
require "../../config/db.php";
include "../../includes/header.php";

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);
if (!preg_match("/^\d+$/", $id)) {
    die("Invalid Product ID Missing");
}
if (!$id) {

    header("Location: index.php");
    exit;
}

$stmt = $mysqli->prepare(
    "SELECT *
     FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();

if (!$user) {

    die("User not found.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Edit User</title>

    <link
        rel="stylesheet"
        href="/pms/assets/css/style.css">

</head>

<body>

    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">

        <h2>Edit User</h2>

        <form action="update.php" method="POST">

            <input
                type="hidden"
                name="id"
                value="<?= $user['id']; ?>">

            <label>Username</label>

            <input
                type="text"
                name="name"
                value="<?= escapeOutput($user['name']); ?>"
                maxlength="100"
                required>

            <br><br>

            <label>Role</label>

            <select
                name="role"
                required>

                <option
                    value="admin"
                    <?= ($user['role'] == 'admin')
                        ? 'selected'
                        : ''; ?>>

                    Admin

                </option>

                <option
                    value="staff"
                    <?= ($user['role'] == 'staff')
                        ? 'selected'
                        : ''; ?>>

                    Staff

                </option>

            </select>

            <br><br>

            <label>Status</label>

            <select
                name="status"
                required>

                <option
                    value="1"
                    <?= ($user['status'] == 1)
                        ? 'selected'
                        : ''; ?>>

                    Active

                </option>

                <option
                    value="0"
                    <?= ($user['status'] == 0)
                        ? 'selected'
                        : ''; ?>>

                    Inactive

                </option>

            </select>

            <br><br>

            <button
                type="submit"
                name="submit">

                Update User

            </button>

        </form>

    </div>

</body>

</html>