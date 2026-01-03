<?php
require '../dbconfig.php';

$item = $_GET['item'] ?? '';

$stmt = $conn->prepare("SELECT unit_price FROM inventory WHERE item = ?");
$stmt->execute([$item]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'price' => $row['unit_price'] ?? ''
]);
?>