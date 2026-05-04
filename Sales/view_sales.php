<?php
require '../dbconfig.php';
$pageTitle = 'View Sales';
require '../includes/header.php';
require '../includes/sidebar.php';

$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$paymentMethod = $_GET['payment_method'] ?? '';

$query = "SELECT * FROM sales WHERE 1";
$params = [];

if ($fromDate) { $query .= " AND sale_date >= ?"; $params[] = $fromDate; }
if ($toDate) { $query .= " AND sale_date <= ?"; $params[] = $toDate . ' 23:59:59'; }
if ($paymentMethod && $paymentMethod !== 'All') { $query .= " AND payment_method = ?"; $params[] = $paymentMethod; }
$query .= " ORDER BY sale_date DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$sales = $stmt->fetchAll();

$totalQty = 0;
$grandTotal = 0;
$paymentTotals = ['Cash' => 0, 'Mobile Money' => 0];
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2>All Sales</h2>
    <div class="no-print">
        <form method="post" action="sales_report_export.php" class="d-inline">
            <input type="hidden" name="from_date" value="<?= htmlspecialchars($fromDate) ?>">
            <input type="hidden" name="to_date" value="<?= htmlspecialchars($toDate) ?>">
            <input type="hidden" name="payment_method" value="<?= htmlspecialchars($paymentMethod) ?>">
            <button type="submit" class="btn btn-success me-2">Export to Excel</button>
        </form>
        <button onclick="window.print()" class="btn btn-outline-primary">Print Report</button>
    </div>
</div>

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
        <label class="form-label">Payment Method</label>
        <select name="payment_method" class="form-select">
            <option value="All">All</option>
            <option value="Cash" <?= $paymentMethod == 'Cash' ? 'selected' : '' ?>>Cash</option>
            <option value="Mobile Money" <?= $paymentMethod == 'Mobile Money' ? 'selected' : '' ?>>Mobile Money</option>
        </select>
    </div>
    <div class="col-md-3 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
        <a href="view_sales.php" class="btn btn-secondary w-100">Reset</a>
    </div>
</form>

<a href="../Dashboard/page.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

<div class="table-responsive">
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr><th>Date</th><th>Time</th><th>Item</th><th>Qty</th><th>Price (GHS)</th><th>Total (GHS)</th><th>Payment</th></tr>
    </thead>
    <tbody>
        <?php if ($sales): ?>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?= date('Y-m-d', strtotime($sale['sale_date'])) ?></td>
                    <td><?= date('H:i', strtotime($sale['sale_date'])) ?></td>
                    <td><?= htmlspecialchars($sale['item']) ?></td>
                    <td><?= $sale['qty'] ?></td>
                    <td><?= number_format($sale['price'], 2) ?></td>
                    <td><?= number_format($sale['total'], 2) ?></td>
                    <td><?= htmlspecialchars($sale['payment_method']) ?></td>
                </tr>
                <?php
                    if (isset($paymentTotals[$sale['payment_method']])) $paymentTotals[$sale['payment_method']] += $sale['total'];
                    $totalQty += $sale['qty'];
                    $grandTotal += $sale['total'];
                ?>
            <?php endforeach; ?>
            <tr class="table-warning fw-bold">
                <td colspan="3" class="text-end">Total</td>
                <td><?= $totalQty ?></td><td></td>
                <td><?= number_format($grandTotal, 2) ?></td><td></td>
            </tr>
        <?php else: ?>
            <tr><td colspan="7" class="text-center">No sales found.</td></tr>
        <?php endif; ?>
        <tr class="table-info fw-bold">
            <td colspan="6" class="text-end">Total (Cash)</td>
            <td><?= number_format($paymentTotals['Cash'], 2) ?></td>
        </tr>
        <tr class="table-info fw-bold">
            <td colspan="6" class="text-end">Total (Mobile Money)</td>
            <td><?= number_format($paymentTotals['Mobile Money'], 2) ?></td>
        </tr>
    </tbody>
</table>
</div>

<?php require '../includes/footer.php'; ?>