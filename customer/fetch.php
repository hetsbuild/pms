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
        p.price,
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