<?php

require "../../includes/auth.php";
require "../../includes/security.php";
require "../../config/db.php";

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {

    echo "Invalid ID";
    exit;
}

$check = $mysqli->prepare(
    "SELECT id
     FROM subcategories
     WHERE id = ?"
);

$check->bind_param("i", $id);
$check->execute();

if ($check->get_result()->num_rows == 0) {

    $check->close();

    echo "Sub Category not found";
    exit;
}

$check->close();


$stmt = $mysqli->prepare(
    "DELETE
     FROM subcategories
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
