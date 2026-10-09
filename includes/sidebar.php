<?php
require_once __DIR__ . '/permission.php';
require 'auth.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <a href="/pms/dashboard.php" class="logo-link">
                <img src="/pms/assets/img/PM.png"
                    alt="PMS Logo"
                    width="60">
            </a>
            <div class="sidebar-user">
                <h4>PMS</h4>

                <h6><?= htmlspecialchars($_SESSION['name']) ?>
                    <span><?= ucfirst($_SESSION['role']) ?></span>
                </h6>
            </div>
        </div>


        <ul>
            <li>
                <a href="/pms/dashboard.php">Dashboard</a>
            </li>

            <li>
                <a href="/pms/modules/products/index.php">Products</a>
            </li>

            <li>
                <a href="/pms/modules/categories/index.php">Categories</a>
            </li>

            <li>
                <a href="/pms/modules/sub_categories/index.php">Subcategories</a>
            </li>

            <?php if (isAdmin()) { ?>
                <li>
                    <a href="/pms/modules/users/index.php">Users</a>
                </li>
            <?php } ?>

            <li>
                <a href="/pms/auth/logout.php">Logout</a>
            </li>
        </ul>
    </div>
</body>

</html>