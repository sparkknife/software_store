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
            session_start();
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

<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow">
      <div class="card-body p-4">
        <h4 class="mb-4 text-center">ورود</h4>

        <?php if($error): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label">ایمیل</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">پسورد</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">ورود</button>
        </form>

        <p class="text-center mt-3">
          حساب نداری؟ <a href="register.php">ثبت‌نام</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>