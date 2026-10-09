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
     FROM subcategories
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

$subcategory = $result->fetch_assoc();

$stmt->close();

if (!$subcategory) {

    die("Sub Category not found.");
}


$categories = $mysqli->query(
    "SELECT *
     FROM categories
     ORDER BY category_name"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Edit Sub Category</title>

    <link
        rel="stylesheet"
        href="/pms/assets/css/style.css">

</head>

<body>

    <?php include "../../includes/sidebar.php"; ?>

    <div class="main-content">

        <h2>Edit Sub Category</h2>

        <form
            action="update.php"
            method="POST">

            <input
                type="hidden"
                name="id"
                value="<?= $subcategory['id']; ?>">

            <label>Parent Category</label>

            <select
                name="category_id"
                required>

                <?php while ($category = $categories->fetch_assoc()) { ?>

                    <option
                        value="<?= $category['id']; ?>"

                        <?= ($category['id'] == $subcategory['category_id'])
                            ? 'selected'
                            : ''; ?>>

                        <?= escapeOutput(
                            $category['category_name']
                        ); ?>

                    </option>

                <?php } ?>

            </select>

            <br><br>

            <label>Sub Category Name</label>

            <input
                type="text"
                name="subcategory_name"
                value="<?= escapeOutput($subcategory['subcategory_name']); ?>"
                maxlength="100"
                required>

            <br><br>

            <button type="submit">

                Update

            </button>

        </form>

    </div>

</body>

</html>