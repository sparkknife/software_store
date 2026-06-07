<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// ساخت لینک‌های فیلتر و صفحه‌بندی به شکل امن و خوانا
function store_index_url(array $params = []): string
{
    $params = array_filter($params, function ($value) {
        return $value !== null && $value !== '' && $value !== 0;
    });

    return 'index.php' . ($params ? '?' . http_build_query($params) : '');
}

// گرفتن دسته‌بندی‌ها برای فیلتر محصولات
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

// فیلترهای صفحه اصلی
$selected_cat = intval($_GET['cat'] ?? 0);
$search       = trim($_GET['search'] ?? '');
$per_page     = 8;
$page         = max(1, intval($_GET['page'] ?? 1));

// ساخت شرط‌های جستجو و دسته‌بندی
$conditions = [];
if ($selected_cat > 0) {
    $conditions[] = "p.category_id = $selected_cat";
}
if ($search !== '') {
    $s            = mysqli_real_escape_string($conn, $search);
    $conditions[] = "(p.name LIKE '%$s%' OR p.description LIKE '%$s%')";
}
$where = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

// شمارش کل محصولات برای صفحه‌بندی
$total_result   = mysqli_query($conn, "SELECT COUNT(*) FROM products p $where");
$total_products = intval(mysqli_fetch_row($total_result)[0]);
$total_pages    = max(1, (int) ceil($total_products / $per_page));
$page           = min($page, $total_pages);
$offset         = ($page - 1) * $per_page;

// گرفتن محصولات صفحه فعلی
$products = mysqli_query(
    $conn,
    "SELECT p.*, c.name as cat_name
     FROM products p
     JOIN categories c ON p.category_id = c.id
     $where
     ORDER BY p.id DESC
     LIMIT $per_page OFFSET $offset"
);
?>

<!-- بخش معرفی و جستجوی فروشگاه -->
<section class="hero mb-4">
  <div class="container">
    <span class="hero-kicker">
      <i class="bi bi-stars me-1"></i> فروشگاه فایل‌های نرم‌افزاری
    </span>
    <h1><i class="bi bi-disc-fill me-2"></i>فروشگاه نرم‌افزار</h1>
    <p class="lead">نرم‌افزارهای موردنیازت رو سریع پیدا کن و بعد از خرید دانلود کن.</p>

    <form method="GET" class="hero-search mx-auto">
      <div class="input-group">
        <input type="text" name="search" class="form-control"
               placeholder="جستجوی نرم‌افزار..."
               value="<?= htmlspecialchars($search) ?>">
        <?php if($selected_cat): ?>
          <input type="hidden" name="cat" value="<?= $selected_cat ?>">
        <?php endif; ?>
        <button class="btn" type="submit" aria-label="جستجو">
          <i class="bi bi-search"></i>
        </button>
      </div>
    </form>
  </div>
</section>

<!-- دکمه‌های فیلتر دسته‌بندی -->
<section class="mb-4 d-flex flex-wrap gap-2 align-items-center">
  <a href="<?= store_index_url(['search' => $search]) ?>"
     class="btn btn-sm category-btn <?= $selected_cat == 0 ? 'btn-dark' : 'btn-outline-dark' ?>">
    <i class="bi bi-grid-fill me-1"></i> همه
  </a>

  <?php while($cat = mysqli_fetch_assoc($categories)): ?>
    <a href="<?= store_index_url(['cat' => (int)$cat['id'], 'search' => $search]) ?>"
       class="btn btn-sm category-btn <?= $selected_cat == $cat['id'] ? 'btn-dark' : 'btn-outline-dark' ?>">
      <?= htmlspecialchars($cat['name']) ?>
    </a>
  <?php endwhile; ?>

  <?php if($search !== ''): ?>
    <a href="index.php" class="btn btn-sm btn-outline-danger category-btn">
      <i class="bi bi-x-circle me-1"></i> پاک کردن
    </a>
  <?php endif; ?>
</section>

<!-- وضعیت نتیجه جستجو -->
<?php if($search !== ''): ?>
  <p class="text-muted mb-3">
    <i class="bi bi-search me-1"></i>
    <?= $total_products ?> نتیجه برای «<?= htmlspecialchars($search) ?>»
  </p>
<?php endif; ?>

<!-- لیست محصولات -->
<?php if($total_products === 0): ?>
  <div class="empty-state alert alert-info text-center py-5">
    <i class="bi bi-search"></i>
    <p class="mt-2 mb-0">محصولی پیدا نشد.</p>
  </div>
<?php else: ?>
  <div class="row g-4">
    <?php while($p = mysqli_fetch_assoc($products)): ?>
      <?php $short_description = (string)($p['description'] ?? ''); ?>
      <div class="col-md-3 col-sm-6">
        <article class="card product-card h-100">
          <!-- تصویر محصول -->
          <?php if(!empty($p['image'])): ?>
            <img src="/software_store/uploads/products/<?= htmlspecialchars($p['image']) ?>"
                 alt="<?= htmlspecialchars($p['name']) ?>"
                 class="product-card-image">
          <?php else: ?>
            <div class="product-card-icon default">
              <i class="bi bi-box-seam"></i>
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
              <?= htmlspecialchars(mb_substr($short_description, 0, 80)) ?><?= mb_strlen($short_description) > 80 ? '...' : '' ?>
            </p>
            <div class="d-flex justify-content-between align-items-center mt-3 gap-2">
              <span class="price"><?= number_format($p['price']) ?> ت</span>
              <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm px-3">
                <i class="bi bi-eye me-1"></i> مشاهده
              </a>
            </div>
          </div>
        </article>
      </div>
    <?php endwhile; ?>
  </div>

  <!-- صفحه‌بندی محصولات -->
  <?php if($total_pages > 1): ?>
    <nav class="mt-5" aria-label="صفحه‌بندی محصولات">
      <ul class="pagination justify-content-center flex-wrap gap-1">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
          <a class="page-link"
             href="<?= store_index_url(['page' => $page - 1, 'cat' => $selected_cat, 'search' => $search]) ?>">
            <i class="bi bi-chevron-right"></i>
          </a>
        </li>

        <?php for($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link"
               href="<?= store_index_url(['page' => $i, 'cat' => $selected_cat, 'search' => $search]) ?>">
              <?= $i ?>
            </a>
          </li>
        <?php endfor; ?>

        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
          <a class="page-link"
             href="<?= store_index_url(['page' => $page + 1, 'cat' => $selected_cat, 'search' => $search]) ?>">
            <i class="bi bi-chevron-left"></i>
          </a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
