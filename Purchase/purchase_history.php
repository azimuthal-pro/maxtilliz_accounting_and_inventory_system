<?php
require '../dbconfig.php';

$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$supplier = $_GET['supplier'] ?? '';

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

$totalQty = 0;
$totalCost = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase History</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<form method="post" action="purchase_history_report.php" class="d-inline">
    <input type="hidden" name="from_date" value="<?= htmlspecialchars($fromDate) ?>">
    <input type="hidden" name="to_date" value="<?= htmlspecialchars($toDate) ?>">
    <input type="hidden" name="supplier" value="<?= htmlspecialchars($supplier) ?>">
    <button type="submit" class="btn btn-success me-2">Export to Excel</button>
</form>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Purchase History</h2>
        <button onclick="window.print()" class="btn btn-outline-primary">Print</button>
    </div>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="from_date" class="form-label">From</label>
            <input type="date" class="form-control" name="from_date" value="<?= htmlspecialchars($fromDate) ?>">
        </div>
        <div class="col-md-3">
            <label for="to_date" class="form-label">To</label>
            <input type="date" class="form-control" name="to_date" value="<?= htmlspecialchars($toDate) ?>">
        </div>
        <div class="col-md-3">
            <label for="supplier" class="form-label">Supplier</label>
            <select name="supplier" class="form-select">
                <option value="All">All</option>
                <?php
                $suppliers = $conn->query("SELECT DISTINCT supplier FROM purchases")->fetchAll();
                foreach ($suppliers as $s):
                ?>
                    <option value="<?= $s['supplier'] ?>" <?= $supplier == $s['supplier'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['supplier']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Item</th>
                <th>Supplier</th>
                <th>Qty</th>
                <th>Cost/Unit (GHS)</th>
                <th>Total Cost (GHS)</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($purchases): ?>
                <?php foreach ($purchases as $purchase): ?>
                    <tr>
                        <td><?= date('Y-m-d', strtotime($purchase['purchase_date'])) ?></td>
                        <td><?= htmlspecialchars($purchase['item']) ?></td>
                        <td><?= htmlspecialchars($purchase['supplier']) ?></td>
                        <td><?= $purchase['quantity'] ?></td>
                        <td>
                            <?= $purchase['quantity'] > 0 ? number_format($purchase['total_cost'] / $purchase['quantity'], 2) : '0.00' ?>
                        </td>
                        <td><?= number_format($purchase['total_cost'], 2) ?></td>
                    </tr>
                    <?php
                        $totalQty += $purchase['quantity'];
                        $totalCost += $purchase['total_cost'];
                    ?>
                <?php endforeach; ?>
                <tr class="table-warning fw-bold">
                    <td colspan="3" class="text-end">Total</td>
                    <td><?= $totalQty ?></td>
                    <td></td>
                    <td><?= number_format($totalCost, 2) ?></td>
                </tr>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No purchases found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="../Dashboard/page.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>
