<?php
require '../dbconfig.php';

$today = date('Y-m-d');

$stmt = $conn->prepare("
    SELECT * FROM sales
    WHERE sale_date BETWEEN ? AND ?
    ORDER BY sale_date DESC
");
$stmt->execute([
    $today . ' 00:00:00',
    $today . ' 23:59:59'
]);
$salesToday = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalQty = 0;
$totalAmount = 0;
foreach ($salesToday as $sale) {
    $totalQty += $sale['qty'];
    $totalAmount += $sale['total'];
}

$itemsStmt = $conn->query("SELECT item FROM inventory ORDER BY item ASC");
$inventoryItems = $itemsStmt->fetchAll(PDO::FETCH_COLUMN);

$paymentBreakdown = [
    'Cash' => 0,
    'Mobile Money' => 0
];
foreach ($salesToday as $sale) {
    if (isset($paymentBreakdown[$sale['payment_method']])) {
        $paymentBreakdown[$sale['payment_method']] += $sale['total'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Entry</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container mt-5">

    <img src="../Dashboard/Maxtilliz_logo.jpg" class="d-block mx-auto mb-3" width="150">
    <h2 class="mb-4 text-center">Sales Entry</h2>

    <!-- SALES FORM -->
    <form method="post" action="add_sale_query.php" class="card p-4 shadow-sm">

        <div id="itemsContainer">
            <div class="item-row d-flex gap-2 mb-2">
                <select name="item[]" class="form-select item-select" required>
                    <option value="">-- Select Item --</option>
                    <?php foreach ($inventoryItems as $invItem): ?>
                        <option value="<?= htmlspecialchars($invItem) ?>">
                            <?= htmlspecialchars($invItem) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="number" name="qty[]" class="form-control qty" placeholder="Qty" required>
                <input type="number" name="price[]" class="form-control price" placeholder="Price" readonly>
                <button type="button" class="btn btn-danger remove-item">X</button>
            </div>
        </div>

        <button type="button" id="addItem" class="btn btn-secondary mb-3">
            + Add Another Item
        </button>

        <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select name="payment_method" class="form-select" required>
                <option value="Cash">Cash</option>
                <option value="Mobile Money">Mobile Money</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            Record Sale
        </button>
    </form>

    <!-- TODAY'S SALES -->
    <h4 class="mt-5">Today's Sales (<?= $today ?>)</h4>

    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
        <tr>
            <th>Time</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Price (GHS)</th>
            <th>Total (GHS)</th>
            <th>Payment</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($salesToday): ?>
            <?php foreach ($salesToday as $sale): ?>
                <tr>
                    <td><?= date('H:i', strtotime($sale['sale_date'])) ?></td>
                    <td><?= htmlspecialchars($sale['item']) ?></td>
                    <td><?= $sale['qty'] ?></td>
                    <td><?= number_format($sale['price'], 2) ?></td>
                    <td><?= number_format($sale['total'], 2) ?></td>
                    <td><?= htmlspecialchars($sale['payment_method']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No sales recorded today</td>
            </tr>
        <?php endif; ?>
        </tbody>

        <?php if ($salesToday): ?>
        <tfoot class="table-secondary fw-bold">
        <tr>
            <td colspan="2" class="text-end">TOTAL</td>
            <td><?= $totalQty ?></td>
            <td></td>
            <td><?= number_format($totalAmount, 2) ?></td>
            <td></td>
        </tr>
        </tfoot>
        <?php endif; ?>
    </table>

    <!-- PAYMENT BREAKDOWN -->
    <h5 class="mt-4">Payment Breakdown</h5>
    <ul class="list-group w-50">
        <?php foreach ($paymentBreakdown as $method => $amount): ?>
            <li class="list-group-item d-flex justify-content-between">
                <?= $method ?>
                <strong><?= number_format($amount, 2) ?> GHS</strong>
            </li>
        <?php endforeach; ?>
    </ul>

</div>

<!-- JS FILES -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/add_sales.js"></script>

<?php if (isset($_GET['status'], $_GET['message'])): ?>
<script>
Swal.fire({
    icon: '<?= $_GET['status'] ?>',
    title: 'Sales Status',
    text: '<?= htmlspecialchars($_GET['message']) ?>',
    confirmButtonText: 'OK'
});
</script>
<?php endif; ?>

</body>
</html>