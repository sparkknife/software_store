<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['add'])) {
    $pid    = intval($_GET['add']);
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id = $pid");
    $product = mysqli_fetch_assoc($result);

    if ($product) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][$pid] = [
            'id'    => $product['id'],
            'name'  => $product['name'],
            'price' => $product['price'],
        ];
    }
    header('Location: cart.php');
    exit;
}

if (isset($_GET['remove'])) {
    $pid = intval($_GET['remove']);
    unset($_SESSION['cart'][$pid]);
    header('Location: cart.php');
    exit;
}

$cart  = $_SESSION['cart'] ?? [];
$total = array_sum(array_column($cart, 'price'));
?>

<div class="d-flex align-items-center gap-2 mb-4">
  <i class="bi bi-cart3" style="font-size:1.8rem; color:var(--primary)"></i>
  <h4 class="mb-0 fw-bold">سبد خرید</h4>
  <?php if(!empty($cart)): ?>
    <span class="badge bg-primary rounded-pill"><?= count($cart) ?></span>
  <?php endif; ?>
</div>

<?php if(empty($cart)): ?>
  <div class="card text-center py-5">
    <div class="card-body">
      <i class="bi bi-cart-x" style="font-size:4rem; color:#ccc;"></i>
      <h5 class="mt-3 text-muted">سبد خریدت خالیه!</h5>
      <a href="index.php" class="btn btn-primary mt-3 px-4">
        <i class="bi bi-shop me-1"></i> برگرد به فروشگاه
      </a>
    </div>
  </div>
<?php else: ?>

  <div class="card shadow-sm">
    <div class="card-body p-0">
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

  <div class="d-flex justify-content-between mt-4">
    <a href="index.php" class="btn btn-outline-secondary px-4">
      <i class="bi bi-arrow-right me-1"></i> ادامه خرید
    </a>
    <a href="checkout.php" class="btn btn-success px-4">
      <i class="bi bi-credit-card me-1"></i> تکمیل خرید
    </a>
  </div>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
