<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_GET['id']) || !intval($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id     = intval($_GET['id']);
$result = mysqli_query($conn,
    "SELECT p.*, c.name as cat_name
     FROM products p
     JOIN categories c ON p.category_id = c.id
     WHERE p.id = $id"
);

$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "<div class='alert alert-danger'><i class='bi bi-exclamation-triangle me-2'></i>محصول پیدا نشد!</div>";
    require_once 'includes/footer.php';
    exit;
}
?>

<div class="row g-4">
  <!-- عکس محصول -->
  <div class="col-md-4">
    <?php if($product['image']): ?>
      <img src="/software_store/uploads/products/<?= htmlspecialchars($product['image']) ?>"
           class="w-100 rounded-4 shadow"
           style="height:300px; object-fit:cover;">
    <?php else: ?>
      <div class="product-detail-icon">
        <i class="bi bi-box-seam" style="color:white;"></i>
      </div>
    <?php endif; ?>
  </div>

  <!-- اطلاعات محصول -->
  <div class="col-md-8">
    <span class="badge bg-primary bg-opacity-10 text-primary mb-3 product-detail-badge">
      <i class="bi bi-folder me-1"></i>
      <?= htmlspecialchars($product['cat_name']) ?>
    </span>

    <h2 class="fw-bold mb-2"><?= htmlspecialchars($product['name']) ?></h2>

    <p class="text-muted mb-3">
      <i class="bi bi-tag me-1"></i> نسخه: <?= htmlspecialchars($product['version']) ?>
    </p>

    <p class="text-secondary lh-lg"><?= htmlspecialchars($product['description']) ?></p>

    <hr>

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
