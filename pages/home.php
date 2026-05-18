<section class="section-home">
  <div class="container hero">
    <div class="reveal">
      <span class="badge"><i class="fa-regular fa-star"></i> Rating 5.0 — Cepat, Rapih, Bergaransi</span>
      <h1>Solusi cepat & rapi untuk laptop, PC, dan elektronik Anda</h1>
      <p>Kami spesialis perbaikan dengan diagnosa transparan, sparepart jelas, dan pengerjaan rapi.</p>
      <div class="actions">
        <a class="cta" href="index.php?page=katalog"><i class="fa-solid fa-robot"></i> Lihat Katalog</a>
        <a class="btn" href="tel:<?= h($CONFIG['phone']) ?>"><i class="fa-solid fa-phone"></i> Telepon</a>
        <a class="btn" href="<?= h($CONFIG['maps']) ?>" target="_blank"><i class="fa-solid fa-location-dot"></i> Lokasi</a>
      </div>
    </div>

    <div class="panel reveal">
      <div class="grid gallery-grid">
        <?php
        // Ambil 6 item terbaru dari galeri admin
        $items = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($items as $it):
          $caption = $it['caption'] ?? '';
          if ($it['type'] === 'image'):
            $full = 'uploads/gallery/' . $it['path'];
        ?>
          <div class="gallery-card">
            <!-- Penting: bungkus gambar dengan <a class="lb"> supaya lightbox mencegat klik -->
            <a class="lb" href="<?= h($full) ?>" title="<?= h($caption) ?>">
              <img src="<?= h($full) ?>" alt="<?= h($caption) ?>">
            </a>
          </div>
        <?php else: ?>
          <div class="gallery-card">
            <div class="ratio-1x1" style="border-radius:12px; overflow:hidden;">
              <iframe
                src="https://www.youtube.com/embed/<?= h($it['path']) ?>"
                title="<?= h($caption ?: 'Video') ?>"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
                style="width:100%; height:100%; display:block;"
              ></iframe>
            </div>
          </div>
        <?php endif; endforeach; ?>
      </div>

      <p class="note">*Tampilan ambil dari galeri admin (otomatis).</p>
    </div>
  </div>
</section>

