<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/db.php';
require_once 'header.php';

// گرفتن همه سفارش‌ها همراه با اطلاعات کاربر سفارش‌دهنده
$orders = mysqli_query(
    $conn,
    "SELECT o.*, u.name as user_name, u.email
     FROM orders o
     JOIN users u ON o.user_id = u.id
     ORDER BY o.created_at DESC"
);
?>

<!-- سربرگ صفحه سفارش‌ها -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0"><i class="bi bi-receipt me-1"></i> مدیریت سفارشات</h4>
</div>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 admin-table">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>کاربر</th>
            <th>ایمیل</th>
            <th>مبلغ</th>
            <th>وضعیت</th>
            <th>تاریخ</th>
          </tr>
        </thead>
        <tbody>
          <?php while($o = mysqli_fetch_assoc($orders)): ?>
            <tr>
              <td><?= $o['id'] ?></td>
              <td class="fw-semibold"><?= htmlspecialchars($o['user_name']) ?></td>
              <td><?= htmlspecialchars($o['email']) ?></td>
              <td class="text-success fw-bold"><?= number_format($o['total']) ?> تومان</td>
              <td>
                <span class="badge bg-success"><?= htmlspecialchars($o['status']) ?></span>
              </td>
              <td><?= htmlspecialchars($o['created_at']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>
