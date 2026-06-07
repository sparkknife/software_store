    </div>
  </main>

  <!-- فوتر پنل ادمین -->
  <footer class="app-footer text-center">
    <strong>پنل مدیریت نرم‌افزار استور</strong>
  </footer>

</div>

<!-- اسکریپت‌های AdminLTE -->
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es5.min.js"></script>
<script src="/software_store/assets/adminlte/js/adminlte.min.js"></script>

<script>
// تبدیل select ساده به select سفارشی در فرم‌های ادمین
document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
  const select   = wrapper.querySelector('select');
  const display  = wrapper.querySelector('.custom-select');
  const dropdown = wrapper.querySelector('.custom-select-dropdown');

  if (!select || !display || !dropdown) {
    return;
  }

  const options = Array.from(select.querySelectorAll('option'));

  options.forEach(opt => {
    const div = document.createElement('div');
    div.textContent = opt.textContent;
    div.dataset.value = opt.value;

    if (opt.selected) {
      div.classList.add('active');
    }

    dropdown.appendChild(div);

    div.addEventListener('click', () => {
      select.value = opt.value;
      display.textContent = opt.textContent;
      dropdown.querySelectorAll('div').forEach(item => item.classList.remove('active'));
      div.classList.add('active');
      wrapper.classList.remove('custom-select-open');
      select.dispatchEvent(new Event('change', { bubbles: true }));
    });
  });

  display.addEventListener('click', event => {
    event.stopPropagation();
    document.querySelectorAll('.custom-select-open').forEach(openWrapper => {
      if (openWrapper !== wrapper) {
        openWrapper.classList.remove('custom-select-open');
      }
    });
    wrapper.classList.toggle('custom-select-open');
  });

  document.addEventListener('click', event => {
    if (!wrapper.contains(event.target)) {
      wrapper.classList.remove('custom-select-open');
    }
  });

  display.textContent = select.options[select.selectedIndex]?.textContent || '-- انتخاب کن --';
});
</script>
</body>
</html>
