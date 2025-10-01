<?php
require '../dbconfig.php';

$fromDate = $_POST['from_date'] ?? '';
$toDate = $_POST['to_date'] ?? '';
$supplier = $_POST['supplier'] ?? '';

$query = "SELECT * FROM purchases WHERE 1";
$params = [];

if ($fromDate) {
    $query .= " AND date >= ?";
    $params[] = $fromDate;
}
if ($toDate) {
    $query .= " AND date <= ?";
    $params[] = $toDate . ' 23:59:59';
}
if ($supplier && $supplier !== 'All') {
    $query .= " AND supplier = ?";
    $params[] = $supplier;
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$purchases = $stmt->fetchAll();

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=purchase_history_report.xls");

//echo "Date\tItem\tSupplier\tQty\tCost/Unit\tTotal Cost\n";

foreach ($purchases as $row) {
    $unitCost = $row['quantity'] > 0 ? $row['total_cost'] / $row['quantity'] : 0;
    echo date('Y-m-d', strtotime($row['purchase_date'])) . "\t" .
         $row['item'] . "\t" .
         $row['supplier'] . "\t" .
         $row['quantity'] . "\t" .
         number_format($unitCost, 2) . "\t" .
         number_format($row['total_cost'], 2) . "\n";
}
?>
