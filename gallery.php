<?php
/**
 * gallery.php - Media Gallery
 * wipeyourpaws.net - PHP 8.5 - Bootstrap 5.3.8
 */
$page_id = 'gallery';
require_once 'includes/header.php';

// Gallery placeholder items - descriptions for the "coming soon" tiles
$placeholders = [
  ['&#128021;', 'Chandra',       'Morning sunshine'],
  ['&#128054;', 'Skipper',       'Beach adventures'],
  ['&#128062;', 'The Dynamic Duo', 'Best friends forever'],
  ['&#127754;', 'Monterey Bay',  'Coastal walks'],
  ['&#128021;', 'Chandra',       'Nap time'],
  ['&#128054;', 'Skipper',       'Zoomies!'],
  ['&#127748;', 'Sunset stroll', 'Carmel Beach'],
  ['&#128062;', 'Chandra',       'Snuggle time'],
  ['&#127958;&#65039;', 'Skipper',       'Sand & surf'],
  ['&#128021;&#8205;&#129466;', 'The pups',      'Playing together'],
  ['&#127807;', 'Trail life',    'Garrapata Park'],
  ['&#128054;', 'Chandra',       'Silly faces'],
];
?>

<!-- PAGE HERO -->
<section class="gallery-hero">
  <div class="container text-center position-relative page-hero-content">
    <span class="page-hero-emoji" aria-hidden="true">&#128248;&#128062;&#10024;</span>
    <h1 class="page-hero-title page-hero-title-shadow-strong">
      Media Gallery
    </h1>
    <p class="page-hero-tagline gallery-hero-tagline">
      Beautiful moments with Chandra &amp; Skipper &mdash; coming soon!
    </p>
  </div>
</section>

<!-- COMING SOON NOTICE -->
<section class="page-section-sm bg-cream">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-7 text-center">
        <div class="gallery-notice-box">
          <div class="gallery-notice-icon" aria-hidden="true">&#128679;</div>
          <h2 class="gallery-notice-title">
            Pictures Loading&hellip;
          </h2>
          <p class="gallery-notice-copy">
            We're busy snapping adorable photos of Chandra and Skipper exploring Monterey Bay!
            This gallery will soon be filled with their cutest moments.
            Check back soon &mdash; it&rsquo;ll be <em>paw-some</em>! <span aria-hidden="true">&#128062;</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PLACEHOLDER GRID -->
<section class="page-section bg-warm-white">
  <div class="container">

    <div class="text-center mb-5">
      <span class="section-eyebrow">Coming Soon</span>
      <h2 class="section-title">Gallery Preview</h2>
      <hr class="section-divider">
      <p class="gallery-preview-helper">
        Photo slots awaiting their stars&hellip; <span aria-hidden="true">&#127775;</span>
      </p>
    </div>

    <div class="gallery-placeholder-grid">
      <?php foreach ($placeholders as $ph): ?>
      <div class="gallery-placeholder-item">
        <span class="gallery-coming-badge">Coming Soon</span>
        <span class="placeholder-icon" aria-hidden="true"><?= $ph[0] ?></span>
        <p class="gallery-placeholder-title">
          <?= htmlspecialchars($ph[1]) ?>
        </p>
        <p class="gallery-placeholder-subtitle">
          <?= htmlspecialchars($ph[2]) ?>
        </p>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<!-- UPLOAD CTA -->
<section class="gallery-upload-strip">
  <div class="container">
    <div class="row g-4 align-items-center">

      <div class="col-lg-8">
        <h3 class="gallery-upload-title">
          Have Photos of Your Small Pups? <span aria-hidden="true">&#128062;</span>
        </h3>
        <p class="gallery-upload-copy">
          We'd love to feature photos from our community of small dog lovers!
          Reach out to us through our contact form and share the joy your furry
          family members bring to your world.
        </p>
      </div>

      <div class="col-lg-4 text-lg-end">
        <a href="contact.php" class="btn-wyp btn-wyp-primary">
          Share Your Pup <span aria-hidden="true">&#128248;</span>
        </a>
      </div>

    </div>
  </div>
</section>

<!-- ABOUT THE DOGS MINI SECTION -->
<section class="page-section-sm bg-cream">
  <div class="container">
    <div class="row g-4 justify-content-center">

      <div class="col-md-5">
        <div class="wyp-card text-center p-4">
          <div class="card-header-band"></div>
          <div class="gallery-mini-icon" aria-hidden="true">&#128021;</div>
          <h3 class="gallery-mini-title">Chandra</h3>
          <p class="gallery-mini-copy">
            Our spirited Chihuahua princess &mdash; her gallery photos will showcase
            her signature sunlit poses and diva energy.
          </p>
          <a href="intro.php" class="story-link">
            Read Chandra&rsquo;s Story <span aria-hidden="true">&#8594;</span>
          </a>
        </div>
      </div>

      <div class="col-md-5">
        <div class="wyp-card text-center p-4">
          <div class="card-header-band card-header-band-alt"></div>
          <div class="gallery-mini-icon" aria-hidden="true">&#128054;</div>
          <h3 class="gallery-mini-title">Skipper</h3>
          <p class="gallery-mini-copy">
            Our adventurous Jack Chi explorer &mdash; expect candid action shots of
            beach zoomies and trail-sniffing expeditions!
          </p>
          <a href="intro.php" class="story-link">
            Read Skipper&rsquo;s Story <span aria-hidden="true">&#8594;</span>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
