<?php

require '../../includes/auth.php';
require '../../includes/security.php';
require '../../config/db.php';
include '../../includes/header.php';

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
     FROM categories
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

$category = $result->fetch_assoc();

if (!$category) {

    die("Category not found.");
}

$stmt->close();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Category</title>
    <link rel="stylesheet" href="/pms/assets/css/style.css">
</head>

<body>

    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">

        <h2>Edit Category</h2>

        <form action="update.php" method="POST">

            <input
                type="hidden"
                name="id"
                value="<?= $category['id']; ?>">

            <label>Category Name</label>

            <input
                type="text"
                name="category_name"
                value="<?= escapeOutput($category['category_name']); ?>"
                required
                maxlength="100">

            <button type="submit">
                Update
            </button>

        </form>

    </div>

</body>

</html>