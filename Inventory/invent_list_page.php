<?php
require '../dbconfig.php';

// $stmt = $conn->query("SELECT * FROM inventory ORDER BY item ASC");
// $items = $stmt->fetchAll();
$search = $_GET['search'] ?? '';

if ($search) {
    $stmt = $conn->prepare("
        SELECT * FROM inventory 
        WHERE item LIKE ? 
           OR item_code LIKE ? 
           OR category LIKE ?
        ORDER BY item ASC
    ");
    $likeSearch = "%$search%";
    $stmt->execute([$likeSearch, $likeSearch, $likeSearch]);
} else {
    $stmt = $conn->query("SELECT * FROM inventory ORDER BY item ASC");
}

$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory List</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .low-stock {
            background-color: #fff3cd !important; /* light yellow */
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>Inventory List</h2>
        <form method="get" class="row mb-3">
    <div class="col-md-6">
        <input 
            type="text" 
            name="search" 
            class="form-control" 
            placeholder="Search by item name, code, or category..."
            value="<?= htmlspecialchars($search) ?>"
        >
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Search</button>
    </div>
    <div class="col-md-2">
        <a href="invent_list_page.php" class="btn btn-secondary w-100">Reset</a>
    </div>
    <div class="col-md-2">
        <a href="add_inventory.php" class="btn btn-secondary">Add Item</a>
    </div>
</form>

        <table class="table table-bordered table-striped mt-4">
            <!-- Add "Action" column to the table header -->
<thead class="table-dark">
    <tr>
        <th>Item Name</th>
        <th>Item Code</th>
        <th>Category</th>
        <th>Unit Price (GHS)</th>
        <th>In Stock</th>
        <th>Min Level</th>
        <th>Actions</th> <!-- New -->
    </tr>
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
                    <a href="edit_inventory.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_inventory.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7" class="text-center">No inventory items found.</td></tr>
    <?php endif; ?>
</tbody>

        </table>
    </div>
</body>
</html>
