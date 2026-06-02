<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_GET['id']) || !intval($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);
$result = mysqli_query($conn, 
    "SELECT p.*, c.name as cat_name 
     FROM products p 
     JOIN categories c ON p.category_id = c.id
     WHERE p.id = $id"
);

$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "<div class='alert alert-danger'>محصول پیدا نشد!</div>";
    require_once 'includes/footer.php';
    exit;
}
?>

<div class="row">
  <!-- عکس محصول -->
  <div class="col-md-4">
    <div class="bg-secondary text-white text-center rounded py-5">
      <span style="font-size:6rem">💿</span>
    </div>
  </div>

  <!-- اطلاعات محصول -->
  <div class="col-md-8">
    <span class="badge bg-info text-dark mb-2">
      <?= htmlspecialchars($product['cat_name']) ?>
    </span>

    <h3><?= htmlspecialchars($product['name']) ?></h3>

    <p class="text-muted">نسخه: <?= htmlspecialchars($product['version']) ?></p>

    <p><?= htmlspecialchars($product['description']) ?></p>

    <h4 class="text-success my-3">
      <?= number_format($product['price']) ?> تومان
    </h4>

    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="checkout.php?id=<?= $product['id'] ?>" class="btn btn-success btn-lg">
        🛒 خرید
      </a>
    <?php else: ?>
      <a href="login.php" class="btn btn-warning btn-lg">
        برای خرید ابتدا وارد شو
      </a>
    <?php endif; ?>

    <a href="index.php" class="btn btn-outline-secondary btn-lg ms-2">
      بازگشت
    </a>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>