<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/db.php';
require_once 'header.php';

// آمارهای اصلی داشبورد مدیریت
$total_products = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$total_orders   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$total_users    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$total_income   = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(total), 0) FROM orders"))[0];
?>

<!-- سربرگ داشبورد -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0"><i class="bi bi-speedometer2 me-1"></i> داشبورد ادمین</h4>
</div>

<!-- کارت‌های آماری -->
<div class="row g-4 mb-5">
  <div class="col-md-6 col-xl-3">
    <div class="card admin-stat-card stat-products">
      <div class="card-body">
        <i class="bi bi-box-seam"></i>
        <div>
          <span>محصولات</span>
          <h2><?= $total_products ?></h2>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="card admin-stat-card stat-orders">
      <div class="card-body">
        <i class="bi bi-cart-check"></i>
        <div>
          <span>سفارشات</span>
          <h2><?= $total_orders ?></h2>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="card admin-stat-card stat-users">
      <div class="card-body">
        <i class="bi bi-people"></i>
        <div>
          <span>کاربران</span>
          <h2><?= $total_users ?></h2>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="card admin-stat-card stat-income">
      <div class="card-body">
        <i class="bi bi-cash-coin"></i>
        <div>
          <span>درآمد (تومان)</span>
          <h2><?= number_format($total_income) ?></h2>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- میانبرهای مدیریتی -->
<div class="d-flex flex-column flex-md-row gap-3">
  <a href="products.php" class="btn btn-primary">
    <i class="bi bi-box-seam me-1"></i> مدیریت محصولات
  </a>
  <a href="orders.php" class="btn btn-success">
    <i class="bi bi-receipt me-1"></i> مدیریت سفارشات
  </a>
  <a href="users.php" class="btn btn-info text-white">
    <i class="bi bi-people me-1"></i> مدیریت کاربران
  </a>
</div>

<?php require_once 'footer.php'; ?>
