<?php
require '../dbconfig.php';

$date_time  = date('Y-m-d H:i:s');
$items      = $_POST['item'] ?? [];
$quantities = $_POST['qty'] ?? [];
$prices     = $_POST['price'] ?? [];
$payment    = $_POST['payment_method'] ?? '';

$status = 'success';
$message = 'Sale recorded successfully.';

if (!empty($items)) {
    $conn->beginTransaction();

    try {
        foreach ($items as $i => $rawItem) {
            $qty   = (int) $quantities[$i];
            $price = (float) $prices[$i];
            $total = $qty * $price;

            // Detect if drug or cosmetic
            if (strpos($rawItem, 'cosmetic:') === 0) {
                $item = substr($rawItem, strlen('cosmetic:'));
                $table = 'cosmetics';
            } else {
                $item = strpos($rawItem, 'drug:') === 0 ? substr($rawItem, strlen('drug:')) : $rawItem;
                $table = 'inventory';
            }

            // Check stock in correct table
            $invStmt = $conn->prepare("SELECT quantity_in_stock FROM `$table` WHERE item = ?");
            $invStmt->execute([$item]);
            $inventory = $invStmt->fetch();

            if (!$inventory) {
                $status = 'error';
                throw new Exception("Item '$item' not found.");
            }

            if ($qty > $inventory['quantity_in_stock']) {
                $status = 'warning';
                throw new Exception("Not enough stock for '$item'.");
            }

            // Insert sale (always into sales table)
            $saleStmt = $conn->prepare("
                INSERT INTO sales (sale_date, item, qty, price, total, payment_method)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $saleStmt->execute([$date_time, $item, $qty, $price, $total, $payment]);

            // Deduct from correct table
            $updateInv = $conn->prepare("UPDATE `$table` SET quantity_in_stock = quantity_in_stock - ? WHERE item = ?");
            $updateInv->execute([$qty, $item]);
        }

        $conn->commit();

    } catch (Exception $e) {
        $conn->rollBack();
        $message = $e->getMessage();
    }
} else {
    $status = 'error';
    $message = 'No items selected.';
}

header("Location: add_sales.php?status=$status&message=" . urlencode($message));
exit;