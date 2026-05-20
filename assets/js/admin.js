// Admin interactions.
(function(){
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
