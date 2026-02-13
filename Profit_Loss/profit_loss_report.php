<?php
require '../dbconfig.php';

$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$itemFilter = $_GET['item'] ?? '';

$query = "SELECT * FROM sales WHERE 1";
$params = [];

if ($fromDate) {
    $query .= " AND sale_date >= ?";
    $params[] = $fromDate;
}
if ($toDate) {
    $query .= " AND sale_date <= ?";
    $params[] = $toDate . ' 23:59:59';
}
if ($itemFilter) {
    $query .= " AND item = ?";
    $params[] = $itemFilter;
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$sales = $stmt->fetchAll();

// === Step 2: Match each sale with latest purchase ===
$report = [];

foreach ($sales as $sale) {
    $saleItem = $sale['item'];
    $saleDate = $sale['sale_date'];

    // Get most recent purchase of same item before the sale date
    $purchaseStmt = $conn->prepare("
        SELECT * FROM purchases 
        WHERE item = ? AND purchase_date <= ? 
        ORDER BY purchase_date DESC LIMIT 1
    ");
    $purchaseStmt->execute([$saleItem, $saleDate]);
    $purchase = $purchaseStmt->fetch();

    // Estimate unit purchase price
    print_r($purchase);
    $unitPurchasePrice = ($purchase && $purchase['quantity'] > 0)
        ? $purchase['total_cost'] / $purchase['quantity']
        : 0.00;

    $profit = ($sale['price'] - $unitPurchasePrice) * $sale['qty'];

    $report[] = [
        'date' => $saleDate,
        'item' => $saleItem,
        'qty' => $sale['qty'],
        'purchase_price' => $unitPurchasePrice,
        'sale_price' => $sale['price'],
        'profit' => $profit
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profit/Loss Report</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Profit/Loss Report</h2>

    <!-- Filters -->
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="from_date" class="form-label">From</label>
            <input type="date" class="form-control" id="from_date" name="from_date" value="<?= htmlspecialchars($fromDate) ?>">
        </div>
        <div class="col-md-3">
            <label for="to_date" class="form-label">To</label>
            <input type="date" class="form-control" id="to_date" name="to_date" value="<?= htmlspecialchars($toDate) ?>">
        </div>
        <div class="col-md-3">
            <label for="item" class="form-label">Item</label>
            <input type="text" class="form-control" id="item" name="item" value="<?= htmlspecialchars($itemFilter) ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <!-- Report Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Purchase Price</th>
                <th>Sale Price</th>
                <th>Profit</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalProfit = 0;
            if (!empty($report)) {
                foreach ($report as $row):
                    $totalProfit += $row['profit'];
                    ?>
                    <tr>
                        <td><?= date('Y-m-d', strtotime($row['date'])) ?></td>
                        <td><?= htmlspecialchars($row['item']) ?></td>
                        <td><?= $row['qty'] ?></td>
                        <td><?= number_format($row['purchase_price'], 2) ?></td>
                        <td><?= number_format($row['sale_price'], 2) ?></td>
                        <td><?= number_format($row['profit'], 2) ?></td>
                    </tr>
                <?php
                endforeach;
            } else {
                echo '<tr><td colspan="6" class="text-center">No data found.</td></tr>';
            }
            ?>
        </tbody>
        <tfoot>
            <tr class="table-success fw-bold">
                <td colspan="5" class="text-end">Total Profit</td>
                <td><?= number_format($totalProfit, 2) ?></td>
            </tr>
        </tfoot>
    </table>
</div>
</body>
</html>
