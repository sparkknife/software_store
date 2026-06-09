<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/db.php';

// حذف محصول باید قبل از خروجی HTML انجام شود تا redirect خراب نشود
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // اول اطلاعات فایل‌های محصول گرفته می‌شود تا بعد از حذف رکورد، فایل‌ها هم پاک شوند
    $stmt = mysqli_prepare($conn, "SELECT image, file_path FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result  = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    $delete = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    mysqli_stmt_bind_param($delete, 'i', $id);

    if (mysqli_stmt_execute($delete) && $product) {
        // بعد از حذف موفق، فایل‌های مرتبط محصول هم حذف می‌شوند
        if (!empty($product['image'])) {
            @unlink(__DIR__ . '/../uploads/products/' . $product['image']);
        }
        if (!empty($product['file_path'])) {
            @unlink(__DIR__ . '/../downloads/' . $product['file_path']);
        }

        header('Location: products.php?deleted=1');
        exit;
    }

    header('Location: products.php?delete_error=1');
    exit;
}

require_once 'header.php';

// گرفتن لیست محصولات همراه با نام دسته‌بندی
$products = mysqli_query(
    $conn,
    "SELECT p.*, c.name as cat_name
     FROM products p
     JOIN categories c ON p.category_id = c.id
     ORDER BY p.id DESC"
);
?>

<!-- سربرگ صفحه محصولات -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
  <h4 class="mb-0"><i class="bi bi-box-seam me-1"></i> مدیریت محصولات</h4>
  <a href="add_product.php" class="btn btn-success">
    <i class="bi bi-plus-circle me-1"></i> افزودن محصول
  </a>
</div>

<!-- پیام نتیجه حذف محصول -->
<?php if(isset($_GET['deleted'])): ?>
  <div class="alert alert-success">محصول با موفقیت حذف شد.</div>
<?php endif; ?>
<?php if(isset($_GET['delete_error'])): ?>
  <div class="alert alert-danger">حذف محصول انجام نشد.</div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 admin-table">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>تصویر</th>
            <th>نام</th>
            <th>دسته‌بندی</th>
            <th>نسخه</th>
            <th>قیمت</th>
            <th>عملیات</th>
          </tr>
        </thead>
        <tbody>
          <?php while($p = mysqli_fetch_assoc($products)): ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td>
                <?php if(!empty($p['image'])): ?>
                  <img src="../uploads/products/<?= htmlspecialchars($p['image']) ?>"
                       alt="<?= htmlspecialchars($p['name']) ?>"
                       class="admin-product-thumb">
                <?php else: ?>
                  <span class="admin-product-thumb admin-product-thumb-empty">
                    <i class="bi bi-image"></i>
                  </span>
                <?php endif; ?>
              </td>
              <td class="fw-semibold"><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['cat_name']) ?></td>
              <td><?= htmlspecialchars($p['version']) ?></td>
              <td><?= number_format($p['price']) ?> تومان</td>
              <td>
                <div class="d-flex gap-1 flex-wrap">
                  <a href="edit_product.php?id=<?= $p['id'] ?>"
                     class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-square me-1"></i> ویرایش
                  </a>
                  <a href="products.php?delete=<?= $p['id'] ?>"
                     class="btn btn-danger btn-sm"
                     onclick="return confirm('مطمئنی این محصول حذف شود؟')">
                    <i class="bi bi-trash3 me-1"></i> حذف
                  </a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>
