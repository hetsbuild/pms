<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../includes/auth.php";
require "../config/db.php";

if (!isset($_POST['ids'])) {

    echo "No IDs received";
    exit;
}

$ids =
    json_decode(
        $_POST['ids'],
        true
    );

if (!$ids) {

    echo "Invalid IDs";
    exit;
}

foreach ($ids as $id) {

    $id = (int)$id;

    $result =
        $mysqli->query(
            "SELECT primary_image
             FROM products
             WHERE id = $id"
        );

    if ($result->num_rows > 0) {

        $product =
            $result->fetch_assoc();

        if (
            !empty(
                $product['primary_image']
            )
        ) {

            $image =
                "../../assets/uploads/products/" .
                $product['primary_image'];

            if (file_exists($image)) {

                unlink($image);
            }
        }

        $mysqli->query(
            "DELETE FROM products
             WHERE id = $id"
        );
    }
}

echo json_encode([
    'status' => 'success',
    'deleted_ids' => $ids
]);