<?php
require '../dbconfig.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $role = $_POST['role'];

    if ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
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
                $message = "Registration successful as $role.";
                header("Location: ../index.html");
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
            }
        }
    }
}
?>




 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register User</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Register New User</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm bg-white">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Select Role</label>
            <select name="role" class="form-select" required>
                <option value="employee">Employee</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
        <a href="admin_dashboard.php" class="btn btn-secondary ms-2">Back to Dashboard</a>
    </form>
</div>
</body>
</html>

