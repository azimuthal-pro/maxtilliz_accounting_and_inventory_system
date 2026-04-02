<?php
// import_olebu.php - Clears Olebu DB and imports fresh data. DELETE after running!

$cfg = [
    'host' => getenv('DB_Olebu_HOST'),
    'dbname' => getenv('DB_Olebu_NAME'),
    'user' => getenv('DB_Olebu_USER'),
    'pass' => getenv('DB_Olebu_PASS'),
];

echo "<h2>Olebu Database Import</h2>";

try {
    $pdo = new PDO(
        "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset=utf8mb4",
        $cfg['user'], $cfg['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "<p>✅ Connected to Olebu database.</p>";

    // Drop all existing tables cleanly
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS `$table`");
        echo "<p>🗑️ Dropped table: $table</p>";
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "<p>✅ All old tables cleared.</p>";

    // Read and run the new SQL file
    $sql = file_get_contents(__DIR__ . '/Olebu.sql');
    if (!$sql) {
        die("<p>❌ Could not read Olebu.sql</p>");
    }

    $statements = array_filter(array_map('trim', explode(';', $sql)));
    $count = 0;
    $errors = 0;

    foreach ($statements as $statement) {
        if (empty($statement) || strpos(ltrim($statement), '--') === 0) continue;
        try {
            $pdo->exec($statement);
            $count++;
        } catch (PDOException $e) {
            echo "<p style='color:orange'>⚠️ Skipped: " . htmlspecialchars(substr($statement, 0, 80)) . "... — " . htmlspecialchars($e->getMessage()) . "</p>";
            $errors++;
        }
    }

    echo "<p>✅ $count statements executed.</p>";
    if ($errors) echo "<p>⚠️ $errors statements skipped.</p>";

    // Show summary
    $tables = ['admin', 'employee', 'inventory', 'sales', 'purchases', 'expenses'];
    echo "<h3>Table Summary</h3><table border='1' cellpadding='5'><tr><th>Table</th><th>Rows</th></tr>";
    foreach ($tables as $table) {
        try {
            $c = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            echo "<tr><td>$table</td><td>$c</td></tr>";
        } catch (Exception $e) {
            echo "<tr><td>$table</td><td>N/A</td></tr>";
        }
    }
    echo "</table>";

} catch (PDOException $e) {
    echo "<p>❌ Connection Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr><p><strong>Done! Delete import_olebu.php and Olebu.sql now.</strong></p>";
?>
