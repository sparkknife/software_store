<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$orders  = mysqli_query($conn,
    "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<h4 class="mb-4">📦 خریدهای من</h4>

<?php if(isset($_GET['success'])): ?>
  <div class="alert alert-success">
    خرید موفق! میتونی فایل‌هات رو دانلود کنی 🎉
  </div>
<?php endif; ?>

<?php while($order = mysqli_fetch_assoc($orders)): ?>

  <div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between">
      <span>سفارش #<?= $order['id'] ?></span>
      <span><?= $order['created_at'] ?></span>
      <span class="badge bg-success"><?= $order['total'] ?> تومان</span>
    </div>
    <div class="card-body">

      <?php
      $items = mysqli_query($conn,
          "SELECT oi.*, p.name, p.file_path 
           FROM order_items oi
           JOIN products p ON oi.product_id = p.id
           WHERE oi.order_id = {$order['id']}");
      ?>

      <table class="table mb-0">
        <thead>
          <tr>
            <th>محصول</th>
            <th>قیمت</th>
            <th>دانلود</th>
          </tr>
        </thead>
        <tbody>
          <?php while($item = mysqli_fetch_assoc($items)): ?>
            <tr>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td><?= number_format($item['price']) ?> تومان</td>
              <td>
                <?php if($item['file_path']): ?>
                  <a href="download.php?order=<?= $order['id'] ?>&product=<?= $item['product_id'] ?>" 
                     class="btn btn-primary btn-sm">
                    ⬇️ دانلود
                  </a>
                <?php else: ?>
                  <span class="text-muted">فایل موجود نیست</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

    </div>
  </div>

<?php endwhile; ?>

<?php require_once 'includes/footer.php'; ?>