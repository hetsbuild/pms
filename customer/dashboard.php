<?php

session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("
    SELECT COUNT(*) AS total
    FROM purchases
    WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$totalPurchase = $row['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Customer Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

    <h2 class="mb-4">
        Welcome,
        <?= htmlspecialchars($_SESSION['name']) ?>
    </h2>

    <div class="row">

        <div class="col-md-4">

            <div class="card shadow text-center">

                <div class="card-body">

                    <h5>Total Purchased Products</h5>

                    <h1 class="text-success">
                        <?= $totalPurchase ?>
                    </h1>

                </div>

            </div>

        </div>

    </div>

    <div class="mt-4">

        <a href="index.php" class="btn btn-primary">
            Buy More Products
        </a>

        <a href="purchases.php" class="btn btn-success">
            My Purchases
        </a>

        <a href="logout.php" class="btn btn-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>

</body>
</html>