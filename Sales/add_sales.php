<?php
require '../dbconfig.php';
$pageTitle = 'Sales Entry';
$isAdmin = isset($_SESSION['admin']);

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Entry - Maxtilliz</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }

        <?php if ($isAdmin): ?>
        /* Admin layout - with sidebar */
        .sidebar {
            width: 250px;
            background-color: #262161;
            color: white;
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }
        .sidebar a, .sidebar button {
            color: white;
            display: block;
            padding: 13px 15px;
            text-decoration: none;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            font-size: 15px;
        }
        .sidebar a:hover, .sidebar button:hover { background-color: #24B8EE; }
        .collapse a { padding-left: 40px; }
        .main-content { margin-left: 250px; padding: 20px; }
        .mobile-navbar {
            display: none;
            background-color: #262161;
            color: white;
            padding: 12px 15px;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .sidebar-overlay.show { display: block; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-250px); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 15px; }
            .mobile-navbar { display: flex; justify-content: space-between; align-items: center; }
        }
        <?php else: ?>
        /* Employee layout - no sidebar, centered content */
        .main-content { 
            max-width: 750px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .employee-header {
            background-color: #262161;
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        <?php endif; ?>
    </style>
</head>
<body>

<?php if ($isAdmin): ?>
<!-- ADMIN: Mobile navbar + sidebar -->
<div class="mobile-navbar">
    <span style="font-size:18px;font-weight:bold;color:white;">
        <img src="../Dashboard/Maxtilliz_logo.jpg" height="30" width="30" class="me-2">Maxtilliz Chem
    </span>
    <button id="sidebarToggle" style="background:none;border:none;color:white;font-size:24px;"><i class="bi bi-list"></i></button>
</div>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="sidebar" id="sidebar">
    <h4 class="text-center py-3 border-bottom d-none d-md-block">
        <img src="../Dashboard/Maxtilliz_logo.jpg" height="50" width="50"> Maxtilliz Chem
    </h4>
    <a href="../Dashboard/page.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <button data-bs-toggle="collapse" data-bs-target="#salesMenu"><i class="bi bi-cart-check me-2"></i>Sales</button>
    <div class="collapse" id="salesMenu">
        <a href="../Sales/add_sales.php"><i class="bi bi-plus-circle me-2"></i>Add Sale</a>
        <a href="../Sales/view_sales.php"><i class="bi bi-list-ul me-2"></i>View Sales</a>
    </div>
    <button data-bs-toggle="collapse" data-bs-target="#drugsMenu"><i class="bi bi-box-seam me-2"></i>Inventory</button>
    <div class="collapse" id="drugsMenu">
        <a href="../Inventory/add_inventory.php"><i class="bi bi-plus-circle me-2"></i>Add Inventory</a>
        <a href="../Inventory/invent_list_page.php"><i class="bi bi-list-ul me-2"></i>View Inventory</a>
        <a href="../Inventory/low_stock_alert.php"><i class="bi bi-exclamation-triangle me-2"></i>Out of Stocks</a>
    </div>
    <?php if (($_SESSION['branch'] ?? '') === 'Olebu'): ?>
    <button data-bs-toggle="collapse" data-bs-target="#cosmeticsMenu"><i class="bi bi-stars me-2"></i>Cosmetics</button>
    <div class="collapse" id="cosmeticsMenu">
        <a href="../Cosmetics/add_cosmetic.php"><i class="bi bi-plus-circle me-2"></i>Add Cosmetic</a>
        <a href="../Cosmetics/cosmetics_list.php"><i class="bi bi-list-ul me-2"></i>View Cosmetics</a>
    </div>
    <?php endif; ?>
    <button data-bs-toggle="collapse" data-bs-target="#purchaseMenu"><i class="bi bi-truck me-2"></i>Purchase</button>
    <div class="collapse" id="purchaseMenu">
        <a href="../Purchase/purchase_form.php"><i class="bi bi-plus-circle me-2"></i>Add Purchase</a>
        <a href="../Purchase/purchase_history.php"><i class="bi bi-clock-history me-2"></i>Purchase History</a>
    </div>
    <button data-bs-toggle="collapse" data-bs-target="#reportsMenu"><i class="bi bi-bar-chart-line me-2"></i>Reports</button>
    <div class="collapse" id="reportsMenu">
        <a href="../Sales/sales_report_export.php"><i class="bi bi-graph-up-arrow me-2"></i>Sales Reports</a>
        <a href="../Purchase/purchase_history_report.php"><i class="bi bi-graph-down me-2"></i>Purchase Reports</a>
        <a href="../Profit_Loss/profit_loss_report.php"><i class="bi bi-graph-up me-2"></i>Profit/Loss</a>
    </div>
    <a href="../Access_control/register_user.php"><i class="bi bi-person-plus me-2"></i>Register User</a>
    <a href="../Access_control/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
</div>

<?php else: ?>
<!-- EMPLOYEE: Simple top bar with logout -->
<div class="employee-header">
    <span>
        <img src="../Dashboard/Maxtilliz_logo.jpg" height="35" width="35" class="me-2">
        <strong>Maxtilliz Chem</strong> — Sales
    </span>
    <a href="../Access_control/logout.php" class="btn btn-sm btn-outline-light">
        <i class="bi bi-box-arrow-right me-1"></i>Logout
    </a>
</div>
<?php endif; ?>

<!-- MAIN CONTENT -->
<div class="main-content">

    <?php if ($isAdmin): ?>
    <a href="../Dashboard/page.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
    <?php endif; ?>

    <h3 class="mb-3 fw-bold" style="color:#262161;">Sales Entry</h3>

    <!-- SALES FORM -->
    <div class="card shadow-sm p-4 mb-4">
        <form method="post" action="add_sale_query.php">
            <div id="itemsContainer">
                <div class="item-row mb-3">
                    <div class="row g-2 align-items-end">
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
                        <div class="col-4 col-md-2">
                            <label class="form-label fw-semibold">Qty</label>
                            <input type="number" name="qty[]" class="form-control qty" placeholder="0" min="1" required>
                        </div>
                        <div class="col-5 col-md-3">
                            <label class="form-label fw-semibold">Price (GHS)</label>
                            <input type="number" name="price[]" class="form-control price" placeholder="0.00" readonly>
                        </div>
                        <div class="col-3 col-md-1">
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

            <button type="submit" class="btn w-100" style="background-color:#262161;color:white;padding:12px;border:none;border-radius:8px;">
                Record Sale
            </button>
        </form>
    </div>

    <!-- SALES SUMMARY -->
    <div class="card shadow-sm p-3 mb-4">
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
                    <td><?= $totalQty ?></td><td></td>
                    <td><?= number_format($totalAmount, 2) ?></td><td></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>

</div><!-- /.main-content -->

<script src="../assets/js/bootstrap.bundle.min.js"></script>
<?php if ($isAdmin): ?>
<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    toggleBtn?.addEventListener('click', () => { sidebar.classList.toggle('show'); overlay.classList.toggle('show'); });
    overlay.addEventListener('click', () => { sidebar.classList.remove('show'); overlay.classList.remove('show'); });
</script>
<?php endif; ?>

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