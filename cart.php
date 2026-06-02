<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// اضافه کردن به سبد
if (isset($_GET['add'])) {
    $pid = intval($_GET['add']);
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

// حذف از سبد
if (isset($_GET['remove'])) {
    $pid = intval($_GET['remove']);
    unset($_SESSION['cart'][$pid]);
    header('Location: cart.php');
    exit;
}

$cart  = $_SESSION['cart'] ?? [];
$total = array_sum(array_column($cart, 'price'));
?>

<h4 class="mb-4">🛒 سبد خرید</h4>

<?php if(empty($cart)): ?>
  <div class="alert alert-info">
    سبد خرید خالیه! <a href="index.php">برگرد به فروشگاه</a>
  </div>
<?php else: ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>محصول</th>
            <th>قیمت</th>
            <th>حذف</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($cart as $item): ?>
            <tr>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td><?= number_format($item['price']) ?> تومان</td>
              <td>
                <a href="cart.php?remove=<?= $item['id'] ?>" 
                   class="btn btn-danger btn-sm">حذف</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th>جمع کل:</th>
            <th><?= number_format($total) ?> تومان</th>
            <th></th>
          </tr>
        </tfoot>
      </table>

      <div class="text-end">
        <a href="index.php" class="btn btn-outline-secondary">ادامه خرید</a>
        <a href="checkout.php" class="btn btn-success me-2">تکمیل خرید</a>
      </div>
    </div>
  </div>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>