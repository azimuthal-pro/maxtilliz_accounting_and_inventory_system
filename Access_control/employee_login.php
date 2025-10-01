<?php
session_start();
require '../dbconfig.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM employee WHERE username = ?");
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Login</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 400px;">
        <img src="../Dashboard/Maxtilliz_logo.jpg" alt="logo here" height="100px" width="100px" class="mb-3 mx-auto d-block">
        <h4 class="text-center mb-4">Employee Login</h4>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="employee_username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="employee_password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>
