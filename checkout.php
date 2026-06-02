<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$total    = array_sum(array_column($cart, 'price'));
$user_id  = $_SESSION['user_id'];
$order_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ثبت سفارش
    $stmt = mysqli_prepare($conn,
        "INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'delivered')");
    mysqli_stmt_bind_param($stmt, 'id', $user_id, $total);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);

    // ثبت آیتم‌های سفارش
    foreach ($cart as $item) {
        $stmt2 = mysqli_prepare($conn,
            "INSERT INTO order_items (order_id, product_id, quantity, price) 
             VALUES (?, ?, 1, ?)");
        mysqli_stmt_bind_param($stmt2, 'iid', $order_id, $item['id'], $item['price']);
        mysqli_stmt_execute($stmt2);
    }

    // سبد رو خالی کن
    unset($_SESSION['cart']);

    header("Location: orders.php?success=1");
    exit;
}
?>

<h4 class="mb-4">✅ تکمیل خرید</h4>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h6 class="mb-3">خلاصه سفارش:</h6>

        <?php foreach($cart as $item): ?>
          <div class="d-flex justify-content-between mb-2">
            <span><?= htmlspecialchars($item['name']) ?></span>
            <span><?= number_format($item['price']) ?> تومان</span>
          </div>
        <?php endforeach; ?>

        <hr>
        <div class="d-flex justify-content-between fw-bold">
          <span>جمع کل:</span>
          <span class="text-success"><?= number_format($total) ?> تومان</span>
        </div>

        <form method="POST" class="mt-4">
          <button type="submit" class="btn btn-success w-100 btn-lg">
            💳 پرداخت و دریافت فایل
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>