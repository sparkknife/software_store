<?php
require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id    = $_SESSION['user_id'];
$order_id   = intval($_GET['order'] ?? 0);
$product_id = intval($_GET['product'] ?? 0);

// چک کن این سفارش مال این کاربره
$stmt = mysqli_prepare($conn,
    "SELECT p.file_path, p.name
     FROM orders o
     JOIN order_items oi ON o.id = oi.order_id
     JOIN products p ON oi.product_id = p.id
     WHERE o.id = ? AND o.user_id = ? AND p.id = ?");
mysqli_stmt_bind_param($stmt, 'iii', $order_id, $user_id, $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$file   = mysqli_fetch_assoc($result);

if (!$file || !$file['file_path']) {
    die("❌ دسترسی غیرمجاز!");
}

$path = __DIR__ . '/downloads/' . $file['file_path'];

if (!file_exists($path)) {
    die("❌ فایل پیدا نشد!");
}

// دانلود فایل
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($path) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
?>