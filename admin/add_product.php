<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/db.php';

$error       = '';
$success     = '';
$name        = '';
$description = '';
$version     = '';
$price       = '';
$category_id = 0;

// وقتی فرم ارسال شد، محصول جدید را ثبت کن
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // گرفتن مقدارهای فرم برای اعتبارسنجی و نمایش دوباره در صورت خطا
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $version     = trim($_POST['version'] ?? '');
    $price       = floatval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $file_path   = null;
    $image_path  = null;

    // فیلدهای ضروری محصول
    if ($name === '' || $price <= 0 || $category_id <= 0) {
        $error = 'فیلدهای الزامی را پر کنید';
    }

    // آپلود عکس محصول، اگر کاربر عکسی انتخاب کرده باشد
    if (!$error && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime    = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($mime, $allowed, true)) {
            $error = 'فرمت عکس محصول معتبر نیست';
        } else {
            $uploadDir = __DIR__ . '/../uploads/products/';

            // اگر پوشه آپلود وجود نداشت، بساز
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // اسم یکتا برای جلوگیری از تداخل اسم فایل‌ها
            $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $imgName = time() . '_' . uniqid() . '.' . $ext;
            $imgDest = $uploadDir . $imgName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $imgDest)) {
                $image_path = $imgName;
            } else {
                $error = 'خطا در آپلود عکس محصول';
            }
        }
    }

    // آپلود فایل نرم‌افزار، اگر کاربر فایلی انتخاب کرده باشد
    if (!$error && isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $downloadDir = __DIR__ . '/../downloads/';

        // اگر پوشه دانلود وجود نداشت، بساز
        if (!is_dir($downloadDir)) {
            mkdir($downloadDir, 0755, true);
        }

        $filename = time() . '_' . basename($_FILES['file']['name']);
        $dest     = $downloadDir . $filename;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
            $file_path = $filename;
        } else {
            $error = 'خطا در آپلود فایل محصول';
        }
    }

    // ذخیره محصول در دیتابیس
    if (!$error) {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO products (category_id, name, description, version, price, image, file_path)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param(
            $stmt,
            'isssdss',
            $category_id,
            $name,
            $description,
            $version,
            $price,
            $image_path,
            $file_path
        );

        if (mysqli_stmt_execute($stmt)) {
            $success     = 'محصول با موفقیت اضافه شد!';
            $name        = '';
            $description = '';
            $version     = '';
            $price       = '';
            $category_id = 0;
        } else {
            // اگر ذخیره دیتابیس شکست خورد، فایل‌های آپلودشده را پاک کن
            if ($image_path) {
                @unlink(__DIR__ . '/../uploads/products/' . $image_path);
            }
            if ($file_path) {
                @unlink(__DIR__ . '/../downloads/' . $file_path);
            }
            $error = 'خطا در ثبت محصول';
        }
    }
}

// گرفتن دسته‌بندی‌ها برای select فرم
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

require_once 'header.php';
?>

<!-- سربرگ صفحه -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0"><i class="bi bi-plus-circle me-1"></i> افزودن محصول</h4>
  <a href="products.php" class="btn btn-outline-secondary">بازگشت</a>
</div>

<div class="card shadow-sm admin-form-card">
  <div class="card-body">

    <!-- پیام خطا یا موفقیت -->
    <?php if($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <!-- نام محصول -->
      <div class="mb-3">
        <label class="form-label">نام محصول *</label>
        <input type="text" name="name" class="form-control"
               value="<?= htmlspecialchars($name) ?>" required>
      </div>

      <!-- دسته‌بندی محصول -->
      <div class="mb-3">
        <label class="form-label">دسته‌بندی *</label>
        <div class="custom-select-wrapper">
          <div class="custom-select">-- انتخاب دسته‌بندی --</div>
          <div class="custom-select-dropdown"></div>
          <select name="category_id" required>
            <option value="">-- انتخاب دسته‌بندی --</option>
            <?php while($cat = mysqli_fetch_assoc($categories)): ?>
              <option value="<?= $cat['id'] ?>" <?= ((int)$cat['id'] === (int)$category_id) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>

      <!-- توضیحات محصول -->
      <div class="mb-3">
        <label class="form-label">توضیحات</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description) ?></textarea>
      </div>

      <!-- نسخه و قیمت -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">نسخه</label>
          <input type="text" name="version" class="form-control"
                 value="<?= htmlspecialchars($version) ?>" placeholder="v1.0">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">قیمت (تومان) *</label>
          <input type="number" name="price" class="form-control"
                 value="<?= htmlspecialchars((string)$price) ?>" required>
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
               style="width:120px;height:120px;object-fit:cover;border-radius:12px;">
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

<script>
// پیش‌نمایش عکس قبل از ذخیره محصول
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

<?php require_once 'footer.php'; ?>
