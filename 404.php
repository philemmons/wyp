<?php
/**
 * 404.php — Custom Not Found page
 * wipeyourpaws.net · PHP 8.5 · Bootstrap 5.3.8
 * Triggered by .htaccess: ErrorDocument 404 /404.php
 */
http_response_code(404);
$page_id = 'error404';
require_once 'includes/header.php';
?>

<section class="error-hero">
  <span class="paw-float" aria-hidden="true">🐾</span>
  <span class="paw-float" aria-hidden="true">🐾</span>
  <span class="paw-float" aria-hidden="true">🐾</span>

  <div class="container page-hero-z">
    <div class="error-card">
      <div class="emoji-xl" aria-hidden="true">🐾</div>
      <h1 class="error-404-heading">404</h1>
      <p class="error-tagline">This page is not here right now.</p>
      <p class="error-body">
        The page you requested may have moved, or the address may be wrong.
        Use one of the links below to get back on track.
      </p>

      <div class="error-btn-row">
        <a href="/" class="btn-wyp btn-wyp-primary">
          <span aria-hidden="true">🏠</span> Go to Home Page
        </a>
        <a href="contact.php" class="btn-wyp btn-wyp-outline">
          Open Contact Page
        </a>
      </div>
    </div>
  </div>
</section>

<section class="page-section-sm bg-cream">
  <div class="container">
    <details class="simple-summary">
      <summary>Simple summary of this page</summary>
      <p>
        We cannot find this page. Pick one of the links below to continue.
      </p>
    </details>
  </div>
</section>

<section class="quicklinks-section">
  <div class="container">
    <h2 class="quicklinks-title text-center">Where would you like to go?</h2>
    <nav aria-label="Error page navigation">
      <div class="row g-3 justify-content-center">
        <?php
        $links = [
          ['index.php',    '🏠', 'Home',         'Start at the beginning'],
          ['intro.php',    '🐶', 'Meet the Pups', 'Get to know Chandra and Skipper'],
          ['monterey.php', '🌊', 'Why Monterey',  'Discover our beautiful home'],
          ['gallery.php',  '📸', 'Gallery',       'See photo updates'],
          ['contact.php',  '✉️', 'Contact Us',     'Send us a message'],
        ];
        foreach ($links as $l): ?>
          <div class="col-sm-4 col-md-2">
            <a href="<?= $l[0] ?>" class="quicklink-card">
              <span class="quicklink-icon" aria-hidden="true"><?= $l[1] ?></span>
              <?= $l[2] ?>
              <span class="quicklink-subtitle"><?= $l[3] ?></span>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </nav>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
