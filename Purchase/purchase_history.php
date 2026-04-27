<?php
require '../dbconfig.php';

$fromDate = $_GET['from_date'] ?? '';
$toDate   = $_GET['to_date'] ?? '';
$supplier = $_GET['supplier'] ?? '';

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

$totalCost = 0;
foreach ($purchases as $p) {
    $totalCost += $p['total_cost'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body>
<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <a href="../Dashboard/page.php" class="btn btn-secondary no-print">← Back to Dashboard</a>
        </div>
        <h3 class="fw-bold mb-0" style="color:#262161;">Purchase History</h3>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-outline-primary me-2">
                <i class="bi bi-printer me-1"></i>Print
            </button>
            <form method="post" action="purchase_history_report.php" class="d-inline">
                <input type="hidden" name="from_date" value="<?= htmlspecialchars($fromDate) ?>">
                <input type="hidden" name="to_date"   value="<?= htmlspecialchars($toDate) ?>">
                <input type="hidden" name="supplier"  value="<?= htmlspecialchars($supplier) ?>">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                </button>
            </form>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-4 no-print">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">From Date</label>
                    <input type="date" class="form-control" name="from_date"
                        value="<?= htmlspecialchars($fromDate) ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">To Date</label>
                    <input type="date" class="form-control" name="to_date"
                        value="<?= htmlspecialchars($toDate) ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Supplier</label>
                    <select name="supplier" class="form-select">
                        <option value="All">All Suppliers</option>
                        <?php
                        $suppliers = $conn->query("SELECT DISTINCT supplier FROM purchases WHERE supplier IS NOT NULL ORDER BY supplier ASC")->fetchAll();
                        foreach ($suppliers as $s):
                        ?>
                            <option value="<?= htmlspecialchars($s['supplier']) ?>"
                                <?= $supplier == $s['supplier'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['supplier']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn w-100" style="background-color:#262161;color:white;">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="purchase_history.php" class="btn btn-secondary no-print  w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="row mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-white" style="background-color:#262161;">
                <div class="card-body">
                    <h6 class="card-title">Total Records</h6>
                    <p class="card-text fw-bold fs-4"><?= count($purchases) ?></p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white" style="background-color:#24B8EE;">
                <div class="card-body">
                    <h6 class="card-title">Total Spent (GHS)</h6>
                    <p class="card-text fw-bold fs-4"><?= number_format($totalCost, 2) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Total Cost (GHS)</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($purchases): ?>
                    <?php foreach ($purchases as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= date('d M Y', strtotime($p['purchase_date'])) ?></td>
                            <td><?= htmlspecialchars($p['supplier']) ?></td>
                            <td><?= number_format($p['total_cost'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-warning fw-bold">
                        <td colspan="3" class="text-end">TOTAL</td>
                        <td><?= number_format($totalCost, 2) ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No purchases found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
