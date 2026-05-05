<?php
require '../dbconfig.php';
if (($_SESSION['branch'] ?? '') !== 'Olebu') { header('Location: ../Dashboard/page.php'); exit(); }

$message = '';
$messageType = '';
$id = $_GET['id'] ?? null;
if (!$id) die("Invalid item ID.");

$stmt = $conn->prepare("SELECT * FROM cosmetics WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();
if (!$item) die("Item not found.");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST['item']);
    $code  = trim($_POST['item_code']);
    $cat   = $_POST['category'];
    $price = (float) $_POST['unit_price'];
    $stock = (int) $_POST['quantity_in_stock'];
    $min   = (int) $_POST['min_stock_level'];
    try {
        $conn->prepare("UPDATE cosmetics SET item=?, item_code=?, category=?, unit_price=?, quantity_in_stock=?, min_stock_level=? WHERE id=?")
            ->execute([$name, $code, $cat, $price, $stock, $min, $id]);
        $message = "Item updated successfully!";
        $messageType = 'success';
        $stmt->execute([$id]);
        $item = $stmt->fetch();
    } catch (PDOException $e) {
        $message = "Failed to update item.";
        $messageType = 'danger';
    }
}

$pageTitle = 'Edit Cosmetic Item';
require '../includes/header.php';
require '../includes/sidebar.php';
?>

<a href="cosmetics_list.php" class="btn btn-secondary mb-3">← Back to Cosmetics</a>
<h2>Edit Cosmetic Item</h2>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div style="max-width:600px;">
<form method="post" class="card p-4 shadow-sm bg-white">
    <div class="mb-3">
        <label class="form-label fw-semibold">Item Name</label>
        <input type="text" name="item" class="form-control" value="<?= htmlspecialchars($item['item']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Item Code</label>
        <input type="text" name="item_code" class="form-control" value="<?= htmlspecialchars($item['item_code']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Category</label>
        <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($item['category']) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Unit Price (GHS)</label>
        <div class="input-group">
            <span class="input-group-text">GHS</span>
            <input type="number" name="unit_price" class="form-control" step="0.01" value="<?= $item['unit_price'] ?>" required>
        </div>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-6">
            <label class="form-label fw-semibold">Quantity In Stock</label>
            <input type="number" name="quantity_in_stock" class="form-control" value="<?= $item['quantity_in_stock'] ?>" required>
        </div>
        <div class="col-6">
            <label class="form-label fw-semibold">Minimum Stock Level</label>
            <input type="number" name="min_stock_level" class="form-control" value="<?= $item['min_stock_level'] ?>" required>
        </div>
    </div>
    <div class="row g-2 mt-2">
        <div class="col-12 col-sm-6">
            <button type="submit" class="btn w-100" style="background-color:#262161;color:white;border-radius:8px;padding:11px;">
                <i class="bi bi-check-lg me-1"></i>Update Item
            </button>
        </div>
        <div class="col-12 col-sm-6">
            <a href="cosmetics_list.php" class="btn btn-secondary w-100" style="border-radius:8px;padding:11px;">
                <i class="bi bi-arrow-left me-1"></i>Back to Cosmetics
            </a>
        </div>
    </div>
</form>
</div>

<?php require '../includes/footer.php'; ?>