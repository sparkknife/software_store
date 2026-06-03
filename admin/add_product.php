<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once '../includes/header.php';

$error   = '';
$success = '';

$categories = mysqli_query($conn, "SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $version     = trim($_POST['version']);
    $price       = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $file_path   = null;

    // آپلود فایل
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $filename  = time() . '_' . basename($_FILES['file']['name']);
        $dest      = __DIR__ . '/../downloads/' . $filename;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
            $file_path = $filename;
        }
    }

    if (empty($name) || empty($price) || empty($category_id)) {
        $error = 'فیلدهای الزامی رو پر کن';
    } else {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO products (category_id, name, description, version, price, file_path) 
             VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'isssds', 
            $category_id, $name, $description, $version, $price, $file_path);

        if (mysqli_stmt_execute($stmt)) {
            $success = 'محصول با موفقیت اضافه شد!';
        } else {
            $error = 'خطا در ثبت محصول';
        }
    }
}
?>

<div class="d-flex justify-content-between mb-4">
  <h4>➕ افزودن محصول</h4>
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
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">دسته‌بندی *</label>
        <select name="category_id" class="form-select" required>
          <?php while($cat = mysqli_fetch_assoc($categories)): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">توضیحات</label>
        <textarea name="description" class="form-control" rows="3"></textarea>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">نسخه</label>
          <input type="text" name="version" class="form-control" placeholder="v1.0">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">قیمت (تومان) *</label>
          <input type="number" name="price" class="form-control" required>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">فایل نرم‌افزار</label>
        <input type="file" name="file" class="form-control">
      </div>
      <button type="submit" class="btn btn-success w-100">ثبت محصول</button>
    </form>

  </div>
</div>

<?php require_once '../includes/footer.php'; ?>