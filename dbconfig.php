<?php
// dbconfig.php
// Branch-aware PDO connection (Option B: separate database per location/branch)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$allowedBranches = ['Agbajeena', 'Olebu'];

// Branch can be set during login via POST, or persisted in session.
// (Optional) you may allow ?branch=A on the URL during login screens.
if (isset($_GET['branch']) && in_array($_GET['branch'], $allowedBranches, true)) {
    $_SESSION['branch'] = $_GET['branch'];
}

$branch = $_SESSION['branch'] ?? null;

$currentFile = basename($_SERVER['PHP_SELF'] ?? '');
$publicFiles = [
    'index.php',
    'admin_login.php',
    'employee_login.php',
    'register_user.php'
];

$isPublic = in_array($currentFile, $publicFiles, true);

if (!$isPublic) {
    $loggedIn = isset($_SESSION['admin']) || isset($_SESSION['employee']);
    if (!$loggedIn) {
        header('Location: /index.php');
        exit();
    }
    if (!$branch || !in_array($branch, $allowedBranches, true)) {
        // No branch selected (or invalid). Force user back to login.
        session_unset();
        session_destroy();
        header('Location: /index.php');
        exit();
    }
}


$branchConfigs = [
    'Agbajeena' => [
        'host' => getenv('DB_Agbajeena_HOST') ?: 'localhost',
        'dbname' => getenv('DB_Agbjeena_NAME') ?: 'maxtilliz_agbajeena',
        'user' => getenv('DB_Agbajeena_USER') ?: 'root',
        'pass' => getenv('DB_Agbajeena_PASS') ?: '',
    ],
    'Olebu' => [
        'host' => getenv('DB_Olebu_HOST') ?: 'localhost',
        'dbname' => getenv('DB_Olebu_NAME') ?: 'maxtilliz_olebu',
        'user' => getenv('DB_Olebu_USER') ?: 'root',
        'pass' => getenv('DB_Olebu_PASS') ?: '',
    ],
];

// For public pages (like login screens), only connect if a valid branch is already set.
// This prevents DB errors when someone is just viewing the login form.
if (!$branch || !isset($branchConfigs[$branch])) {
    // No connection is created here.
    // Login scripts should set $_SESSION['branch'] before requiring dbconfig.php.
    return;
}

$cfg = $branchConfigs[$branch];

try {
    $conn = new PDO(
        "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset=utf8",
        $cfg['user'],
        $cfg['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // In production, avoid showing raw errors. Log them instead.
    die("Database connection failed.");
}
?>
