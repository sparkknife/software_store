<?php

require_once 'auth.php';
require_once '../config/db.php';
require_once 'header.php';

// حذف محصول
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    header('Location: products.php');
    exit;
}

$products = mysqli_query($conn,
    "SELECT p.*, c.name as cat_name 
     FROM products p 
     JOIN categories c ON p.category_id = c.id 
     ORDER BY p.id DESC");
?>

<div class="d-flex justify-content-between mb-4">
  <h4>📦 مدیریت محصولات</h4>
  <a href="add_product.php" class="btn btn-success">+ افزودن محصول</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <table class="table table-hover">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>نام</th>
          <th>دسته‌بندی</th>
          <th>نسخه</th>
          <th>قیمت</th>
          <th>عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php while($p = mysqli_fetch_assoc($products)): ?>
          <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['cat_name']) ?></td>
            <td><?= htmlspecialchars($p['version']) ?></td>
            <td><?= number_format($p['price']) ?> تومان</td>
            <td>
              <a href="edit_product.php?id=<?= $p['id'] ?>" 
                 class="btn btn-warning btn-sm">ویرایش</a>
              <a href="products.php?delete=<?= $p['id'] ?>" 
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('مطمئنی؟')">حذف</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'footer.php'; ?>