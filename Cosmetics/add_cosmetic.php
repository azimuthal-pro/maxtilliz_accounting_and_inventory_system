<?php
require '../dbconfig.php';
if (($_SESSION['branch'] ?? '') !== 'Olebu') { header('Location: ../Dashboard/page.php'); exit(); }
$pageTitle = 'Add Cosmetic Item';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name  = trim($_POST['item']);
    $item_code  = trim($_POST['item_code']);
    $category   = $_POST['category'] ?: 'Cosmetics';
    $unit_price = (float) $_POST['unit_price'];
    $quantity   = (int) $_POST['quantity'];
    $min_stock  = (int) $_POST['min_stock'];
    try {
        $conn->prepare("INSERT INTO cosmetics (item, item_code, category, unit_price, quantity_in_stock, min_stock_level) VALUES (?, ?, ?, ?, ?, ?)")
            ->execute([$item_name, $item_code, $category, $unit_price, $quantity, $min_stock]);
        header('Location: cosmetics_list.php');
        exit();
    } catch (PDOException $e) {
        $message = "Failed to add item. Item code might already exist.";
    }
}

require '../includes/header.php';
require '../includes/sidebar.php';
?>

<a href="cosmetics_list.php" class="btn btn-secondary mb-3">← Back to Cosmetics</a>
<h2>Add Cosmetic Item</h2>

<?php if ($message): ?>
    <div class="alert alert-danger mt-3"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div style="max-width:600px;">
<form method="post" class="card p-4 shadow-sm bg-white mt-4">
    <div class="mb-3">
        <label class="form-label fw-semibold">Item Name</label>
        <input type="text" name="item" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Item Code</label>
        <input type="text" name="item_code" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Category</label>
        <input type="text" name="category" class="form-control" value="Cosmetics">
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Unit Price (GHS)</label>
        <input type="number" name="unit_price" step="0.01" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Quantity in Stock</label>
        <input type="number" name="quantity" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Minimum Stock Level</label>
        <input type="number" name="min_stock" class="form-control" value="1" required>
    </div>
    <button type="submit" class="btn btn-success w-100">Add Item</button>
</form>
</div>

<?php require '../includes/footer.php'; ?>