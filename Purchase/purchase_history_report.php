<?php
require '../dbconfig.php';

$fromDate = $_POST['from_date'] ?? '';
$toDate   = $_POST['to_date'] ?? '';
$supplier = $_POST['supplier'] ?? '';

$query  = "SELECT * FROM purchases WHERE 1";
$params = [];

if ($fromDate) {
    $query   .= " AND purchase_date >= ?";
    $params[] = $fromDate;
}
if ($toDate) {
    $query   .= " AND purchase_date <= ?";
    $params[] = $toDate . ' 23:59:59';
}
if ($supplier && $supplier !== 'All') {
    $query   .= " AND supplier = ?";
    $params[] = $supplier;
}

$query .= " ORDER BY purchase_date DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$purchases = $stmt->fetchAll();

$totalCost = array_sum(array_column($purchases, 'total_cost'));

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=purchase_history_report.xls");

// Header row
echo "Date\tSupplier\tTotal Cost (GHS)\n";

// Data rows
foreach ($purchases as $row) {
    echo date('d M Y', strtotime($row['purchase_date'])) . "\t" .
         $row['supplier'] . "\t" .
         number_format($row['total_cost'], 2) . "\n";
}

// Total row
echo "\t" . "TOTAL\t" . number_format($totalCost, 2) . "\n";
?>