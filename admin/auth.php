<?php
// محافظ صفحه‌های ادمین؛ فقط کاربر admin اجازه ورود دارد
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: /software_store/login.php');
    exit;
}
