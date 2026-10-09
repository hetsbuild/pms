<?php
// require_once "includes/header.php";
require 'includes/auth.php';
require_once "config/db.php";
include 'includes/header.php';

// Selected year for Revenue Chart
$selectedYear = isset($_GET['year'])
    ? (int) $_GET['year']
    : date('Y');

// session_start();

if (
    !isset($_SESSION['role']) ||
    !in_array($_SESSION['role'], ['admin', 'staff'])
) {
    header("Location: customer/login.php");
    exit;
}

//categories
$sql = " select count(*) as total_categories  from  categories";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$totalCategories = $row['total_categories'];



//sub-categories 

$sql = " select count(*) as total_sub_categories  from  subcategories";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$totalSubCategories = $row['total_sub_categories'];


//fetch products
$sql = "SELECT COUNT(*) AS total_products FROM products";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$totalProducts = $row['total_products'];


//active product 

$sql = "select count(*) as active_products  from  products where status = 1";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$activeProducts = $row['active_products'];


//user

$sql = " select count(*) as ttl_users  from  users";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$totalUser = $row['ttl_users'];


//active user
$sql = " select count(*) as active_users  from  users where status = 1";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$activeUser = $row['active_users'];
?>


<!-- chart for order by week -->
<?php
$weekLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
$weekData = [0, 0, 0, 0, 0];

$sql = "
SELECT
    FLOOR((DAY(purchase_date)-1)/7)+1 AS week_no,
    SUM(quantity) AS total_products
FROM purchases
WHERE
    MONTH(purchase_date)=MONTH(CURDATE())
AND YEAR(purchase_date)=YEAR(CURDATE())
GROUP BY week_no
";

$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {

    $weekData[$row['week_no'] - 1] = (int)$row['total_products'];
}
?>

<!-- //chart for revenue -->
<?php

// Get available years
$years = [];

$yearQuery = $mysqli->query("
    SELECT DISTINCT YEAR(purchase_date) AS year
    FROM purchases
    ORDER BY year DESC
");

while ($row = $yearQuery->fetch_assoc()) {
    $years[] = $row['year'];
}

// Month labels
$monthLabels = [
    'Jan',
    'Feb',
    'Mar',
    'Apr',
    'May',
    'Jun',
    'Jul',
    'Aug',
    'Sep',
    'Oct',
    'Nov',
    'Dec'
];

// Revenue array (default 0)
$monthRevenue = array_fill(0, 12, 0);

$sql = "
SELECT
    MONTH(purchase_date) AS month_no,
    SUM(total_price) AS revenue
FROM purchases
WHERE YEAR(purchase_date) = $selectedYear
GROUP BY MONTH(purchase_date)
ORDER BY MONTH(purchase_date)
";

$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {

    $monthRevenue[$row['month_no'] - 1] = (float)$row['revenue'];
}
?>

<!-- piechart -->
<?php

$productLabels = [];
$productQuantity = [];

$sql = "
SELECT
    p.product_name,
    SUM(pr.quantity) AS total_quantity
FROM purchases pr
JOIN products p
ON pr.product_id = p.id
GROUP BY pr.product_id
ORDER BY total_quantity DESC
LIMIT 5
";

$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {

    $productLabels[] = $row['product_name'];
    $productQuantity[] = (int)$row['total_quantity'];
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- <link rel="icon" type="image/png" href="/pms/assets/img/logo.jpg"> -->

</head>

<body>

    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">

        <h1 class="dashboard-title">
            Dashboard
        </h1>

        <div class="dashboard-cards">

            <div class="dashboard-card">
                <h3>Total Categories</h3>
                <p><?= $totalCategories; ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Total Subcategories</h3>
                <p><?= $totalSubCategories; ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Total Products</h3>
                <p><?= $totalProducts; ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Active Products</h3>
                <p><?= $activeProducts; ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Total Users</h3>
                <p><?= $totalUser; ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Active Users</h3>
                <p><?= $activeUser; ?></p>
            </div>

        </div>

        <!-- charts -->
         <?php if (isAdmin()) { ?>
        <div class="row mt-4">

            <!-- Weekly -->
            <div class="col-md-4">

                <div class="card shadow">

                    <div class="card-header">
                        Weekly Product Sales
                    </div>

                    <div class="card-body">
                        <div style="height:300px;">
                            <canvas id="weeklyChart"></canvas>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Revenue -->
            <div class="col-md-4">

                <div class="card shadow">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <span>Monthly Revenue</span>

                        <form method="GET" class="m-0">

                            <select
                                name="year"
                                class="form-select form-select-sm"
                                onchange="this.form.submit()">

                                <?php foreach ($years as $year): ?>

                                    <option
                                        value="<?= $year ?>"
                                        <?= $selectedYear == $year ? 'selected' : '' ?>>

                                        <?= $year ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </form>

                    </div>

                    <div class="card-body">

                        <div style="height:300px;">
                            <canvas id="revenueChart"></canvas>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Pie -->
            <div class="col-md-4">

                <div class="card shadow">

                    <div class="card-header">
                        Top Purchased Products
                    </div>

                    <div class="card-body">

                        <div style="height:300px;">
                            <canvas id="productPieChart"></canvas>
                        </div>

                    </div>

                </div>

            </div>

        </div>
        <?php } ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- weekly charts -->
    <script>
        const weeklyChart = new Chart(document.getElementById('weeklyChart'), {

            type: 'bar',

            data: {

                labels: <?= json_encode($weekLabels) ?>,

                datasets: [{

                    label: 'Products Sold',

                    data: <?= json_encode($weekData) ?>,

                    backgroundColor: '#0d6efd',

                    borderRadius: 8

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {

                            precision: 0

                        }

                    }

                }

            }

        });
    </script>

    <!-- revenue chart -->
    <script>
        const revenueChart = new Chart(document.getElementById('revenueChart'), {

            type: 'line',

            data: {

                labels: <?= json_encode($monthLabels) ?>,

                datasets: [{

                    label: 'Revenue',

                    data: <?= json_encode($monthRevenue) ?>,

                    borderColor: '#198754',

                    backgroundColor: 'rgba(25,135,84,.2)',

                    fill: true,

                    tension: .4

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false

            }

        });

        //pie chart
        const productPieChart = new Chart(
            document.getElementById('productPieChart'), {

                type: 'pie',

                data: {

                    labels: <?= json_encode($productLabels) ?>,

                    datasets: [{

                        data: <?= json_encode($productQuantity) ?>,

                        backgroundColor: [

                            '#0d6efd',
                            '#198754',
                            '#ffc107',
                            '#dc3545',
                            '#6f42c1'

                        ]

                    }]

                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    plugins: {

                        legend: {

                            position: 'bottom'

                        }

                    }

                }

            }
        );
    </script>

</body>

</html>