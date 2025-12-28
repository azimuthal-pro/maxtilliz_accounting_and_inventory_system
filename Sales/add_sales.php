<?php
require '../dbconfig.php';

$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_time = $_POST['sale_date'];
    $item = $_POST['item'];
    $quantity = (int) $_POST['qty'];
    $price = (float) $_POST['price'];
    $payment = $_POST['payment_method'];
    $total = $quantity * $price;


    // Check current stock
    $invStmt = $conn->prepare("SELECT quantity_in_stock FROM inventory WHERE item = ?");
    $invStmt->execute([$item]);
    $inventory = $invStmt->fetch();

    if (!$inventory) {
        $message = "Error: Item not found in inventory.";
    } elseif ($quantity > $inventory['quantity_in_stock']) {
        $message = "Error: Not enough stock available.";
    } else {
        // Proceed with sale
        $conn->beginTransaction();

        try {
            $saleStmt = $conn->prepare("INSERT INTO sales (sale_date, item, qty, price, total, payment_method) VALUES (?, ?, ?, ?, ?, ?)");
            $saleStmt->execute([$date_time, $item, $quantity, $price, $total, $payment]);

            $updateInv = $conn->prepare("UPDATE inventory SET quantity_in_stock = quantity_in_stock - ? WHERE item = ?");
            $updateInv->execute([$quantity, $item]);

            $conn->commit();
            $message = "Sale recorded and stock updated.";
        } catch (Exception $e) {
            $conn->rollBack();
            $message = "Failed to record sale. Please try again.";
        }
    }
}

// Get today's sales
$today = date('Y-m-d');
$salesTodayStmt = $conn->prepare("SELECT * FROM sales WHERE sale_date >= ? AND sale_date < ?");
$startOfDay = $today . ' 00:00:00';
$endOfDay = $today . ' 23:59:59';
$salesTodayStmt->execute([$startOfDay,$endOfDay]);
$salesToday = $salesTodayStmt->fetchAll();

// Calculate totals
$totalQty = 0;
$totalAmount = 0.0;
foreach ($salesToday as $sale) {
    $totalQty += $sale['qty'];
    $totalAmount += $sale['total'];
}

$inventoryItemsStmt = $conn->prepare("SELECT item FROM inventory ORDER BY item ASC");
$inventoryItemsStmt->execute();
$inventoryItems = $inventoryItemsStmt->fetchAll(PDO::FETCH_COLUMN);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sales Entry - OTC Accounting</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <img src="../Dashboard/Maxtilliz_logo.jpg" alt="logo here" height="150px" width="150px" class="mb-3 mx-auto d-block">
        <h2 class="mb-4">Sales Entry</h2>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>


        <form method="post" class="card p-4 shadow-sm bg-white">
            <div class="mb-3">
                <label for="date" class="form-label">Date & Time</label>
                <input type="datetime-local" class="form-control" id="date" name="sale_date" required>
            </div>
            <div class="mb-3">
                <div class="mb-3">
                    <label for="item" class="form-label">Item</label>
                    <select name="item" id="item" class="form-select" required>
                        <option value="">-- Select Item --</option>
                        <?php foreach ($inventoryItems as $invItem): ?>
                            <option value="<?= htmlspecialchars($invItem) ?>"><?= htmlspecialchars($invItem) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>
            <div class="mb-3">
                <label for="qty" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="qty" name="qty" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price per Unit (GHS)</label>
                <input type="number" class="form-control" step="0.01" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select name="payment_method" class="form-select" id="payment_method" required>
                    <option value="Cash">Cash</option>
                    <option value="Mobile Money">Mobile Money</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Record Sale</button>
        </form>

        <!-- <div class="mt-4">
            <a href="../index.php" class="btn btn-secondary">Back to Dashboard</a>
        </div> -->

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
                        <td colspan="2" class="text-end">Total</td>
                        <td><?= $totalQty ?></td>
                        <td></td>
                        <td><?= number_format($totalAmount, 2) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>

        <?php
        // Initialize payment breakdown
        $paymentBreakdown = [
            'Cash' => 0,
            'Mobile Money' => 0,
        ];

        foreach ($salesToday as $sale) {
            $method = $sale['payment_method'];
            if (isset($paymentBreakdown[$method])) {
                $paymentBreakdown[$method] += $sale['total'];
            }
        }
        ?>

        <h5 class="mt-4">Breakdown by Payment Method</h5>
        <ul class="list-group w-50">
            <?php foreach ($paymentBreakdown as $method => $amount): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= $method ?>:
                    <span><strong><?= number_format($amount, 2) ?> GHS</strong></span>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>

      <script>
document.getElementById('item').addEventListener('change', function () {
    const itemName = this.value;
    const priceInput = document.getElementById('price');

    if (!itemName) {
        priceInput.value = '';
        return;
    }

    fetch('get_item_price.php?item=' + encodeURIComponent(itemName))
        .then(response => response.json())
        .then(data => {
            priceInput.value = data.price;
        })
        .catch(() => {
            priceInput.value = '';
        });
});
</script>
</body>

</html>