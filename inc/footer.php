</main>
  <footer class="site-footer">
    <div class="container footer-inner">
      <img src="assets/img/gambar/01_logo_dark.png" alt="<?= h($CONFIG['brand']) ?>">
      <p>&copy; <?= date('Y') ?> <?= h($CONFIG['brand']) ?>. All rights reserved.</p>
      <div class="footer-social" aria-label="Media sosial">
        <span aria-label="Instagram"><i class="fa-brands fa-instagram"></i></span>
        <span aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></span>
        <span aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></span>
      </div>
    </div>
  </footer>
  <script src="assets/js/core.js?v=<?= h((string)filemtime(__DIR__ . '/../assets/js/core.js')) ?>"></script>
  <script src="assets/js/lightbox.js?v=<?= h((string)filemtime(__DIR__ . '/../assets/js/lightbox.js')) ?>"></script>
  <?php if(!empty($pageJs)) echo $pageJs; ?>
</body>
</html>
