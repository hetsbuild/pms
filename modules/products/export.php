<?php

require '../../includes/auth.php';
require '../../config/db.php';

// File Name
$filename = "products_" . date("Y-m-d_H-i-s") . ".csv";

// Headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open Output
$output = fopen('php://output', 'w');

// CSV Heading
fputcsv($output, [
    'ID',
    'Product Name',
    'Category',
    'Sub Category',
    'Brand',
    'Price',
    'Sale Price',
    'Quantity',
    'Status',
    'Created By',
    'Created At'
]);

$sql = "

SELECT

p.id,
p.product_name,
c.category_name,
s.subcategory_name,
p.brand,
p.price,
p.sale_price,
p.quantity,

CASE
WHEN p.status = 1 THEN 'Active'
ELSE 'Inactive'
END AS status,

u.name AS created_by,

p.created_at

FROM products p

LEFT JOIN categories c
ON p.category_id = c.id

LEFT JOIN subcategories s
ON p.subcategory_id = s.id

LEFT JOIN users u
ON p.created_by = u.id

ORDER BY p.id DESC

";

$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {

    fputcsv($output, [
        $row['id'],
        $row['product_name'],
        $row['category_name'],
        $row['subcategory_name'],
        $row['brand'],
        $row['price'],
        $row['sale_price'],
        $row['quantity'],
        $row['status'],
        $row['created_by'],
        $row['created_at']
    ]);

}

fclose($output);
exit;