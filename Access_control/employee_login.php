<?php
session_start();

// No caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Only redirect if employee is already logged in
if (isset($_SESSION['employee'])) {
    header("Location: ../Dashboard/page.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $branch = $_POST['branch'] ?? '';
    $allowed = ['Agbajeena','Olebu'];

    if (!in_array($branch, $allowed, true)) {
        $error = "Please select a valid branch/location.";
    } else {
        $_SESSION['branch'] = $branch;

        require '../dbconfig.php';

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $conn->prepare("SELECT * FROM `employee` WHERE username = ?");
        $stmt->execute([$username]);
        $employee = $stmt->fetch();

        if ($employee && password_verify($password, $employee['password'])) {
            $_SESSION['employee'] = $employee['username'];
            header("Location: ../Sales/add_sales.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #262161 0%, #24B8EE 100%);
            min-height: 100vh;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 440px;
            width: 100%;
        }
        .logo {
            height: 100px;
            width: 100px;
            object-fit: contain;
        }
        .btn-login {
            background-color: #24B8EE;
            border: none;
            color: white;
            padding: 12px;
            font-size: 16px;
            border-radius: 10px;
        }
        .btn-login:hover {
            background-color: #1a9fd4;
            color: white;
        }
        .back-link {
            color: #262161;
            text-decoration: none;
            font-size: 14px;
        }
        .back-link:hover { color: #24B8EE; }
        @media (max-width: 480px) {
            .login-card { padding: 30px 20px; margin: 20px; }
            .logo { height: 75px; width: 75px; }
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">
 
<div class="login-card">
    <img src="../Dashboard/Maxtilliz_logo.jpg" alt="logo" class="logo d-block mx-auto mb-3">
    <h4 class="text-center fw-bold mb-1" style="color:#262161;">Employee Login</h4>
    <p class="text-center text-muted mb-4" style="font-size:14px;">Select your branch and sign in</p>
 
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
 
    <form method="post">
        <div class="mb-3">
            <label class="form-label fw-semibold">Branch / Location</label>
            <select name="branch" class="form-select" required>
                <option value="">-- Select branch --</option>
                <option value="Agbajeena">Agbajeena Branch</option>
                <option value="Olebu">Olebu Branch</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-login">Login</button>
        </div>
    </form>
 
    <div class="text-center mt-3">
        <a href="../index.php" class="back-link">← Back to Home</a>
    </div>
</div>
 
</body>
</html>