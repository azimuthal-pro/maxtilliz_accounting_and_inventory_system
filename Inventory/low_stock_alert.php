<?php
require '../dbconfig.php';

$stmt = $conn->query("SELECT * FROM inventory WHERE quantity_in_stock <= min_stock_level");
$lowStockItems = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Low Stock Alerts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4 text-danger">Low Stock Alerts</h2>

    <?php if (count($lowStockItems) > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-warning">
                <tr>
                    <th>Item Name</th>
                    <th>Item Code</th>
                    <th>Quantity in Stock</th>
                    <th>Minimum Stock Level</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lowStockItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= htmlspecialchars($item['item_code']) ?></td>
                        <td><?= $item['quantity_in_stock'] ?></td>
                        <td><?= $item['min_stock_level'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-success">All items are sufficiently stocked.</div>
    <?php endif; ?>
</div>
</body>
</html>
