<?php

require "../../includes/auth.php";
require "../../includes/security.php";
require "../../includes/flash_message.php";
require_once "../../config/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$id) {

        setMessage("Invalid Sub Category ID.");

        header("Location: index.php");
        exit;
    }

    $category_id = filter_input(
        INPUT_POST,
        'category_id',
        FILTER_VALIDATE_INT
    );

    if (!$category_id) {

        setMessage("Please select a category.");

        header("Location: edit.php?id=" . $id);
        exit;
    }

    $subcategory_name = preg_replace(
        '/\s+/',
        ' ',
        sanitizeInput($_POST['subcategory_name'])
    );

    if (empty($subcategory_name)) {

        setMessage("Sub Category name is required.");

        header("Location: edit.php?id=" . $id);
        exit;
    }

    if (strlen($subcategory_name) > 100) {

        setMessage("Sub Category name must not exceed 100 characters.");

        header("Location: edit.php?id=" . $id);
        exit;
    }


    $check = $mysqli->prepare(
        "SELECT id
         FROM subcategories
         WHERE category_id = ?
         AND subcategory_name = ?
         AND id != ?"
    );

    $check->bind_param(
        "isi",
        $category_id,
        $subcategory_name,
        $id
    );

    $check->execute();

    if ($check->get_result()->num_rows > 0) {

        $check->close();

        setMessage("Sub Category already exists in this category.");

        header("Location: edit.php?id=" . $id);
        exit;
    }

    $check->close();

    $stmt = $mysqli->prepare(
        "UPDATE subcategories
         SET
            category_id = ?,
            subcategory_name = ?
         WHERE id = ?"
    );

    $stmt->bind_param(
        "isi",
        $category_id,
        $subcategory_name,
        $id
    );

    if ($stmt->execute()) {

        $stmt->close();

        setMessage("Sub Category updated successfully!");

        header("Location: index.php");
        exit;
    }

    $stmt->close();

    setMessage("Failed to update Sub Category.");

    header("Location: edit.php?id=" . $id);
    exit;
}