<!-- =============== INLINE LIGHTBOX (CSS + JS) =============== -->
<style>
  .lb-overlay{position:fixed;inset:0;z-index:9999;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.62);backdrop-filter:blur(2px);opacity:0;transition:opacity .25s ease}
  .lb-overlay.show{display:flex;opacity:1}
  .lb-overlay.hide{opacity:0}
  .lb-box{position:relative;border-radius:14px;overflow:hidden;background:#0b0f14;border:1px solid rgba(255,255,255,.14);box-shadow:0 24px 120px rgba(0,0,0,.5);transform:translateY(8px) scale(.96);opacity:0;transition:transform .24s cubic-bezier(.2,.8,.2,1),opacity .24s ease}
  .lb-overlay.show .lb-box{transform:translateY(0) scale(1);opacity:1}
  .lb-media{width:min(90vw,820px);max-height:78vh;display:grid;place-items:center;padding:14px}
  .lb-media img,.lb-media iframe{max-width:100%;max-height:70vh;border-radius:12px;display:block}
  .lb-cap{padding:10px 14px;color:#cfe7ff;font:500 14px/1.4 Inter,system-ui,sans-serif;border-top:1px solid rgba(255,255,255,.08);background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.03))}
  .lb-close,.lb-prev,.lb-next{position:absolute;width:42px;height:36px;display:grid;place-items:center;cursor:pointer;color:#eaf1ff;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.18);border-radius:10px;transition:background .15s ease}
  .lb-close:hover,.lb-prev:hover,.lb-next:hover{background:rgba(255,255,255,.14)}
  .lb-close{top:10px;right:10px}
  .lb-prev{top:50%;left:12px;transform:translateY(-50%)}
  .lb-next{top:50%;right:12px;transform:translateY(-50%)}
  .gallery-grid img,.gallery-card img{cursor:zoom-in;pointer-events:auto}
  @media (max-width:768px){.lb-media{width:94vw;max-height:72vh;padding:10px}.lb-media img,.lb-media iframe{max-height:64vh}.lb-cap{font-size:13px}}
</style>
<script>
(function(){
  const overlay = document.createElement('div');
  overlay.className = 'lb-overlay';
  overlay.innerHTML = `
    <div class="lb-box">
      <div class="lb-media"></div>
      <div class="lb-cap"></div>
      <button type="button" class="lb-close" aria-label="Tutup">✕</button>
      <button type="button" class="lb-prev"  aria-label="Sebelumnya">‹</button>
      <button type="button" class="lb-next"  aria-label="Berikutnya">›</button>
    </div>`;

  let list = [], idx = 0;

  function disableScroll(){ document.documentElement.style.overflow='hidden'; }
  function enableScroll(){ document.documentElement.style.overflow=''; }

  function collect(){ list = Array.from(document.querySelectorAll('a.lb')); }

  function open(i){
    idx = (i + list.length) % list.length;
    const a   = list[idx];
    const src = a.getAttribute('href');
    const txt = a.getAttribute('title') || (a.querySelector('img')?.alt || '');

    const media = overlay.querySelector('.lb-media');
    const cap   = overlay.querySelector('.lb-cap');
    
    // Pastikan overlay sudah di-reset dan tidak ada class show
    overlay.classList.remove('show');
    
    // Reset isi
    media.innerHTML = '';
    cap.textContent = txt;

    const img = new Image();
    img.onload = ()=>{ 
      overlay.classList.add('show'); 
      disableScroll(); 
    };
    img.src = src; 
    img.alt = txt;
    media.appendChild(img);
  }

  function close(){
    overlay.classList.remove('show');
    enableScroll();
    // tunggu transisi selesai baru bersihkan isi
    setTimeout(()=>{
      overlay.querySelector('.lb-media').innerHTML='';
      overlay.querySelector('.lb-cap').textContent='';
    }, 300);
  }

  function next(n){ if(overlay.classList.contains('show')) open(idx + n); }

  // Append overlay ke body langsung
  if(document.body) {
    document.body.appendChild(overlay);
  } else {
    document.addEventListener('DOMContentLoaded', ()=> document.body.appendChild(overlay));
  }

  // Satu delegasi event utama untuk semua klik
  document.addEventListener('click', (e)=>{
    // Jangan proses jika tidak dalam lightbox context
    const isInLightbox = e.target.closest('.lb-box') || e.target === overlay;
    const isGalleryLink = e.target.closest('a.lb');
    
    if(!isInLightbox && !isGalleryLink) return;
    
    // Klik gambar untuk buka lightbox
    if(isGalleryLink && !overlay.classList.contains('show')){
      e.preventDefault();
      if(!list.length) collect();
      const i = list.indexOf(isGalleryLink);
      open(i >= 0 ? i : 0);
      return;
    }
    
    if(!overlay.classList.contains('show')) return;
    
    // Klik tombol close
    if(e.target.closest('.lb-close')){
      e.stopPropagation();
      e.preventDefault();
      console.log('Close button clicked');
      close();
      return;
    }
    
    // Klik tombol prev
    if(e.target.closest('.lb-prev')){
      e.stopPropagation();
      e.preventDefault();
      next(-1);
      return;
    }
    
    // Klik tombol next
    if(e.target.closest('.lb-next')){
      e.stopPropagation();
      e.preventDefault();
      next(1);
      return;
    }
    
    // Klik backdrop (area gelap, bukan .lb-box)
    if(e.target === overlay){
      e.stopPropagation();
      e.preventDefault();
      console.log('Backdrop clicked');
      close();
      return;
    }
  }, false); // bubble phase, bukan capture

  // keyboard
  document.addEventListener('keydown', (e)=>{
    if(!overlay.classList.contains('show')) return;
    if(e.key === 'Escape'){ close(); e.preventDefault(); }
    if(e.key === 'ArrowLeft'){ next(-1); e.preventDefault(); }
    if(e.key === 'ArrowRight'){ next(1); e.preventDefault(); }
  });

  // collect saat window load
  window.addEventListener('load', collect);
})();
</script>
