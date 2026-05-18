<?php
$status = null;
$error = null;

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])){
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $msg = trim($_POST['message'] ?? '');
  $hp = trim($_POST['website'] ?? '');

  if($hp === ''){
    if($name && $email && $msg){
      $st = $pdo->prepare('INSERT INTO messages(name,email,content) VALUES(?,?,?)');
      $st->execute([$name,$email,$msg]);
      $status = 'Pesan terkirim. Terima kasih!';
    } else {
      $error = 'Lengkapi semua field.';
    }
  }
}

$waNumber = preg_replace('/\D+/', '', $CONFIG['wa']);
if (str_starts_with($waNumber, '0')) {
  $waNumber = '62' . substr($waNumber, 1);
}
$waLink = 'https://wa.me/' . $waNumber . '?text=' . rawurlencode('Halo ADZI Computer, saya ingin konsultasi servis.');
$mapsSrc = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.133870883912!2d103.55593239999999!3d-1.6649658999999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e2587e31244add1%3A0x8701b461d37129c4!2sService%20Laptop%20dan%20Elektronik!5e0!3m2!1sid!2sid!4v1762330598318!5m2!1sid!2sid';
?>

<section id="kontak" class="section-kontak">
  <div class="container">
    <div class="contact-head reveal">
      <span>KONTAK KAMI</span>
      <h1>Hubungi ADZI Computer</h1>
      <p>Konsultasikan kerusakan perangkat Anda melalui WhatsApp, telepon, email, atau formulir kontak.</p>
    </div>

    <div class="contact-grid">
      <div class="contact-card reveal">
        <?php if($status): ?>
          <div class="alert success"><?= h($status) ?></div>
        <?php endif; ?>
        <?php if($error): ?>
          <div class="alert error"><?= h($error) ?></div>
        <?php endif; ?>

        <form method="post" class="contact-form">
          <input type="hidden" name="contact_submit" value="1">
          <input type="text" name="website" class="honeypot" tabindex="-1" autocomplete="off">

          <div class="form-row">
            <label>Nama
              <input name="name" type="text" placeholder="Nama lengkap" required>
            </label>
            <label>Email
              <input name="email" type="email" placeholder="email@domain.com" required>
            </label>
          </div>

          <label>Pesan
            <textarea name="message" placeholder="Ceritakan kendala perangkat Anda" required></textarea>
          </label>

          <div class="contact-actions">
            <button class="cta" type="submit"><i class="fa-regular fa-paper-plane"></i> Kirim</button>
            <a class="btn" href="<?= h($waLink) ?>" target="_blank"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
            <a class="btn" href="tel:<?= h($CONFIG['phone']) ?>"><i class="fa-solid fa-phone"></i> Telepon</a>
            <a class="btn" href="mailto:<?= h($CONFIG['email']) ?>"><i class="fa-regular fa-envelope"></i> Email</a>
          </div>
        </form>
      </div>

      <aside class="contact-side reveal">
        <div class="info-card">
          <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
          <div>
            <h2>Lokasi Service</h2>
            <p><?= h($CONFIG['address']) ?></p>
          </div>
        </div>
        <div class="info-card">
          <div class="info-icon"><i class="fa-brands fa-whatsapp"></i></div>
          <div>
            <h2>WhatsApp</h2>
            <p><?= h($CONFIG['phone']) ?></p>
          </div>
        </div>
        <iframe
          title="Lokasi ADZI Computer"
          src="<?= h($mapsSrc) ?>"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          allowfullscreen
        ></iframe>
      </aside>
    </div>
  </div>
</section>
