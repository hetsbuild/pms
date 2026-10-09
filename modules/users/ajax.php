<?php

require '../../includes/auth.php';
require '../../includes/security.php';
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
        $order_by = "name ASC";
        break;

    case 'za':
        $order_by = "name DESC";
        break;

    default:
        $order_by = "id DESC";
}

$searchLike = "%{$search}%";

$countStmt = $mysqli->prepare(
    "SELECT COUNT(*) AS total
     FROM users
     WHERE
        name LIKE ?
        OR role LIKE ?"
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
    "SELECT *
     FROM users
     WHERE
        name LIKE ?
        OR role LIKE ?
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

    <th>Name</th>

    <th>Role</th>

    <th>Status</th>

    <th>Action</th>

</tr>

<?php while ($row = $result->fetch_assoc()) { ?>

<tr>

    <td><?= $row['id']; ?></td>

    <td><?= escapeOutput($row['name']); ?></td>

    <td><?= escapeOutput($row['role']); ?></td>

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
            onclick="deleteUser(<?= $row['id']; ?>)">

            Delete

        </button>

    </td>

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