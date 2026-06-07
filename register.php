<?php
require_once 'config/db.php';

// برای redirect قبل از خروجی HTML، session همین‌جا شروع می‌شود
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error   = '';
$success = '';
$name    = '';
$email   = '';

// اگر کاربر وارد شده باشد، نیاز به ثبت‌نام دوباره ندارد
if (isset($_SESSION['user_id'])) {
    header('Location: /software_store/index.php');
    exit;
}

// ثبت‌نام کاربر بعد از ارسال فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    // اعتبارسنجی مرحله‌به‌مرحله فرم ثبت‌نام
    if ($name === '' || $email === '' || $password === '' || $confirm === '') {
        $error = 'همه فیلدها الزامی هستند';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'ایمیل معتبر نیست';
    } elseif (strlen($password) < 6) {
        $error = 'پسورد باید حداقل ۶ کاراکتر باشد';
    } elseif ($password !== $confirm) {
        $error = 'پسورد و تکرار آن یکسان نیستند';
    } else {
        // جلوگیری از ثبت ایمیل تکراری
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = 'این ایمیل قبلاً ثبت شده است';
        } else {
            // رمز عبور فقط به صورت hash شده در دیتابیس ذخیره می‌شود
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt2  = mysqli_prepare(
                $conn,
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt2, 'sss', $name, $email, $hashed);

            if (mysqli_stmt_execute($stmt2)) {
                $success = 'ثبت‌نام موفق بود. الان می‌تونی وارد بشی.';
                $name    = '';
                $email   = '';
            } else {
                $error = 'خطا در ثبت‌نام، دوباره امتحان کن';
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-shell row justify-content-center">
  <div class="col-md-6 col-lg-4">
    <div class="card auth-card">
      <div class="card-header">
        <i class="bi bi-rocket-takeoff-fill" style="font-size:2.5rem"></i>
        <h4 class="mt-2">ثبت‌نام</h4>
        <p class="opacity-75 mb-0 small">یه حساب جدید بساز و خریدت رو سریع‌تر انجام بده</p>
      </div>
      <div class="card-body">

        <!-- نمایش پیام خطا یا موفقیت -->
        <?php if($error): ?>
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>
        <?php if($success): ?>
          <div class="alert alert-success">
            <i class="bi bi-check-circle me-1"></i>
            <?= htmlspecialchars($success) ?> <a href="login.php">ورود</a>
          </div>
        <?php endif; ?>

        <form method="POST" novalidate>
          <!-- نام کاربر -->
          <div class="mb-3">
            <label class="form-label">
              <i class="bi bi-person me-1"></i> نام
            </label>
            <input type="text" name="name" class="form-control"
                   placeholder="نام کامل"
                   value="<?= htmlspecialchars($name) ?>"
                   autocomplete="name" required>
          </div>

          <!-- ایمیل کاربر -->
          <div class="mb-3">
            <label class="form-label">
              <i class="bi bi-envelope me-1"></i> ایمیل
            </label>
            <input type="email" name="email" class="form-control"
                   placeholder="example@email.com"
                   value="<?= htmlspecialchars($email) ?>"
                   autocomplete="email" required>
          </div>

          <!-- رمز عبور و تکرار آن -->
          <div class="mb-3">
            <label class="form-label">
              <i class="bi bi-lock me-1"></i> پسورد
            </label>
            <input type="password" name="password" class="form-control"
                   placeholder="حداقل ۶ کاراکتر"
                   autocomplete="new-password" required>
          </div>
          <div class="mb-4">
            <label class="form-label">
              <i class="bi bi-lock-fill me-1"></i> تکرار پسورد
            </label>
            <input type="password" name="confirm" class="form-control"
                   placeholder="••••••••"
                   autocomplete="new-password" required>
          </div>

          <button type="submit" class="btn btn-success w-100 py-2">
            <i class="bi bi-person-check me-1"></i> ثبت‌نام
          </button>
        </form>

        <hr class="my-3">
        <p class="text-center text-muted mb-0">
          حساب داری؟
          <a href="login.php" class="text-decoration-none fw-bold">ورود</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
