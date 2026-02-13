<?php
require '../dbconfig.php';

$fromDate = $_POST['from_date'] ?? '';
$toDate = $_POST['to_date'] ?? '';
$paymentMethod = $_POST['payment_method'] ?? '';

$query = "SELECT * FROM sales WHERE 1";
$params = [];

if ($fromDate) {
    $query .= " AND date >= ?";
    $params[] = $fromDate;
}
if ($toDate) {
    $query .= " AND date <= ?";
    $params[] = $toDate . ' 23:59:59';
}
if ($paymentMethod && $paymentMethod !== 'All') {
    $query .= " AND payment_method = ?";
    $params[] = $paymentMethod;
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$sales = $stmt->fetchAll();

// Totals
$totalQty = 0;
$grandTotal = 0;
$paymentTotals = [
    'Cash' => 0,
    'Mobile Money' => 0,
    'Card' => 0
];

// Prepare CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sales_report.csv"');

$output = fopen('php://output', 'w');

// Column headers
fputcsv($output, ['Date', 'Time', 'Item', 'Qty', 'Price (GHS)', 'Total (GHS)', 'Payment Method']);

// Rows
foreach ($sales as $sale) {
    fputcsv($output, [
        date('Y-m-d', strtotime($sale['sale_date'])),
        date('H:i', strtotime($sale['sale_date'])),
        $sale['item'],
        $sale['qty'],
        number_format($sale['price'], 2),
        number_format($sale['total'], 2),
        $sale['payment_method']
    ]);

    $totalQty += $sale['qty'];
    $grandTotal += $sale['total'];

    if (isset($paymentTotals[$sale['payment_method']])) {
        $paymentTotals[$sale['payment_method']] += $sale['total'];
    }
}

// Blank row before totals
fputcsv($output, []);
fputcsv($output, ['Totals']);
fputcsv($output, ['Total Quantity', $totalQty]);
fputcsv($output, ['Grand Total (GHS)', number_format($grandTotal, 2)]);
fputcsv($output, ['Cash Total (GHS)', number_format($paymentTotals['Cash'], 2)]);
fputcsv($output, ['Mobile Money Total (GHS)', number_format($paymentTotals['Mobile Money'], 2)]);

fclose($output);
exit;
