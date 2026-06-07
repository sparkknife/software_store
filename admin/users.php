<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'header.php';

// گرفتن کاربران از جدیدترین به قدیمی‌ترین
$users = mysqli_query(
    $conn,
    "SELECT * FROM users ORDER BY created_at DESC"
);
?>

<!-- سربرگ صفحه کاربران -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0"><i class="bi bi-people me-1"></i> مدیریت کاربران</h4>
</div>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 admin-table">
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
              <td class="fw-semibold"><?= htmlspecialchars($u['name']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <span class="badge <?= $u['role'] === 'admin' ? 'bg-danger' : 'bg-secondary' ?>">
                  <?= htmlspecialchars($u['role']) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($u['created_at']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>
