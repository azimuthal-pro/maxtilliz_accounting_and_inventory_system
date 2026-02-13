<?php
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $branch = $_POST['branch'] ?? '';
    $allowed = ['Agbajeena','Olebu'];

    if (!in_array($branch, $allowed, true)) {
        $message = "Please select a valid branch/location.";
    } else {
        $_SESSION['branch'] = $branch;
        require '../dbconfig.php';

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? '';

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
                    header("Location: ../index.php");
                    exit();
                } catch (PDOException $e) {
                    $message = "Registration error (username may already exist).";
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
    <title>Register User</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 480px;">
        <h4 class="text-center mb-4">Register User</h4>

        <?php if ($message): ?>
            <div class="alert alert-warning"><?= $message ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Branch / Location</label>
                <select name="branch" class="form-select" required>
                    <option value="">-- Select branch --</option>
                    <option value="agbajeena">Agbajeena Branch</option>
                    <option value="olebu">Olebu Branch</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="">-- Select role --</option>
                    <option value="admin">Admin</option>
                    <option value="employee">Employee</option>
                </select>
            </div>

            <button type="submit" class="btn btn-dark w-100">Register</button>
        </form>

        <div class="text-center mt-3">
            <a href="../index.php">Back</a>
        </div>
    </div>
</div>
</body>
</html>
