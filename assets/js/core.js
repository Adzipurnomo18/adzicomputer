// Smooth scrolling for same-page hash links.
(function(){
  document.querySelectorAll('a[href*="#"]').forEach((a)=>{
    a.addEventListener('click',(e)=>{
      const url = new URL(a.href, window.location.href);
      if(url.pathname !== window.location.pathname || url.search !== window.location.search || !url.hash) return;
      const el = document.querySelector(url.hash);
      if(!el) return;
      e.preventDefault();
      el.scrollIntoView({behavior:'smooth', block:'start'});
      history.replaceState(null,'',url.hash);
    });
  });
})();

// Theme switcher.
(function(){
  const storageKey = 'adzi-theme';

  function getInitialTheme(){
    try {
      const saved = localStorage.getItem(storageKey);
      if(saved === 'dark' || saved === 'light') return saved;
    } catch(e) {}
    return 'light';
  }

  function applyTheme(theme){
    const isDark = theme === 'dark';
    document.documentElement.classList.toggle('theme-dark', isDark);
    document.body?.classList.toggle('dark-mode', isDark);
    document.querySelectorAll('[data-theme-toggle]').forEach((button)=>{
      button.setAttribute('aria-pressed', String(isDark));
      button.setAttribute('aria-label', isDark ? 'Aktifkan light mode' : 'Aktifkan dark mode');
      button.innerHTML = isDark ? '<i class="fa-regular fa-sun"></i>' : '<i class="fa-regular fa-moon"></i>';
    });
    document.querySelectorAll('[data-logo-light][data-logo-dark]').forEach((logo)=>{
      logo.setAttribute('src', isDark ? logo.dataset.logoDark : logo.dataset.logoLight);
    });
    const themeMeta = document.querySelector('meta[name="theme-color"]');
    if(themeMeta) themeMeta.setAttribute('content', isDark ? '#10131c' : '#ef1212');
  }

  let currentTheme = getInitialTheme();
  document.documentElement.classList.toggle('theme-dark', currentTheme === 'dark');

  document.addEventListener('DOMContentLoaded', ()=>{
    applyTheme(currentTheme);
    document.querySelectorAll('[data-theme-toggle]').forEach((button)=>{
      button.addEventListener('click', ()=>{
        currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
        try { localStorage.setItem(storageKey, currentTheme); } catch(e) {}
        applyTheme(currentTheme);
      });
    });
  });
})();

// Active state based on current page.
(function(){
  function normalizePath(pathname){
    return pathname.replace(/\/+$/, '') || '/';
  }

  function pageFromUrl(url){
    const pageParam = url.searchParams.get('page');
    if(pageParam) return pageParam;

    const currentDir = normalizePath(new URL('.', window.location.href).pathname);
    let path = normalizePath(url.pathname);
    if(currentDir !== '/' && path === currentDir) path = '/';
    else if(currentDir !== '/' && path.startsWith(currentDir + '/')){
      path = path.slice(currentDir.length) || '/';
    }

    const firstSegment = path.split('/').filter(Boolean)[0] || 'home';
    return firstSegment === 'home' ? 'home' : firstSegment;
  }

  function updateActiveLink(){
    const currentPage = pageFromUrl(new URL(window.location.href));
    document.querySelectorAll('.nav a.link').forEach((link)=>{
      const href = link.getAttribute('href') || '';
      const url = new URL(href, window.location.href);
      const page = pageFromUrl(url);
      link.classList.toggle('active', page === currentPage && !url.hash);
    });
  }
  document.addEventListener('DOMContentLoaded', updateActiveLink);
  window.addEventListener('popstate', updateActiveLink);
  updateActiveLink();
})();

// Hide navbar while scrolling down.
(function(){
  const navEl = document.getElementById('nav');
  if(!navEl) return;
  let lastY = window.scrollY;
  let ticking = false;
  function autoHide(){
    const y = window.scrollY;
    if(y > lastY && y > 140 && !navEl.classList.contains('open')) navEl.classList.add('hide');
    else navEl.classList.remove('hide');
    lastY = y;
  }
  window.addEventListener('scroll',()=>{
    if(ticking) return;
    requestAnimationFrame(()=>{ autoHide(); ticking = false; });
    ticking = true;
  });
})();

// Reveal animations.
(function(){
  const items = document.querySelectorAll('.reveal');
  if(!items.length) return;
  const io = new IntersectionObserver((entries)=>{
    entries.forEach((entry)=>{
      if(entry.isIntersecting){
        entry.target.classList.add('show');
        io.unobserve(entry.target);
      }
    });
  },{threshold:.12});
  items.forEach((el)=>io.observe(el));
})();

// Mobile navigation.
(function(){
  document.addEventListener('DOMContentLoaded',()=>{
    const nav = document.getElementById('nav');
    const burger = nav?.querySelector('.nav-burger');
    const backdrop = document.querySelector('.nav-backdrop');
    if(!nav || !burger) return;

    function setNav(open){
      nav.classList.toggle('open', open);
      burger.setAttribute('aria-expanded', String(open));
    }
    function closeNav(){ setNav(false); }
    burger.addEventListener('click',(e)=>{
      e.preventDefault();
      if(window.matchMedia('(max-width: 860px)').matches) return;
      setNav(!nav.classList.contains('open'));
      nav.classList.remove('hide');
    });
    backdrop?.addEventListener('click', closeNav);
    nav.querySelectorAll('.nav-menu a').forEach((a)=>a.addEventListener('click', closeNav));
    document.addEventListener('keydown',(e)=>{ if(e.key === 'Escape') closeNav(); });
  });
})();
