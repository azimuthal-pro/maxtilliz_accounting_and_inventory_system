<?php
require '../dbconfig.php';

// Only logged-in admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit();
}

$message = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $branch   = $_POST['branch'] ?? '';
    $allowed  = ['Agbajeena', 'Olebu'];

    if (!in_array($branch, $allowed, true)) {
        $message = "Please select a valid branch/location.";
    } else {
        $username        = trim($_POST['username'] ?? '');
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role            = $_POST['role'] ?? '';

        if (empty($username)) {
            $message = "Username cannot be empty.";
        } elseif ($password !== $confirmPassword) {
            $message = "Passwords do not match.";
        } elseif (strlen($password) < 6) {
            $message = "Password must be at least 6 characters.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            if ($role === 'admin') {
                $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
            } elseif ($role === 'employee') {
                $stmt = $conn->prepare("INSERT INTO employee (username, password) VALUES (?, ?)");
            } else {
                $message = "Invalid role selected.";
            }

            if (isset($stmt)) {
                try {
                    $stmt->execute([$username, $hashedPassword]);
                    $success = "User '$username' registered successfully as $role for $branch branch!";
                } catch (PDOException $e) {
                    $message = "Registration failed — username may already exist.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #262161 0%, #24B8EE 100%);
            min-height: 100vh;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
        }
        .btn-register {
            background-color: #262161;
            border: none;
            color: white;
            padding: 12px;
            font-size: 16px;
            border-radius: 10px;
        }
        .btn-register:hover { background-color: #1a1645; color: white; }
        .back-link { color: #262161; text-decoration: none; font-size: 14px; }
        .back-link:hover { color: #24B8EE; }
        @media (max-width: 480px) {
            .register-card { padding: 30px 20px; margin: 20px; }
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">

<div class="register-card">
    <img src="../Dashboard/Maxtilliz_logo.jpg" alt="logo" height="80" width="80" class="d-block mx-auto mb-3">
    <h4 class="text-center fw-bold mb-1" style="color:#262161;">Register User</h4>
    <p class="text-center text-muted mb-4" style="font-size:14px;">Create a new admin or employee account</p>

    <?php if ($message): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
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
            <input type="text" name="username" class="form-control" placeholder="Enter username" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Role</label>
            <select name="role" class="form-select" required>
                <option value="">-- Select role --</option>
                <option value="admin">Admin</option>
                <option value="employee">Employee</option>
            </select>
        </div>
        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-register">Register User</button>
        </div>
    </form>

    <div class="text-center mt-3">
        <a href="../Dashboard/page.php" class="back-link">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>