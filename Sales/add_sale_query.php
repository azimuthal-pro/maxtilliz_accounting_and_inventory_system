<?php
require '../dbconfig.php';

$date_time  = date('Y-m-d H:i:s'); // automatic timestamp
$items      = $_POST['item'] ?? [];
$quantities = $_POST['qty'] ?? [];
$prices     = $_POST['price'] ?? [];
$payment    = $_POST['payment_method'] ?? '';

$status = 'success';
$message = 'Sale recorded successfully.';

if (!empty($items)) {
    $conn->beginTransaction();

    try {
        foreach ($items as $i => $item) {

            $qty   = (int) $quantities[$i];
            $price = (float) $prices[$i];
            $total = $qty * $price;

            // Check stock
            $invStmt = $conn->prepare(
                "SELECT quantity_in_stock FROM inventory WHERE item = ?"
            );
            $invStmt->execute([$item]);
            $inventory = $invStmt->fetch();

            if (!$inventory) {
                $status = 'error';
                throw new Exception("Item '$item' not found in inventory.");
            }

            if ($qty > $inventory['quantity_in_stock']) {
                $status = 'warning';
                throw new Exception("Not enough stock for '$item'.");
            }

            // Insert sale
            $saleStmt = $conn->prepare("
                INSERT INTO sales 
                (sale_date, item, qty, price, total, payment_method)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $saleStmt->execute([
                $date_time,
                $item,
                $qty,
                $price,
                $total,
                $payment
            ]);

            // Update inventory
            $updateInv = $conn->prepare("
                UPDATE inventory
                SET quantity_in_stock = quantity_in_stock - ?
                WHERE item = ?
            ");
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
