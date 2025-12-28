<?php
require '../dbconfig.php';

if (!isset($_GET['item'])) {
    echo json_encode(['price' => 0]);
    exit;
}

$item = $_GET['item'];

$stmt = $conn->prepare("SELECT unit_price FROM inventory WHERE item = ?");
$stmt->execute([$item]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'price' => $row ? $row['unit_price'] : 0
]);
?>