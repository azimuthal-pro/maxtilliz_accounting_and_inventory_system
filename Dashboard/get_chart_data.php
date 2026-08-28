<?php
require '../dbconfig.php';

$monthNames = [1=>"Jan", 2=>"Feb", 3=>"Mar", 4=>"Apr", 5=>"May", 6=>"Jun",
               7=>"Jul", 8=>"Aug", 9=>"Sep", 10=>"Oct", 11=>"Nov", 12=>"Dec"];

$currentYear = date('Y');

// === Sales Data (current year only, ordered by month) ===
$salesLabels = [];
$salesData   = [];

$salesResult = $conn->prepare("
    SELECT MONTH(sale_date) as month, SUM(total) as total_sales
    FROM sales
    WHERE YEAR(sale_date) = ?
    GROUP BY MONTH(sale_date)
    ORDER BY MONTH(sale_date) ASC
");
$salesResult->execute([$currentYear]);

while ($row = $salesResult->fetch(PDO::FETCH_ASSOC)) {
    $salesLabels[] = $monthNames[(int)$row['month']];
    $salesData[]   = (float)$row['total_sales'];
}

// === Purchases Data (current year only, ordered by month) ===
$purchaseLabels = [];
$purchaseData   = [];

$purchaseResult = $conn->prepare("
    SELECT MONTH(purchase_date) as month, SUM(total_cost) as total_purchases
    FROM purchases
    WHERE YEAR(purchase_date) = ?
    GROUP BY MONTH(purchase_date)
    ORDER BY MONTH(purchase_date) ASC
");
$purchaseResult->execute([$currentYear]);

while ($row = $purchaseResult->fetch(PDO::FETCH_ASSOC)) {
    $purchaseLabels[] = $monthNames[(int)$row['month']];
    $purchaseData[]   = (float)$row['total_purchases'];
}

header('Content-Type: application/json');
echo json_encode([
    'sales' => [
        'labels' => $salesLabels,
        'data'   => $salesData
    ],
    'purchases' => [
        'labels' => $purchaseLabels,
        'data'   => $purchaseData
    ]
]);
?>