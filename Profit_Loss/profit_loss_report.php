<?php
require '../dbconfig.php';
$pageTitle = 'Profit/Loss Report';
require '../includes/header.php';
require '../includes/sidebar.php';

$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$itemFilter = $_GET['item'] ?? '';

$query = "SELECT * FROM sales WHERE 1";
$params = [];
if ($fromDate) { $query .= " AND sale_date >= ?"; $params[] = $fromDate; }
if ($toDate)   { $query .= " AND sale_date <= ?"; $params[] = $toDate . ' 23:59:59'; }
if ($itemFilter) { $query .= " AND item = ?"; $params[] = $itemFilter; }

$stmt = $conn->prepare($query);
$stmt->execute($params);
$sales = $stmt->fetchAll();

$report = [];
foreach ($sales as $sale) {
    $purchaseStmt = $conn->prepare("SELECT * FROM purchases WHERE item = ? AND purchase_date <= ? ORDER BY purchase_date DESC LIMIT 1");
    $purchaseStmt->execute([$sale['item'], $sale['sale_date']]);
    $purchase = $purchaseStmt->fetch();
    $unitPurchasePrice = ($purchase && $purchase['quantity'] > 0) ? $purchase['total_cost'] / $purchase['quantity'] : 0.00;
    $profit = ($sale['price'] - $unitPurchasePrice) * $sale['qty'];
    $report[] = ['date' => $sale['sale_date'], 'item' => $sale['item'], 'qty' => $sale['qty'], 'purchase_price' => $unitPurchasePrice, 'sale_price' => $sale['price'], 'profit' => $profit];
}
?>

<a href="../Dashboard/page.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
<h2 class="mb-4">Profit/Loss Report</h2>

<form method="get" class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">From</label>
        <input type="date" class="form-control" name="from_date" value="<?= htmlspecialchars($fromDate) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">To</label>
        <input type="date" class="form-control" name="to_date" value="<?= htmlspecialchars($toDate) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Item</label>
        <input type="text" class="form-control" name="item" value="<?= htmlspecialchars($itemFilter) ?>">
    </div>
    <div class="col-md-3 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
        <a href="profit_loss_report.php" class="btn btn-secondary w-100">Reset</a>
    </div>
</form>

<div class="table-responsive">
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr><th>Date</th><th>Item</th><th>Qty</th><th>Purchase Price</th><th>Sale Price</th><th>Profit</th></tr>
    </thead>
    <tbody>
        <?php $totalProfit = 0; ?>
        <?php if (!empty($report)): ?>
            <?php foreach ($report as $row): $totalProfit += $row['profit']; ?>
                <tr>
                    <td><?= date('Y-m-d', strtotime($row['date'])) ?></td>
                    <td><?= htmlspecialchars($row['item']) ?></td>
                    <td><?= $row['qty'] ?></td>
                    <td><?= number_format($row['purchase_price'], 2) ?></td>
                    <td><?= number_format($row['sale_price'], 2) ?></td>
                    <td class="<?= $row['profit'] >= 0 ? 'text-success' : 'text-danger' ?>"><?= number_format($row['profit'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No data found.</td></tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr class="table-success fw-bold">
            <td colspan="5" class="text-end">Total Profit</td>
            <td><?= number_format($totalProfit, 2) ?></td>
        </tr>
    </tfoot>
</table>
</div>

<?php require '../includes/footer.php'; ?>