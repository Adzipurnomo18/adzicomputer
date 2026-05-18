// ===== Simple Lightbox (image + youtube) =====
(function(){
  const SEL = '.gallery-grid img, .gallery-card img, a[data-lightbox]';
  let items = []; // {type:'img'|'yt', src:'...', caption:'...', el:Element}
  let current = 0;

  // build overlay
  const backdrop = document.createElement('div');
  backdrop.className = 'lb-backdrop';
  backdrop.innerHTML = `
    <div class="lb-wrap" role="dialog" aria-modal="true">
      <div class="lb-media"></div>
      <div class="lb-caption"></div>
      <button class="lb-close" aria-label="Tutup">✕</button>
      <button class="lb-prev"  aria-label="Sebelumnya">‹</button>
      <button class="lb-next"  aria-label="Berikutnya">›</button>
    </div>`;
  document.body.appendChild(backdrop);
  const media = backdrop.querySelector('.lb-media');
  const caption = backdrop.querySelector('.lb-caption');

  function collect(){
    items = [];
    // 1) <img> di grid/galeri
    document.querySelectorAll('.gallery-grid img, .gallery-card img').forEach(img=>{
      items.push({
        type: 'img',
        src: img.getAttribute('data-full') || img.src,
        caption: img.getAttribute('data-caption') || img.alt || '',
        el: img
      });
      img.style.cursor = 'zoom-in';
      img.addEventListener('click', () => openIndex(items.findIndex(it => it.el === img)));
    });
    // 2) <a data-lightbox data-src="..."> (opsional utk video YouTube)
    document.querySelectorAll('a[data-lightbox]').forEach(a=>{
      const ytid = a.dataset.ytid || '';
      const yt = ytid || /youtube\.com|youtu\.be/.test(a.dataset.src||'');
      items.push({
        type: yt ? 'yt' : 'img',
        src: a.dataset.src || a.href,
        ytid,
        caption: a.dataset.caption || a.title || '',
        el: a
      });
      a.addEventListener('click', (e)=>{ e.preventDefault(); openIndex(items.findIndex(it => it.el === a)); });
    });
  }

  function openIndex(i){
    current = (i+items.length) % items.length;
    const it = items[current];
    // render
    media.innerHTML = '';
    if(it.type === 'yt'){
      const id = it.ytid || extractYT(it.src);
      const iframe = document.createElement('iframe');
      iframe.width = '100%'; iframe.height = '100%';
      iframe.allow =
        'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
      iframe.allowFullscreen = true;
      iframe.src = `https://www.youtube.com/embed/${id}?rel=0&showinfo=0&autoplay=1`;
      media.appendChild(iframe);
    } else {
      const img = new Image();
      img.src = it.src;
      img.alt = it.caption || '';
      media.appendChild(img);
    }
    caption.textContent = it.caption || '';
    backdrop.classList.add('show');
  }

  function close(){ backdrop.classList.remove('show'); media.innerHTML=''; }

  function next(n=1){ openIndex(current + n); }

  // helpers
  function extractYT(url){
    const m = url.match(/(?:v=|\/)([0-9A-Za-z_-]{11})(?:\?|&|$)/);
    return m ? m[1] : url;
  }

  // events
  backdrop.addEventListener('click', (e)=>{ if(e.target === backdrop) close(); });
  backdrop.querySelector('.lb-close').addEventListener('click', close);
  backdrop.querySelector('.lb-prev').addEventListener('click', ()=>next(-1));
  backdrop.querySelector('.lb-next').addEventListener('click', ()=>next(1));
  document.addEventListener('keydown', (e)=>{
    if(!backdrop.classList.contains('show')) return;
    if(e.key==='Escape') close();
    if(e.key==='ArrowLeft') next(-1);
    if(e.key==='ArrowRight') next(1);
  });

  // init
  window.addEventListener('DOMContentLoaded', collect);
  // kalau grids di-render ulang, panggil collect() lagi
  window.refreshLightbox = collect;
})();
