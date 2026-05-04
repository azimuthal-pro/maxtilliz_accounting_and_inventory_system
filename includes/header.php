<?php
// includes/header.php
// Usage: require_once '../includes/header.php';
// Set $pageTitle before requiring this file e.g: $pageTitle = 'Inventory List';
$pageTitle = $pageTitle ?? 'Maxtilliz';
$isOlebu = ($_SESSION['branch'] ?? '') === 'Olebu';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?> - Maxtilliz</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
  <style>
    body { background-color: #f8f9fa; }

    .sidebar {
      width: 250px;
      background-color: #262161;
      color: white;
      min-height: 100vh;
      position: fixed;
      top: 0; left: 0;
      z-index: 1000;
      overflow-y: auto;
      transition: transform 0.3s ease;
    }
    .sidebar a, .sidebar button {
      color: white;
      display: block;
      padding: 13px 15px;
      text-decoration: none;
      background: none;
      border: none;
      width: 100%;
      text-align: left;
      font-size: 15px;
    }
    .sidebar a:hover, .sidebar button:hover { background-color: #24B8EE; }
    .collapse a { padding-left: 40px; }

    .main-content {
      margin-left: 250px;
      padding: 20px;
      transition: margin-left 0.3s ease;
    }

    .mobile-navbar {
      display: none;
      background-color: #262161;
      color: white;
      padding: 12px 15px;
      position: sticky;
      top: 0;
      z-index: 999;
    }
    .mobile-navbar .brand { font-size: 18px; font-weight: bold; color: white; }

    .sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 999;
    }
    .sidebar-overlay.show { display: block; }

    @media (max-width: 768px) {
      .sidebar { transform: translateX(-250px); }
      .sidebar.show { transform: translateX(0); }
      .main-content { margin-left: 0; padding: 15px; }
      .mobile-navbar { display: flex; justify-content: space-between; align-items: center; }
    }
  </style>
</head>
<body>

<!-- Mobile Top Navbar -->
<div class="mobile-navbar">
  <span class="brand">
    <img src="../Dashboard/Maxtilliz_logo.jpg" height="30" width="30" class="me-2">
    Maxtilliz Chem
  </span>
  <button id="sidebarToggle" style="background:none;border:none;color:white;font-size:24px;">
    <i class="bi bi-list"></i>
  </button>
</div>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
