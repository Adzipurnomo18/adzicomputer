// smooth nav click (only for hash links; router uses page=)
(document.querySelectorAll('.nav a.link')||[]).forEach(a=>a.addEventListener('click',e=>{
  if(a.getAttribute('href') && a.getAttribute('href').startsWith('#')){
    e.preventDefault(); const id=a.getAttribute('href'); const el=document.querySelector(id); if(el) el.scrollIntoView({behavior:'smooth'});
  }
}));
// active link + auto hide on scroll (for one-page sections too)
const sections=[...document.querySelectorAll('section')]; const links=[...document.querySelectorAll('.nav a.link')];
const onScroll=()=>{const y=scrollY+120; let cur=sections[0]?.id; sections.forEach(s=>{if(y>=s.offsetTop) cur=s.id}); links.forEach(l=>l.classList.toggle('active', l.getAttribute('href')==='#'+cur));}
addEventListener('scroll', onScroll); onScroll();
let lastY=scrollY,ticking=false,navEl=document.getElementById('nav');
function autoHide(){const y=scrollY; if(y>lastY&&y>120) navEl.classList.add('hide'); else navEl.classList.remove('hide'); lastY=y;}
addEventListener('scroll',()=>{if(!ticking){requestAnimationFrame(()=>{autoHide(); ticking=false;}); ticking=true;}});
// reveal
const io=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting)e.target.classList.add('show')}),{threshold:.13});
(document.querySelectorAll('.reveal')||[]).forEach(el=>io.observe(el));
// minimal particles
const c=document.getElementById('particles'); if(c){const ctx=c.getContext('2d'); let W,H,P=[],N=88,connect=120; function rs(){W=c.width=innerWidth; H=c.height=innerHeight;} rs(); addEventListener('resize',rs); function rand(a,b){return Math.random()*(b-a)+a} for(let i=0;i<N;i++){P.push({x:rand(0,W),y:rand(0,H),vx:rand(-.4,.4),vy:rand(-.4,.4)})} function loop(){ctx.clearRect(0,0,W,H); ctx.fillStyle='rgba(108,240,255,.9)'; for(const p of P){p.x+=p.vx;p.y+=p.vy;if(p.x<0||p.x>W)p.vx*=-1;if(p.y<0||p.y>H)p.vy*=-1;ctx.beginPath();ctx.arc(p.x,p.y,1.2,0,Math.PI*2);ctx.fill();} for(let i=0;i<N;i++){for(let j=i+1;j<N;j++){const a=P[i],b=P[j];const d=Math.hypot(a.x-b.x,a.y-b.y); if(d<connect){ctx.strokeStyle='rgba(177,92,255,'+(1-d/connect)*.25+')';ctx.lineWidth=1;ctx.beginPath();ctx.moveTo(a.x,a.y);ctx.lineTo(b.x,b.y);ctx.stroke();}}} requestAnimationFrame(loop);} loop();}
// Trigger animasi per-halaman saat halaman tampil
(function(){
  function triggerPageEnter(){
    // reset lalu pasang ulang supaya bisa retrigger saat navigasi
    document.body.classList.remove('page-enter');
    // force reflow
    void document.body.offsetWidth;
    document.body.classList.add('page-enter');
    // lepas class setelah selesai
    setTimeout(()=>document.body.classList.remove('page-enter'), 900);
  }
  window.addEventListener('pageshow', triggerPageEnter);
})();

// Mobile nav: clone existing links into a mobile menu, handle burger & backdrop
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    const nav = document.getElementById('nav');
    if(!nav) return;

    // ensure .nav-links exists (for mobile dropdown); clone anchors to avoid changing server markup
    let navLinks = nav.querySelector('.nav-links');
    if(!navLinks){
      navLinks = document.createElement('div');
      navLinks.className = 'nav-links';

      // clone page links
      const links = nav.querySelectorAll('a.link');
      links.forEach(a=> navLinks.appendChild(a.cloneNode(true)));

      // clone CTA WhatsApp into wrapper .cta-wa
      const cta = nav.querySelector('a.cta');
      const waWrap = document.createElement('div'); waWrap.className = 'cta-wa';
      if(cta) waWrap.appendChild(cta.cloneNode(true));
      navLinks.appendChild(waWrap);

      nav.appendChild(navLinks);
    }

    const burger = nav.querySelector('.nav-burger');
    const backdrop = document.querySelector('.nav-backdrop');

    function closeNav(){ nav.classList.remove('open'); }
    function openNav(){ nav.classList.add('open'); }

    if(burger){
      burger.addEventListener('click', function(e){
        e.preventDefault(); nav.classList.toggle('open');
      });
    }

    if(backdrop){
      backdrop.addEventListener('click', function(){ closeNav(); });
    }

    // close when a mobile link is clicked
    nav.querySelectorAll('.nav-links a').forEach(a=> a.addEventListener('click', function(){ closeNav(); }));

    // accessibility: close on Escape when open
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeNav(); });
  });
})();

// Update active link berdasarkan ?page= parameter di URL
(function(){
  function updateActiveLink(){
    // ambil nilai page dari query string (default: home)
    const params = new URLSearchParams(window.location.search);
    const currentPage = params.get('page') || 'home';
    
    // console.log('Current page:', currentPage); // debugging
    
    // hilangkan active dari semua link
    document.querySelectorAll('.nav a.link').forEach(link => {
      link.classList.remove('active');
    });
    
    // tambah active ke link yang sesuai dengan current page
    // contoh: ?page=home -> tandai link dengan href="index.php?page=home"
    document.querySelectorAll('.nav a.link').forEach(link => {
      const href = link.getAttribute('href') || '';
      // console.log('Checking link:', href, 'for page:', currentPage); // debugging
      if(href.includes('page=' + currentPage)){
        link.classList.add('active');
        // console.log('Marked as active:', href); // debugging
      }
    });
  }
  
  // jalankan saat page load
  document.addEventListener('DOMContentLoaded', updateActiveLink);
  // jalankan juga saat history berubah (jika pakai AJAX/fetch)
  window.addEventListener('popstate', updateActiveLink);
  // jalankan sekaligus saat script dimuat (jika DOM sudah siap)
  updateActiveLink();
})();
