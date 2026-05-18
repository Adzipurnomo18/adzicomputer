// ===== Simple Lightbox (image + youtube) =====
(function(){
  let items = [];
  let current = 0;

  const backdrop = document.createElement('div');
  backdrop.className = 'lb-backdrop';
  backdrop.innerHTML = `
    <div class="lb-wrap" role="dialog" aria-modal="true">
      <div class="lb-media"></div>
      <div class="lb-caption"></div>
      <button class="lb-close" aria-label="Tutup">&times;</button>
      <button class="lb-prev" aria-label="Sebelumnya">&lsaquo;</button>
      <button class="lb-next" aria-label="Berikutnya">&rsaquo;</button>
    </div>`;
  document.body.appendChild(backdrop);

  const media = backdrop.querySelector('.lb-media');
  const caption = backdrop.querySelector('.lb-caption');

  function collect(){
    items = [];
    document.querySelectorAll('.gallery-grid img, .gallery-card img').forEach((img)=>{
      items.push({
        type:'img',
        src:img.getAttribute('data-full') || img.src,
        caption:img.getAttribute('data-caption') || img.alt || '',
        el:img
      });
      img.style.cursor = 'zoom-in';
      img.addEventListener('click',()=>openIndex(items.findIndex((it)=>it.el === img)));
    });

    document.querySelectorAll('a[data-lightbox]').forEach((a)=>{
      const ytid = a.dataset.ytid || '';
      const isYoutube = ytid || /youtube\.com|youtu\.be/.test(a.dataset.src || '');
      items.push({
        type:isYoutube ? 'yt' : 'img',
        src:a.dataset.src || a.href,
        ytid,
        caption:a.dataset.caption || a.title || '',
        el:a
      });
      a.addEventListener('click',(e)=>{
        e.preventDefault();
        openIndex(items.findIndex((it)=>it.el === a));
      });
    });
  }

  function openIndex(i){
    if(!items.length) return;
    current = (i + items.length) % items.length;
    const it = items[current];
    media.innerHTML = '';
    document.documentElement.style.overflow = 'hidden';

    if(it.type === 'yt'){
      const iframe = document.createElement('iframe');
      iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
      iframe.allowFullscreen = true;
      iframe.src = `https://www.youtube.com/embed/${it.ytid || extractYT(it.src)}?rel=0&showinfo=0&autoplay=1`;
      media.appendChild(iframe);
    } else {
      const img = new Image();
      img.src = it.src;
      img.alt = it.caption || '';
      media.appendChild(img);
    }

    caption.textContent = it.caption || '';
    caption.hidden = !caption.textContent;
    backdrop.classList.add('show');
  }

  function close(){
    backdrop.classList.remove('show');
    media.innerHTML = '';
    document.documentElement.style.overflow = '';
  }

  function next(n){ openIndex(current + n); }

  function extractYT(url){
    const m = url.match(/(?:v=|\/)([0-9A-Za-z_-]{11})(?:\?|&|$)/);
    return m ? m[1] : url;
  }

  backdrop.addEventListener('click',(e)=>{ if(e.target === backdrop) close(); });
  backdrop.querySelector('.lb-close').addEventListener('click', close);
  backdrop.querySelector('.lb-prev').addEventListener('click',()=>next(-1));
  backdrop.querySelector('.lb-next').addEventListener('click',()=>next(1));
  document.addEventListener('keydown',(e)=>{
    if(!backdrop.classList.contains('show')) return;
    if(e.key === 'Escape') close();
    if(e.key === 'ArrowLeft') next(-1);
    if(e.key === 'ArrowRight') next(1);
  });

  window.addEventListener('DOMContentLoaded', collect);
  window.refreshLightbox = collect;
})();
