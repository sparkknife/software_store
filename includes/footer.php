  </main>
        <!-- فوتر عمومی سایت -->
  <footer class="site-footer">
    <div class="container">
      <div class="row g-4 footer-grid">
        <div class="col-md-4">
          <div class="footer-brand">
            <img src="/software_store/assets/images/logo-vertical.svg" 
            alt="لوگو فوتر" 
            width="230">
            
          </div>
          <p class="footer-about mt-3 mb-0">
            فروشگاه آنلاین نرم‌افزار با دانلود فوری پس از خرید.
          </p>
        </div>

        <div class="col-md-4">
          <h6 class="footer-heading">دسترسی سریع</h6>
          <ul class="footer-links list-unstyled mb-0">
            <li><a href="/software_store/index.php">فروشگاه</a></li>
            <li><a href="/software_store/index.php#products">محصولات</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
              <li><a href="/software_store/orders.php">خریدهای من</a></li>
              <li><a href="/software_store/cart.php">سبد خرید</a></li>
            <?php else: ?>
              <li><a href="/software_store/login.php">ورود</a></li>
              <li><a href="/software_store/register.php">ثبت‌نام</a></li>
            <?php endif; ?>
          </ul>
        </div>

        <div class="col-md-4">
          <h6 class="footer-heading">چرا ما؟</h6>
          <ul class="footer-features list-unstyled mb-0">
            <li><i class="bi bi-shield-check me-1"></i> پرداخت امن</li>
            <li><i class="bi bi-cloud-download me-1"></i> دانلود آنی</li>
            <li><i class="bi bi-headset me-1"></i> پشتیبانی فارسی</li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <p class="mb-0">© <?= date('Y') ?> نرم‌افزار استور</p>
      </div>
    </div>
  </footer>
              <!-- اسکریپت Bootstrap برای منوی موبایل و کامپوننت‌ها -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
