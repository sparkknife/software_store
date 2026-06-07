<?php
require_once 'config/db.php';

// برای پردازش ورود قبل از خروجی HTML، session همین‌جا شروع می‌شود
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$email = '';

// اگر کاربر قبلاً وارد شده باشد، دوباره فرم ورود نشان داده نشود
if (isset($_SESSION['user_id'])) {
    $redirect = ($_SESSION['user_role'] ?? '') === 'admin'
        ? '/software_store/admin/index.php'
        : '/software_store/index.php';
    header("Location: $redirect");
    exit;
}

// بررسی اطلاعات ورود بعد از ارسال فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // اعتبارسنجی اولیه فرم
    if ($email === '' || $password === '') {
        $error = 'همه فیلدها الزامی هستند';
    } else {
        // پیدا کردن کاربر با ایمیل واردشده
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);

        // اگر رمز درست بود، اطلاعات کاربر داخل session ذخیره می‌شود
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: /software_store/admin/index.php');
            } else {
                header('Location: /software_store/index.php');
            }
            exit;
        }

        $error = 'ایمیل یا پسورد اشتباه است';
    }
}

require_once 'includes/header.php';
?>

<div class="auth-shell row justify-content-center">
  <div class="col-md-6 col-lg-4">
    <div class="card auth-card">
      <div class="card-header">
        <i class="bi bi-disc-fill" style="font-size:2.5rem"></i>
        <h4 class="mt-2">ورود به حساب</h4>
        <p class="opacity-75 mb-0 small">خوش برگشتی، ادامه خرید منتظرته</p>
      </div>
      <div class="card-body">

        <!-- نمایش خطای ورود -->
        <?php if($error): ?>
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form method="POST" novalidate>
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

          <!-- رمز عبور کاربر -->
          <div class="mb-4">
            <label class="form-label">
              <i class="bi bi-lock me-1"></i> پسورد
            </label>
            <input type="password" name="password" class="form-control"
                   placeholder="••••••••"
                   autocomplete="current-password" required>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="bi bi-box-arrow-in-right me-1"></i> ورود
          </button>
        </form>

        <hr class="my-3">
        <p class="text-center text-muted mb-0">
          حساب نداری؟
          <a href="register.php" class="text-decoration-none fw-bold">ثبت‌نام</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
