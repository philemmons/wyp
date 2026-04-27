<?php
/**
 * intro.php — Meet the Chihuahuas
 * wipeyourpaws.net · PHP 8.5 · Bootstrap 5.3.8
 */
$page_id = 'intro';
require_once 'includes/header.php';
?>

<section class="monterey-hero intro-hero">
  <div class="container text-center page-hero-z">
    <span class="page-hero-emoji" aria-hidden="true">🐶🐾🐶</span>
    <h1 class="page-hero-h1">Meet the Chihuahuas</h1>
    <p class="page-hero-tagline">Faithful, loving, and full of personality</p>
  </div>
</section>

<section class="page-section-sm bg-cream">
  <div class="container">
    <details class="simple-summary">
      <summary>Simple summary of this page</summary>
      <p>
        This page introduces Chandra and Skipper. You can read their traits, see quick breed facts,
        and learn key terms in plain language.
      </p>
    </details>
  </div>
</section>

<section class="page-section-sm bg-cream">
  <div class="container">
    <div class="dedication-banner">
      <h2>Dedicated to Chandra and Skipper</h2>
      <p>Two small dogs with big hearts <span aria-hidden="true">🌊</span></p>
    </div>
  </div>
</section>

<section class="page-section bg-warm-white">
  <div class="container">

    <div class="text-center mb-5">
      <span class="section-eyebrow">Our Beloved Companions</span>
      <h2 class="section-title">The Dynamic Duo</h2>
      <hr class="section-divider">
      <p class="story-body story-body--wide mx-auto">
        Chandra and Skipper fill our days with joy. Here is a closer look at each pup.
      </p>
    </div>

    <div class="row g-5 justify-content-center">

      <div class="col-lg-5 col-md-6">
        <div class="dog-profile-card p-4 text-center h-100">
          <div class="dog-card-top-stripe"></div>
          <div class="dog-avatar-frame mb-4" aria-hidden="true">🐕</div>
          <div class="dog-breed-badge">Chihuahua</div>
          <h3 class="dog-name">Chandra</h3>
          <p class="dog-catchphrase">"Princess of the house"</p>

          <div class="mb-3">
            <span class="dog-stat-chip"><i class="bi bi-gender-female" aria-hidden="true"></i> Female</span>
            <span class="dog-stat-chip"><span aria-hidden="true">🐾</span> Chihuahua</span>
            <span class="dog-stat-chip"><span aria-hidden="true">📍</span> Monterey Bay, <abbr title="California">CA</abbr></span>
          </div>

          <p class="dog-bio">
            Chandra is a purebred Chihuahua with confidence and charm.
            She loves warm cuddles and sunny windows.
          </p>

          <ul class="trait-list text-start">
            <li>Spirited and confident</li>
            <li>Loves naps and cuddles</li>
            <li>Loyal and protective</li>
            <li>Enjoys Monterey coastal walks</li>
            <li>Favorite toy: plush squeaky taco <span aria-hidden="true">🌮</span></li>
          </ul>
        </div>
      </div>

      <div class="col-lg-5 col-md-6">
        <div class="dog-profile-card p-4 text-center h-100">
          <div class="dog-card-top-stripe dog-card-top-stripe--skipper"></div>

          <div class="dog-avatar-frame mb-4" aria-hidden="true">🐶</div>
          <div class="dog-breed-badge">Chihuahua × Jack Russell</div>
          <h3 class="dog-name">Skipper</h3>
          <p class="dog-catchphrase">"The little explorer"</p>

          <div class="mb-3">
            <span class="dog-stat-chip"><i class="bi bi-gender-male" aria-hidden="true"></i> Male</span>
            <span class="dog-stat-chip"><span aria-hidden="true">🐾</span> <abbr title="Chihuahua and Jack Russell Terrier mix">Jack Chi</abbr></span>
            <span class="dog-stat-chip"><span aria-hidden="true">📍</span> Monterey Bay, <abbr title="California">CA</abbr></span>
          </div>

          <p class="dog-bio">
            Skipper is a Chihuahua and Jack Russell mix with high energy and strong curiosity.
            He loves exploring every path near the coast.
          </p>

          <ul class="trait-list text-start">
            <li>Full of energy and adventure</li>
            <li>Quick learner and playful companion</li>
            <li>Best friends with Chandra most days <span aria-hidden="true">😄</span></li>
            <li>Loves splashing near the shoreline</li>
            <li>Favorite activity: <dfn>zoomies</dfn> at Carmel Beach <span aria-hidden="true">🏖️</span></li>
          </ul>
        </div>
      </div>

    </div>
  </div>
</section>

<section class="page-section-sm section-pastel">
  <div class="container">
    <div class="row align-items-center g-5">

      <div class="col-lg-6 text-center">
        <div class="story-emoji" aria-hidden="true">🐕🐶</div>
      </div>

      <div class="col-lg-6">
        <span class="section-eyebrow">Together, Always</span>
        <h2 class="section-title mb-3">The Best of Friends</h2>
        <p class="story-body">
          Chandra and Skipper are family, companions, and daily reminders of joy.
          They explore, cuddle, and play together every day.
        </p>
        <p class="story-body">
          This website is a love letter to them and to all small dogs. <span aria-hidden="true">🐾</span>
        </p>
        <div class="mt-3">
          <a href="gallery.php" class="btn-wyp btn-wyp-primary me-2">Open the Gallery Page</a>
          <a href="contact.php" class="btn-wyp btn-wyp-outline-light">Open the Contact Form</a>
        </div>
      </div>

    </div>
  </div>
</section>

<section class="page-section bg-cream">
  <div class="container">

    <div class="text-center mb-5">
      <span class="section-eyebrow">Breed Spotlight</span>
      <h2 class="section-title">About Their Breeds</h2>
      <hr class="section-divider">
    </div>

    <div class="row g-4">
      <div class="col-md-6">
        <div class="wyp-card h-100">
          <div class="card-header-band"></div>
          <div class="p-4">
            <h3 class="breed-fact-heading">
              <span aria-hidden="true">🐕</span> Chihuahua
            </h3>
            <ul class="trait-list">
              <li>World’s smallest recognized dog breed</li>
              <li>Lifespan: typically 12 to 20 years</li>
              <li>Weight: usually 2 to 6 pounds (0.9 to 2.7 kg)</li>
              <li>Known for loyalty and bold personality</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="wyp-card h-100">
          <div class="card-header-band dog-card-top-stripe--skipper"></div>
          <div class="p-4">
            <h3 class="breed-fact-heading">
              <span aria-hidden="true">🐶</span> Chihuahua × Jack Russell Terrier
            </h3>
            <ul class="trait-list">
              <li>Also called Jack Chi or Chi-Jack</li>
              <li>Mixes terrier energy with Chihuahua loyalty</li>
              <li>Weight: typically 8 to 18 pounds (3.6 to 8 kg)</li>
              <li>Smart, playful, and active</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="page-section-sm bg-warm-white">
  <div class="container">
    <h2 class="section-title text-center">Helpful Terms</h2>
    <hr class="section-divider">
    <dl class="wyp-glossary">
      <dt>Jack Chi (Chi-Jack)</dt>
      <dd>A mixed breed dog with one Chihuahua parent and one Jack Russell Terrier parent.</dd>
      <dt>Zoomies</dt>
      <dd>Short bursts of fast, playful running that many dogs do when excited.</dd>
      <dt>Monterey Bay, <abbr title="California">CA</abbr></dt>
      <dd>Monterey Bay in the U.S. state of California.</dd>
    </dl>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
