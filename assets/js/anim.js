// ====== PAGE TRANSITION CONTROLLER ======
(function(){
  const BODY = document.body;
  // Tambah elemen overlay & glow
  const glow = document.createElement('div');
  glow.className = 'page-glow';
  const overlay = document.createElement('div');
  overlay.className = 'page-transition';
  overlay.innerHTML = '<div class="orb"></div>';
  document.documentElement.appendChild(glow);
  document.documentElement.appendChild(overlay);

  // Trigger animasi masuk saat load
  const enter = () => {
    BODY.classList.add('page-enter');
    // Hapus class setelah selesai agar tidak mengganggu reflow lain
    setTimeout(()=> BODY.classList.remove('page-enter'), 700);
  };
  // Panggil saat halaman tampil (termasuk bfcache)
  window.addEventListener('pageshow', enter, { once: true });

  // Intersep klik internal link untuk animasi keluar
  const isInternal = (a) => a.origin === location.origin;
  const shouldIntercept = (a) => {
    // hanya intercept link yang menuju file kita sendiri atau query page=...
    if (!isInternal(a)) return false;
    const href = a.getAttribute('href') || '';
    if (href.startsWith('#')) return false;
    if (a.hasAttribute('data-no-transition')) return false;
    return true;
  };

  document.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (!a || !shouldIntercept(a)) return;
    e.preventDefault();
    const go = () => location.href = a.href;
    BODY.classList.add('page-exit');
    // jeda animasi exit
    setTimeout(go, 300);
  });

  // handle back/forward: hilangkan state exit
  window.addEventListener('popstate', () => BODY.classList.remove('page-exit'));
})();
