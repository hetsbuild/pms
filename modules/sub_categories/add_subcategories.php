<?php

require "../../includes/auth.php";
require "../../includes/security.php";
require "../../includes/flash_message.php";
require_once "../../config/db.php";
include '../../includes/header.php';

$categories = $mysqli->query("
    SELECT *
    FROM categories
    ORDER BY category_name
");

if (isset($_POST['submit'])) {

    $category_id = filter_input(
        INPUT_POST,
        'category_id',
        FILTER_VALIDATE_INT
    );

    $subcategory_name = preg_replace(
        '/\s+/',
        ' ',
        sanitizeInput($_POST['subcategory_name'])
    );

    // Category Validation
    if (!$category_id) {

        setMessage("Please select a category.");

        header("Location: add_subcategories.php");
        exit;
    }

    // Name Validation
    if (empty($subcategory_name)) {

        setMessage("Sub Category name is required.");

        header("Location: add_subcategories.php");
        exit;
    }

    // Length Validation
    if (strlen($subcategory_name) > 100) {

        setMessage("Sub Category name must not exceed 100 characters.");

        header("Location: add_subcategories.php");
        exit;
    }

    // Duplicate Validation
    $check = $mysqli->prepare(
        "SELECT id
         FROM subcategories
         WHERE category_id = ?
         AND subcategory_name = ?"
    );

    $check->bind_param(
        "is",
        $category_id,
        $subcategory_name
    );

    $check->execute();

    if ($check->get_result()->num_rows > 0) {

        $check->close();

        setMessage("Sub Category already exists in this category.");

        header("Location: add_subcategories.php");
        exit;
    }

    $check->close();

    // Insert Sub Category
    $stmt = $mysqli->prepare(
        "INSERT INTO subcategories
        (
            category_id,
            subcategory_name
        )
        VALUES
        (
            ?, ?
        )"
    );

    $stmt->bind_param(
        "is",
        $category_id,
        $subcategory_name
    );

    if ($stmt->execute()) {

        $stmt->close();

        setMessage("Sub Category added successfully!");

        header("Location: index.php");
        exit;
    }

    $stmt->close();

    setMessage("Failed to add Sub Category.");

    header("Location: add_subcategories.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Sub Category</title>
    <link rel="stylesheet" href="/pms/assets/css/style.css">
</head>

<body>

    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">

        <h2>Add Sub Category</h2>

        <?php showMessages(); ?>

        <form method="POST">

            <label>Category</label><br>

            <select name="category_id" required>

                <option value="">Select Category</option>

                <?php while ($cat = $categories->fetch_assoc()) { ?>

                    <option value="<?= $cat['id']; ?>">

                        <?= escapeOutput($cat['category_name']); ?>

                    </option>

                <?php } ?>

            </select>

            <br><br>

            <label>Sub Category Name</label><br>

            <input
                type="text"
                name="subcategory_name"
                placeholder="Enter Sub Category"
                maxlength="100"
                required>

            <br><br>

            <button
                type="submit"
                name="submit">

                Add Sub Category

            </button>

        </form>

    </div>

</body>

</html>