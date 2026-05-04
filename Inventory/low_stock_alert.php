<?php
require '../dbconfig.php';
$pageTitle = 'Out of Stocks';
require '../includes/header.php';
require '../includes/sidebar.php';

$stmt = $conn->query("SELECT * FROM inventory WHERE quantity_in_stock <= min_stock_level ORDER BY quantity_in_stock ASC");
$lowStockItems = $stmt->fetchAll();
?>

<a href="../Dashboard/page.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
<h2 class="mb-4 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Out of Stock / Low Stock Items</h2>

<?php if (count($lowStockItems) > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-warning">
                <tr><th>Item Name</th><th>Item Code</th><th>In Stock</th><th>Min Level</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($lowStockItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item']) ?></td>
                        <td><?= htmlspecialchars($item['item_code']) ?></td>
                        <td class="text-danger fw-bold"><?= $item['quantity_in_stock'] ?></td>
                        <td><?= $item['min_stock_level'] ?></td>
                        <td><a href="edit_inventory.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-primary">Restock</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>All items are sufficiently stocked.</div>
<?php endif; ?>

<?php require '../includes/footer.php'; ?>