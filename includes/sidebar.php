<?php
// includes/sidebar.php
// Included after header.php — outputs sidebar + opens .main-content div
?>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <h4 class="text-center py-3 border-bottom d-none d-md-block">
    <img src="../Dashboard/Maxtilliz_logo.jpg" height="50" width="50">
    Maxtilliz Chem
  </h4>

  <a href="../Dashboard/page.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>

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
    <a href="../Profit_Loss/profit_loss_report.php"><i class="bi bi-graph-up me-2"></i>Profit/Loss</a>
  </div>

  <a href="../Access_control/register_user.php"><i class="bi bi-person-plus me-2"></i>Register User</a>
  <a href="../Access_control/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
</div>

<!-- Main Content Wrapper -->
<div class="main-content">
