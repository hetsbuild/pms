<?php

require '../../includes/auth.php';
require '../../includes/security.php';
require '../../includes/permission.php';
require '../../config/db.php';

$search = sanitizeInput($_GET['search'] ?? '');

$sort = $_GET['sort'] ?? 'newest';

$page = filter_input(
    INPUT_GET,
    'page',
    FILTER_VALIDATE_INT
);

$page = ($page && $page > 0) ? $page : 1;

$records_per_page = 5;
$offset = ($page - 1) * $records_per_page;

switch ($sort) {

    case 'az':
        $order_by = "s.subcategory_name ASC";
        break;

    case 'za':
        $order_by = "s.subcategory_name DESC";
        break;

    default:
        $order_by = "s.id DESC";
}

$searchLike = "%{$search}%";


$countStmt = $mysqli->prepare(
    "SELECT COUNT(*) AS total
     FROM subcategories s
     INNER JOIN categories c
        ON s.category_id = c.id
     WHERE
        s.subcategory_name LIKE ?
        OR c.category_name LIKE ?"
);

$countStmt->bind_param(
    "ss",
    $searchLike,
    $searchLike
);

$countStmt->execute();

$total_records = $countStmt
    ->get_result()
    ->fetch_assoc()['total'];

$countStmt->close();

$total_pages = ceil(
    $total_records / $records_per_page
);


$stmt = $mysqli->prepare(
    "SELECT
        s.*,
        c.category_name
     FROM subcategories s
     INNER JOIN categories c
        ON s.category_id = c.id
     WHERE
        s.subcategory_name LIKE ?
        OR c.category_name LIKE ?
     ORDER BY $order_by
     LIMIT ? OFFSET ?"
);

$stmt->bind_param(
    "ssii",
    $searchLike,
    $searchLike,
    $records_per_page,
    $offset
);

$stmt->execute();

$result = $stmt->get_result();
?>

<table border="2" cellpadding="10" cellspacing="1">


    <tr>

        <th>ID</th>

        <th>Category</th>

        <th>Sub Category</th>

        <?php if (isAdmin()) { ?>

            <th>Status</th>

            <th>Action</th>

        <?php } ?>

    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>

        <tr>

            <td><?= $row['id']; ?></td>

            <td><?= escapeOutput($row['category_name']); ?></td>

            <td><?= escapeOutput($row['subcategory_name']); ?></td>

            <?php if (isAdmin()) { ?>

                <td>

                    <input
                        type="checkbox"
                        <?= ($row['status'] == 1) ? 'checked' : ''; ?>
                        onchange="toggleStatus(<?= $row['id']; ?>)">

                    <?= ($row['status'] == 1)
                        ? 'Active'
                        : 'Inactive'; ?>

                </td>

                <td>

                    <a href="edit.php?id=<?= $row['id']; ?>">
                        Edit
                    </a>

                    /

                    <button
                        onclick="deleteSubCategory(<?= $row['id']; ?>)">

                        Delete

                    </button>

                </td>

            <?php } ?>

        </tr>

    <?php } ?>

</table>

<br>

<?php if ($total_pages > 1) { ?>

    <div style="margin-top:20px;">

        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>

            <button
                class="pagination-link"
                data-page="<?= $i; ?>">

                <?= $i; ?>

            </button>

        <?php } ?>

    </div>

<?php }

$stmt->close();
?>