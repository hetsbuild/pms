<?php

require '../../includes/auth.php';
require_once '../../config/db.php';
require "../../includes/flash_message.php";
require "../../includes/permission.php";
include '../../includes/header.php';


$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : '';

$id = isset($_GET['price_filter'])
    ? (int)$_GET['price_filter']
    : 0;

$sort = isset($_GET['sort'])
    ? $_GET['sort']
    : 'newest';

$price_condition = "";

if ($id > 0) {

    $filter = $mysqli->query("
        SELECT *
        FROM price_filters
        WHERE id = $id
        AND status = 1
    ")->fetch_assoc();

    if ($filter) {

        $min_price = $filter['min_price'];
        $max_price = $filter['max_price'];

        $price_condition =
            " AND p.price BETWEEN $min_price AND $max_price ";
    }
}

$price_filters = $mysqli->query("
    SELECT *
    FROM price_filters
    WHERE status = 1
");

$records_per_page = 5;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $records_per_page;

$order_by = "p.id DESC";

switch ($sort) {

    case 'name_asc':
        $order_by = "p.product_name ASC";
        break;

    case 'name_desc':
        $order_by = "p.product_name DESC";
        break;

    case 'price_asc':
        $order_by = "p.price ASC";
        break;

    case 'price_desc':
        $order_by = "p.price DESC";
        break;

    default:
        $order_by = "p.id DESC";
        break;
}


$count_sql = "
SELECT COUNT(*) as total
FROM products p
WHERE p.product_name LIKE '%$search%'
$price_condition
";

$total_records =
    $mysqli->query($count_sql)
        ->fetch_assoc()['total'];

$total_pages = ceil($total_records / $records_per_page);


?>

<!DOCTYPE html>
<html>

<head>
    <title>Products</title>
    <link rel="stylesheet" href="/pms/assets/css/style.css">
</head>

<body>

    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid mt-4">
            <h2>Products</h2>
        </div>

        <?php showMessages(); ?>

        <?php if (isAdmin()) { ?>

            <a
                href="add.php"
                class="btn btn-primary">
                Add Product
            </a>

            <a href="export.php" class="btn btn-success">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>

        <?php } ?>

        <br><br>

        <form>

            <input
                type="text"
                id="search"
                name="search"
                placeholder="Search Product Name"
                value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">

            <select id="price_filter" name="price_filter">

                <option value="0">
                    All Prices
                </option>

                <?php while ($pf = $price_filters->fetch_assoc()) { ?>

                    <option
                        value="<?php echo $pf['id']; ?>"
                        <?php echo ($id == $pf['id']) ? 'selected' : ''; ?>>

                        <?php echo htmlspecialchars($pf['title']); ?>

                    </option>

                <?php } ?>

            </select>

        </form>

        <br>

        <br>

        <input
            type="hidden"
            name="search"
            value="<?php echo htmlspecialchars($search); ?>">

        <input
            type="hidden"
            name="price_filter"
            value="<?php echo $id; ?>">

        <label>
            Order By:
        </label>

        <select id="sort" name="sort">
            <option value="newest"
                <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>
                Newest First
            </option>

            <option value="name_asc"
                <?php echo ($sort == 'name_asc') ? 'selected' : ''; ?>>
                Name A-Z
            </option>

            <option value="name_desc"
                <?php echo ($sort == 'name_desc') ? 'selected' : ''; ?>>
                Name Z-A
            </option>

            <option value="price_asc"
                <?php echo ($sort == 'price_asc') ? 'selected' : ''; ?>>
                Price Low to High
            </option>

            <option value="price_desc"
                <?php echo ($sort == 'price_desc') ? 'selected' : ''; ?>>
                Price High to Low
            </option>

        </select>

        </form>

        <br><br>
        <div id="product-results">

        </div>
        <br><br>

    </div>

    <div id="productModal" style="display:none;">

        <div id="productDetails"></div>

        <button onclick="closeModal()">
            Close
        </button>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function loadProducts(page = 1) {

            const search =
                document.getElementById('search').value;

            const price_filter =
                document.getElementById('price_filter').value;

            const sort =
                document.getElementById('sort').value;

            fetch(
                    'ajax.php?' +
                    'search=' + encodeURIComponent(search) +
                    '&price_filter=' + price_filter +
                    '&sort=' + sort +
                    '&page=' + page
                )

                .then(response => response.text())

                .then(data => {

                    document
                        .getElementById('product-results')
                        .innerHTML = data;

                });
        }

        loadProducts();

        document
            .getElementById('search')
            .addEventListener('keyup', function() {

                loadProducts();

            });

        document
            .getElementById('price_filter')
            .addEventListener('change', function() {

                loadProducts();

            });

        document
            .getElementById('sort')
            .addEventListener('change', function() {

                loadProducts();

            });

        function deleteProduct(id) {

    Swal.fire({
        title: "Are you sure?",
        text: "You won't to Delete this Product!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {

        if (result.isConfirmed) {

            fetch('delete.php?id=' + id)
                .then(response => response.text())
                .then(() => {

                    Swal.fire({
                        title: "Deleted!",
                        text: "Product deleted successfully.",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Reload the product list after a short delay
                    setTimeout(() => {
                        loadProducts();
                    }, 1500);

                });

        }

    });

}

        function toggleStatus(id) {

            fetch("../../ajax/toggle_status.php", {

                    method: "POST",

                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },

                    body: "id=" + id + "&table=products"

                })

                .then(response => response.text())

                .then(data => {

                    console.log(data);

                    loadProducts();

                })

                .catch(error => {

                    console.error(error);

                });
        }

        document.addEventListener("change", function(e) {

            if (e.target.id === "select_all") {

                document
                    .querySelectorAll(".product_checkbox")
                    .forEach(function(cb) {

                        cb.checked = e.target.checked;

                    });
            }
        });

        function bulkDelete() {

    let ids = [];

    document.querySelectorAll('.product_checkbox:checked').forEach(function (checkbox) {
        ids.push(checkbox.value);
    });

    // No products selected
    if (ids.length === 0) {

        Swal.fire({
            title: 'No Products Selected',
            text: 'Please select at least one product.',
            icon: 'warning',
            confirmButtonColor: '#3085d6'
        });

        return;
    }

    // Confirmation popup
    Swal.fire({
        title: 'Are you sure?',
        text: "Selected products will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, Delete!',
        cancelButtonText: 'Cancel'
    }).then((result) => {

        if (result.isConfirmed) {

            fetch('../../ajax/bulk_delete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'ids=' + encodeURIComponent(JSON.stringify(ids))
            })

            .then(response => response.text())

            .then(data => {

                if (data.includes('success')) {

                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Selected products deleted successfully.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {

                        loadProducts();

                    });

                } else {

                    Swal.fire({
                        title: 'Error!',
                        text: data,
                        icon: 'error',
                        confirmButtonColor: '#3085d6'
                    });

                }

            })

            .catch(error => {

                console.error(error);

                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });

            });

        }

    });

}

        function viewProduct(id) {

            fetch('view_ajax.php?id=' + id)

                .then(response => response.text())

                .then(data => {

                    document.getElementById(
                        'productDetails'
                    ).innerHTML = data;

                    document.getElementById(
                        'productModal'
                    ).style.display = 'block';

                });
        }

        function closeModal() {

            document.getElementById(
                'productModal'
            ).style.display = 'none';
        }
    </script>
</body>

</html>