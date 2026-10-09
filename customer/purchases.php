<?php

session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("
    SELECT
        p.product_name,
        p.brand,
        p.sale_price,
        p.primary_image,
        pu.purchase_date
    FROM purchases pu
    INNER JOIN products p
        ON pu.product_id = p.id
    WHERE pu.user_id = ?
    ORDER BY pu.purchase_date DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>My Purchases</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">

        <h2>My Purchases</h2><br>

        <button type="button" onclick="window.location.href='dashboard.php'">
            Move Back
        </button><br><br>
        
        <table class="table table-bordered">

            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Brand</th>
                <th>Price</th>
                <th>Date</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()) { ?>

                <tr>

                    <td>
                        <img
                            src="../assets/uploads/products/<?= htmlspecialchars($row['primary_image']) ?>"
                            width="100px">
                    </td>

                    <td><?= htmlspecialchars($row['product_name']) ?></td>

                    <td><?= htmlspecialchars($row['brand']) ?></td>

                    <td><?= $row['sale_price'] ?></td>

                    <td><?= $row['purchase_date'] ?></td>

                </tr>

            <?php } ?>

        </table>

        <button type="button" onclick="window.location.href='dashboard.php'">
            Move Back
        </button>
    </div>
</body>

</html>