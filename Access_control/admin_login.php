<?php
session_start();

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

        $stmt = $conn->prepare("SELECT * FROM `admin` WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['username'];
            header("Location: ../Dashboard/page.php");
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
    <title>Admin Login</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 420px;">
        <img src="../Dashboard/Maxtilliz_logo.jpg" alt="logo" height="100" width="100" class="mb-3 mx-auto d-block">
        <h4 class="text-center mb-4">Admin Login</h4>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
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

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="text-center mt-3">
            <a href="../index.php">Back</a>
        </div>
    </div>
</div>
</body>
</html>
