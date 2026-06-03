<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>فروشگاه نرم‌افزار</title>
  <link rel="stylesheet" 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700&display=swap" 
    rel="stylesheet">
  <link rel="stylesheet" href="/software_store/assets/css/custom.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/software_store/index.php">
      💿 نرم‌افزار استور
    </a>
    <div class="d-flex gap-2 align-items-center">
      <?php if(isset($_SESSION['user_id'])): ?>
        <span class="text-white opacity-75 small">
          سلام، <?= htmlspecialchars($_SESSION['user_name']) ?>
        </span>
        <a href="/software_store/orders.php" class="btn btn-outline-light btn-sm">
          📦 خریدهای من
        </a>
        <?php if($_SESSION['user_role'] === 'admin'): ?>
          <a href="/software_store/admin/index.php" class="btn btn-warning btn-sm">
            ⚙️ ادمین
          </a>
        <?php endif; ?>
        <a href="/software_store/logout.php" class="btn btn-danger btn-sm">خروج</a>
      <?php else: ?>
        <a href="/software_store/login.php" class="btn btn-outline-light btn-sm">ورود</a>
        <a href="/software_store/register.php" class="btn btn-success btn-sm">ثبت‌نام</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<div class="container mt-4">