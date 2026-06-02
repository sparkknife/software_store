<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// گرفتن دسته‌بندی‌ها
$categories = mysqli_query($conn, "SELECT * FROM categories");

// فیلتر دسته‌بندی
$where = '';
$selected_cat = 0;
if (isset($_GET['cat']) && intval($_GET['cat']) > 0) {
    $selected_cat = intval($_GET['cat']);
    $where = "WHERE p.category_id = $selected_cat";
}

// گرفتن محصولات
$products = mysqli_query($conn, 
    "SELECT p.*, c.name as cat_name 
     FROM products p 
     JOIN categories c ON p.category_id = c.id
     $where
     ORDER BY p.id DESC"
);
?>

<!-- دسته‌بندی‌ها -->
<div class="mb-4">
  <a href="index.php" 
     class="btn btn-sm <?= $selected_cat == 0 ? 'btn-dark' : 'btn-outline-dark' ?> me-1">
    همه
  </a>
  <?php while($cat = mysqli_fetch_assoc($categories)): ?>
    <a href="index.php?cat=<?= $cat['id'] ?>" 
       class="btn btn-sm <?= $selected_cat == $cat['id'] ? 'btn-dark' : 'btn-outline-dark' ?> me-1">
      <?= htmlspecialchars($cat['name']) ?>
    </a>
  <?php endwhile; ?>
</div>

<!-- لیست محصولات -->
<div class="row g-4">
  <?php while($p = mysqli_fetch_assoc($products)): ?>
    <div class="col-md-3">
      <div class="card h-100 shadow-sm">

        <!-- عکس -->
        <div class="bg-secondary text-white text-center py-4">
          <span style="font-size:3rem">💿</span>
        </div>

        <div class="card-body d-flex flex-column">
          <!-- دسته‌بندی -->
          <span class="badge bg-info text-dark mb-2">
            <?= htmlspecialchars($p['cat_name']) ?>
          </span>

          <!-- اسم -->
          <h6 class="card-title"><?= htmlspecialchars($p['name']) ?></h6>

          <!-- ورژن -->
          <small class="text-muted mb-2">نسخه: <?= htmlspecialchars($p['version']) ?></small>

          <!-- توضیح -->
          <p class="card-text text-muted small flex-grow-1">
            <?= htmlspecialchars(mb_substr($p['description'], 0, 60)) ?>...
          </p>

          <!-- قیمت -->
          <div class="d-flex justify-content-between align-items-center mt-2">
            <strong class="text-success">
              <?= number_format($p['price']) ?> تومان
            </strong>
            <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">
              مشاهده
            </a>
          </div>
        </div>

      </div>
    </div>
  <?php endwhile; ?>
</div>

<?php require_once 'includes/footer.php'; ?>