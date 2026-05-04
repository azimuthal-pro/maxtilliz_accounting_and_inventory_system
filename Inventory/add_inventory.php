<?php
require '../dbconfig.php';

$message = '';
$messageType = '';
$formData = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name  = trim($_POST['item']);
    $item_code  = trim($_POST['item_code']);
    $unit_price = (float) $_POST['unit_price'];
    $quantity   = (int) $_POST['quantity'];
    $min_stock  = (int) $_POST['min_stock'];

    // Keep form data so user doesn't retype everything on error
    $formData = $_POST;

    try {
        // Check if item code already exists
        $check = $conn->prepare("SELECT id FROM inventory WHERE item_code = ?");
        $check->execute([$item_code]);
        if ($check->fetch()) {
            throw new Exception("item_code_exists");
        }

        // Check if item name already exists
        $checkName = $conn->prepare("SELECT id FROM inventory WHERE item = ?");
        $checkName->execute([$item_name]);
        if ($checkName->fetch()) {
            throw new Exception("item_name_exists");
        }

        $stmt = $conn->prepare("INSERT INTO inventory (item, item_code, unit_price, quantity_in_stock, min_stock_level)
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$item_name, $item_code, $unit_price, $quantity, $min_stock]);

        header('Location: invent_list_page.php');
        exit();

    } catch (Exception $e) {
        if ($e->getMessage() === 'item_code_exists') {
            $message = "Item code <strong>" . htmlspecialchars($item_code) . "</strong> already exists. Please use a different item code.";
        } elseif ($e->getMessage() === 'item_name_exists') {
            $message = "Item <strong>" . htmlspecialchars($item_name) . "</strong> already exists in inventory. Use the edit page to update it instead.";
        } else {
            $message = "Failed to add item. Please try again.";
        }
        $messageType = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Inventory Item</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container mt-3" style="max-width:600px;">
    <a href="../Dashboard/page.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
    <h2>Add Item To Inventory</h2>

    <?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show mt-3 d-flex align-items-start gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 mt-1"></i>
        <div>
            <?= $message ?>
            <?php if (str_contains($message, 'already exists in inventory')): ?>
                <br><a href="invent_list_page.php" class="alert-link">Go to inventory list →</a>
            <?php endif; ?>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <form method="post" class="card p-4 shadow-sm bg-white mt-3">
        <div class="mb-3">
            <label class="form-label fw-semibold">Item Name</label>
            <input type="text" name="item" class="form-control"
                value="<?= htmlspecialchars($formData['item'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Item Code</label>
            <input type="text" name="item_code" class="form-control"
                value="<?= htmlspecialchars($formData['item_code'] ?? '') ?>" required>
            <div class="form-text">Must be unique e.g. 001, 002</div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Unit Price (GHS)</label>
            <input type="number" name="unit_price" step="0.01" class="form-control"
                value="<?= htmlspecialchars($formData['unit_price'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Quantity in Stock</label>
            <input type="number" name="quantity" class="form-control"
                value="<?= htmlspecialchars($formData['quantity'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Minimum Stock Level</label>
            <input type="number" name="min_stock" class="form-control"
                value="<?= htmlspecialchars($formData['min_stock'] ?? '') ?>" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Add Item</button>
    </form>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>