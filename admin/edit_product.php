<?php
require_once 'auth.php';
require_once '../config/db.php';

// اگر آیدی محصول در آدرس نبود، به لیست محصولات برگرد
if (!isset($_GET['id']) || intval($_GET['id']) <= 0) {
    header('Location: products.php');
    exit;
}

// مقدارهای اولیه صفحه
$id      = intval($_GET['id']);
$error   = '';
$success = '';

// گرفتن اطلاعات محصول از دیتابیس
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result  = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

// اگر محصول پیدا نشد، به لیست محصولات برگرد
if (!$product) {
    header('Location: products.php');
    exit;
}

// وقتی فرم ارسال شد، اطلاعات محصول را ویرایش کن
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // گرفتن مقدارهای فرم و نگه داشتن عکس/فایل قبلی تا وقتی مورد جدید ذخیره نشده
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $version     = trim($_POST['version'] ?? '');
    $price       = floatval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $file_path   = $product['file_path'];
    $image_path  = $product['image'];
    $new_image   = null;
    $new_file    = null;

    // چک کردن فیلدهای الزامی
    if ($name === '' || $price <= 0 || $category_id <= 0) {
        $error = 'فیلدهای الزامی را پر کنید';
    }

    // اگر عکس جدید انتخاب شده بود، فرمتش را چک کن و آپلودش کن
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

            // ساخت اسم یکتا برای عکس جدید
            $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $imgName = time() . '_' . uniqid() . '.' . $ext;
            $imgDest = $uploadDir . $imgName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $imgDest)) {
                $image_path = $imgName;
                $new_image  = $imgName;
            } else {
                $error = 'خطا در آپلود عکس محصول';
            }
        }
    }

    // اگر فایل نرم‌افزار جدید انتخاب شده بود، آن را آپلود کن
    if (!$error && isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $downloadDir = __DIR__ . '/../downloads/';

        // اگر پوشه دانلود وجود نداشت، بساز
        if (!is_dir($downloadDir)) {
            mkdir($downloadDir, 0755, true);
        }

        // ساخت اسم فایل و انتقال آن به پوشه downloads
        $filename = time() . '_' . basename($_FILES['file']['name']);
        $dest     = $downloadDir . $filename;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
            $file_path = $filename;
            $new_file  = $filename;
        } else {
            $error = 'خطا در آپلود فایل محصول';
        }
    }

    // اگر خطایی نبود، اطلاعات جدید محصول را در دیتابیس ذخیره کن
    if (!$error) {
        $update = mysqli_prepare(
            $conn,
            "UPDATE products
             SET category_id=?, name=?, description=?, version=?, price=?, image=?, file_path=?
             WHERE id=?"
        );

        mysqli_stmt_bind_param(
            $update,
            'isssdssi',
            $category_id,
            $name,
            $description,
            $version,
            $price,
            $image_path,
            $file_path,
            $id
        );

        if (mysqli_stmt_execute($update)) {
            // بعد از ذخیره موفق، فایل‌های قبلی حذف می‌شوند
            if ($new_image && !empty($product['image'])) {
                @unlink(__DIR__ . '/../uploads/products/' . $product['image']);
            }
            if ($new_file && !empty($product['file_path'])) {
                @unlink(__DIR__ . '/../downloads/' . $product['file_path']);
            }

            $success = 'محصول با موفقیت ویرایش شد!';

            // اطلاعات جدید محصول را دوباره بگیر تا در فرم نمایش داده شود
            $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $result  = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);
        } else {
            // اگر ذخیره دیتابیس شکست خورد، فایل جدید آپلودشده پاک شود
            if ($new_image) {
                @unlink(__DIR__ . '/../uploads/products/' . $new_image);
            }
            if ($new_file) {
                @unlink(__DIR__ . '/../downloads/' . $new_file);
            }
            $error = 'خطا در ویرایش محصول';
        }
    }
}

// گرفتن دسته‌بندی‌ها برای select فرم
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

require_once 'header.php';
?>

<!-- سربرگ صفحه و دکمه بازگشت -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0"><i class="bi bi-pencil-square me-1"></i> ویرایش محصول</h4>
  <a href="products.php" class="btn btn-outline-secondary">بازگشت</a>
</div>

<div class="card shadow-sm admin-form-card">
  <div class="card-body">

    <!-- نمایش پیام خطا یا موفقیت -->
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
               value="<?= htmlspecialchars($product['name']) ?>" required>
      </div>

      <!-- انتخاب دسته‌بندی محصول -->
      <div class="mb-3">
        <label class="form-label">دسته‌بندی *</label>
        <div class="custom-select-wrapper">
          <div class="custom-select">-- انتخاب دسته‌بندی --</div>
          <div class="custom-select-dropdown"></div>
          <select name="category_id" required>
            <option value="">-- انتخاب دسته‌بندی --</option>
            <?php while($cat = mysqli_fetch_assoc($categories)): ?>
              <option value="<?= $cat['id'] ?>" <?= ((int)$cat['id'] === (int)$product['category_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>

      <!-- توضیحات محصول -->
      <div class="mb-3">
        <label class="form-label">توضیحات</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
      </div>

      <!-- نسخه و قیمت محصول -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">نسخه</label>
          <input type="text" name="version" class="form-control"
                 value="<?= htmlspecialchars($product['version']) ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">قیمت (تومان) *</label>
          <input type="number" name="price" class="form-control"
                 value="<?= htmlspecialchars($product['price']) ?>" required>
        </div>
      </div>

      <!-- نمایش عکس فعلی و انتخاب عکس جدید -->
      <div class="mb-3">
        <label class="form-label">
          <i class="bi bi-image me-1"></i> عکس محصول
        </label>

        <?php if(!empty($product['image'])): ?>
          <div class="mb-2">
            <img src="../uploads/products/<?= htmlspecialchars($product['image']) ?>"
                 alt="<?= htmlspecialchars($product['name']) ?>"
                 style="width:100px;height:100px;object-fit:cover;border-radius:12px;">
            <small class="text-muted d-block mt-1">عکس فعلی</small>
          </div>
        <?php endif; ?>

        <input type="file" name="image" class="form-control" accept="image/*"
               onchange="previewImage(this)">
        <div id="imagePreview" class="mt-2" style="display:none">
          <img id="preview" src="" alt="preview"
               style="width:100px;height:100px;object-fit:cover;border-radius:12px;">
        </div>
      </div>

      <!-- نمایش فایل فعلی و انتخاب فایل جدید نرم‌افزار -->
      <div class="mb-3">
        <label class="form-label">فایل جدید نرم‌افزار</label>
        <input type="file" name="file" class="form-control">
        <?php if(!empty($product['file_path'])): ?>
          <small class="text-success mt-1 d-block">
            فایل فعلی: <?= htmlspecialchars($product['file_path']) ?>
          </small>
        <?php else: ?>
          <small class="text-muted mt-1 d-block">هنوز فایلی آپلود نشده</small>
        <?php endif; ?>
      </div>

      <button type="submit" class="btn btn-warning w-100">ذخیره تغییرات</button>
    </form>

  </div>
</div>

<script>
// پیش‌نمایش عکس انتخاب‌شده قبل از ذخیره فرم
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
