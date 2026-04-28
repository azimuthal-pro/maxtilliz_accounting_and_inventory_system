<?php
require '../dbconfig.php';

$saleStmnt = $conn->query("SELECT SUM(price*qty) AS total_sales FROM sales");
$totalsales = $saleStmnt->fetchColumn() ?? 0;

$purchaseStmt = $conn->query("SELECT SUM(total_cost) AS total_purchases FROM purchases");
$totalPurchases = $purchaseStmt->fetchColumn() ?? 0;

$lowStockStmt = $conn->query("SELECT COUNT(*) FROM inventory WHERE quantity_in_stock <= min_stock_level");
$lowStockCount = $lowStockStmt->fetchColumn() ?? 0;

$lowCosmeticsCount = 0;
if (($_SESSION['branch'] ?? '') === 'Olebu') {
    try {
        $lowCosStmt = $conn->query("SELECT COUNT(*) FROM cosmetics WHERE quantity_in_stock <= min_stock_level");
        $lowCosmeticsCount = $lowCosStmt->fetchColumn() ?? 0;
    } catch (Exception $e) {}
}

$isOlebu = ($_SESSION['branch'] ?? '') === 'Olebu';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
  <script src="../assets/js/chart.min.js"></script>
  <style>
    body { background-color: #f8f9fa; }
 
    /* Sidebar */
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
    .sidebar.hidden { transform: translateX(-250px); }
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
 
    /* Main content offset */
    .main-content {
      margin-left: 250px;
      padding: 20px;
      transition: margin-left 0.3s ease;
    }
    .main-content.full { margin-left: 0; }
 
    /* Top navbar for mobile */
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
 
    /* Overlay */
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
    <img src="Maxtilliz_logo.jpg" height="30" width="30" class="me-2">
    Maxtilliz Chem
  </span>
  <button id="sidebarToggle" style="background:none;border:none;color:white;font-size:24px;">
    <i class="bi bi-list"></i>
  </button>
</div>
 
<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
 
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <h4 class="text-center py-3 border-bottom d-none d-md-block">
    <img src="Maxtilliz_logo.jpg" height="50" width="50">
    Maxtilliz Chem
  </h4>
 
  <a href="#"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
 
  <button data-bs-toggle="collapse" data-bs-target="#salesMenu">
    <i class="bi bi-cart-check me-2"></i>Sales
  </button>
  <div class="collapse" id="salesMenu">
    <a href="../Sales/add_sales.php"><i class="bi bi-plus-circle me-2"></i>Add Sale</a>
    <a href="../Sales/view_sales.php"><i class="bi bi-list-ul me-2"></i>View Sales</a>
  </div>
 
  <button data-bs-toggle="collapse" data-bs-target="#drugsMenu">
    <i class="bi bi-box-seam me-2"></i>Inventory
  </button>
   <div class="collapse" id="drugsMenu">
     <a href="../Inventory/add_inventory.php"><i class="bi bi-plus-circle me-2"></i>Add Inventory</a>
     <a href="../Inventory/invent_list_page.php"><i class="bi bi-list-ul me-2"></i>View Inventory</a>
     <a href="../Inventory/low_stock_alert.php"><i class="bi bi-exclamation-triangle me-2"></i>Out of Stocks</a>
   </div>
 
  <?php if ($isOlebu): ?>
  <button data-bs-toggle="collapse" data-bs-target="#cosmeticsMenu">
    <i class="bi bi-stars me-2"></i>Cosmetics
  </button>
  <div class="collapse" id="cosmeticsMenu">
    <a href="../Cosmetics/add_cosmetic.php"><i class="bi bi-plus-circle me-2"></i>Add Cosmetic</a>
    <a href="../Cosmetics/cosmetics_list.php"><i class="bi bi-list-ul me-2"></i>View Cosmetics</a>
  </div>
  <?php endif; ?>
 
  <button data-bs-toggle="collapse" data-bs-target="#purchaseMenu">
    <i class="bi bi-truck me-2"></i>Purchase
  </button>
  <div class="collapse" id="purchaseMenu">
    <a href="../Purchase/purchase_form.php"><i class="bi bi-plus-circle me-2"></i>Add Purchase</a>
    <a href="../Purchase/purchase_history.php"><i class="bi bi-clock-history me-2"></i>Purchase History</a>
  </div>
 
  <button data-bs-toggle="collapse" data-bs-target="#reportsMenu">
    <i class="bi bi-bar-chart-line me-2"></i>Reports
  </button>
   <div class="collapse" id="reportsMenu">
     <a href="../Sales/sales_report_export.php"><i class="bi bi-graph-up-arrow me-2"></i>Sales Reports</a>
     <a href="../Purchase/purchase_history_report.php"><i class="bi bi-graph-down me-2"></i>Purchase Reports</a>
     <a href="../Inventory/low_stock_report_export.php"><i class="bi bi-file-earmark-excel me-2"></i>Low Stock Report</a>
   </div>
 
  <a href="../Access_control/register_user.php"><i class="bi bi-person-plus me-2"></i>Register User</a>
  <a href="../Access_control/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
</div>
 
<!-- Main Content -->
<div class="main-content">
  <h2 class="mb-1">Welcome, Admin</h2>
  <p class="text-muted">Dashboard Overview</p>
 
  <!-- Summary Cards -->
  <div class="row g-3 my-2">
    <div class="col-6 col-md-3">
      <div class="card text-white h-100" style="background-color:#24B8EE;">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-cash-coin me-1"></i>Total Sales</h6>
          <p class="card-text fw-bold"><?= number_format($totalsales, 2) ?></p>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-white h-100" style="background-color:#262161;">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-bag-check me-1"></i>Total Purchases</h6>
          <p class="card-text fw-bold"><?= number_format($totalPurchases, 2) ?></p>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-white bg-danger h-100">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock (Drugs)</h6>
          <p class="card-text fw-bold"><?= $lowStockCount ?></p>
        </div>
      </div>
    </div>
    <?php if ($isOlebu): ?>
    <div class="col-6 col-md-3">
      <div class="card text-white h-100" style="background-color:#e83e8c;">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-stars me-1"></i>Low Stock (Cosmetics)</h6>
          <p class="card-text fw-bold"><?= $lowCosmeticsCount ?></p>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
 
  <!-- Charts -->
  <div class="row g-3 my-2">
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-header" style="background-color:#24B8EE;" >Sales Overview</div>
        <div class="card-body">
          <canvas id="salesChart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-header"  style="background-color:#262161;">Purchase Trends</div>
        <div class="card-body">
          <canvas id="purchaseChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>
 
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script>
  // Sidebar toggle for mobile
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const toggleBtn = document.getElementById('sidebarToggle');
 
  toggleBtn?.addEventListener('click', () => {
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
  });
  overlay.addEventListener('click', () => {
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
  });
 
  // Charts
  fetch('get_chart_data.php')
    .then(res => res.json())
    .then(data => {
      new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
          labels: data.sales.labels,
          datasets: [{ label: 'Monthly Sales', data: data.sales.data, backgroundColor: '#24B8EE', borderColor: '#24B8EE' }]
        },
        options: { responsive: true }
      });
      new Chart(document.getElementById('purchaseChart'), {
        type: 'bar',
        data: {
          labels: data.purchases.labels,
          datasets: [{ label: 'Monthly Purchases', data: data.purchases.data, backgroundColor: '#262161' }]
        },
        options: { responsive: true }
      });
    });
</script>
</body>
         