<?php
/**
 * 404.php - Custom Not Found page
 * wipeyourpaws.net - PHP 8.5 - Bootstrap 5.3.8
 * Triggered by .htaccess: ErrorDocument 404 /404.php
 */
http_response_code(404);
$page_id = 'home'; // keeps "Home" as active in nav
require_once 'includes/header.php';
?>

<section class="error-hero">

  <!-- Decorative paw floats -->
  <span class="paw-float" aria-hidden="true">&#128062;</span>
  <span class="paw-float" aria-hidden="true">&#128062;</span>
  <span class="paw-float" aria-hidden="true">&#128062;</span>

  <div class="container text-center position-relative page-hero-content">
    <div class="error-hero-card">

      <div class="error-hero-icon" aria-hidden="true">&#128062;</div>

      <h1 class="error-hero-code">
        404
      </h1>

      <p class="error-hero-tagline">
        Oops &mdash; this page ran off like Skipper on a zoomie!
      </p>

      <p class="error-hero-copy">
        The page you&rsquo;re looking for doesn&rsquo;t exist, may have moved,
        or perhaps Chandra buried it somewhere in the garden.
        Let&rsquo;s get you back on the right trail!
      </p>

      <div class="error-hero-actions">
        <a href="index.php" class="btn-wyp btn-wyp-primary">
          <span aria-hidden="true">&#127968;</span> Back to Home
        </a>
        <a href="contact.php" class="btn-wyp btn-wyp-outline">
          Contact Us
        </a>
      </div>

    </div>
  </div>
</section>

<!-- Quick links strip -->
<section class="error-links-section">
  <div class="container">
    <p class="text-center error-links-title">
      Where would you like to go?
    </p>
    <nav aria-label="Error page navigation">
      <div class="row g-3 justify-content-center">
        <?php
        $links = [
          ['index.php',    '&#127968;', 'Home',          'Start at the beginning'],
          ['intro.php',    '&#128054;', 'Meet the Pups',  'Get to know Chandra &amp; Skipper'],
          ['monterey.php', '&#127754;', 'Why Monterey',   'Discover our beautiful home'],
          ['gallery.php',  '&#128248;', 'Gallery',        'Photos coming soon!'],
          ['contact.php',  '&#9993;&#65039;', 'Contact Us',      'Say hello'],
        ];
        foreach ($links as $l): ?>
        <div class="col-sm-4 col-md-2">
          <a href="<?= $l[0] ?>" class="error-link-card">
            <span class="error-link-icon" aria-hidden="true"><?= $l[1] ?></span>
            <?= $l[2] ?>
            <span class="error-link-meta"><?= $l[3] ?></span>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </nav>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
