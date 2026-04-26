<?php
/**
 * intro.php — Meet the Chihuahuas
 * wipeyourpaws.net · PHP 8.5 · Bootstrap 5.3.8
 */
$page_id = 'intro';
require_once 'includes/header.php';
?>

<!--  PAGE HERO  -->
<section class="monterey-hero intro-hero">
  <div class="container text-center page-hero-z">
    <span class="page-hero-emoji" aria-hidden="true">🐶🐾🐶</span>
    <h1 class="page-hero-h1">Meet the Chihuahuas!</h1>
    <p class="page-hero-tagline">Faithful, Loving &amp; Full of Personality</p>
  </div>
</section>

<!--  DEDICATION BANNER  -->
<section class="page-section-sm bg-cream">
  <div class="container">
    <div class="dedication-banner">
      <h2>Dedicated to Chandra and Skipper</h2>
      <p>Faithful and Loving — Two small dogs with hearts the size of the ocean <span aria-hidden="true">🌊</span></p>
    </div>
  </div>
</section>

<!--  DOG PROFILES  -->
<section class="page-section bg-warm-white">
  <div class="container">

    <div class="text-center mb-5">
      <span class="section-eyebrow">Our Beloved Companions</span>
      <h2 class="section-title">The Dynamic Duo</h2>
      <hr class="section-divider">
      <p class="story-body story-body--wide mx-auto">
        Every wag of a tail, every gleaming pair of eyes at breakfast time, and every
        cozy nap on the couch — Chandra and Skipper fill our days with joy. Here's a
        little more about who they are.
      </p>
    </div>

    <div class="row g-5 justify-content-center">

      <!-- ── CHANDRA ── -->
      <div class="col-lg-5 col-md-6">
        <div class="dog-profile-card p-4 text-center h-100">

          <div class="dog-card-top-stripe"></div>

          <div class="dog-avatar-frame mb-4" aria-hidden="true">🐕</div>
          <div class="dog-breed-badge">Chihuahua</div>
          <h3 class="dog-name">Chandra</h3>
          <p class="dog-catchphrase">"Princess of the House"</p>

          <div class="mb-3">
            <span class="dog-stat-chip"><i class="bi bi-gender-female" aria-hidden="true"></i> Female</span>
            <span class="dog-stat-chip"><span aria-hidden="true">🐾</span> Chihuahua</span>
            <span class="dog-stat-chip"><span aria-hidden="true">📍</span> Monterey, CA</span>
          </div>

          <p class="dog-bio">
            Chandra is a purebred Chihuahua with all the charm and confidence the breed
            is famous for. Despite her petite frame, she commands every room she enters
            with her bold personality and expressive eyes. She loves sunny spots by the
            window, belly rubs, and is fiercely devoted to her family.
          </p>

          <ul class="trait-list text-start">
            <li>Spirited, bold, and full of confidence</li>
            <li>Loves warm cuddles and afternoon naps</li>
            <li>Fiercely loyal and protective of her home</li>
            <li>Adores walks along the Monterey coastal trail</li>
            <li>Favourite toy: her plush squeaky taco <span aria-hidden="true">🌮</span></li>
          </ul>

        </div>
      </div>

      <!-- ── SKIPPER ── -->
      <div class="col-lg-5 col-md-6">
        <div class="dog-profile-card p-4 text-center h-100">

          <div class="dog-card-top-stripe dog-card-top-stripe--skipper"></div>

          <div class="dog-avatar-frame mb-4" aria-hidden="true">🐶</div>
          <div class="dog-breed-badge">Chihuahua × Jack Russell</div>
          <h3 class="dog-name">Skipper</h3>
          <p class="dog-catchphrase">"The Little Explorer"</p>

          <div class="mb-3">
            <span class="dog-stat-chip"><i class="bi bi-gender-male" aria-hidden="true"></i> Male</span>
            <span class="dog-stat-chip"><span aria-hidden="true">🐾</span> Chi-Jack</span>
            <span class="dog-stat-chip"><span aria-hidden="true">📍</span> Monterey, CA</span>
          </div>

          <p class="dog-bio">
            Skipper is a Chihuahua–Jack Russell Terrier hybrid, which means he has
            double the energy and triple the curiosity! He's always on the move,
            sniffing out every corner of Monterey Bay. Witty, fast, and endlessly
            entertaining, Skipper brings laughter to every moment of the day.
          </p>

          <ul class="trait-list text-start">
            <li>Boundless energy and a nose for adventure</li>
            <li>Quick learner — loves to show off his tricks</li>
            <li>Best friends with Chandra (most of the time <span aria-hidden="true">😄</span>)</li>
            <li>Loves splashing near the water's edge</li>
            <li>Favourite activity: zoomies at Carmel Beach <span aria-hidden="true">🏖️</span></li>
          </ul>

        </div>
      </div>

    </div>
  </div>
</section>

<!--  TOGETHER SECTION  -->
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
          Chandra and Skipper are more than just dogs — they are family, companions,
          and daily reminders of what truly matters in life. Whether they're chasing
          each other through the garden, snuggled together on a rainy afternoon, or
          exploring the coastal paths of beautiful Monterey Bay, every moment with them
          is a treasure.
        </p>
        <p class="story-body">
          This website is a love letter to them — and to all small dogs who bring
          enormous joy to the lives they touch. <span aria-hidden="true">🐾</span>
        </p>
        <div class="mt-3">
          <a href="gallery.php" class="btn-wyp btn-wyp-primary me-2">See the Gallery</a>
          <a href="contact.php" class="btn-wyp btn-wyp-outline-light">Say Hello</a>
        </div>
      </div>

    </div>
  </div>
</section>

<!--  BREED QUICK FACTS  -->
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
              <li>World's smallest recognised dog breed</li>
              <li>Lifespan: typically 12–20 years</li>
              <li>Weight: usually 2–6 lbs (0.9–2.7 kg)</li>
              <li>Known for fierce loyalty and big personality</li>
              <li>Alert, confident, and highly adaptable</li>
              <li>Originally from the Mexican state of Chihuahua</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="wyp-card h-100">
          <div class="card-header-band dog-card-top-stripe--skipper"></div>
          <div class="p-4">
            <h3 class="breed-fact-heading">
              <span aria-hidden="true">🐶</span> Chihuahua &times; Jack Russell Terrier
            </h3>
            <ul class="trait-list">
              <li>Affectionately known as a "Jack Chi" or "Chi-Jack"</li>
              <li>Inherits the terrier's energy and chi's loyalty</li>
              <li>Weight: typically 8–18 lbs (3.6–8 kg)</li>
              <li>Highly intelligent and easy to train with positive reinforcement</li>
              <li>Energetic, playful, and excellent with active families</li>
              <li>Coat and colour can vary widely from pup to pup</li>
            </ul>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
