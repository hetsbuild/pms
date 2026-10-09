<?php

require '../../config/db.php';
require_once '../../includes/auth.php';
require '../../includes/permission.php';

$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : '';

$sort = isset($_GET['sort'])
    ? $_GET['sort']
    : 'newest';

$page = isset($_GET['page'])
    ? (int)$_GET['page']
    : 1;

if ($page < 1) {
    $page = 1;
}

$records_per_page = 5;

$offset =
    ($page - 1)
    * $records_per_page;

$order_by = "id DESC";

if ($sort == 'az') {
    $order_by = "category_name ASC";
} elseif ($sort == 'za') {
    $order_by = "category_name DESC";
} elseif ($sort == 'newest') {
    $order_by = "id DESC";
}


$count_result = $mysqli->query("
    SELECT COUNT(*) AS total
    FROM categories
    WHERE category_name LIKE '%$search%'
");

$total_records =
    $count_result->fetch_assoc()['total'];

$total_pages =
    ceil(
        $total_records /
            $records_per_page
    );

$result = $mysqli->query("
    SELECT *
    FROM categories
    WHERE category_name LIKE '%$search%'
    ORDER BY $order_by
    LIMIT $records_per_page
    OFFSET $offset
");

?>

<div class="card shadow w-100">
    <div class="card-body">
        <div class="table-responsive">

            <table class="table table-bordered table-striped table-hover align-middle">

                <thead class="table-dark">

                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <?php if (isAdmin()) { ?>
                            <th>Status</th>
                        <?php } ?>
                        <?php if (isAdmin()) { ?>
                            <th style="width:180px;">Action</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>

                        <tr>

                            <td>
                                <?php echo $row['id']; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['category_name']); ?>
                            </td>

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
                                </td>


                            <?php } ?>

                            <?php if (isAdmin()) { ?>
                                <td>
                                    <div class="btn-group" role="group">

                                        <a
                                            href="edit.php?id=<?= $row['id']; ?>"
                                            class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm"
                                            onclick="deleteCategory(<?= $row['id'] ?>)">
                                            Delete
                                        </button>

                                </td>
                            <?php } ?>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>

            <br>

            <div class="pagination">

                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>

                    <button
                        class="pagination-link"
                        data-page="<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </button>

                <?php } ?>

            </div>