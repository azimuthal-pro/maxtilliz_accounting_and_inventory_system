<?php
require '../dbconfig.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier = trim($_POST['supplier']);
    $date     = $_POST['purchase_date'];
    $total    = (float) $_POST['total_cost'];

    try {
        $insert = $conn->prepare("
            INSERT INTO purchases (supplier, purchase_date, total_cost, item, quantity)
            VALUES (?, ?, ?, 'General Purchase', 0)
        ");
        $insert->execute([$supplier, $date, $total]);
        $success = 'Purchase recorded successfully!';
    } catch (Exception $e) {
        $error = 'Failed: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Purchase</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #262161 0%, #24B8EE 100%);
            min-height: 100vh;
        }
        .purchase-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
        }
        .btn-save {
            background-color: #262161;
            border: none;
            color: white;
            padding: 12px;
            font-size: 16px;
            border-radius: 10px;
        }
        .btn-save:hover { background-color: #1a1645; color: white; }
        .back-link { color: #262161; text-decoration: none; font-size: 14px; }
        .back-link:hover { color: #24B8EE; }
        @media (max-width: 480px) {
            .purchase-card { padding: 30px 20px; margin: 20px; }
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">

<div class="purchase-card">
    <img src="../Dashboard/Maxtilliz_logo.jpg" alt="logo" height="80" width="80" class="d-block mx-auto mb-3">
    <h4 class="text-center fw-bold mb-1" style="color:#262161;">Record Purchase</h4>
    <p class="text-center text-muted mb-4" style="font-size:14px;">Fill in the purchase details below</p>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label fw-semibold">Supplier Name</label>
            <input type="text" name="supplier" class="form-control" placeholder="Enter supplier name" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Purchase Date</label>
            <input type="date" name="purchase_date" class="form-control"
                value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Purchase Total (GHS)</label>
            <input type="number" step="0.01" name="total_cost" class="form-control"
                placeholder="0.00" required>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-save">Save Purchase</button>
        </div>
    </form>

    <div class="text-center mt-3">
        <a href="../Dashboard/page.php" class="back-link">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>