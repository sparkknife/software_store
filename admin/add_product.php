<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'header.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $version     = trim($_POST['version']);
    $price       = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $file_path   = null;
    $image_path = null;

// آپلود عکس
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $allowed     = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $mime        = mime_content_type($_FILES['image']['tmp_name']);
    if (in_array($mime, $allowed)) {
        $ext        = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imgName    = time() . '_' . uniqid() . '.' . $ext;
        $imgDest    = __DIR__ . '/../uploads/products/' . $imgName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imgDest)) {
            $image_path = $imgName;
        }
    }
    }
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $filename = time() . '_' . basename($_FILES['file']['name']);
        $dest     = __DIR__ . '/../downloads/' . $filename;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
            $file_path = $filename;
        }
    }

    if (empty($name) || empty($price) || empty($category_id)) {
        $error = 'فیلدهای الزامی رو پر کن';
    } else {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO products (category_id, name, description, version, price, image, file_path) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'isssdss',
            $category_id, $name, $description, $version, $price, $image_path, $file_path);

        if (mysqli_stmt_execute($stmt)) {
            $success = 'محصول با موفقیت اضافه شد!';
        } else {
            $error = 'خطا در ثبت محصول';
        }
    }
}

// دسته‌بندی‌ها رو اینجا میگیریم (بعد از POST)
$categories = mysqli_query($conn, "SELECT * FROM categories");
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

      <!-- عکس محصول -->
<div class="mb-3">
  <label class="form-label">
    <i class="bi bi-image me-1"></i> عکس محصول
  </label>
  <input type="file" name="image" class="form-control" accept="image/*"
         onchange="previewImage(this)">
  <div id="imagePreview" class="mt-2" style="display:none">
    <img id="preview" src="" alt="preview" 
         style="width:120px; height:120px; object-fit:cover; border-radius:12px;">
  </div>
</div>

<!-- فایل نرم‌افزار -->
<div class="mb-3">
  <label class="form-label">
    <i class="bi bi-file-earmark-zip me-1"></i> فایل نرم‌افزار
  </label>
  <input type="file" name="file" class="form-control">
</div>
      <button type="submit" class="btn btn-success w-100">ثبت محصول</button>
    </form>

  </div>
</div>

<?php require_once 'footer.php'; ?>

<script>
function previewImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('preview').src = e.target.result;
      document.getElementById('imagePreview').style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>