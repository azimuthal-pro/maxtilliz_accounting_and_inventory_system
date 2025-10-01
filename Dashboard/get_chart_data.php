<?php
require '../dbconfig.php';


$monthNames = [1=>"Jan", 2=>"Feb", 3=>"Mar", 4=>"Apr", 5=>"May", 6=>"Jun", 7=>"Jul", 8=>"Aug", 9=>"Sep", 10=>"Oct", 11=>"Nov", 12=>"Dec"];

// === Sales Data ===
$salesLabels = [];
$salesData = [];

$sqlSales = "SELECT MONTH(sale_date) as month, SUM(total) as total_sales 
             FROM sales 
             GROUP BY MONTH(sale_date)";
$salesResult = $conn->query($sqlSales);

while ($row = $salesResult->fetch(PDO::FETCH_ASSOC)) {
    $salesLabels[] = $monthNames[(int)$row['month']];
    $salesData[] = (float)$row['total_sales'];
}

// === Purchases Data ===
$purchaseLabels = [];
$purchaseData = [];

$sqlPurchases = "SELECT MONTH(purchase_date) as month, SUM(total_cost) as total_purchases 
                 FROM purchases 
                 GROUP BY MONTH(purchase_date)";
$purchaseResult = $conn->query($sqlPurchases);

while ($row = $purchaseResult->fetch(PDO::FETCH_ASSOC)) {
    $purchaseLabels[] = $monthNames[(int)$row['month']];
    $purchaseData[] = (float)$row['total_purchases'];
}

// === Final JSON Response ===
$response = [
    'sales' => [
        'labels' => $salesLabels,
        'data' => $salesData
    ],
    'purchases' => [
        'labels' => $purchaseLabels,
        'data' => $purchaseData
    ]
];

header('Content-Type: application/json');
echo json_encode($response);
?>

