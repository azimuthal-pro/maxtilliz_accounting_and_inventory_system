<?php
require '../dbconfig.php';

$stmt = $conn->query("SELECT * FROM inventory WHERE quantity_in_stock <= min_stock_level ORDER BY item ASC");
$lowStockItems = $stmt->fetchAll();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="low_stock_report.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Item Name', 'Quantity in Stock', 'Minimum Stock Level']);

foreach ($lowStockItems as $item) {
    fputcsv($output, [
        $item['item'],
        $item['quantity_in_stock'],
        $item['min_stock_level'],
    ]);
}

fclose($output);
exit;
