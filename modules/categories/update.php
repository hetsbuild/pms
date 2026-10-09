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

        setMessage("Invalid Category ID.");

        header("Location: index.php");
        exit;
    }

    $category_name = preg_replace(
        '/\s+/',
        ' ',
        sanitizeInput($_POST['category_name'])
    );

    if (empty($category_name)) {

        setMessage("Category name is required.");

        header("Location: edit.php?id=" . $id);
        exit;
    }

    if (strlen($category_name) > 100) {

        setMessage("Category name must not exceed 100 characters.");

        header("Location: edit.php?id=" . $id);
        exit;
    }

    // Check duplicate category except current record
    $check = $mysqli->prepare(
        "SELECT id
         FROM categories
         WHERE category_name = ?
         AND id != ?"
    );

    $check->bind_param(
        "si",
        $category_name,
        $id
    );

    $check->execute();

    if ($check->get_result()->num_rows > 0) {

        setMessage("Category already exists.");

        header("Location: edit.php?id=" . $id);
        exit;
    }

    $check->close();

    // Update category
    $stmt = $mysqli->prepare(
        "UPDATE categories
         SET category_name = ?
         WHERE id = ?"
    );

    $stmt->bind_param(
        "si",
        $category_name,
        $id
    );

    if ($stmt->execute()) {

        $stmt->close();

        setMessage("Category updated successfully!");

        header("Location: index.php");
        exit;
    }

    $stmt->close();

    setMessage("Failed to update category.");

    header("Location: edit.php?id=" . $id);
    exit;
}
