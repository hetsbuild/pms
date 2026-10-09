<?php

require '../../config/db.php';
require_once '../../includes/auth.php';
require '../../includes/permission.php';


if (!isset($_SESSION['user_id'])) {
    die("User not authenticated");
}

$search = $_GET['search'] ?? '';

$price_filter = isset($_GET['price_filter'])
    ? (int)$_GET['price_filter']
    : 0;

$sort = $_GET['sort']
    ?? 'newest';

$price_condition = "";
if ($price_filter > 0) {

    $filter = $mysqli->query("
        SELECT *
        FROM price_filters
        WHERE id = $price_filter
        AND status = 1
    ")->fetch_assoc();

    if ($filter) {

        $min_price = $filter['min_price'];
        $max_price = $filter['max_price'];

        $price_condition =
            " AND p.price BETWEEN $min_price AND $max_price ";
    }
}
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

$page = isset($_GET['page'])
    ? (int)$_GET['page']
    : 1;

$records_per_page = 5;

$offset =
    ($page - 1)
    * $records_per_page;

$count_sql = "
SELECT COUNT(*) as total
FROM products p
INNER JOIN categories c
ON p.category_id = c.id
INNER JOIN subcategories s
ON p.subcategory_id = s.id
WHERE (
    p.product_name LIKE '%$search%'
    OR c.category_name LIKE '%$search%'
    OR s.subcategory_name LIKE '%$search%'
)
$price_condition
";

$total_records =
    $mysqli
        ->query($count_sql)
        ->fetch_assoc()['total'];

$total_pages =
    ceil(
        $total_records /
            $records_per_page
    );

$sql = "
SELECT
    p.*,
    c.category_name,
    s.subcategory_name,
    COALESCE(u.name, 'Unknown') AS created_by_name
FROM products p
INNER JOIN categories c
    ON p.category_id = c.id
INNER JOIN subcategories s
    ON p.subcategory_id = s.id
LEFT JOIN users u
    ON p.created_by = u.id
WHERE (
    p.product_name LIKE '%$search%'
    OR c.category_name LIKE '%$search%'
    OR s.subcategory_name LIKE '%$search%'
    OR p.brand LIKE '%$search%'
    OR COALESCE(u.name, 'Unknown') LIKE '%$search%'
)

$price_condition
ORDER BY $order_by
LIMIT $records_per_page
OFFSET $offset
";
$result = $mysqli->query($sql);

?>

<div class="card shadow w-100">
    <div class="card-body">
        <div class="table-responsive">

            <table class="table table-bordered table-striped table-hover align-middle">

                <thead class="table-dark">
                    <tr>

                        <?php if (isAdmin()) { ?>
                            <th>
                                <input type="checkbox" id="select_all">
                            </th>
                        <?php } ?>

                        <th style="width:70px;">ID</th>

                        <th style="width:90px;">Image</th>

                        <th style="width:100px;">Product Name</th>

                        <th style="width:100px;">Category</th>

                        <th style="width:100px;">Sub Category</th>

                        <th style="width:100px;">Brand</th>

                        <th style="width:100px;">Price</th>

                        <th style="width:100px;">Sale Price</th>

                        <th style="width:80px;">Qty</th>

                        <?php if (isAdmin()) { ?>
                            <th style="width:80px;">Status</th>
                        <?php } ?>

                        <?php if (isAdmin()) { ?>
                            <th style="width:180px;">Action</th>

                        <?php } ?>

                        <?php if (isAdmin()) { ?>
                            <th style="width:100px;">Created By</th>
                        <?php } ?>

                    </tr>
                </thead>
                <tbody>

                    <?php while ($row = $result->fetch_assoc()) { ?>

                        <tr>
                            <?php if (isAdmin()) { ?>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="product_checkbox"
                                        value="<?= $row['id'] ?>">
                                </td>
                            <?php } ?>
                            </td>

                            <td><?= $row['id'] ?></td>

                            <td>
                                <a href="view.php?id=<?= $row['id'] ?>">
                                    <img
                                        src="/pms/assets/uploads/products/<?= htmlspecialchars($row['primary_image']) ?>"
                                        class="img-thumbnail"
                                        width="70">
                                </a>
                            </td>

                            <td><?= htmlspecialchars($row['product_name']) ?></td>

                            <td><?= htmlspecialchars($row['category_name']) ?></td>

                            <td><?= htmlspecialchars($row['subcategory_name']) ?></td>

                            <td><?= htmlspecialchars($row['brand']) ?></td>

                            <td><?= $row['price'] ?></td>

                            <td><?= htmlspecialchars($row['sale_price']) ?></td>

                            <td><?= htmlspecialchars($row['quantity']) ?></td>

                            <?php if (isAdmin()) { ?>
                                <td>

                                    <input
                                        type="checkbox"
                                        <?php echo ($row['status'] == 1) ? 'checked' : ''; ?>
                                        onchange="toggleStatus(<?php echo $row['id']; ?>)">

                                    <?php if ($row['status']) { ?>
                                        <span class="badge bg-success">
                                            Active
                                        </span>

                                    <?php } else { ?>

                                        <span class="badge bg-danger">
                                            Inactive
                                        </span>

                                    <?php } ?>

                                <?php } ?>
                                </td>

                                <?php if (isAdmin()) { ?>

                                    <td>
                                        <div class="btn-group" role="group">

                                            <a
                                                href="view.php?id=<?= $row['id']; ?>"
                                                class="btn btn-info btn-sm">
                                                View
                                            </a>

                                            <a
                                                href="edit.php?id=<?= $row['id']; ?>"
                                                class="btn btn-warning btn-sm">
                                                Edit
                                            </a>

                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm"
                                                onclick="deleteProduct(<?= $row['id'] ?>)">
                                                Delete
                                            </button>

                                        </div>
                                    </td>

                                <?php } ?>

                                <?php if (isAdmin()) { ?>

                                    <td><?= htmlspecialchars($row['created_by_name']) ?></td>
                                <?php } ?>


                        </tr>
                    <?php } ?>

            </table>

        </div>
    </div>
</div>

<br>
<?php if (isAdmin()) { ?>

    <button
        type="button"
        class="btn btn-danger mt-3"
        onclick="bulkDelete()">

        Delete Selected

    </button>

<?php } ?>
<br><br>
<nav class="mt-3">
    <ul class="pagination justify-content-center">

        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>

            <li class="page-item">

                <button
                    type="button"
                    class="page-link"
                    onclick="loadProducts(<?= $i; ?>)">

                    <?= $i; ?>

                </button>

            </li>

        <?php } ?>

    </ul>
</nav>

</div>