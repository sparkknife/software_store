<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = 'همه فیلدها الزامی هستند';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: /software_store/admin/index.php');
            } else {
                header('Location: /software_store/index.php');
            }
            exit;
        } else {
            $error = 'ایمیل یا پسورد اشتباه است';
        }
    }
}
?>

<div class="row justify-content-center mt-5">
  <div class="col-md-5 col-lg-4">
    <div class="card auth-card">
      <div class="card-header">
        <i class="bi bi-disc-fill" style="font-size:2.5rem"></i>
        <h4 class="mt-2">ورود به حساب</h4>
        <p class="opacity-75 mb-0 small">خوش برگشتی!</p>
      </div>
      <div class="card-body">

        <?php if($error): ?>
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle me-1"></i><?= $error ?>
          </div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label">
              <i class="bi bi-envelope me-1"></i> ایمیل
            </label>
            <input type="email" name="email" class="form-control"
                   placeholder="example@email.com" required>
          </div>
          <div class="mb-4">
            <label class="form-label">
              <i class="bi bi-lock me-1"></i> پسورد
            </label>
            <input type="password" name="password" class="form-control"
                   placeholder="••••••••" required>
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
