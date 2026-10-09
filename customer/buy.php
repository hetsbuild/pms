<?php

session_start();
require '../config/db.php';

// Check product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$product_id = (int) $_GET['id'];

// Check customer login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {

    // Save product id so we can buy it after login
    $_SESSION['buy_product_id'] = $product_id;

    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get product price
$stmt = $mysqli->prepare("
    SELECT price
    FROM products
    WHERE id = ?
");

$stmt->bind_param("i", $product_id);
$stmt->execute();

$result = $stmt->get_result();
$product = $result->fetch_assoc();

$total_price = $product['price'];

// Check if already purchased (optional)
$stmt = $mysqli->prepare("
    SELECT id
    FROM purchases
    WHERE user_id = ? AND product_id = ?
");


// multiple time purchase product block

// $stmt->bind_param("ii", $user_id, $product_id);
// $stmt->execute();
// $result = $stmt->get_result();

// if ($result->num_rows > 0) {

//     echo "<script>
//         alert('You have already purchased this product.');
//         window.location='dashboard.php';
//     </script>";
//     exit;
// }


// Insert purchase
$stmt = $mysqli->prepare("
    INSERT INTO purchases(user_id, product_id,total_price)
    VALUES(?, ?, ?)
");

$stmt->bind_param("iii", $user_id, $product_id, $total_price);
$stmt->execute();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Success</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    title: "Success!",
    text: "Product purchased successfully.",
    icon: "success",
    confirmButtonText: "OK"
}).then(() => {
    window.location.href = "purchases.php";
});
</script>

</body>
</html>
<?php
exit;
