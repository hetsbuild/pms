<?php

require "../../includes/auth.php";
require "../../includes/flash_message.php";
require_once "../../config/db.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {

    header("Location:index.php");

    exit;
}

// Get image name before deleting
$stmt = $mysqli->prepare("SELECT primary_image FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$product = $result->fetch_assoc();

$stmt->close();

if (!$product) {
    setMessage("Product not found.");
    header("Location: index.php");
    exit;
}

// Delete image file if exists
if (!empty($product['primary_image'])) {

    $imagePath = "../../assets/uploads/products/" . $product['primary_image'];

    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

// Delete product from database
$stmt = $mysqli->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    setMessage("Product deleted successfully!");
} else {
    setMessage("Failed to delete product.");
}

$stmt->close();

header("Location: index.php");
exit;