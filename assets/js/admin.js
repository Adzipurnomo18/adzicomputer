// Admin interactions.
(function(){
  const menuButton = document.querySelector('.admin-menu-button');
  if(menuButton) {
    menuButton.addEventListener('click', function(){
      document.body.classList.toggle('admin-sidebar-collapsed');
    });
  }

  const adminSearch = document.querySelector('.admin-search input');
  if(adminSearch) {
    const searchForm = adminSearch.closest('form');
    searchForm && searchForm.addEventListener('submit', function(event){
      if(!adminSearch.value.trim()) {
        event.preventDefault();
        adminSearch.focus();
      }
    });

    document.addEventListener('keydown', function(event){
      if((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        adminSearch.focus();
      }
    });
  }

  const profileTrigger = document.querySelector('.admin-profile-trigger');
  const profileMenu = document.querySelector('.admin-profile-menu');
  if(profileTrigger && profileMenu) {
    profileTrigger.addEventListener('click', function(event){
      event.stopPropagation();
      const open = profileMenu.hasAttribute('hidden');
      profileMenu.toggleAttribute('hidden', !open);
      profileTrigger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.addEventListener('click', function(event){
      if(profileMenu.hasAttribute('hidden')) return;
      if(event.target.closest('.admin-profile-menu') || event.target.closest('.admin-profile-trigger')) return;
      profileMenu.setAttribute('hidden', '');
      profileTrigger.setAttribute('aria-expanded', 'false');
    });
  }

  document.addEventListener('click', async (event)=>{
    const trigger = event.target.closest('[data-confirm-logout]');
    if(!trigger) return;

    event.preventDefault();
    const href = trigger.getAttribute('href') || 'logout.php';

    if(!window.Swal){
      if(window.confirm('Apakah Anda yakin ingin logout?')) {
        window.location.href = href;
      }
      return;
    }

    const result = await Swal.fire({
      title: 'Logout dari admin?',
      text: 'Sesi admin akan diakhiri.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, logout',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#ef1212',
      cancelButtonColor: '#6b7280',
      reverseButtons: true,
      focusCancel: true,
    });

    if(result.isConfirmed) {
      window.location.href = href;
    }
  });
})();
