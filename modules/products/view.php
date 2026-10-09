<?php
require '../../includes/security.php';
require '../../includes/auth.php';
require '../../config/db.php';
include '../../includes/header.php';

if (!isset($_GET['id'])) {

    die("Product ID Missing");
}

$id =  $_GET['id'];

if (!preg_match("/^\d+$/", $id)) {
    die("Invalid Product ID Missing");
}

$stmt = $mysqli->prepare(
    "SELECT
        p.*,
        c.category_name,
        s.subcategory_name,
        u.name AS created_by_name
    FROM products p

    INNER JOIN categories c
        ON p.category_id = c.id

    INNER JOIN subcategories s
        ON p.subcategory_id = s.id

    LEFT JOIN users u
        ON p.created_by = u.id

    WHERE p.id = $id "
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    die("Product not found");
}

$product = $result->fetch_assoc();

$stmt->close();


$meta_stmt = $mysqli->prepare(
    "SELECT
        meta_key,
        meta_value
    FROM product_meta
    WHERE product_id = ?"
);

$meta_stmt->bind_param("i", $id);
$meta_stmt->execute();

$meta_result = $meta_stmt->get_result();

$meta = [];

while ($row = $meta_result->fetch_assoc()) {

    $meta[$row['meta_key']] =
        $row['meta_value'];
}

$meta_stmt->close();

//fetch multiple images
$gallery_stmt = $mysqli->prepare(
    "SELECT *
     FROM product_images
     WHERE product_id = ?"
);

$gallery_stmt->bind_param("i", $id);
$gallery_stmt->execute();

$gallery_images = $gallery_stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>

    <title>
        View Product
    </title>

    <link
        rel="stylesheet"
        href="/pms/assets/css/style.css">

</head>

<body>

    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">

        <h2>
            Product Details
        </h2>

        <table
            border="2"
            cellpadding="5"
            cellspacing="2"
            width="50%">

            <tr>
                <th width="40%">
                    Product Name
                </th>

                <td>
                    <?php echo htmlspecialchars($product['product_name']); ?>
                </td>
            </tr>

            <tr>
                <th>
                    Category
                </th>

                <td>
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </td>
            </tr>

            <tr>
                <th>
                    Sub Category
                </th>

                <td>
                    <?php echo htmlspecialchars($product['subcategory_name']); ?>
                </td>
            </tr>

            <tr>
                <th>
                    Brand
                </th>

                <td>
                    <?php echo htmlspecialchars($product['brand']); ?>
                </td>
            </tr>

            <tr>
                <th>
                    Price
                </th>

                <td>
                    <?php echo number_format($product['price'], 2); ?>
                </td>
            </tr>

            <tr>
                <th>
                    Sale Price
                </th>

                <td>
                    <?php
                    echo number_format($product['sale_price'], 2);
                    ?>
                </td>
            </tr>

            <tr>
                <th>
                    Quantity
                </th>

                <td>
                    <?php echo $product['quantity']; ?>
                </td>
            </tr>

            <tr>
                <th>
                    Status
                </th>

                <td>

                    <?php
                    echo $product['status']
                        ? 'Active'
                        : 'Inactive';
                    ?>

                </td>
            </tr>

            <tr>
                <th>
                    Created By
                </th>

                <td>
                    <?php echo htmlspecialchars($product['created_by_name'] ?? 'Unknown'); ?>
                </td>
            </tr>

            <tr>
                <th>
                    Description
                </th>

                <td>
                    <?php echo nl2br(htmlspecialchars($product['description'] ?? ' - ')); ?>

                </td>
            </tr>

            <tr>
                <th>
                    Primary Image
                </th>

                <td>

                    <?php if (!empty($product['primary_image'])) { ?>

                        <img
                            src="../../assets/uploads/products/<?php echo $product['primary_image']; ?>"
                            width="100%">

                    <?php } else { ?>

                        No Image

                    <?php } ?>

                </td>
            </tr>

            <tr>
                <th>
                    Gallery Images
                </th>

                <td>

                    <?php if ($gallery_images->num_rows > 0) { ?>

                        <div
                            style="
                    display:flex;
                    flex-wrap:wrap;
                    gap:10px;
                ">

                            <?php while ($img = $gallery_images->fetch_assoc()) { ?>

                                <img
                                    src="../../assets/uploads/products/<?=
                                                                        htmlspecialchars($img['image_name']); ?>"
                                    width="150"
                                    height="150"
                                    style="
                            border:1px solid #ddd;
                            padding:5px;
                            object-fit:cover;
                        ">

                            <?php } ?>

                        </div>

                    <?php } else { ?>

                        No Gallery Images

                    <?php } ?>

                </td>
            </tr>
        </table>

        <br>

        <h3>
            Product Specifications
        </h3>

        <table
            border="2"
            cellpadding="5"
            cellspacing="2"
            width="50%">

            <tr>
                <th width="50%">
                    Weight
                </th>

                <td>
                    <?php echo htmlspecialchars($meta['weight'] ?? '-'); ?>
                </td>
            </tr>

            <tr>
                <th>
                    Color
                </th>

                <td>
                    <?php echo htmlspecialchars($meta['color'] ?? '-'); ?>
                </td>
            </tr>
        </table>

        <br>

        <a href="index.php">
            Back To Products
        </a>

    </div>

</body>

</html>