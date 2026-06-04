</div>
  </main>

  <footer class="app-footer text-center">
    <strong>پنل مدیریت نرم‌افزار استور</strong>
  </footer>

</div>

<!-- OverlayScrollbars -->
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es5.min.js"></script>
<!-- AdminLTE -->
<script src="/software_store/assets/adminlte/js/adminlte.min.js"></script>
</body>
<script>
document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
  const select    = wrapper.querySelector('select');
  const display   = wrapper.querySelector('.custom-select');
  const dropdown  = wrapper.querySelector('.custom-select-dropdown');
  const options   = select.querySelectorAll('option');

  // ساخت آپشن‌ها
  options.forEach(opt => {
    const div = document.createElement('div');
    div.textContent  = opt.textContent;
    div.dataset.value = opt.value;
    dropdown.appendChild(div);

    div.addEventListener('click', () => {
      select.value     = opt.value;
      display.textContent = opt.textContent;
      wrapper.classList.remove('custom-select-open');
    });
  });

  // باز و بسته کردن
  display.addEventListener('click', () => {
    wrapper.classList.toggle('custom-select-open');
  });

  // بستن با کلیک بیرون
  document.addEventListener('click', e => {
    if (!wrapper.contains(e.target)) {
      wrapper.classList.remove('custom-select-open');
    }
  });

  // مقدار پیشفرض
  display.textContent = select.options[select.selectedIndex]?.textContent || '-- انتخاب کن --';
});
</script>
</html>