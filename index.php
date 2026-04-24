<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Maxtilliz OTC Accounting System</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #262161 0%, #24B8EE 100%);
            min-height: 100vh;
        }
        .welcome-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 480px;
            width: 100%;
        }
        .logo {
            height: 120px;
            width: 120px;
            object-fit: contain;
        }
        .btn-admin {
            background-color: #262161;
            border: none;
            color: white;
            padding: 14px;
            font-size: 16px;
            border-radius: 10px;
        }
        .btn-admin:hover {
            background-color: #1a1645;
            color: white;
        }
        .btn-employee {
            background-color: #24B8EE;
            border: none;
            color: white;
            padding: 14px;
            font-size: 16px;
            border-radius: 10px;
        }
        .btn-employee:hover {
            background-color: #1a9fd4;
            color: white;
        }
        @media (max-width: 480px) {
            .welcome-card { padding: 30px 20px; margin: 20px; }
            .logo { height: 90px; width: 90px; }
            h1 { font-size: 1.4rem; }
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">

    <div class="welcome-card text-center">
        <img src="Dashboard/Maxtilliz_logo.jpg" alt="Maxtilliz Logo" class="logo mb-3">

        <h1 class="fw-bold mb-1" style="color:#262161;">MAXTILLIZ</h1>
        <p class="text-muted mb-1">OTC Accounting System</p>
        <hr class="my-3">
        <p class="mb-4 text-muted">Please choose your login type</p>

        <div class="d-grid gap-3">
            <a href="Access_control/admin_login.php" class="btn btn-admin">
                <i class="bi bi-shield-lock me-2"></i>Login as Admin
            </a>
            <a href="Access_control/employee_login.php" class="btn btn-employee">
                <i class="bi bi-person me-2"></i>Login as Employee
            </a>
        </div>
    </div>

    <link rel="stylesheet" href="assets/css/bootstrap-icons.css">
</body>
</html>