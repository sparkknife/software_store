<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'header.php';

$orders = mysqli_query($conn,
    "SELECT o.*, u.name as user_name, u.email 
     FROM orders o 
     JOIN users u ON o.user_id = u.id 
     ORDER BY o.created_at DESC");
?>

<h4 class="mb-4">🧾 مدیریت سفارشات</h4>

<div class="card shadow-sm">
  <div class="card-body">
    <table class="table table-hover">
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
            <td><?= htmlspecialchars($o['user_name']) ?></td>
            <td><?= htmlspecialchars($o['email']) ?></td>
            <td><?= number_format($o['total']) ?> تومان</td>
            <td><span class="badge bg-success"><?= $o['status'] ?></span></td>
            <td><?= $o['created_at'] ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'footer.php'; ?>