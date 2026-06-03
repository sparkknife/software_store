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
  <title>پنل مدیریت</title>

  <!-- Bootstrap Icons -->
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <!-- OverlayScrollbars -->
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css">

  <!-- AdminLTE RTL -->
  <link rel="stylesheet" href="/software_store/assets/adminlte/css/adminlte.rtl.min.css">

  <!-- فونت فارسی -->
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700&display=swap"
    rel="stylesheet">

  <style>
    * { font-family: 'Vazirmatn', sans-serif !important; }
  </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

  <!-- هدر -->
  <nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
            <i class="bi bi-list fs-5"></i>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a href="/software_store/index.php" class="nav-link">
            <i class="bi bi-shop me-1"></i> مشاهده سایت
          </a>
        </li>
        <li class="nav-item">
          <span class="nav-link">
            <i class="bi bi-person-circle me-1"></i>
            <?= htmlspecialchars($_SESSION['user_name']) ?>
          </span>
        </li>
        <li class="nav-item">
          <a href="/software_store/logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right me-1"></i> خروج
          </a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- سایدبار -->
  <aside class="app-sidebar bg-dark sidebar-dark shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
      <a href="/software_store/admin/index.php" class="brand-link">
        <i class="bi bi-compact-disc brand-image opacity-75 shadow"></i>
        <span class="brand-text fw-light me-2">نرم‌افزار استور</span>
      </a>
    </div>

    <div class="sidebar-wrapper">
      <nav class="mt-2">
        <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

          <li class="nav-item">
            <a href="/software_store/admin/index.php" class="nav-link">
              <i class="nav-icon bi bi-speedometer2"></i>
              <p>داشبورد</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="/software_store/admin/products.php" class="nav-link">
              <i class="nav-icon bi bi-box-seam"></i>
              <p>محصولات</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="/software_store/admin/orders.php" class="nav-link">
              <i class="nav-icon bi bi-cart-check"></i>
              <p>سفارشات</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="/software_store/admin/users.php" class="nav-link">
              <i class="nav-icon bi bi-people"></i>
              <p>کاربران</p>
            </a>
          </li>

        </ul>
      </nav>
    </div>
  </aside>

  <!-- محتوا -->
  <main class="app-main">
    <div class="app-content-header py-3 px-4">
    </div>
    <div class="app-content px-4 pb-4">