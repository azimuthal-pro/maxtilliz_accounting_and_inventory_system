<?php
require '../dbconfig.php';
if (($_SESSION['branch'] ?? '') !== 'Olebu') { header('Location: ../Dashboard/page.php'); exit(); }
$pageTitle = 'Cosmetics List';
require '../includes/header.php';
require '../includes/sidebar.php';

$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM cosmetics WHERE item LIKE ? OR item_code LIKE ? OR category LIKE ? ORDER BY item ASC");
    $like = "%$search%";
    $stmt->execute([$like, $like, $like]);
} else {
    $stmt = $conn->query("SELECT * FROM cosmetics ORDER BY item ASC");
}
$items = $stmt->fetchAll();
?>

<a href="../Dashboard/page.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
<h2>Cosmetics List</h2>

<form method="get" class="row mb-3">
    <div class="col-md-6">
        <input type="text" name="search" class="form-control" placeholder="Search by item name, code, or category..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Search</button></div>
    <div class="col-md-2"><a href="cosmetics_list.php" class="btn btn-secondary w-100">Reset</a></div>
    <div class="col-md-2"><a href="add_cosmetic.php" class="btn btn-success w-100">Add Item</a></div>
</form>

<style>.low-stock { background-color: #fff3cd !important; }</style>
<div class="table-responsive">
<table class="table table-bordered table-striped mt-4">
    <thead class="table-dark">
        <tr><th>Item Name</th><th>Item Code</th><th>Category</th><th>Unit Price (GHS)</th><th>In Stock</th><th>Min Level</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php if ($items): ?>
        <?php foreach ($items as $item): ?>
            <tr class="<?= $item['quantity_in_stock'] < $item['min_stock_level'] ? 'low-stock' : '' ?>">
                <td><?= htmlspecialchars($item['item']) ?></td>
                <td><?= htmlspecialchars($item['item_code']) ?></td>
                <td><?= htmlspecialchars($item['category']) ?></td>
                <td><?= number_format($item['unit_price'], 2) ?></td>
                <td><?= $item['quantity_in_stock'] ?></td>
                <td><?= $item['min_stock_level'] ?></td>
                <td>
                    <a href="edit_cosmetic.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $item['id'] ?>" data-name="<?= htmlspecialchars($item['item']) ?>">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7" class="text-center">No cosmetics items found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        Swal.fire({
            title: 'Delete Item?',
            html: `Are you sure you want to delete <strong>${this.dataset.name}</strong>?<br><small class="text-muted">This action cannot be undone.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) window.location.href = `delete_cosmetic.php?id=${this.dataset.id}`;
        });
    });
});
</script>

<?php require '../includes/footer.php'; ?>