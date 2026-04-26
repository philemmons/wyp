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
  ['🐶', 'Skipper',        'Zoomies!'],
  ['🌅', 'Sunset stroll',  'Carmel Beach'],
  ['🐾', 'Chandra',        'Snuggle time'],
  ['🏖️', 'Skipper',        'Sand & surf'],
  ['🐕‍🦺','The pups',       'Playing together'],
  ['🌿', 'Trail life',     'Garrapata Park'],
  ['🐶', 'Chandra',        'Silly faces'],
];
?>

<!--  PAGE HERO  -->
<section class="gallery-hero">
  <div class="container text-center page-hero-z">
    <span class="page-hero-emoji" aria-hidden="true">📸🐾✨</span>
    <h1 class="page-hero-h1">Media Gallery</h1>
    <p class="page-hero-tagline">
      Beautiful moments with Chandra &amp; Skipper &mdash; coming soon!
    </p>
  </div>
</section>

<!--  COMING SOON NOTICE  -->
<section class="page-section-sm bg-cream">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-7 text-center">
        <div class="gallery-coming-soon">
          <div class="gallery-coming-soon__icon" aria-hidden="true">🚧</div>
          <!-- 1.5rem = 24px Berkshire Swash — large text → orange-deep 4.07:1 passes 3:1 ✅ -->
          <h2 class="section-title">Pictures Loading&hellip;</h2>
          <p class="gallery-coming-soon__body">
            We're busy snapping adorable photos of Chandra and Skipper exploring Monterey Bay!
            This gallery will soon be filled with their cutest moments.
            Check back soon &mdash; it&rsquo;ll be <em>paw-some</em>! <span aria-hidden="true">🐾</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!--  PLACEHOLDER GRID  -->
<section class="page-section bg-warm-white">
  <div class="container">

    <div class="text-center mb-5">
      <span class="section-eyebrow">Coming Soon</span>
      <h2 class="section-title">Gallery Preview</h2>
      <hr class="section-divider">
      <p class="gallery-tip-text">
        Photo slots awaiting their stars&hellip; <span aria-hidden="true">🌟</span>
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

<!--  UPLOAD CTA  -->
<section class="section-gallery-story">
  <div class="container">
    <div class="row g-4 align-items-center">

      <div class="col-lg-8">
        <h3 class="gallery-story-heading">
          Have Photos of Your Small Pups? <span aria-hidden="true">🐾</span>
        </h3>
        <p class="gallery-story-body">
          We'd love to feature photos from our community of small dog lovers!
          Reach out to us through our contact form and share the joy your furry
          family members bring to your world.
        </p>
      </div>

      <div class="col-lg-4 text-lg-end">
        <a href="contact.php" class="btn-wyp btn-wyp-primary">
          Share Your Pup <span aria-hidden="true">📸</span>
        </a>
      </div>

    </div>
  </div>
</section>

<!--  ABOUT THE DOGS MINI SECTION  -->
<section class="page-section-sm bg-cream">
  <div class="container">
    <div class="row g-4 justify-content-center">

      <div class="col-md-5">
        <div class="wyp-card text-center p-4">
          <div class="card-header-band"></div>
          <div class="gallery-dog-card-icon" aria-hidden="true">🐕</div>
          <!-- Bootstrap h3 default ~1.75rem=28px — large text, orange-deep 4.07:1 passes 3:1 ✅ -->
          <h3 class="section-title">Chandra</h3>
          <p class="gallery-dog-teaser">
            Our spirited Chihuahua princess — her gallery photos will showcase
            her signature sunlit poses and diva energy.
          </p>
          <a href="intro.php" class="gallery-dog-link">
            Read Chandra&rsquo;s Story <span aria-hidden="true">→</span>
          </a>
        </div>
      </div>

      <div class="col-md-5">
        <div class="wyp-card text-center p-4">
          <div class="card-header-band dog-card-top-stripe--skipper"></div>
          <div class="gallery-dog-card-icon" aria-hidden="true">🐶</div>
          <h3 class="section-title">Skipper</h3>
          <p class="gallery-dog-teaser">
            Our adventurous Jack Chi explorer — expect candid action shots of
            beach zoomies and trail-sniffing expeditions!
          </p>
          <a href="intro.php" class="gallery-dog-link">
            Read Skipper&rsquo;s Story <span aria-hidden="true">→</span>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
