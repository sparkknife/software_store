<?php
require_once 'config/db.php';

// اگر شناسه محصول معتبر نبود، کاربر به صفحه اصلی برگردد
if (!isset($_GET['id']) || intval($_GET['id']) <= 0) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);

// گرفتن اطلاعات محصول همراه با نام دسته‌بندی
$stmt = mysqli_prepare(
    $conn,
    "SELECT p.*, c.name as cat_name
     FROM products p
     JOIN categories c ON p.category_id = c.id
     WHERE p.id = ?"
);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result  = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

require_once 'includes/header.php';

// اگر محصول وجود نداشت، پیام مناسب نشان بده
if (!$product) {
    echo "<div class='alert alert-danger'><i class='bi bi-exclamation-triangle me-2'></i>محصول پیدا نشد!</div>";
    require_once 'includes/footer.php';
    exit;
}
?>

<div class="product-detail row g-4 align-items-start">
  <!-- تصویر محصول یا جایگزین پیش‌فرض -->
  <div class="col-md-5 col-lg-4">
    <?php if(!empty($product['image'])): ?>
      <img src="/software_store/uploads/products/<?= htmlspecialchars($product['image']) ?>"
           alt="<?= htmlspecialchars($product['name']) ?>"
           class="product-detail-image w-100">
    <?php else: ?>
      <div class="product-detail-icon">
        <i class="bi bi-box-seam" style="color:white;"></i>
      </div>
    <?php endif; ?>
  </div>

  <!-- اطلاعات اصلی محصول -->
  <div class="col-md-7 col-lg-8">
    <span class="badge bg-primary bg-opacity-10 text-primary mb-3 product-detail-badge">
      <i class="bi bi-folder me-1"></i>
      <?= htmlspecialchars($product['cat_name']) ?>
    </span>

    <h2 class="fw-bold mb-2"><?= htmlspecialchars($product['name']) ?></h2>

    <p class="text-muted mb-3">
      <i class="bi bi-tag me-1"></i> نسخه: <?= htmlspecialchars($product['version']) ?>
    </p>

    <p class="text-secondary lh-lg product-description">
      <?= nl2br(htmlspecialchars($product['description'])) ?>
    </p>

    <hr>

    <!-- قیمت و عملیات خرید -->
    <div class="d-flex align-items-center gap-2 mb-4">
      <i class="bi bi-cash-coin text-success" style="font-size:1.5rem"></i>
      <h3 class="fw-bold text-success mb-0">
        <?= number_format($product['price']) ?> تومان
      </h3>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <?php if(isset($_SESSION['user_id'])): ?>
        <a href="cart.php?add=<?= $product['id'] ?>" class="btn btn-success btn-lg px-4">
          <i class="bi bi-cart-plus me-1"></i> افزودن به سبد
        </a>
        <a href="cart.php" class="btn btn-outline-primary btn-lg px-4">
          <i class="bi bi-cart3 me-1"></i> سبد خرید
        </a>
      <?php else: ?>
        <a href="login.php" class="btn btn-warning btn-lg px-4">
          <i class="bi bi-person-lock me-1"></i> برای خرید وارد شو
        </a>
      <?php endif; ?>
      <a href="index.php" class="btn btn-outline-secondary btn-lg px-4">
        <i class="bi bi-arrow-right me-1"></i> بازگشت
      </a>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
