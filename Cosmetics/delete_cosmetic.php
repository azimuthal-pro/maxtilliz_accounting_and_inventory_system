<?php
require '../dbconfig.php';

if (($_SESSION['branch'] ?? '') !== 'Olebu') {
    header('Location: ../Dashboard/page.php');
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) die("Invalid item ID.");

$stmt = $conn->prepare("DELETE FROM cosmetics WHERE id = ?");
$stmt->execute([$id]);

header('Location: cosmetics_list.php');
exit();
