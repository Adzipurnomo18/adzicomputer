// Home specific enhancements
(function(){
  const slider = document.querySelector('[data-testimonial-slider]');
  if(!slider) return;

  const slides = Array.from(slider.querySelectorAll('.testimonial-track img'));
  const dots = Array.from(document.querySelectorAll('.testimonial-dots button'));
  const prev = slider.querySelector('.testimonial-nav.prev');
  const next = slider.querySelector('.testimonial-nav.next');
  let current = 0;
  let timer = null;

  function show(index){
    current = (index + slides.length) % slides.length;
    slides.forEach((slide, i)=>slide.classList.toggle('active', i === current));
    dots.forEach((dot, i)=>dot.classList.toggle('active', i === current));
  }

  function restart(){
    if(timer) window.clearInterval(timer);
    timer = window.setInterval(()=>show(current + 1), 5500);
  }

  prev?.addEventListener('click',()=>{ show(current - 1); restart(); });
  next?.addEventListener('click',()=>{ show(current + 1); restart(); });
  dots.forEach((dot, i)=>dot.addEventListener('click',()=>{ show(i); restart(); }));

  show(0);
  restart();
})();

(function(){
  const openButtons = document.querySelectorAll('[data-open-gallery]');
  const modals = document.querySelectorAll('[data-gallery-modal]');
  if(!openButtons.length || !modals.length) return;

  function openModal(id){
    const modal = document.querySelector(`[data-gallery-modal="${id}"]`);
    if(!modal) return;
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
    document.documentElement.style.overflow = 'hidden';
    if(typeof window.refreshLightbox === 'function') window.refreshLightbox();
  }

  function closeModal(modal){
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    document.documentElement.style.overflow = '';
  }

  openButtons.forEach((button)=>{
    button.addEventListener('click',()=>openModal(button.dataset.openGallery));
  });

  modals.forEach((modal)=>{
    modal.querySelectorAll('[data-close-gallery]').forEach((button)=>{
      button.addEventListener('click',()=>closeModal(modal));
    });
    modal.addEventListener('click',(event)=>{
      if(event.target === modal) closeModal(modal);
    });
  });

  document.addEventListener('keydown',(event)=>{
    if(event.key !== 'Escape') return;
    document.querySelectorAll('.inline-gallery-modal.open').forEach(closeModal);
  });
})();

(function(){
  const counters = document.querySelectorAll('[data-count-to]');
  if(!counters.length) return;

  function animateCounter(el){
    const target = Number(el.dataset.countTo || 0);
    const decimals = Number(el.dataset.countDecimals || 0);
    const suffix = el.dataset.countSuffix || '';
    const duration = 1400;
    const start = performance.now();

    function frame(now){
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      const value = target * eased;
      el.textContent = value.toFixed(decimals) + suffix;
      if(progress < 1){
        requestAnimationFrame(frame);
      } else {
        el.textContent = target.toFixed(decimals) + suffix;
      }
    }

    requestAnimationFrame(frame);
  }

  const observer = new IntersectionObserver((entries)=>{
    entries.forEach((entry)=>{
      if(!entry.isIntersecting || entry.target.dataset.counted === '1') return;
      entry.target.dataset.counted = '1';
      animateCounter(entry.target);
    });
  },{threshold:.45});

  counters.forEach((counter)=>observer.observe(counter));
})();
