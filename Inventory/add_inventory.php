<?php
require '../dbconfig.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item'];
    $item_code = $_POST['item_code'];
    $unit_price = (float) $_POST['unit_price'];
    $quantity = (int) $_POST['quantity'];
    $min_stock = (int) $_POST['min_stock'];

    $stmt = $conn->prepare("INSERT INTO drugs_tb (item, item_code, unit_price, quantity_in_stock, min_stock_level) 
                            VALUES (?, ?, ?, ?, ?)");

    if ($stmt->execute([$item_name, $item_code, $unit_price, $quantity, $min_stock])) {
        $message = "Item added successfully!";
        header('Location:invent_list_page.php');
    } else {
        $message = "Failed to add item. Item code might already exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add drugs_tb Item</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-3">
        <a href="../Dashboard/page.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
        <h2>Add Item To drugs_tb </h2>

        <?php if ($message): ?>
            <div class="alert alert-info mt-3"><?= $message ?></div>
        <?php endif; ?>

        <form method="post" class="card p-4 shadow-sm bg-white mt-4">
            <div class="mb-3">
                <label class="form-label">Item Name</label>
                <input type="text" name="item" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Item Code</label>
                <input type="text" name="item_code" class="form-control" required>
            </div>

            <!-- <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-control">
            </div> -->

            <div class="mb-3">
                <label class="form-label">Unit Price (GHS)</label>
                <input type="number" name="unit_price" step="0.01" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity in Stock</label>
                <input type="number" name="quantity" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Minimum Stock Level</label>
                <input type="number" name="min_stock" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Item</button>
            <!-- <a href="index.php" class="btn btn-secondary">Back to Dashboard</a> -->
        </form>
    </div>
</body>
</html>
