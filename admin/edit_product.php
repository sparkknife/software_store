<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'header.php';

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$id      = intval($_GET['id']);
$error   = '';
$success = '';

// گرفتن محصول
$result  = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header('Location: products.php');
    exit;
}

$categories = mysqli_query($conn, "SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $version     = trim($_POST['version']);
    $price       = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $file_path   = $product['file_path']; // فایل قبلی رو نگه میداره

    // اگه فایل جدید آپلود شد
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $filename = time() . '_' . basename($_FILES['file']['name']);
        $dest     = __DIR__ . '/../downloads/' . $filename;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
            // فایل قدیمی رو حذف کن
            if ($product['file_path']) {
                $old = __DIR__ . '/../downloads/' . $product['file_path'];
                if (file_exists($old)) unlink($old);
            }
            $file_path = $filename;
        }
    }

    if (empty($name) || empty($price) || empty($category_id)) {
        $error = 'فیلدهای الزامی رو پر کن';
    } else {
        $stmt = mysqli_prepare($conn,
            "UPDATE products 
             SET category_id=?, name=?, description=?, version=?, price=?, file_path=?
             WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'isssdsi',
            $category_id, $name, $description, $version, $price, $file_path, $id);

        if (mysqli_stmt_execute($stmt)) {
            $success = 'محصول با موفقیت ویرایش شد!';
            // اطلاعات جدید رو بگیر
            $result  = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
            $product = mysqli_fetch_assoc($result);
        } else {
            $error = 'خطا در ویرایش محصول';
        }
    }
}
?>

<div class="d-flex justify-content-between mb-4">
  <h4>✏️ ویرایش محصول</h4>
  <a href="products.php" class="btn btn-outline-secondary">بازگشت</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">

    <?php if($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">نام محصول *</label>
        <input type="text" name="name" class="form-control" 
               value="<?= htmlspecialchars($product['name']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">دسته‌بندی *</label>
        <div class="mb-3">
  <label class="form-label">دسته‌بندی *</label>
  <div class="custom-select-wrapper">
    <div class="custom-select">-- انتخاب دسته‌بندی --</div>
    <div class="custom-select-dropdown"></div>
    <select name="category_id" required>
      <option value="">-- انتخاب دسته‌بندی --</option>
      <?php while($cat = mysqli_fetch_assoc($categories)): ?>
        <option value="<?= $cat['id'] ?>">
          <?= htmlspecialchars($cat['name']) ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>
</div>
      </div>
      <div class="mb-3">
        <label class="form-label">توضیحات</label>
        <textarea name="description" class="form-control" rows="3"><?= 
          htmlspecialchars($product['description']) 
        ?></textarea>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">نسخه</label>
          <input type="text" name="version" class="form-control" 
                 value="<?= htmlspecialchars($product['version']) ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">قیمت (تومان) *</label>
          <input type="number" name="price" class="form-control" 
                 value="<?= $product['price'] ?>" required>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">فایل جدید نرم‌افزار</label>
        <input type="file" name="file" class="form-control">
        <?php if($product['file_path']): ?>
          <small class="text-success mt-1 d-block">
            ✅ فایل فعلی: <?= $product['file_path'] ?>
          </small>
        <?php else: ?>
          <small class="text-muted mt-1 d-block">هنوز فایلی آپلود نشده</small>
        <?php endif; ?>
      </div>
      <button type="submit" class="btn btn-warning w-100">ذخیره تغییرات</button>
    </form>

  </div>
</div>

<?php require_once 'footer.php'; ?>