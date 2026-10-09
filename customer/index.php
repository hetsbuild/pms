<?php
session_start();
require '../config/db.php';

$sql = "SELECT
            id,
            product_name,
            brand,
            sale_price,
            primary_image
        FROM products
        WHERE status = 1
        ORDER BY created_at DESC";

$result = $mysqli->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
        }

        .card {
            transition: .3s;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .15);
        }

        .card img {
            height: 220px;
            object-fit: contain;
            padding: 15px;
        }

        .price {
            color: #198754;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <h2 class="text-center mb-4">
            Our Products
        </h2>
        <?php
        if (isset($_SESSION['user_id'])) {
        ?>
            <button type="button" onclick="window.location.href='dashboard.php'">
                Move Back
            </button>
        <?php } ?><br><br>
        <div class="row">

            <?php if ($result->num_rows > 0): ?>

                <?php while ($product = $result->fetch_assoc()): ?>

                    <?php
                    $imagePath = "../assets/uploads/products/" . $product['primary_image'];

                    if (empty($product['primary_image']) || !file_exists($imagePath)) {
                        $imagePath = "../assets/img/no-image.png";
                    }
                    ?>

                    <div class="col-md-3 mb-4">

                        <div class="card h-100 shadow-sm">

                            <img
                                src="<?= htmlspecialchars($imagePath) ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($product['product_name']) ?>">

                            <div class="card-body d-flex flex-column">

                                <h5 class="card-title">
                                    <?= htmlspecialchars($product['product_name']) ?>
                                </h5>

                                <p class="text-muted mb-1">
                                    <?= htmlspecialchars($product['brand']) ?>
                                </p>

                                <p class="price">
                                    ₹<?= number_format($product['sale_price'], 2) ?>
                                </p>

                                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'customer') { ?>

                                    <a href="buy.php?id=<?= $product['id'] ?>" class="btn btn-success w-100">
                                        Buy Now
                                    </a>

                                <?php } else { ?>

                                    <a href="#" class="btn btn-success w-100"
                                        onclick="loginRequired(); return false;">
                                        Buy Now
                                    </a>

                                <?php } ?>

                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="col-12">

                    <div class="alert alert-warning text-center">
                        No Products Found.
                    </div>

                </div>

            <?php endif; ?>

        </div>

        <?php
        // session_start();
        if (isset($_SESSION['user_id'])) {
        ?>
            <button type="button" onclick="window.location.href='dashboard.php'">
                Move Back
            </button>
        <?php } ?>
    </div>

    <script>
        function loginRequired() {
            alert("Please login first to purchase this product.");
            window.location.href = "../auth/login.php";
        }
    </script>

</body>

</html>