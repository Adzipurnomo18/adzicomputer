(function(){
  const audio   = document.getElementById('bgAudio');
  const btnPlay = document.getElementById('awPlay');
  const btnMute = document.getElementById('awMute');
  const volBar  = document.getElementById('awVol');

  // defaults
  const savedVol   = parseFloat(localStorage.getItem('bgVol') ?? '0.3');
  const savedMute  = localStorage.getItem('bgMuted') === '1';
  const savedAuto  = localStorage.getItem('bgAuto')  ?? '1'; // 1=autoplay if possible

  audio.volume = isNaN(savedVol) ? 0.3 : Math.min(1, Math.max(0, savedVol));
  volBar.value = audio.volume;
  audio.muted  = !!savedMute;

  const setPlayIcon = (playing) => {
    btnPlay.innerHTML = `<i class="fa-solid ${playing ? 'fa-pause' : 'fa-play'}"></i>`;
  };
  const setMuteIcon = (muted) => {
    btnMute.innerHTML = `<i class="fa-solid ${muted ? 'fa-volume-xmark' : 'fa-volume-high'}"></i>`;
  };

  setMuteIcon(audio.muted);
  setPlayIcon(false);

  // Try autoplay (many browsers block until user gesture)
  const tryAutoplay = () => {
    if (savedAuto !== '1') return; // user disabled earlier
    audio.play().then(() => {
      setPlayIcon(true);
    }).catch(() => {
      // Autoplay blocked; wait for first user interaction
      const unlock = () => {
        audio.play().then(()=> setPlayIcon(true)).catch(()=>{ /* still blocked */ });
        window.removeEventListener('pointerdown', unlock, {once:true});
        window.removeEventListener('keydown', unlock, {once:true});
      };
      window.addEventListener('pointerdown', unlock, {once:true});
      window.addEventListener('keydown', unlock, {once:true});
    });
  };

  // controls
  btnPlay.addEventListener('click', () => {
    if (audio.paused) {
      audio.play().then(()=> {
        setPlayIcon(true);
        localStorage.setItem('bgAuto','1');
      }).catch(()=>{ /* ignore */ });
    } else {
      audio.pause();
      setPlayIcon(false);
      localStorage.setItem('bgAuto','0'); // jangan autoplay lagi
    }
  });

  btnMute.addEventListener('click', () => {
    audio.muted = !audio.muted;
    setMuteIcon(audio.muted);
    localStorage.setItem('bgMuted', audio.muted ? '1' : '0');
  });

  volBar.addEventListener('input', () => {
    audio.volume = parseFloat(volBar.value);
    if (audio.muted && audio.volume > 0) {
      audio.muted = false;
      setMuteIcon(false);
      localStorage.setItem('bgMuted','0');
    }
    localStorage.setItem('bgVol', String(audio.volume));
  });

  // optional: kecilkan saat tab di-background
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      audio._prevVol = audio.volume;
      audio.volume = Math.max(0.05, audio.volume * 0.4);
    } else if (audio._prevVol != null) {
      audio.volume = parseFloat(localStorage.getItem('bgVol') ?? audio._prevVol);
    }
  });

  // start
  // Jika user prefer-reduced-motion, jangan autoplay
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!prefersReduced) tryAutoplay();
})();
