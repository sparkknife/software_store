<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = 'همه فیلدها الزامی هستند';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'ایمیل معتبر نیست';
    } elseif (strlen($password) < 6) {
        $error = 'پسورد باید حداقل ۶ کاراکتر باشد';
    } elseif ($password !== $confirm) {
        $error = 'پسورد و تکرار آن یکسان نیستند';
    } else {
        // چک کن ایمیل قبلاً ثبت شده یا نه
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = 'این ایمیل قبلاً ثبت شده است';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = mysqli_prepare($conn, 
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, 'sss', $name, $email, $hashed);

            if (mysqli_stmt_execute($stmt2)) {
                $success = 'ثبت‌نام موفق! الان میتونی وارد بشی';
            } else {
                $error = 'خطا در ثبت‌نام، دوباره امتحان کن';
            }
        }
    }
}
?>

<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow">
      <div class="card-body p-4">
        <h4 class="mb-4 text-center">ثبت‌نام</h4>

        <?php if($error): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if($success): ?>
          <div class="alert alert-success">
            <?= $success ?>
            <a href="login.php">ورود</a>
          </div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label">نام</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">ایمیل</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">پسورد</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">تکرار پسورد</label>
            <input type="password" name="confirm" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-success w-100">ثبت‌نام</button>
        </form>

        <p class="text-center mt-3">
          حساب داری؟ <a href="login.php">ورود</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>