<?php
require_once 'config/db.php';

// دانلود فقط برای کاربر واردشده مجاز است
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// شناسه‌ها از آدرس گرفته می‌شوند و قبل از استفاده عددی می‌شوند
$user_id    = intval($_SESSION['user_id']);
$order_id   = intval($_GET['order'] ?? 0);
$product_id = intval($_GET['product'] ?? 0);

if ($order_id <= 0 || $product_id <= 0) {
    http_response_code(400);
    die('درخواست دانلود معتبر نیست.');
}

// چک می‌کنیم این محصول واقعاً داخل سفارش همین کاربر وجود داشته باشد
$stmt = mysqli_prepare(
    $conn,
    "SELECT p.file_path, p.name
     FROM orders o
     JOIN order_items oi ON o.id = oi.order_id
     JOIN products p ON oi.product_id = p.id
     WHERE o.id = ? AND o.user_id = ? AND p.id = ?"
);
mysqli_stmt_bind_param($stmt, 'iii', $order_id, $user_id, $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$file   = mysqli_fetch_assoc($result);

if (!$file || empty($file['file_path'])) {
    http_response_code(403);
    die('دسترسی به این فایل مجاز نیست.');
}

// مسیر نهایی فایل باید حتماً داخل پوشه downloads باشد
$downloadDir = realpath(__DIR__ . '/downloads');
$path        = $downloadDir ? realpath($downloadDir . DIRECTORY_SEPARATOR . $file['file_path']) : false;

if (!$path || strpos($path, $downloadDir . DIRECTORY_SEPARATOR) !== 0 || !is_file($path)) {
    http_response_code(404);
    die('فایل پیدا نشد.');
}

// قبل از ارسال فایل، هر خروجی احتمالی قبلی را پاک کن
while (ob_get_level()) {
    ob_end_clean();
}

// ارسال فایل به مرورگر به شکل attachment
$downloadName = str_replace('"', '', basename($path));
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($path));
header('X-Content-Type-Options: nosniff');
readfile($path);
exit;
