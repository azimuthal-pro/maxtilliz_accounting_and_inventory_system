<?php
require '../dbconfig.php';
$pageTitle = 'Add Purchase';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier = trim($_POST['supplier']);
    $date     = $_POST['purchase_date'];
    $total    = (float) $_POST['total_cost'];
    try {
        $conn->prepare("INSERT INTO purchases (supplier, purchase_date, total_cost, item, quantity) VALUES (?, ?, ?, 'General Purchase', 0)")
            ->execute([$supplier, $date, $total]);
        $success = 'Purchase recorded successfully!';
    } catch (Exception $e) {
        $error = 'Failed: ' . $e->getMessage();
    }
}

require '../includes/header.php';
require '../includes/sidebar.php';
?>

<a href="../Dashboard/page.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
<h2 class="mb-4">Record Purchase</h2>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div style="max-width:500px;">
<form method="post" class="card p-4 shadow-sm bg-white">
    <div class="mb-3">
        <label class="form-label fw-semibold">Supplier Name</label>
        <input type="text" name="supplier" class="form-control" placeholder="Enter supplier name" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Purchase Date</label>
        <input type="date" name="purchase_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Purchase Total (GHS)</label>
        <input type="number" step="0.01" name="total_cost" class="form-control" placeholder="0.00" required>
    </div>
    <div class="d-grid mt-4">
        <button type="submit" class="btn" style="background-color:#262161;color:white;border-radius:10px;padding:12px;">Save Purchase</button>
    </div>
</form>
</div>

<?php require '../includes/footer.php'; ?>