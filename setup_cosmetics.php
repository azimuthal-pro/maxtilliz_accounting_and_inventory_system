<?php
// setup_cosmetics.php - Creates cosmetics_tb table in Olebu DB only. DELETE after running!

$cfg = [
    'host' => getenv('DB_Olebu_HOST'),
    'dbname' => getenv('DB_Olebu_NAME'),
    'user' => getenv('DB_Olebu_USER'),
    'pass' => getenv('DB_Olebu_PASS'),
];

echo "<h2>Cosmetics Table Setup (Olebu Only)</h2>";
try {
    $pdo = new PDO(
        "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset=utf8",
        $cfg['user'], $cfg['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Create cosmetics_tb table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `cosmetics_tb` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `item` varchar(255) NOT NULL,
        `item_code` varchar(50) NOT NULL,
        `category` varchar(255) DEFAULT 'Cosmetics',
        `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
        `quantity_in_stock` int(11) DEFAULT 0,
        `min_stock_level` int(11) DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `item_code` (`item_code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    echo "<p>✅ Cosmetics table created.</p>";

    $count = 0;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Heavens Spray', 'COS001', 3, 40.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Camel s/s', 'COS002', 3, 19.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Camel b/s', 'COS003', 6, 32.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Dettol m/s', 'COS004', 2, 37.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Savlon 500ml', 'COS005', 2, 80.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Savlon 250ml', 'COS006', 3, 47.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Savlon 125ml', 'COS007', 3, 35.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Softcare pad', 'COS008', 12, 18.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Softcare Xtra long pad', 'COS009', 3, 20.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Propa Lady Max Pad', 'COS010', 1, 23.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Propa Pad', 'COS011', 3, 23.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Air Freshner', 'COS012', 1, 5.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Faytex', 'COS013', 3, 20.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Yazz Pad', 'COS014', 6, 18.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Yazz Xtra long Pad', 'COS015', 4, 19.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Propa Panty liner', 'COS016', 4, 20.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Yazz panty liner', 'COS017', 2, 15.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Baby Oil s/s', 'COS018', 3, 16.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Baby Oil b/s', 'COS019', 9, 21.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Powerhouse roll on', 'COS020', 10, 25.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Nivea man Dry impact roll on', 'COS021', 3, 25.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Nivea men cool kick roll on', 'COS022', 1, 32.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Nivea Dry comfort roll on', 'COS023', 2, 32.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Nivea black and white roll on', 'COS024', 1, 32.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Softcare wipes b/s', 'COS025', 2, 23.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Softcare wipes s/s', 'COS026', 3, 14.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Yazz wipes', 'COS027', 3, 23.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['ABC wipes', 'COS028', 1, 22.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Nivea Lotion', 'COS029', 6, 60.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Vaseline Lotion', 'COS030', 4, 50.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Vaseline Petroleum Jelly s/s', 'COS031', 3, 23.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Vaseline Petroleum Jelly b/s', 'COS032', 3, 45.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Dovemen Spray', 'COS033', 7, 40.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Niveamen Dry impact spray', 'COS034', 2, 60.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Nivea black and white spray', 'COS035', 1, 50.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Dove Spray', 'COS036', 11, 40.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Rightguard spray', 'COS037', 14, 42.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Colgate herbal b/s', 'COS038', 4, 28.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Colgate herbal s/s', 'COS039', 3, 22.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Pepsodent s/s', 'COS040', 4, 10.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Pepsodent b/s', 'COS041', 4, 20.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Pepsodent 123', 'COS042', 1, 20.00]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Brush VIP', 'COS043', 7, 2.50]); $count++;
        $pdo->prepare("INSERT IGNORE INTO `cosmetics_tb` (`item`, `item_code`, `quantity_in_stock`, `unit_price`, `min_stock_level`) VALUES (?, ?, ?, ?, 1)")->execute(['Brush Yazz', 'COS044', 9, 5.00]); $count++;

    echo "<p>✅ $count items inserted.</p>";

    $total = $pdo->query("SELECT COUNT(*) FROM cosmetics_tb")->fetchColumn();
    echo "<p>Total cosmetics_tb items in DB: $total</p>";

} catch (PDOException $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "<hr><p><strong>Done! Delete this file now.</strong></p>";
?>