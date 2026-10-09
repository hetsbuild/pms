<?php

require '../../includes/auth.php';
require '../../includes/security.php';
require '../../config/db.php';

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {

    echo "Invalid User ID";
    exit;
}

$check = $mysqli->prepare(
    "SELECT id
     FROM users
     WHERE id = ?"
);

$check->bind_param("i", $id);
$check->execute();

if ($check->get_result()->num_rows == 0) {

    $check->close();

    echo "User not found";
    exit;
}

$check->close();

$stmt = $mysqli->prepare(
    "DELETE
     FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    echo "success";
} else {

    echo "error";
}

$stmt->close();
$mysqli->close();

exit;
