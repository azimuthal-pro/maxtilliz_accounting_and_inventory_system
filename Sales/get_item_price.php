<?php
require '../dbconfig.php';

$raw = $_GET['item'] ?? '';

// Parse prefix: drug:ItemName or cosmetic:ItemName
if (strpos($raw, 'cosmetic:') === 0) {
    $item = substr($raw, strlen('cosmetic:'));
    $stmt = $conn->prepare("SELECT unit_price FROM cosmetics WHERE item = ?");
} else {
    $item = strpos($raw, 'drug:') === 0 ? substr($raw, strlen('drug:')) : $raw;
    $stmt = $conn->prepare("SELECT unit_price FROM inventory WHERE item = ?");
}

$stmt->execute([$item]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode(['price' => $row['unit_price'] ?? '']);
?>
