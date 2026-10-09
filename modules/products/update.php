<?php

require '../../includes/auth.php';
require '../../includes/security.php';
require '../../config/db.php';

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    die("Invalid Product ID");
}

$product_name = sanitizeInput($_POST['product_name']);
$description = sanitizeInput($_POST['description']);

$price = filter_input(
    INPUT_POST,
    'price',
    FILTER_VALIDATE_FLOAT
);

$quantity = filter_input(
    INPUT_POST,
    'quantity',
    FILTER_VALIDATE_INT
);

$category_id = filter_input(
    INPUT_POST,
    'category_id',
    FILTER_VALIDATE_INT
);

$status = filter_input(
    INPUT_POST,
    'status',
    FILTER_VALIDATE_INT
);

$weight = sanitizeInput($_POST['weight']);
$color = sanitizeInput($_POST['color']);

if (
    $price === false ||
    $quantity === false ||
    $category_id === false
) {
    die("Invalid Input");
}

$stmt = $mysqli->prepare(
    "SELECT *
     FROM products
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$product = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$product) {
    die("Product Not Found");
}

$image = $product['primary_image'];


if (
    isset($_FILES['image']) &&
    $_FILES['image']['error'] == 0
) {

    $allowed = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];

    $extension = strtolower(
        pathinfo(
            $_FILES['image']['name'],
            PATHINFO_EXTENSION
        )
    );

    if (!in_array($extension, $allowed)) {
        die("Invalid Image Type");
    }

    if ($_FILES['image']['size'] > 2097152) {
        die("Image size must be below 2MB");
    }

    $new_image =
        time() . '_' .
        basename($_FILES['image']['name']);

    $upload_dir =
        '../../assets/uploads/products/';

    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        $upload_dir . $new_image
    );

    if (
        !empty($product['primary_image']) &&
        file_exists(
            $upload_dir .
                $product['primary_image']
        )
    ) {

        unlink(
            $upload_dir .
                $product['primary_image']
        );
    }

    $image = $new_image;
}


$update = $mysqli->prepare(
    "UPDATE products
     SET
        product_name = ?,
        price = ?,
        quantity = ?,
        description = ?,
        category_id = ?,
        status = ?,
        primary_image = ?
     WHERE id = ?"
);

$update->bind_param(
    "sdisiisi",
    $product_name,
    $price,
    $quantity,
    $description,
    $category_id,
    $status,
    $image,
    $id
);

$update->execute();

//galary images uplod

if (
    isset($_FILES['gallery_images']) &&
    !empty($_FILES['gallery_images']['name'][0])
) {

    $upload_dir = "../../assets/uploads/products/";

    foreach ($_FILES['gallery_images']['name'] as $key => $imageName) {

        if ($_FILES['gallery_images']['error'][$key] == 0) {

            $extension = strtolower(
                pathinfo(
                    $imageName,
                    PATHINFO_EXTENSION
                )
            );

            $allowed = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];

            if (!in_array($extension, $allowed)) {
                continue;
            }

            $newImage =
                time() . "_" .
                basename($imageName);

            move_uploaded_file(
                $_FILES['gallery_images']['tmp_name'][$key],
                $upload_dir . $newImage
            );

            $stmt = $mysqli->prepare("
                INSERT INTO product_images
                (
                    product_id,
                    image_name
                )
                VALUES
                (
                    ?, ?
                )
            ");

            $stmt->bind_param(
                "is",
                $id,
                $newImage
            );

            $stmt->execute();
        }
    }
}

$meta_data = [
    'weight' => $weight,
    'color' => $color
];

foreach ($meta_data as $key => $value) {

    $check = $mysqli->prepare(
        "SELECT id
         FROM product_meta
         WHERE product_id = ?
         AND meta_key = ?"
    );

    $check->bind_param(
        "is",
        $id,
        $key
    );

    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows > 0) {

        $updateMeta = $mysqli->prepare(
            "UPDATE product_meta
             SET meta_value = ?
             WHERE product_id = ?
             AND meta_key = ?"
        );

        $updateMeta->bind_param(
            "sis",
            $value,
            $id,
            $key
        );

        $updateMeta->execute();
    } else {

        $insertMeta = $mysqli->prepare(
            "INSERT INTO product_meta
            (
                product_id,
                meta_key,
                meta_value
            )
            VALUES
            (
                ?, ?, ?
            )"
        );

        $insertMeta->bind_param(
            "iss",
            $id,
            $key,
            $value
        );

        $insertMeta->execute();
    }
}

header("Location: index.php");
exit;
