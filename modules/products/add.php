<?php
require '../../includes/security.php';
require '../../includes/auth.php';
require_once '../../config/db.php';
require "../../includes/flash_message.php";
include '../../includes/header.php';

if (
    isset($_POST['action']) &&
    $_POST['action'] == 'get_subcategories'
) {

    $category_id = (int)$_POST['category_id'];

    $stmt = $mysqli->prepare("
        SELECT id, subcategory_name
        FROM subcategories
        WHERE category_id = ?
        ORDER BY subcategory_name
    ");

    $stmt->bind_param("i", $category_id);
    $stmt->execute();

    $result = $stmt->get_result();

    echo '<option value="">Select Sub Category</option>';

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['id'] . '">'
            . htmlspecialchars($row['subcategory_name'])
            . '</option>';
    }

    exit;
}

$categories = $mysqli->query("
    SELECT *
    FROM categories
    ORDER BY category_name
");


?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="/pms/assets/css/style.css">
</head>

<body>

    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">

        <h2>Add Product</h2>
        <?php showMessages(); ?>


        <?php if (isset($_GET['error'])) { ?>
            <p style="color:red;">
                Please fill all required fields.
            </p>
        <?php } ?>

        <form
            action="insert.php"
            method="POST"
            enctype="multipart/form-data">

            <label>Category</label><br>

            <select name="category_id" id="category_id" required>
                <option value="">Select Category</option>

                <?php while ($cat = $categories->fetch_assoc()) { ?>
                    <option value="<?php echo $cat['id']; ?>">
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
                <?php } ?>

            </select>

            <br><br>

            <label>Sub Category</label><br>

            <select name="subcategory_id" id="subcategory_id" required>
                <option value="">Select Sub Category</option>
            </select>

            <br><br>

            <label>Product Name</label><br>

            <input
                type="text"
                name="product_name"
                placeholder="Enter Name"
                required>

            <br><br>

            <label>Brand</label><br>

            <input
                type="text"
                name="brand"
                placeholder="Enter Name">

            <br><br>

            <label>Price</label><br>

            <input
                type="number"
                step="0.01"
                name="price"
                placeholder="Enter Price"
                required>

            <br><br>

            <label>Sale Price</label><br>

            <input
                type="number"
                step="0.01"
                name="sale_price"
                placeholder="Enter SalePrice">

            <br><br>

            <label>Quantity</label><br>

            <input
                type="number"
                name="quantity"
                value="0">

            <br><br>

            <label>Description</label><br>

            <textarea
                name="description"
                rows="5"
                cols="50"
                placeholder="Enter Product Description"></textarea>

            <br><br>

            <label>Primary Image</label><br>

            <input
                type="file"
                name="primary_image"
                accept=".jpg,.jpeg,.png,.webp">

            <br><br>


            <label>Gallery Images</label><br>

            <input
                type="file"
                name="gallery_images[]"
                multiple
                accept=".jpg,.jpeg,.png,.webp">

            <br><br>
            <label>Status</label><br>

            <select name="status">

                <option value="1">
                    Active
                </option>

                <option value="0">
                    Inactive
                </option>

            </select>
            <br><br>
            <label>Weight</label><br>
            <input type="text" name="weight" placeholder="Weight">
            <br><br><label>Color</label><br>
            <input type="text" name="color" placeholder="Color">
            <br><br>
            <button type="submit" name="submit">
                Add Product
            </button>

        </form>

    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#category_id').change(function() {

                let category_id = $(this).val();

                $.ajax({
                    url: 'add.php',
                    type: 'POST',
                    data: {
                        action: 'get_subcategories',
                        category_id: category_id
                    },
                    success: function(response) {
                        $('#subcategory_id').html(response);
                    }
                });

            });

        });
    </script>
</body>

</html>