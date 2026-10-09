<?php
require '../../includes/security.php';
require '../../includes/auth.php';
require '../../config/db.php';
include '../../includes/header.php';

if (!isset($_GET['id'])) {

    die("Product ID Missing");
}

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);
if (!preg_match("/^\d+$/", $id)) {
    die("Invalid Product ID Missing");
}
if (!$id) {

    header("Location:index.php");

    exit;
}
$status = $_GET['status'] ?? '';

$product = $mysqli->query("
    SELECT * FROM products
    WHERE id = $id
")->fetch_assoc();

$gallery_images = $mysqli->query("
    SELECT *
    FROM product_images
    WHERE product_id = $id
    ORDER BY id ASC
");

$meta = [];

$meta_result = $mysqli->query("
    SELECT meta_key, meta_value
    FROM product_meta
    WHERE product_id = $id
");

while ($row = $meta_result->fetch_assoc()) {
    $meta[$row['meta_key']] = $row['meta_value'];
}

$weight = $meta['weight'] ?? '';
$color  = $meta['color'] ?? '';

if (!isset($product)) {

    die("Product not found");
}

$categories = $mysqli->query("
    SELECT * FROM categories
");

$subcategories = $mysqli->query("
    SELECT * FROM subcategories
");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit products</title>
    <link rel="stylesheet" href="/pms/assets/css/style.css">
</head>

<body>
    <?php include '../../includes/sidebar.php'; ?>
    <div class="main-content">


        <h2>Edit Products</h2>
        <form action="update.php" method="POST" enctype="multipart/form-data">

            <input type="hidden"
                name="id"
                value="<?php echo $product['id']; ?>"><br>

            <label>Name</label>
            <input type="text"
                name="product_name"
                value="<?= escapeOutput(
                            $product['product_name']
                        ); ?>"><br><br>

            <label>Price</label>
            <input type="text"
                name="price"
                value="<?php echo $product['price']; ?>"><br><br>

            <label>Quantity</label>
            <input type="text"
                name="quantity"
                value="<?php echo $product['quantity']; ?>"><br><br>

            <label>Category</label>

            <select name="category_id">

                <?php while ($cat = $categories->fetch_assoc()) { ?>

                    <option
                        value="<?php echo $cat['id']; ?>"
                        <?php
                        if ($cat['id'] == $product['category_id']) {
                            echo "selected";
                        }
                        ?>>
                        <?php echo $cat['category_name']; ?>
                    </option>

                <?php } ?>

            </select><br><br>

            <label>Description</label><br>

            <textarea
                name="description"
                rows="5"
                cols="50"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>

            <br><br>


            <label>Status</label>

            <select name="status">
                <option value="1"
                    <?php echo ($product['status'] == '1') ? 'selected' : ''; ?>>
                    Active
                </option>

                <option value="0"
                    <?php echo ($product['status'] == '0') ? 'selected' : ''; ?>>
                    Inactive
                </option>
            </select><br><br>

            <label>Current Image</label>

            <br><br>

            <img
                id="imagePreview"
                src="../../assets/uploads/products/<?php echo $product['primary_image']; ?>"
                width="100">

            <br><br>

            <label>New Image (Optional)</label>

            <input
                type="file"
                name="image"
                id="image"
                accept="image/*">

            <br><br>
            <label>Gallery Images</label>

            <br><br>

            <?php
            if ($gallery_images->num_rows > 0) {

                while ($img = $gallery_images->fetch_assoc()) {
            ?>

                    <img
                        src="../../assets/uploads/products/<?php echo htmlspecialchars($img['image_name']); ?>"
                        width="100"
                        style="margin-right:10px; margin-bottom:10px;">

            <?php
                }
            } else {

                echo "No gallery images found.";
            }
            ?>

            <br><br>

            <label>Add Gallery Images</label>

            <input
                type="file"
                name="gallery_images[]"
                multiple
                accept="image/*">

            <br><br>
            <label>Weight</label><br>
            <input
                type="text"
                name="weight"
                value="<?php echo htmlspecialchars($weight); ?>">

            <br><br>

            <label>Color</label><br>
            <input
                type="text"
                name="color"
                value="<?php echo htmlspecialchars($color); ?>">

            <br><br>

            <button type="submit">
                Update Product
            </button>

        </form>
        <script>
            const image = document.getElementById("image");
            const preview = document.getElementById("imagePreview");

            image.addEventListener("change", function() {

                if (this.files.length > 0) {

                    const file = this.files[0];

                    const reader = new FileReader();

                    reader.onload = function(e) {

                        preview.src = e.target.result;

                    };

                    reader.readAsDataURL(file);
                }

            });
        </script>
</body>

</html>