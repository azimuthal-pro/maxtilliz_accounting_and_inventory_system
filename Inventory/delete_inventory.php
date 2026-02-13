<?php
require '../dbconfig.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: invent_list_page.php?message=Invalid+ID");
    exit;
}

$stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
if ($stmt->execute([$id])) {
    header("Location: invent_list_page.php?message=Item+deleted+successfully");
    exit;
} else {
    header("Location: invent_list_page.php?message=Failed+to+delete+item");
    exit;
}
?>
