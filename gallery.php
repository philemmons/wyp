<?php
/**
 * gallery.php — Media Gallery
 * wipeyourpaws.net · PHP 8.5 · Bootstrap 5.3.8
 */
$page_id = 'gallery';
require_once 'includes/header.php';

// Gallery placeholder items — descriptions for the "coming soon" tiles
$placeholders = [
  ['🐕', 'Chandra',       'Morning sunshine'],
  ['🐶', 'Skipper',       'Beach adventures'],
  ['🐾', 'The Dynamic Duo','Best friends forever'],
  ['🌊', 'Monterey Bay',  'Coastal walks'],
  ['🐕', 'Chandra',       'Nap time'],
  ['🐶', 'Skipper',       'Zoomies!'],
  ['🌅', 'Sunset stroll', 'Carmel Beach'],
  ['🐾', 'Chandra',       'Snuggle time'],
  ['🏖️', 'Skipper',       'Sand & surf'],
  ['🐕‍🦺','The pups',      'Playing together'],
  ['🌿', 'Trail life',    'Garrapata Park'],
  ['🐶', 'Chandra',       'Silly faces'],
];
?>

<!-- ░░ PAGE HERO ░░ -->
<section class="gallery-hero">
  <div class="container text-center position-relative" style="z-index:2;">
    <span style="font-size:3.5rem; display:block; margin-bottom:0.5rem;" aria-hidden="true">📸🐾✨</span>
    <h1 style="font-family:var(--font-display); color:#fff; font-size:clamp(2rem,5vw,3.5rem); text-shadow:3px 4px 12px rgba(0,0,0,0.4); margin-bottom:0.5rem;">
      Media Gallery
    </h1>
    <p style="font-family:var(--font-accent); font-style:italic; color:rgba(255,255,255,0.95); font-size:1.1rem; max-width:500px; margin:0 auto; text-shadow:0 2px 8px rgba(0,0,0,0.35);">
      Beautiful moments with Chandra &amp; Skipper &mdash; coming soon!
    </p>
  </div>
</section>

<!-- ░░ COMING SOON NOTICE ░░ -->
<section class="page-section-sm" style="background:var(--cream);">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-7 text-center">
        <div style="background:linear-gradient(135deg, var(--yellow-soft), var(--orange-pale)); border-radius:var(--radius-xl); padding:2.5rem; border: 2px dashed var(--orange-primary);">
          <div style="font-size:4rem; margin-bottom:0.75rem;" aria-hidden="true">🚧</div>
          <h2 style="font-family:var(--font-display); color:var(--orange-deep); font-size:1.5rem; margin-bottom:0.75rem;">
            Pictures Loading&hellip;
          </h2>
          <p style="font-size:0.98rem; color:var(--text-mid); line-height:1.75; margin:0;">
            We're busy snapping adorable photos of Chandra and Skipper exploring Monterey Bay!
            This gallery will soon be filled with their cutest moments.
          Check back soon &mdash; it&rsquo;ll be <em>paw-some</em>! <span aria-hidden="true">🐾</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ░░ PLACEHOLDER GRID ░░ -->
<section class="page-section" style="background:var(--warm-white);">
  <div class="container">

    <div class="text-center mb-5">
      <span class="section-eyebrow">Coming Soon</span>
      <h2 class="section-title">Gallery Preview</h2>
      <hr class="section-divider">
      <p style="font-size:0.93rem; color:var(--text-light);">
        Photo slots awaiting their stars&hellip; <span aria-hidden="true">🌟</span>
      </p>
    </div>

    <div class="gallery-placeholder-grid">
      <?php foreach ($placeholders as $ph): ?>
      <div class="gallery-placeholder-item">
        <span class="gallery-coming-badge">Coming Soon</span>
        <span class="placeholder-icon" aria-hidden="true"><?= $ph[0] ?></span>
        <p style="font-size:0.8rem; font-weight:700; color:var(--text-mid); margin:0.5rem 0 0; letter-spacing:0.05em;">
          <?= htmlspecialchars($ph[1]) ?>
        </p>
        <p style="font-size:0.7rem; color:var(--text-light); margin:0;">
          <?= htmlspecialchars($ph[2]) ?>
        </p>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<!-- ░░ UPLOAD CTA ░░ -->
<section style="background: linear-gradient(135deg, var(--orange-pale), var(--pink-pale)); padding: 4rem 0;">
  <div class="container">
    <div class="row g-4 align-items-center">

      <div class="col-lg-8">
        <h3 style="font-family:var(--font-display); color:var(--orange-deep); font-size:1.7rem; margin-bottom:0.5rem;">
        Have Photos of Your Small Pups? <span aria-hidden="true">🐾</span>
        </h3>
        <p style="font-size:1rem; color:var(--text-mid); line-height:1.75; margin:0;">
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

<!-- ░░ ABOUT THE DOGS MINI SECTION ░░ -->
<section class="page-section-sm" style="background:var(--cream);">
  <div class="container">
    <div class="row g-4 justify-content-center">

      <div class="col-md-5">
        <div class="wyp-card text-center p-4">
          <div class="card-header-band"></div>
          <div style="font-size:4rem; padding:1rem 0;" aria-hidden="true">🐕</div>
          <h3 style="font-family:var(--font-display); color:var(--orange-deep);">Chandra</h3>
          <p style="font-size:0.88rem; color:var(--text-mid); line-height:1.65;">
            Our spirited Chihuahua princess — her gallery photos will showcase
            her signature sunlit poses and diva energy.
          </p>
          <a href="intro.php" style="font-size:0.82rem; font-weight:700; color:var(--orange-primary); text-decoration:none;">
            Read Chandra&rsquo;s Story <span aria-hidden="true">→</span>
          </a>
        </div>
      </div>

      <div class="col-md-5">
        <div class="wyp-card text-center p-4">
          <div class="card-header-band" style="background:linear-gradient(90deg, var(--pink-mauve), var(--orange-light), var(--yellow-bright));"></div>
          <div style="font-size:4rem; padding:1rem 0;" aria-hidden="true">🐶</div>
          <h3 style="font-family:var(--font-display); color:var(--orange-deep);">Skipper</h3>
          <p style="font-size:0.88rem; color:var(--text-mid); line-height:1.65;">
            Our adventurous Jack Chi explorer — expect candid action shots of
            beach zoomies and trail-sniffing expeditions!
          </p>
          <a href="intro.php" style="font-size:0.82rem; font-weight:700; color:var(--orange-primary); text-decoration:none;">
            Read Skipper&rsquo;s Story <span aria-hidden="true">→</span>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
