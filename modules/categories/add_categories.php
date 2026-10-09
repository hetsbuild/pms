<?php

require '../../includes/auth.php';
require '../../includes/security.php';
require '../../includes/flash_message.php';
require_once '../../config/db.php';
include '../../includes/header.php';


if (isset($_POST['submit'])) {

    $category_name = sanitizeInput($_POST['category_name']);

    if (empty($category_name)) {

        setMessage("Category name is required.");

        header("Location: add_categories.php");
        exit;
    }

    if (strlen($category_name) > 100) {

        setMessage("Category name must not exceed 100 characters.");

        header("Location: add_categories.php");
        exit;
    }
    $check = $mysqli->prepare("
    SELECT id
    FROM categories
    WHERE category_name = ?
");

    $check->bind_param("s", $category_name);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {

        setMessage("Category already exists.");

        header("Location: add_categories.php");
        exit;
    }

    $stmt = $mysqli->prepare(
        "INSERT INTO categories (category_name)
         VALUES (?)"
    );

    $stmt->bind_param("s", $category_name);

    if ($stmt->execute()) {

        setMessage("Category added successfully!");

        header("Location: index.php");
        exit;
    }

    setMessage("Failed to add category.");

    header("Location: add_categories.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Category</title>
    <link rel="stylesheet" href="/pms/assets/css/style.css">
</head>

<body>
    <?php include '../../includes/sidebar.php'; ?>
    <div class="main-content">

        <h2>Add Category</h2>
        <?php showMessages(); ?>

        <form method="POST">
            <label>Category Name</label>
            <input type="text" name="category_name" placeholder="Enter Category Name" required>

            <button type="submit" name="submit">
                Add Category
            </button>
        </form>

</body>

</html>