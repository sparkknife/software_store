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

$total   = array_sum(array_column($cart, 'price'));
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'delivered')");
    mysqli_stmt_bind_param($stmt, 'id', $user_id, $total);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);

    foreach ($cart as $item) {
        $stmt2 = mysqli_prepare($conn,
            "INSERT INTO order_items (order_id, product_id, quantity, price)
             VALUES (?, ?, 1, ?)");
        mysqli_stmt_bind_param($stmt2, 'iid', $order_id, $item['id'], $item['price']);
        mysqli_stmt_execute($stmt2);
    }

    unset($_SESSION['cart']);
    header("Location: orders.php?success=1");
    exit;
}
?>

<div class="d-flex align-items-center gap-2 mb-4">
  <i class="bi bi-shield-check" style="font-size:1.8rem; color:var(--success)"></i>
  <h4 class="mb-0 fw-bold">تکمیل خرید</h4>
</div>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h6 class="fw-bold">
          <i class="bi bi-receipt me-1"></i> خلاصه سفارش
        </h6>
      </div>
      <div class="card-body px-4">

        <?php foreach($cart as $item): ?>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span>
              <i class="bi bi-box-seam me-1 text-muted"></i>
              <?= htmlspecialchars($item['name']) ?>
            </span>
            <span class="fw-bold"><?= number_format($item['price']) ?> تومان</span>
          </div>
        <?php endforeach; ?>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-4">
          <span class="fw-bold fs-5">جمع کل:</span>
          <span class="fw-bold fs-4 text-success"><?= number_format($total) ?> تومان</span>
        </div>

        <div class="alert alert-info py-2 mb-4">
          <i class="bi bi-info-circle me-1"></i>
          بعد از پرداخت لینک دانلود فایل‌هات فعال میشه
        </div>

        <form method="POST">
          <button type="submit" class="btn btn-success w-100 btn-lg py-3">
            <i class="bi bi-shield-lock me-1"></i> پرداخت و دریافت فایل
          </button>
        </form>

        <a href="cart.php" class="btn btn-outline-secondary w-100 mt-2">
          <i class="bi bi-arrow-right me-1"></i> بازگشت به سبد
        </a>

      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
