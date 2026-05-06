<?php
require '../dbconfig.php';
$pageTitle = 'Sales Entry';

$today = date('Y-m-d');

$stmt = $conn->prepare("SELECT * FROM sales WHERE sale_date BETWEEN ? AND ? ORDER BY sale_date DESC");
$stmt->execute([$today . ' 00:00:00', $today . ' 23:59:59']);
$salesToday = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalQty = 0;
$totalAmount = 0;
foreach ($salesToday as $sale) {
    $totalQty += $sale['qty'];
    $totalAmount += $sale['total'];
}

$inventoryItems = $conn->query("SELECT item FROM inventory ORDER BY item ASC")->fetchAll(PDO::FETCH_COLUMN);

$cosmeticsItems = [];
$cosmeticItemNames = [];
if (($_SESSION['branch'] ?? '') === 'Olebu') {
    try {
        $cosmeticsItems = $conn->query("SELECT item FROM cosmetics ORDER BY item ASC")->fetchAll(PDO::FETCH_COLUMN);
        $cosmeticItemNames = $cosmeticsItems;
    } catch (Exception $e) {}
}

$totalDrugs = $totalCosmetics = $totalCash = $totalMobileMoney = 0;
foreach ($salesToday as $sale) {
    $amount = $sale['total'];
    in_array($sale['item'], $cosmeticItemNames) ? $totalCosmetics += $amount : $totalDrugs += $amount;
    if ($sale['payment_method'] === 'Cash') $totalCash += $amount;
    elseif ($sale['payment_method'] === 'Mobile Money') $totalMobileMoney += $amount;
}

$isAdmin = isset($_SESSION['admin']);

require '../includes/header.php';
require '../includes/sidebar.php';
?>

<?php if ($isAdmin): ?>
<a href="../Dashboard/page.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
<?php endif; ?>

<h3 class="mb-3 fw-bold" style="color:#262161;">Sales Entry</h3>

<!-- SALES FORM -->
<div class="card shadow-sm p-4 mb-4" style="max-width:700px;">
    <form method="post" action="add_sale_query.php">
        <div id="itemsContainer">
            <div class="item-row mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Item</label>
                        <select name="item[]" class="form-select item-select" required>
                            <option value="">-- Select Item --</option>
                            <?php if ($inventoryItems): ?>
                            <optgroup label="DRUGS / PHARMACEUTICALS">
                            <?php foreach ($inventoryItems as $invItem): ?>
                                <option value="drug:<?= htmlspecialchars($invItem) ?>"><?= htmlspecialchars($invItem) ?></option>
                            <?php endforeach; ?>
                            </optgroup>
                            <?php endif; ?>
                            <?php if ($cosmeticsItems): ?>
                            <optgroup label="COSMETICS">
                            <?php foreach ($cosmeticsItems as $cosItem): ?>
                                <option value="cosmetic:<?= htmlspecialchars($cosItem) ?>"><?= htmlspecialchars($cosItem) ?></option>
                            <?php endforeach; ?>
                            </optgroup>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-5 col-md-2">
                        <label class="form-label fw-semibold">Qty</label>
                        <input type="number" name="qty[]" class="form-control qty" placeholder="0" min="1" required>
                    </div>
                    <div class="col-5 col-md-3">
                        <label class="form-label fw-semibold">Price (GHS)</label>
                        <input type="number" name="price[]" class="form-control price" placeholder="0.00" readonly>
                    </div>
                    <div class="col-2 col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-item w-100">✕</button>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" id="addItem" class="btn btn-outline-secondary btn-sm mb-3">
            + Add Another Item
        </button>

        <div class="mb-3">
            <label class="form-label fw-semibold">Payment Method</label>
            <select name="payment_method" class="form-select">
                <option value="Cash">Cash</option>
                <option value="Mobile Money">Mobile Money</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100" style="background-color:#262161;border:none;padding:12px;">
            Record Sale
        </button>
    </form>
</div>

<!-- SALES SUMMARY -->
<div class="card shadow-sm p-3 mb-4" style="max-width:700px;">
    <h5 class="fw-bold mb-3" style="color:#262161;">Sales Summary</h5>
    <ul class="list-group list-group-flush">
        <?php if (($_SESSION['branch'] ?? '') === 'Olebu'): ?>
        <li class="list-group-item d-flex justify-content-between px-0">
            <span>💊 Drugs Total</span><strong><?= number_format($totalDrugs, 2) ?> GHS</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between px-0">
            <span>💄 Cosmetics Total</span><strong><?= number_format($totalCosmetics, 2) ?> GHS</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between px-0 fw-bold">
            <span>📦 Grand Total</span><strong><?= number_format($totalAmount, 2) ?> GHS</strong>
        </li>
        <?php else: ?>
        <li class="list-group-item d-flex justify-content-between px-0">
            <span>💊 Drugs Total</span><strong><?= number_format($totalAmount, 2) ?> GHS</strong>
        </li>
        <?php endif; ?>
        <li class="list-group-item d-flex justify-content-between px-0">
            <span>💵 Cash Total</span><strong><?= number_format($totalCash, 2) ?> GHS</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between px-0">
            <span>📱 Mobile Money Total</span><strong><?= number_format($totalMobileMoney, 2) ?> GHS</strong>
        </li>
    </ul>
</div>

<!-- TODAY'S SALES TABLE -->
<h5 class="fw-bold mb-2" style="color:#262161;">Today's Sales — <?= date('d M Y') ?></h5>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr><th>Time</th><th>Item</th><th>Qty</th><th>Price (GHS)</th><th>Total (GHS)</th><th>Payment</th></tr>
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
            <tr><td colspan="6" class="text-center text-muted py-3">No sales recorded today</td></tr>
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
</div>

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

<?php require '../includes/footer.php'; ?>