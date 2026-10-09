<?php

require '../config/db.php';

$table = $_POST['table'];
$id = (int)$_POST['id'];

$allowed_tables = [
    'categories',
    'subcategories',
    'products',
    'users',    
    'price_filters'
];

if (!in_array($table, $allowed_tables)) {
    exit('Invalid table');
}

$result = $mysqli->query("
    SELECT status
    FROM $table
    WHERE id = $id
");

$row = $result->fetch_assoc();

$new_status = ($row['status'] == 1) ? 0 : 1;

$mysqli->query("
    UPDATE $table
    SET status = $new_status
    WHERE id = $id
");

echo "success";