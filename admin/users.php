<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'header.php';

$users = mysqli_query($conn, 
    "SELECT * FROM users ORDER BY created_at DESC");
?>

<h4 class="mb-4">👥 مدیریت کاربران</h4>

<div class="card shadow-sm">
  <div class="card-body">
    <table class="table table-hover">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>نام</th>
          <th>ایمیل</th>
          <th>نقش</th>
          <th>تاریخ ثبت</th>
        </tr>
      </thead>
      <tbody>
        <?php while($u = mysqli_fetch_assoc($users)): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td>
              <span class="badge <?= $u['role'] === 'admin' ? 'bg-danger' : 'bg-secondary' ?>">
                <?= $u['role'] ?>
              </span>
            </td>
            <td><?= $u['created_at'] ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'footer.php'; ?>