<?php
// هدر مشترک پنل ادمین و شروع session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تشخیص صفحه فعلی برای active کردن آیتم‌های منو
$current_page = basename($_SERVER['PHP_SELF']);
$product_pages = ['products.php', 'add_product.php', 'edit_product.php'];
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
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">

  <style>
    /* پایه ظاهری پنل ادمین */
    * { font-family: 'Vazirmatn', sans-serif !important; }
    body { background: #f5f7fb; }
    .app-header { border-bottom: 1px solid #e9edf5; }
    .app-content { max-width: 1320px; margin: 0 auto; width: 100%; }
    .card { border: 0 !important; border-radius: 8px !important; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.07); }
    .btn { border-radius: 8px; font-weight: 600; }
    .form-control { border-radius: 8px; padding: 10px 12px; }
    .form-control:focus { box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12); }

    /* منوی کناری */
    .sidebar-brand .brand-link { text-decoration: none; }
    .sidebar-menu .nav-link { border-radius: 8px; margin: 2px 10px; }
    .sidebar-menu .nav-link.active { background: rgba(255,255,255,0.14); color: #fff; }

    /* select سفارشی برای فرم‌های محصول */
    .custom-select-wrapper { position: relative; }
    .custom-select-wrapper select { display: none; }
    .custom-select {
      background: #fff;
      border: 1px solid #d6dbe6;
      border-radius: 8px;
      padding: 10px 14px;
      cursor: pointer;
      color: #212529;
      position: relative;
    }
    .custom-select::after {
      content: '\F282';
      font-family: 'bootstrap-icons';
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
      font-size: 0.8rem;
    }
    .custom-select-dropdown {
      position: absolute;
      top: calc(100% + 6px);
      left: 0;
      right: 0;
      background: #fff;
      border: 1px solid #d6dbe6;
      border-radius: 8px;
      z-index: 9999;
      max-height: 220px;
      overflow-y: auto;
      display: none;
      box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
    }
    .custom-select-dropdown div {
      padding: 10px 14px;
      cursor: pointer;
      color: #212529;
    }
    .custom-select-dropdown div:hover,
    .custom-select-dropdown div.active {
      background: #eef4ff;
      color: #0d6efd;
    }
    .custom-select-open .custom-select-dropdown { display: block; }
    .custom-select-open .custom-select {
      border-color: #0d6efd;
      box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
    }

    /* داشبورد و جدول‌های ادمین */
    .admin-stat-card { color: #fff; overflow: hidden; }
    .admin-stat-card .card-body {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      min-height: 120px;
    }
    .admin-stat-card i { font-size: 2.3rem; opacity: 0.86; }
    .admin-stat-card span { opacity: 0.86; }
    .admin-stat-card h2 { margin: 4px 0 0; font-weight: 800; }
    .stat-products { background: linear-gradient(135deg, #2563eb, #0f766e); }
    .stat-orders { background: linear-gradient(135deg, #059669, #16a34a); }
    .stat-users { background: linear-gradient(135deg, #7c3aed, #2563eb); }
    .stat-income { background: linear-gradient(135deg, #f59e0b, #ef4444); }
    .admin-table th,
    .admin-table td { vertical-align: middle; white-space: nowrap; }
    .admin-product-thumb {
      width: 48px;
      height: 48px;
      border-radius: 8px;
      object-fit: cover;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    .admin-product-thumb-empty {
      background: #eef2f7;
      color: #64748b;
    }
    .admin-form-card { max-width: 920px; }
  </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

  <!-- هدر بالای پنل -->
  <nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="باز و بسته کردن منو">
            <i class="bi bi-list fs-5"></i>
          </a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a href="/software_store/index.php" class="nav-link">
            <i class="bi bi-shop me-1"></i> مشاهده سایت
          </a>
        </li>
        <li class="nav-item d-none d-md-block">
          <span class="nav-link">
            <i class="bi bi-person-circle me-1"></i>
            <?= htmlspecialchars($_SESSION['user_name'] ?? 'ادمین') ?>
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

  <!-- سایدبار پنل -->
  <aside class="app-sidebar bg-dark sidebar-dark shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
      <a href="/software_store/admin/index.php" class="brand-link">
  <img src="/software_store/assets/images/logo-admin.svg"
       height="55"
       alt="ادمین" 
       style="display:block;margin:0 auto">
</a>
    </div>

    <div class="sidebar-wrapper">
      <nav class="mt-2">
        <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

          <li class="nav-item">
            <a href="/software_store/admin/index.php"
               class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-speedometer2"></i>
              <p>داشبورد</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="/software_store/admin/products.php"
               class="nav-link <?= in_array($current_page, $product_pages, true) ? 'active' : '' ?>">
              <i class="nav-icon bi bi-box-seam"></i>
              <p>محصولات</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="/software_store/admin/orders.php"
               class="nav-link <?= $current_page === 'orders.php' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-cart-check"></i>
              <p>سفارشات</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="/software_store/admin/users.php"
               class="nav-link <?= $current_page === 'users.php' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-people"></i>
              <p>کاربران</p>
            </a>
          </li>

        </ul>
      </nav>
    </div>
  </aside>

  <!-- محتوای اصلی پنل -->
  <main class="app-main">
    <div class="app-content-header py-3 px-4"></div>
    <div class="app-content px-4 pb-4">
