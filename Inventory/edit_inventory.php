<?php
require '../dbconfig.php';

$message = '';
$messageType = '';
$id = $_GET['id'] ?? null;
if (!$id) die("Invalid item ID.");

$stmt = $conn->prepare("SELECT * FROM inventory WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();
if (!$item) die("Item not found.");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST['item']);
    $code  = trim($_POST['item_code']);
    $price = (float) $_POST['unit_price'];
    $stock = (int) $_POST['quantity_in_stock'];
    $min   = (int) $_POST['min_stock_level'];
    try {
        $conn->prepare("UPDATE inventory SET item=?, item_code=?, unit_price=?, quantity_in_stock=?, min_stock_level=? WHERE id=?")
            ->execute([$name, $code, $price, $stock, $min, $id]);
        $message = "Item updated successfully!";
        $messageType = 'success';
        $stmt->execute([$id]);
        $item = $stmt->fetch();
    } catch (PDOException $e) {
        $message = "Failed to update — item code may already exist.";
        $messageType = 'danger';
    }
}

$pageTitle = 'Edit Inventory Item';
require '../includes/header.php';
require '../includes/sidebar.php';
?>

<style>
.edit-card { max-width:580px; background:white; border-radius:16px; padding:35px 30px; box-shadow:0 4px 20px rgba(0,0,0,0.08); }
.page-header { background-color:#262161; color:white; border-radius:12px; padding:15px 20px; margin-bottom:25px; }
.btn-update { background-color:#262161; color:white; border:none; padding:11px; border-radius:8px; font-size:15px; }
.btn-update:hover { background-color:#1a1645; color:white; }
.btn-back { background-color:#f0f0f0; color:#333; border:none; padding:11px; border-radius:8px; font-size:15px; }
.btn-back:hover { background-color:#e0e0e0; color:#333; }
</style>

<div class="edit-card">
    <div class="page-header d-flex align-items-center gap-2">
        <i class="bi bi-pencil-square fs-5"></i>
        <div>
            <h5 class="mb-0">Edit Inventory Item</h5>
            <small style="opacity:0.8;">Editing: <?= htmlspecialchars($item['item']) ?></small>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
        <span><?= htmlspecialchars($message) ?></span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label fw-semibold">Item Name</label>
            <input type="text" name="item" class="form-control" value="<?= htmlspecialchars($item['item']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Item Code</label>
            <input type="text" name="item_code" class="form-control" value="<?= htmlspecialchars($item['item_code']) ?>" required>
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
                <button type="submit" class="btn btn-update w-100"><i class="bi bi-check-lg me-1"></i>Update Item</button>
            </div>
            <div class="col-12 col-sm-6">
                <a href="invent_list_page.php" class="btn btn-back w-100"><i class="bi bi-arrow-left me-1"></i>Back to Inventory</a>
            </div>
        </div>
    </form>
</div>

<?php require '../includes/footer.php'; ?>