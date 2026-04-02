<?php
// import_olebu.php - DELETE after running!

$host   = getenv('DB_Olebu_HOST');
$dbname = getenv('DB_Olebu_NAME');
$user   = getenv('DB_Olebu_USER');
$pass   = getenv('DB_Olebu_PASS');

echo "<h2>Olebu Database Import</h2>";

// Connect using mysqli (better for SQL dumps)
$mysqli = new mysqli($host, $user, $pass, $dbname);
if ($mysqli->connect_error) {
    die("<p>❌ Connection failed: " . htmlspecialchars($mysqli->connect_error) . "</p>");
}
echo "<p>✅ Connected to Olebu database.</p>";

$mysqli->set_charset('utf8mb4');

// Drop all existing tables
$mysqli->query("SET FOREIGN_KEY_CHECKS = 0");
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $mysqli->query("DROP TABLE IF EXISTS `{$row[0]}`");
    echo "<p>🗑️ Dropped: {$row[0]}</p>";
}
$mysqli->query("SET FOREIGN_KEY_CHECKS = 1");
echo "<p>✅ All old tables cleared.</p>";

// Read SQL file
$sqlFile = __DIR__ . '/Olebu.sql';
if (!file_exists($sqlFile)) {
    die("<p>❌ Olebu.sql not found in project root.</p>");
}

$sql = file_get_contents($sqlFile);

// Remove comments and problematic lines
$sql = preg_replace('/--[^\n]*\n/', "\n", $sql);
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
$sql = str_replace("CREATE DATABASE", "-- CREATE DATABASE", $sql);
$sql = str_replace("USE `maxtilliz`", "-- USE", $sql);

// Execute using multi_query
$count = 0;
$errors = 0;

// Split on semicolons but handle multi-line statements
$statements = [];
$current = '';
$lines = explode("\n", $sql);

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '--') === 0) continue;
    $current .= ' ' . $line;
    if (substr(rtrim($line), -1) === ';') {
        $stmt = trim(rtrim($current, ';'));
        if (!empty($stmt)) {
            $statements[] = $stmt;
        }
        $current = '';
    }
}

foreach ($statements as $stmt) {
    if ($mysqli->query($stmt)) {
        $count++;
    } else {
        $errors++;
        echo "<p style='color:orange'>⚠️ " . htmlspecialchars(substr($stmt, 0, 100)) . " — " . htmlspecialchars($mysqli->error) . "</p>";
    }
}

echo "<p>✅ $count statements executed.</p>";
if ($errors) echo "<p>⚠️ $errors statements had errors.</p>";

// Summary
$tables = ['admin', 'employee', 'inventory', 'sales', 'purchases', 'expenses'];
echo "<h3>Table Summary</h3><table border='1' cellpadding='5'><tr><th>Table</th><th>Rows</th></tr>";
foreach ($tables as $table) {
    $r = $mysqli->query("SELECT COUNT(*) FROM `$table`");
    $count_val = $r ? $r->fetch_row()[0] : 'N/A';
    echo "<tr><td>$table</td><td>$count_val</td></tr>";
}
echo "</table>";

$mysqli->close();
echo "<hr><p><strong>Done! Delete import_olebu.php and Olebu.sql now.</strong></p>";
?>
