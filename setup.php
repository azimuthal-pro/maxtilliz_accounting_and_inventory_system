<?php
// setup.php - Run this ONCE to create all tables in both databases
// Delete this file after running it!

$branches = [
    'Agbajeena' => [
        'host' => getenv('DB_Agbajeena_HOST'),
        'dbname' => getenv('DB_Agbjeena_NAME'), // note: typo in original config
        'user' => getenv('DB_Agbajeena_USER'),
        'pass' => getenv('DB_Agbajeena_PASS'),
    ],
    'Olebu' => [
        'host' => getenv('DB_Olebu_HOST'),
        'dbname' => getenv('DB_Olebu_NAME'),
        'user' => getenv('DB_Olebu_USER'),
        'pass' => getenv('DB_Olebu_PASS'),
    ],
];

$password = '@zimuth@l26';
$hash = password_hash($password, PASSWORD_BCRYPT);

$sql = "
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `description` varchar(150) NOT NULL,
  `amount` varchar(150) NOT NULL,
  `category` enum('Cash','Momo') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(100) NOT NULL,
  `item_code` varchar(50) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity_in_stock` int(11) DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_code` (`item_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(255) NOT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `purchase_date` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_date` datetime NOT NULL DEFAULT current_timestamp(),
  `item` varchar(255) NOT NULL,
  `qty` int(255) NOT NULL,
  `price` float NOT NULL,
  `total` float NOT NULL,
  `payment_method` enum('Cash','Mobile Money') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

echo "<h2>Database Setup</h2>";

foreach ($branches as $name => $cfg) {
    echo "<h3>Branch: $name</h3>";
    try {
        $pdo = new PDO(
            "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset=utf8",
            $cfg['user'],
            $cfg['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // Run each CREATE TABLE statement
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            if ($statement) $pdo->exec($statement);
        }
        echo "<p>✅ Tables created successfully.</p>";

        // Insert admin if not exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `admin` WHERE username = ?");
        $stmt->execute(['azimuthal_pro']);
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO `admin` (username, password) VALUES (?, ?)")
                ->execute(['azimuthal_pro', $hash]);
            echo "<p>✅ Admin account created.</p>";
        } else {
            echo "<p>ℹ️ Admin account already exists.</p>";
        }

        // Insert into users table too
        $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE username = ?");
        $stmt2->execute(['azimuthal_pro']);
        if ($stmt2->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO `users` (username, password, role) VALUES (?, ?, 'admin')")
                ->execute(['azimuthal_pro', $hash]);
        }

    } catch (PDOException $e) {
        echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "<hr><p><strong>Done! Please delete or rename this file now for security.</strong></p>";
?>
