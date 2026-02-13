<?php
require '../dbconfig.php';

$message = '';
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid item ID.");
}

// Fetch item details
$stmt = $conn->prepare("SELECT * FROM inventory WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    die("Item not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['item'];
    $code = $_POST['item_code'];
    $category = $_POST['category'];
    $price = (float) $_POST['unit_price'];
    $stock = (int) $_POST['quantity_in_stock'];
    $min = (int) $_POST['min_stock_level'];

    $updateStmt = $conn->prepare("UPDATE inventory 
        SET item = ?, item_code = ?, category = ?, unit_price = ?, quantity_in_stock = ?, min_stock_level = ? 
        WHERE id = ?");
    
    if ($updateStmt->execute([$name, $code, $category, $price, $stock, $min, $id])) {
        $message = "Item updated successfully!";
        // Refresh item data
        $stmt->execute([$id]);
        $item = $stmt->fetch();
    } else {
        $message = "Failed to update item.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Inventory Item</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Edit Inventory Item</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" class="card p-4 shadow-sm bg-white">
        <div class="mb-3">
            <label class="form-label">Item Name</label>
            <input type="text" name="item" class="form-control" value="<?= htmlspecialchars($item['item']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Item Code</label>
            <input type="text" name="item_code" class="form-control" value="<?= htmlspecialchars($item['item_code']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($item['category']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Unit Price (GHS)</label>
            <input type="number" name="unit_price" class="form-control" step="0.01" value="<?= $item['unit_price'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantity In Stock</label>
            <input type="number" name="quantity_in_stock" class="form-control" value="<?= $item['quantity_in_stock'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Minimum Stock Level</label>
            <input type="number" name="min_stock_level" class="form-control" value="<?= $item['min_stock_level'] ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Update Item</button>
        <a href="invent_list_page.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
</body>
</html>
