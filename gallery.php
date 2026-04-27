<?php
/**
 * gallery.php — Media Gallery
 * wipeyourpaws.net · PHP 8.5 · Bootstrap 5.3.8
 */
$page_id = 'gallery';
require_once 'includes/header.php';

$placeholders = [
  ['🐕', 'Chandra',        'Morning sunshine'],
  ['🐶', 'Skipper',        'Beach adventures'],
  ['🐾', 'The Dynamic Duo','Best friends forever'],
  ['🌊', 'Monterey Bay',   'Coastal walks'],
  ['🐕', 'Chandra',        'Nap time'],
  ['🐶', 'Skipper',        'Play time'],
  ['🌅', 'Sunset stroll',  'Carmel Beach'],
  ['🐾', 'Chandra',        'Snuggle time'],
  ['🏖️', 'Skipper',        'Sand and surf'],
  ['🐕‍🦺', 'The pups',       'Playing together'],
  ['🌿', 'Trail life',     'Garrapata Park'],
  ['🐶', 'Chandra',        'Silly faces'],
];
?>

<section class="gallery-hero">
  <div class="container text-center page-hero-z">
    <span class="page-hero-emoji" aria-hidden="true">📸🐾✨</span>
    <h1 class="page-hero-h1">Media Gallery</h1>
    <p class="page-hero-tagline">
      Beautiful moments with Chandra and Skipper — coming soon
    </p>
  </div>
</section>

<section class="page-section-sm bg-cream">
  <div class="container">
    <details class="simple-summary">
      <summary>Simple summary of this page</summary>
      <p>
        This gallery is still being built. You can preview placeholders now and return later for full photos.
      </p>
    </details>
  </div>
</section>

<section class="page-section-sm bg-cream">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-7 text-center">
        <div class="gallery-coming-soon">
          <div class="gallery-coming-soon__icon" aria-hidden="true">🚧</div>
          <h2 class="section-title">Pictures Loading…</h2>
          <p class="gallery-coming-soon__body">
            We are collecting adorable photos of Chandra and Skipper around Monterey Bay.
            Check back soon for the full gallery.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="page-section bg-warm-white">
  <div class="container">

    <div class="text-center mb-5">
      <span class="section-eyebrow">Coming Soon</span>
      <h2 class="section-title">Gallery Preview</h2>
      <hr class="section-divider">
      <p class="gallery-tip-text">
        Photo slots are ready for new moments. <span aria-hidden="true">🌟</span>
      </p>
    </div>

    <div class="gallery-placeholder-grid">
      <?php foreach ($placeholders as $ph): ?>
      <div class="gallery-placeholder-item">
        <span class="gallery-coming-badge">Coming Soon</span>
        <span class="placeholder-icon" aria-hidden="true"><?= $ph[0] ?></span>
        <p class="spot-name mt-2 mb-0"><?= htmlspecialchars($ph[1]) ?></p>
        <p class="gallery-tip-text mb-0"><?= htmlspecialchars($ph[2]) ?></p>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<section class="section-gallery-story">
  <div class="container">
    <div class="row g-4 align-items-center">

      <div class="col-lg-8">
        <h2 class="gallery-story-heading">
          Have photos of your small pups? <span aria-hidden="true">🐾</span>
        </h2>
        <p class="gallery-story-body">
          We welcome community photos from small dog lovers.
          Send us a message and share your favorite pictures.
        </p>
      </div>

      <div class="col-lg-4 text-lg-end">
        <a href="contact.php" class="btn-wyp btn-wyp-primary">
          Share Your Pup Photos <span aria-hidden="true">📸</span>
        </a>
      </div>

    </div>
  </div>
</section>

<section class="page-section-sm bg-cream">
  <div class="container">
    <div class="row g-4 justify-content-center">

      <div class="col-md-5">
        <div class="wyp-card text-center p-4">
          <div class="card-header-band"></div>
          <div class="gallery-dog-card-icon" aria-hidden="true">🐕</div>
          <h3 class="section-title">Chandra</h3>
          <p class="gallery-dog-teaser">
            Chandra’s gallery photos will highlight her sunlit poses and expressive personality.
          </p>
          <a href="intro.php" class="gallery-dog-link">
            Read Chandra’s Story <span aria-hidden="true">→</span>
          </a>
        </div>
      </div>

      <div class="col-md-5">
        <div class="wyp-card text-center p-4">
          <div class="card-header-band dog-card-top-stripe--skipper"></div>
          <div class="gallery-dog-card-icon" aria-hidden="true">🐶</div>
          <h3 class="section-title">Skipper</h3>
          <p class="gallery-dog-teaser">
            Skipper’s gallery photos will capture trail adventures and playful beach moments.
          </p>
          <a href="intro.php" class="gallery-dog-link">
            Read Skipper’s Story <span aria-hidden="true">→</span>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
