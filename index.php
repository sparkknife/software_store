<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// گرفتن دسته‌بندی‌ها
$categories = mysqli_query($conn, "SELECT * FROM categories");

// فیلترها
$selected_cat = intval($_GET['cat'] ?? 0);
$search       = trim($_GET['search'] ?? '');

// صفحه‌بندی
$per_page    = 8;
$page        = intval($_GET['page'] ?? 1);
if ($page < 1) $page = 1;
$offset      = ($page - 1) * $per_page;

// ساخت WHERE
$conditions = [];
if ($selected_cat > 0) {
    $conditions[] = "p.category_id = $selected_cat";
}
if (!empty($search)) {
    $s            = mysqli_real_escape_string($conn, $search);
    $conditions[] = "(p.name LIKE '%$s%' OR p.description LIKE '%$s%')";
}
$where = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

// تعداد کل محصولات
$total_result   = mysqli_query($conn, "SELECT COUNT(*) FROM products p $where");
$total_products = mysqli_fetch_row($total_result)[0];
$total_pages    = ceil($total_products / $per_page);

// گرفتن محصولات
$products = mysqli_query($conn,
    "SELECT p.*, c.name as cat_name 
     FROM products p 
     JOIN categories c ON p.category_id = c.id
     $where
     ORDER BY p.id DESC
     LIMIT $per_page OFFSET $offset"
);
?>

<!-- جستجو -->
<form method="GET" class="mb-4">
  <div class="input-group">
    <input type="text" name="search" class="form-control form-control-lg"
           placeholder="جستجوی نرم‌افزار..."
           value="<?= htmlspecialchars($search) ?>">
    <?php if($selected_cat): ?>
      <input type="hidden" name="cat" value="<?= $selected_cat ?>">
    <?php endif; ?>
    <button class="btn btn-primary" type="submit">🔍 جستجو</button>
    <?php if(!empty($search)): ?>
      <a href="index.php" class="btn btn-outline-secondary">✕ پاک کن</a>
    <?php endif; ?>
  </div>
</form>

<!-- دسته‌بندی‌ها -->
<div class="mb-4">
  <a href="index.php<?= !empty($search) ? '?search='.$search : '' ?>"
     class="btn btn-sm <?= $selected_cat == 0 ? 'btn-dark' : 'btn-outline-dark' ?> me-1">
    همه
  </a>
  <?php while($cat = mysqli_fetch_assoc($categories)): ?>
    <a href="index.php?cat=<?= $cat['id'] ?><?= !empty($search) ? '&search='.$search : '' ?>"
       class="btn btn-sm <?= $selected_cat == $cat['id'] ? 'btn-dark' : 'btn-outline-dark' ?> me-1">
      <?= htmlspecialchars($cat['name']) ?>
    </a>
  <?php endwhile; ?>
</div>

<!-- نتیجه جستجو -->
<?php if(!empty($search)): ?>
  <p class="text-muted mb-3">
    <?= $total_products ?> نتیجه برای «<?= htmlspecialchars($search) ?>»
  </p>
<?php endif; ?>

<!-- لیست محصولات -->
<?php if($total_products == 0): ?>
  <div class="alert alert-info text-center">
    محصولی پیدا نشد! 😕
  </div>
<?php else: ?>
  <div class="row g-4">
    <?php while($p = mysqli_fetch_assoc($products)): ?>
      <div class="col-md-3">
        <div class="card h-100 shadow-sm">
          <div class="bg-secondary text-white text-center py-4">
            <span style="font-size:3rem">💿</span>
          </div>
          <div class="card-body d-flex flex-column">
            <span class="badge bg-info text-dark mb-2">
              <?= htmlspecialchars($p['cat_name']) ?>
            </span>
            <h6 class="card-title"><?= htmlspecialchars($p['name']) ?></h6>
            <small class="text-muted mb-2">
              نسخه: <?= htmlspecialchars($p['version']) ?>
            </small>
            <p class="card-text text-muted small flex-grow-1">
              <?= htmlspecialchars(mb_substr($p['description'], 0, 60)) ?>...
            </p>
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

  <!-- صفحه‌بندی -->
  <?php if($total_pages > 1): ?>
    <nav class="mt-5">
      <ul class="pagination justify-content-center">

        <!-- قبلی -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" 
             href="?page=<?= $page-1 ?>&cat=<?= $selected_cat ?>&search=<?= urlencode($search) ?>">
            قبلی
          </a>
        </li>

        <!-- شماره صفحات -->
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link"
               href="?page=<?= $i ?>&cat=<?= $selected_cat ?>&search=<?= urlencode($search) ?>">
              <?= $i ?>
            </a>
          </li>
        <?php endfor; ?>

        <!-- بعدی -->
        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
          <a class="page-link"
             href="?page=<?= $page+1 ?>&cat=<?= $selected_cat ?>&search=<?= urlencode($search) ?>">
            بعدی
          </a>
        </li>

      </ul>
    </nav>
  <?php endif; ?>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>