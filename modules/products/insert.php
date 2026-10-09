<?php
require '../../includes/security.php';
require '../../includes/auth.php';
require '../../includes/flash_message.php';
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $category_id = (int) $_POST['category_id'];
    $subcategory_id = (int) $_POST['subcategory_id'];

    $product_name = sanitizeInput(
        $_POST['product_name']
    );
    $brand = sanitizeInput(
        $_POST['brand']
    );

    $price = (float) $_POST['price'];

    $sale_price = !empty($_POST['sale_price'])
        ? (float) $_POST['sale_price']
        : null;

    $quantity = (int) $_POST['quantity'];

    $description = sanitizeInput(
        $_POST['description']
    );

    $status = (int) $_POST['status'];

    $created_by = $_SESSION['user_id'];

    // Validation
    if (
        empty($product_name) ||
        $price <= 0 ||
        $category_id <= 0 ||
        $subcategory_id <= 0
    ) {

        setMessage("Please fill all required fields.");

        header("Location: add.php");
        exit;
    }

    $image_name = null;

    // Upload Image
    if (
        isset($_FILES['primary_image']) &&
        $_FILES['primary_image']['error'] == 0
    ) {

        $image_name =
            time() . '_' .
            basename($_FILES['primary_image']['name']);

        $upload_dir = '../../assets/uploads/products/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $upload_path = $upload_dir . $image_name;

        if (
            !move_uploaded_file(
                $_FILES['primary_image']['tmp_name'],
                $upload_path
            )
        ) {

            setMessage("Image upload failed.");

            header("Location: add.php");
            exit;
        }
    }

    $stmt = $mysqli->prepare(
        "INSERT INTO products
        (
            category_id,
            subcategory_id,
            product_name,
            brand,
            price,
            sale_price,
            quantity,
            description,
            primary_image,
            status,
            created_by
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )"
    );

    $stmt->bind_param(
        "iissddissii",
        $category_id,
        $subcategory_id,
        $product_name,
        $brand,
        $price,
        $sale_price,
        $quantity,
        $description,
        $image_name,
        $status,
        $created_by
    );

    if ($stmt->execute()) {

        $product_id = $mysqli->insert_id;

        // Upload Multiple Images
        if (
            isset($_FILES['gallery_images']) &&
            !empty($_FILES['gallery_images']['name'][0])
        ) {

            $gallery_stmt = $mysqli->prepare(
                "INSERT INTO product_images
        (
            product_id,
            image_name
        )
        VALUES
        (
            ?, ?
        )"
            );

            foreach (
                $_FILES['gallery_images']['tmp_name']
                as $key => $tmp_name
            ) {

                if (
                    $_FILES['gallery_images']['error'][$key] == 0
                ) {

                    $gallery_image =
                        time() . '_' .
                        basename(
                            $_FILES['gallery_images']['name'][$key]
                        );

                    move_uploaded_file(
                        $tmp_name,
                        '../../assets/uploads/products/' .
                            $gallery_image
                    );

                    $gallery_stmt->bind_param(
                        "is",
                        $product_id,
                        $gallery_image
                    );

                    $gallery_stmt->execute();
                }
            }

            $gallery_stmt->close();
        }
        $meta_data = [

            'weight' => trim($_POST['weight']),
            'color' => trim($_POST['color'])

        ];

        $meta_stmt = $mysqli->prepare(
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

        foreach ($meta_data as $key => $value) {

            if (!empty($value)) {

                $meta_stmt->bind_param(
                    "iss",
                    $product_id,
                    $key,
                    $value
                );

                $meta_stmt->execute();
            }
        }

        $meta_stmt->close();
        setMessage("Product added successfully!");

        header("Location: index.php");
        exit;
    } else {

        setMessage("Database Error: " . $stmt->error);

        header("Location: add.php");
        exit;
    }


    $stmt->close();
}
