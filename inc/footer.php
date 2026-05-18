</main>
  <!-- =============== FLOATING MUSIC BALLOON (YouTube) =============== -->
  <button type="button" class="music-balloon" aria-label="Putar Musik" data-yt-id="xP3LUSHj2J0">
    <span class="mb-icon"><i class="fa-solid fa-music"></i></span>
    <span class="mb-text">Play</span>
  </button>
  <div class="music-player" aria-hidden="true"></div>

  <footer>© <?= date('Y') ?> <?= h($CONFIG['brand']) ?> · Dibuat dengan ❤️ oleh Adzi.</footer>
  <script src="assets/js/core.js"></script>
  <script src="assets/js/lightbox.js"></script>
  <?php if(!empty($pageJs)) echo $pageJs; ?>
  <style>
    .music-balloon{position:fixed;right:18px;bottom:18px;z-index:9998;display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:999px;border:1px solid rgba(255,255,255,.18);background:linear-gradient(180deg,#ffdcd1,#ffb7a1);color:#3b1b0f;font:600 14px/1.1 "Poppins",system-ui,sans-serif;box-shadow:0 12px 38px rgba(255,114,88,.35);cursor:pointer;transition:transform .18s ease,box-shadow .18s ease}
    .music-balloon:hover{transform:translateY(-2px);box-shadow:0 16px 48px rgba(255,114,88,.45)}
    .music-balloon .mb-icon{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;background:rgba(255,255,255,.75)}
    .music-balloon.is-playing{background:linear-gradient(180deg,#ffe3f5,#ffbfe1);color:#3d1530}
    .music-balloon.is-playing .mb-icon{background:rgba(255,255,255,.9)}
    .music-player{position:fixed;inset:auto auto 90px 18px;width:0;height:0;opacity:0;pointer-events:none}
    .music-player iframe{width:0;height:0;border:0}
    @media (max-width:768px){.music-balloon{right:12px;bottom:12px;padding:9px 12px;font-size:13px}.music-balloon .mb-icon{width:30px;height:30px}}
  </style>
  <script>
  (function(){
    const btn = document.querySelector('.music-balloon');
    const holder = document.querySelector('.music-player');
    if(!btn || !holder) return;

    const id = btn.dataset.ytId || '';
    const key = 'music_playing';
    let playing = false;

    function renderPlayer(){
      holder.innerHTML = `
        <iframe
          src="https://www.youtube.com/embed/${id}?autoplay=1&loop=1&playlist=${id}&controls=0&mute=0"
          title="Music"
          allow="autoplay; encrypted-media"
        ></iframe>`;
    }

    function stopPlayer(){
      holder.innerHTML = '';
    }

    function syncUi(){
      btn.classList.toggle('is-playing', playing);
      btn.querySelector('.mb-text').textContent = playing ? 'Stop' : 'Play';
      btn.setAttribute('aria-label', playing ? 'Hentikan Musik' : 'Putar Musik');
      holder.setAttribute('aria-hidden', playing ? 'false' : 'true');
    }

    btn.addEventListener('click', ()=>{
      if(!id) return;
      playing = !playing;
      if(playing){
        renderPlayer();
        localStorage.setItem(key, '1');
      } else {
        stopPlayer();
        localStorage.removeItem(key);
      }
      syncUi();
    });

    if(localStorage.getItem(key) === '1'){
      playing = true;
      renderPlayer();
      syncUi();
    } else {
      syncUi();
    }
  })();
  </script>
</body>
</html>
