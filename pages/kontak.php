<?php
$status = null; $error = null;

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['contact_submit'])){
  $name = trim($_POST['name']??'');
  $email= trim($_POST['email']??'');
  $msg  = trim($_POST['message']??'');
  $hp   = trim($_POST['website']??''); // honeypot anti-bot

  if($hp===''){
    if($name && $email && $msg){
      $st = $pdo->prepare('INSERT INTO messages(name,email,content) VALUES(?,?,?)');
      $st->execute([$name,$email,$msg]);
      $status = 'Pesan terkirim. Terima kasih!';
    } else {
      $error='Lengkapi semua field.';
    }
  }
}

// pakai link embed langsung (tanpa API key)
$mapsSrc = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.133870883912!2d103.55593239999999!3d-1.6649658999999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e2587e31244add1%3A0x8701b461d37129c4!2sService%20Laptop%20dan%20Elektronik!5e0!3m2!1sid!2sid!4v1762330598318!5m2!1sid!2sid';
?>

<section id="kontak" class="section-kontak">
  <div class="container contact">
    <div class="reveal">
      <h2>Kontak & Lokasi</h2>
      <p class="note">Hubungi via WhatsApp, telepon, atau kirim pesan dari formulir.</p>

      <?php if($status): ?>
        <div class="alert success"><?= h($status) ?></div>
      <?php endif; ?>
      <?php if($error): ?>
        <div class="alert error"><?= h($error) ?></div>
      <?php endif; ?>

      <form method="post" class="panel">
        <input type="hidden" name="contact_submit" value="1">
        <input type="text" name="website" style="display:none">

        <div class="row">
          <div><label>Nama<br><input name="name" required></label></div>
          <div><label>Email<br><input name="email" type="email" required></label></div>
        </div>

        <div style="margin-top:12px">
          <label>Pesan<br><textarea name="message" required></textarea></label>
        </div>

        <div class="actions">
          <button class="cta" type="submit"><i class="fa-regular fa-paper-plane"></i> Kirim</button>
          <a class="btn" href="https://wa.me/<?= h($CONFIG['wa']) ?>?text=Halo%20saya%20ingin%20servis" target="_blank">
            <i class="fa-brands fa-whatsapp"></i> WhatsApp
          </a>
          <a class="btn" href="tel:<?= h($CONFIG['phone']) ?>">
            <i class="fa-solid fa-phone"></i> Telepon
          </a>
          <a class="btn" href="mailto:<?= h($CONFIG['email']) ?>">
            <i class="fa-regular fa-envelope"></i> Email
          </a>
        </div>

        <div class="note">
          <i class="fa-solid fa-location-dot"></i> <?= h($CONFIG['address']) ?>
        </div>
      </form>
    </div>

    <div class="panel reveal">
      <iframe
        title="maps"
        src="<?= h($mapsSrc) ?>"
        style="width:100%; height:360px; border:0; border-radius:14px"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        allowfullscreen
      ></iframe>
    </div>
  </div>
</section>
