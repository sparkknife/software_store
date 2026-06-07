<?php
require_once 'config/db.php';

// عملیات سبد خرید قبل از خروجی HTML انجام می‌شود
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// سبد خرید فقط برای کاربران واردشده فعال است
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// اضافه کردن محصول به سبد خرید
if (isset($_GET['add'])) {
    $pid = intval($_GET['add']);

    $stmt = mysqli_prepare($conn, "SELECT id, name, price FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $pid);
    mysqli_stmt_execute($stmt);
    $result  = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    if ($product) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // کلید محصول باعث می‌شود یک محصول چند بار تکراری وارد سبد نشود
        $_SESSION['cart'][$pid] = [
            'id'    => $product['id'],
            'name'  => $product['name'],
            'price' => $product['price'],
        ];
    }

    header('Location: cart.php');
    exit;
}

// حذف محصول از سبد خرید
if (isset($_GET['remove'])) {
    $pid = intval($_GET['remove']);
    unset($_SESSION['cart'][$pid]);
    header('Location: cart.php');
    exit;
}

// محاسبه آیتم‌ها و جمع کل
$cart  = $_SESSION['cart'] ?? [];
$total = array_sum(array_column($cart, 'price'));

require_once 'includes/header.php';
?>

<div class="page-title d-flex align-items-center gap-2 mb-4">
  <i class="bi bi-cart3"></i>
  <h4 class="mb-0 fw-bold">سبد خرید</h4>
  <?php if(!empty($cart)): ?>
    <span class="badge bg-primary rounded-pill"><?= count($cart) ?></span>
  <?php endif; ?>
</div>

<?php if(empty($cart)): ?>
  <!-- حالت خالی بودن سبد خرید -->
  <div class="empty-state card text-center py-5">
    <div class="card-body">
      <i class="bi bi-cart-x"></i>
      <h5 class="mt-3 text-muted">سبد خریدت خالیه!</h5>
      <a href="index.php" class="btn btn-primary mt-3 px-4">
        <i class="bi bi-shop me-1"></i> برگشت به فروشگاه
      </a>
    </div>
  </div>
<?php else: ?>

  <!-- جدول آیتم‌های سبد خرید -->
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th class="px-4 py-3">
                <i class="bi bi-box-seam me-1"></i> محصول
              </th>
              <th class="py-3">
                <i class="bi bi-cash-coin me-1"></i> قیمت
              </th>
              <th class="py-3">عملیات</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($cart as $item): ?>
              <tr>
                <td class="px-4 py-3 fw-500"><?= htmlspecialchars($item['name']) ?></td>
                <td class="py-3 text-success fw-bold">
                  <?= number_format($item['price']) ?> تومان
                </td>
                <td class="py-3">
                  <a href="cart.php?remove=<?= $item['id'] ?>"
                     class="btn btn-outline-danger btn-sm"
                     onclick="return confirm('از سبد حذف بشه؟')">
                    <i class="bi bi-trash3"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr class="table-light">
              <th class="px-4 py-3">
                <i class="bi bi-receipt me-1"></i> جمع کل:
              </th>
              <th class="py-3 text-success fs-5"><?= number_format($total) ?> تومان</th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <!-- دکمه‌های ادامه خرید و پرداخت -->
  <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mt-4">
    <a href="index.php" class="btn btn-outline-secondary px-4">
      <i class="bi bi-arrow-right me-1"></i> ادامه خرید
    </a>
    <a href="checkout.php" class="btn btn-success px-4">
      <i class="bi bi-credit-card me-1"></i> تکمیل خرید
    </a>
  </div>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
