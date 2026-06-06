<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$categories = mysqli_query($conn, "SELECT * FROM categories");

$selected_cat = intval($_GET['cat'] ?? 0);
$search       = trim($_GET['search'] ?? '');

$per_page = 8;
$page     = max(1, intval($_GET['page'] ?? 1));
$offset   = ($page - 1) * $per_page;

$conditions = [];
if ($selected_cat > 0) {
    $conditions[] = "p.category_id = $selected_cat";
}
if (!empty($search)) {
    $s            = mysqli_real_escape_string($conn, $search);
    $conditions[] = "(p.name LIKE '%$s%' OR p.description LIKE '%$s%')";
}
$where = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

$total_result   = mysqli_query($conn, "SELECT COUNT(*) FROM products p $where");
$total_products = mysqli_fetch_row($total_result)[0];
$total_pages    = ceil($total_products / $per_page);

$products = mysqli_query($conn,
    "SELECT p.*, c.name as cat_name
     FROM products p
     JOIN categories c ON p.category_id = c.id
     $where
     ORDER BY p.id DESC
     LIMIT $per_page OFFSET $offset"
);
?>

<!-- هیرو -->
<div class="hero mb-4">
  <div class="container">
    <h1><i class="bi bi-disc-fill me-2"></i>فروشگاه نرم‌افزار</h1>
    <p class="lead opacity-75">بهترین نرم‌افزارها با بهترین قیمت</p>
    <form method="GET" class="hero-search mx-auto">
      <div class="input-group">
        <input type="text" name="search" class="form-control"
               placeholder="جستجوی نرم‌افزار..."
               value="<?= htmlspecialchars($search) ?>">
        <?php if($selected_cat): ?>
          <input type="hidden" name="cat" value="<?= $selected_cat ?>">
        <?php endif; ?>
        <button class="btn" type="submit">
          <i class="bi bi-search"></i>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- دسته‌بندی‌ها -->
<div class="mb-4 d-flex flex-wrap gap-2">
  <a href="index.php<?= !empty($search) ? '?search='.$search : '' ?>"
     class="btn btn-sm category-btn <?= $selected_cat == 0 ? 'btn-dark' : 'btn-outline-dark' ?>">
    <i class="bi bi-grid-fill me-1"></i> همه
  </a>
  <?php while($cat = mysqli_fetch_assoc($categories)): ?>
    <a href="index.php?cat=<?= $cat['id'] ?><?= !empty($search) ? '&search='.$search : '' ?>"
       class="btn btn-sm category-btn <?= $selected_cat == $cat['id'] ? 'btn-dark' : 'btn-outline-dark' ?>">
      <?= htmlspecialchars($cat['name']) ?>
    </a>
  <?php endwhile; ?>
  <?php if(!empty($search)): ?>
    <a href="index.php" class="btn btn-sm btn-outline-danger category-btn">
      <i class="bi bi-x-circle me-1"></i> پاک کردن
    </a>
  <?php endif; ?>
</div>

<!-- نتیجه جستجو -->
<?php if(!empty($search)): ?>
  <p class="text-muted mb-3">
    <i class="bi bi-search me-1"></i>
    <?= $total_products ?> نتیجه برای «<?= htmlspecialchars($search) ?>»
  </p>
<?php endif; ?>

<!-- لیست محصولات -->
<?php if($total_products == 0): ?>
  <div class="alert alert-info text-center py-5">
    <i class="bi bi-emoji-frown" style="font-size:2rem"></i>
    <p class="mt-2 mb-0">محصولی پیدا نشد!</p>
  </div>
<?php else: ?>
  <div class="row g-4">
    <?php while($p = mysqli_fetch_assoc($products)): ?>
      <div class="col-md-3 col-sm-6">
        <div class="card product-card h-100">
          <?php if($p['image']): ?>
            <img src="/software_store/uploads/products/<?= htmlspecialchars($p['image']) ?>"
                 style="width:100%; height:140px; object-fit:cover;">
          <?php else: ?>
            <div class="product-card-icon default">
              <i class="bi bi-box-seam" style="font-size:3rem; color:white;"></i>
            </div>
          <?php endif; ?>

          <div class="card-body d-flex flex-column">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-2 align-self-start">
              <?= htmlspecialchars($p['cat_name']) ?>
            </span>
            <h6 class="card-title"><?= htmlspecialchars($p['name']) ?></h6>
            <small class="text-muted mb-2">
              <i class="bi bi-tag me-1"></i>نسخه <?= htmlspecialchars($p['version']) ?>
            </small>
            <p class="card-text text-muted small flex-grow-1">
              <?= htmlspecialchars(mb_substr($p['description'], 0, 70)) ?>...
            </p>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <span class="price"><?= number_format($p['price']) ?> ت</span>
              <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm px-3">
                <i class="bi bi-eye me-1"></i> مشاهده
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
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
          <a class="page-link"
             href="?page=<?= $page-1 ?>&cat=<?= $selected_cat ?>&search=<?= urlencode($search) ?>">
            <i class="bi bi-chevron-right"></i>
          </a>
        </li>
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link"
               href="?page=<?= $i ?>&cat=<?= $selected_cat ?>&search=<?= urlencode($search) ?>">
              <?= $i ?>
            </a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
          <a class="page-link"
             href="?page=<?= $page+1 ?>&cat=<?= $selected_cat ?>&search=<?= urlencode($search) ?>">
            <i class="bi bi-chevron-left"></i>
          </a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
