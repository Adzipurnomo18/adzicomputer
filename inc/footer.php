</main>
  <footer>&copy; <?= date('Y') ?> <?= h($CONFIG['brand']) ?>. All rights reserved.</footer>
  <script src="assets/js/core.js"></script>
  <script src="assets/js/lightbox.js"></script>
  <?php if(!empty($pageJs)) echo $pageJs; ?>
</body>
</html>
