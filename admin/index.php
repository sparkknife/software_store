<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'header.php';

$total_products = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$total_orders   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$total_users    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$total_income   = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total) FROM orders"))[0];
?>

<div class="d-flex justify-content-between mb-4">
  <h4>📊 داشبورد ادمین</h4>
</div>

<div class="row g-4 mb-5">
  <div class="col-md-3">
    <div class="card bg-primary text-white shadow">
      <div class="card-body text-center">
        <h2><?= $total_products ?></h2>
        <p class="mb-0">محصولات</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-success text-white shadow">
      <div class="card-body text-center">
        <h2><?= $total_orders ?></h2>
        <p class="mb-0">سفارشات</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-info text-white shadow">
      <div class="card-body text-center">
        <h2><?= $total_users ?></h2>
        <p class="mb-0">کاربران</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-warning text-white shadow">
      <div class="card-body text-center">
        <h2><?= number_format($total_income) ?></h2>
        <p class="mb-0">درآمد (تومان)</p>
      </div>
    </div>
  </div>
</div>

<div class="d-flex gap-3">
  <a href="products.php" class="btn btn-primary">مدیریت محصولات</a>
  <a href="orders.php" class="btn btn-success">مدیریت سفارشات</a>
  <a href="users.php" class="btn btn-info text-white">مدیریت کاربران</a>
</div>

<?php require_once 'footer.php'; ?>