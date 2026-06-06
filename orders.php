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
$order_count = mysqli_num_rows($orders);
?>

<div class="d-flex align-items-center gap-2 mb-4">
  <i class="bi bi-bag-check-fill" style="font-size:1.8rem; color:var(--primary)"></i>
  <h4 class="mb-0 fw-bold">خریدهای من</h4>
  <span class="badge bg-primary rounded-pill"><?= $order_count ?></span>
</div>

<?php if(isset($_GET['success'])): ?>
  <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-check-circle-fill fs-4"></i>
    <div>
      <strong>خرید موفق!</strong> فایل‌هات آماده دانلوده 🎉
    </div>
  </div>
<?php endif; ?>

<?php if($order_count === 0): ?>
  <div class="card text-center py-5">
    <div class="card-body">
      <i class="bi bi-bag-x" style="font-size:4rem; color:#ccc;"></i>
      <h5 class="mt-3 text-muted">هنوز خریدی نداری!</h5>
      <a href="index.php" class="btn btn-primary mt-3 px-4">
        <i class="bi bi-shop me-1"></i> مشاهده محصولات
      </a>
    </div>
  </div>
<?php else: ?>

  <?php
  // reset pointer
  mysqli_data_seek($orders, 0);
  while($order = mysqli_fetch_assoc($orders)):
  ?>
    <div class="card shadow-sm mb-4">
      <div class="card-header d-flex justify-content-between align-items-center py-3 px-4">
        <span class="fw-bold">
          <i class="bi bi-receipt me-1"></i> سفارش #<?= $order['id'] ?>
        </span>
        <span class="text-muted small">
          <i class="bi bi-clock me-1"></i><?= $order['created_at'] ?>
        </span>
        <span class="badge bg-success px-3 py-2">
          <i class="bi bi-check-circle me-1"></i>
          <?= number_format($order['total']) ?> تومان
        </span>
      </div>
      <div class="card-body p-0">
        <?php
        $items = mysqli_query($conn,
            "SELECT oi.*, p.name, p.file_path
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = {$order['id']}");
        ?>
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th class="px-4 py-3">
                <i class="bi bi-box-seam me-1"></i> محصول
              </th>
              <th class="py-3">قیمت</th>
              <th class="py-3">دانلود</th>
            </tr>
          </thead>
          <tbody>
            <?php while($item = mysqli_fetch_assoc($items)): ?>
              <tr>
                <td class="px-4 py-3"><?= htmlspecialchars($item['name']) ?></td>
                <td class="py-3 text-success fw-bold">
                  <?= number_format($item['price']) ?> تومان
                </td>
                <td class="py-3">
                  <?php if($item['file_path']): ?>
                    <a href="download.php?order=<?= $order['id'] ?>&product=<?= $item['product_id'] ?>"
                       class="btn btn-primary btn-sm px-3">
                      <i class="bi bi-cloud-arrow-down me-1"></i> دانلود
                    </a>
                  <?php else: ?>
                    <span class="text-muted small">
                      <i class="bi bi-clock me-1"></i> در انتظار فایل
                    </span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endwhile; ?>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
