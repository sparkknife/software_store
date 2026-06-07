<?php
require_once 'config/db.php';

// پرداخت و redirect باید قبل از خروجی HTML انجام شود
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// صفحه پرداخت فقط برای کاربران واردشده در دسترس است
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];

// اگر سبد خالی باشد، پرداخت معنی ندارد
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$total   = array_sum(array_column($cart, 'price'));
$user_id = intval($_SESSION['user_id']);
$error   = '';

// ثبت سفارش بعد از زدن دکمه پرداخت
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    mysqli_begin_transaction($conn);
    $saved = true;

    // فعلاً پرداخت شبیه‌سازی شده و سفارش مستقیم delivered می‌شود
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'delivered')"
    );
    mysqli_stmt_bind_param($stmt, 'id', $user_id, $total);
    $saved = $saved && mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);

    // ذخیره تک‌تک آیتم‌های سبد داخل order_items
    if ($saved && $order_id > 0) {
        $stmt2 = mysqli_prepare(
            $conn,
            "INSERT INTO order_items (order_id, product_id, quantity, price)
             VALUES (?, ?, 1, ?)"
        );

        foreach ($cart as $item) {
            $product_id = intval($item['id']);
            $price      = floatval($item['price']);
            mysqli_stmt_bind_param($stmt2, 'iid', $order_id, $product_id, $price);

            if (!mysqli_stmt_execute($stmt2)) {
                $saved = false;
                break;
            }
        }
    } else {
        $saved = false;
    }

    if ($saved) {
        mysqli_commit($conn);
        unset($_SESSION['cart']);
        header('Location: orders.php?success=1');
        exit;
    }

    mysqli_rollback($conn);
    $error = 'ثبت سفارش کامل نشد. لطفاً دوباره امتحان کن.';
}

require_once 'includes/header.php';
?>

<div class="page-title d-flex align-items-center gap-2 mb-4">
  <i class="bi bi-shield-check text-success"></i>
  <h4 class="mb-0 fw-bold">تکمیل خرید</h4>
</div>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-6">
    <div class="card shadow-sm checkout-card">
      <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-receipt me-1"></i> خلاصه سفارش
        </h6>
      </div>
      <div class="card-body px-4">

        <!-- پیام خطا هنگام ثبت سفارش -->
        <?php if($error): ?>
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <!-- لیست آیتم‌های سفارش -->
        <?php foreach($cart as $item): ?>
          <div class="order-summary-row d-flex justify-content-between align-items-center mb-3">
            <span>
              <i class="bi bi-box-seam me-1 text-muted"></i>
              <?= htmlspecialchars($item['name']) ?>
            </span>
            <span class="fw-bold"><?= number_format($item['price']) ?> تومان</span>
          </div>
        <?php endforeach; ?>

        <hr>

        <!-- جمع کل سفارش -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <span class="fw-bold fs-5">جمع کل:</span>
          <span class="fw-bold fs-4 text-success"><?= number_format($total) ?> تومان</span>
        </div>

        <div class="alert alert-info py-2 mb-4">
          <i class="bi bi-info-circle me-1"></i>
          بعد از پرداخت، لینک دانلود فایل‌ها در بخش خریدهای من فعال می‌شود.
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
