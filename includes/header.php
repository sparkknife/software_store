<?php
// شروع session برای دسترسی به وضعیت ورود کاربر و سبد خرید
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تعداد آیتم‌های سبد برای نمایش در نوار بالا
$cart_count = count($_SESSION['cart'] ?? []);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- BUG FIX: عنوان صفحه پویا - هر صفحه عنوان خودش رو داره -->
  <title><?= htmlspecialchars($page_title ?? 'فروشگاه نرم‌افزار') ?></title>

  <!-- Bootstrap RTL و آیکن‌ها -->
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <!-- فونت فارسی و استایل اختصاصی سایت -->
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="/software_store/assets/css/custom.css">
</head>
<body>

<!-- نوار بالای سایت -->
<nav class="navbar navbar-expand-lg navbar-dark site-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/software_store/index.php">
      <i class="bi bi-disc-fill me-1"></i> نرم‌افزار استور
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNavbar" aria-controls="mainNavbar"
            aria-expanded="false" aria-label="باز کردن منو">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
      <div class="navbar-nav ms-auto align-items-lg-center gap-2 mt-3 mt-lg-0">
        <a href="/software_store/index.php" class="btn btn-outline-light btn-sm">
          <i class="bi bi-shop me-1"></i> فروشگاه
        </a>

        <?php if(isset($_SESSION['user_id'])): ?>
          <a href="/software_store/cart.php" class="btn btn-outline-light btn-sm position-relative">
            <i class="bi bi-cart3 me-1"></i> سبد خرید
            <?php if($cart_count > 0): ?>
              <span class="badge bg-warning text-dark ms-1"><?= $cart_count ?></span>
            <?php endif; ?>
          </a>

          <a href="/software_store/orders.php" class="btn btn-outline-light btn-sm">
            <i class="bi bi-bag-check me-1"></i> خریدهای من
          </a>

          <?php if(($_SESSION['user_role'] ?? '') === 'admin'): ?>
            <a href="/software_store/admin/index.php" class="btn btn-warning btn-sm">
              <i class="bi bi-gear-fill me-1"></i> ادمین
            </a>
          <?php endif; ?>

          <span class="navbar-user text-white-50 small">
            <i class="bi bi-person-circle me-1"></i>
            <?= htmlspecialchars($_SESSION['user_name'] ?? 'کاربر') ?>
          </span>

          <a href="/software_store/logout.php" class="btn btn-danger btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i> خروج
          </a>
        <?php else: ?>
          <a href="/software_store/login.php" class="btn btn-outline-light btn-sm">
            <i class="bi bi-box-arrow-in-right me-1"></i> ورود
          </a>
          <a href="/software_store/register.php" class="btn btn-success btn-sm">
            <i class="bi bi-person-plus me-1"></i> ثبت‌نام
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- محتوای اصلی صفحه‌ها از اینجا شروع می‌شود -->
<main class="container site-main">