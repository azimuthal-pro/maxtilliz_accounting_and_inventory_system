<?php
require '../dbconfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = $_POST['item'];
    $supplier = $_POST['supplier'];
    $quantity = (int) $_POST['quantity'];
    $cost = (float) $_POST['cost'];
    $date = $_POST['purchase_date'];

    try {
        $conn->beginTransaction();

        // Insert into purchases table
        $insert = $conn->prepare("INSERT INTO purchases (item, supplier, quantity, total_cost, purchase_date)
                                  VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$item, $supplier, $quantity, $cost, $date]);

        // Update inventory
        $update = $conn->prepare("UPDATE inventory SET quantity_in_stock = quantity_in_stock + ? WHERE item = ?");
        $update->execute([$quantity, $item]);

        $conn->commit();
        //echo "Purchase recorded and inventory updated.";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
<form method="post" class="container mt-4">
    <h3>Record New Purchase</h3>
    <div class="mb-3">
        <label for="item" class="form-label">Item Name</label>
        <select name="item" id="item" class="form-select" required>
    <option value="">Select Item</option>
    <?php
        $items = $conn->query("SELECT item FROM inventory ORDER BY item ASC")->fetchAll();
        foreach ($items as $inv) {
            echo "<option value=\"" . htmlspecialchars($inv['item']) . "\">" . htmlspecialchars($inv['item']) . "</option>";
        }
    ?>
</select>

    </div>
    <div class="mb-3">
        <label for="supplier" class="form-label">Supplier</label>
        <input type="text" name="supplier" id="supplier" class="form-control">
    </div>
    <div class="mb-3">
        <label for="quantity" class="form-label">Quantity Bought</label>
        <input type="number" name="quantity" id="quantity" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="cost" class="form-label">Purchase Cost (Total)</label>
        <input type="number" step="0.01" name="cost" id="cost" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="purchase_date" class="form-label">Purchase Date</label>
        <input type="datetime-local" name="purchase_date" id="purchase_date" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Save Purchase</button>
</form>

</body>
</html>
