<?php

/**
 * intro.php — Meet the Chihuahuas
 * wipeyourpaws.net · PHP 8.x · Bootstrap 5.3.8
 */
$activePageKey = 'intro';
require_once 'includes/header.php';
?>

<!--  PAGE HERO  -->
<section class="monterey-hero intro-hero">
  <div class="container text-center page-hero-z">
    <h1 class="page-hero-h1">Meet the Chihuahuas!</h1>
    <p class="page-hero-tagline">Faithful and Loving — Two small dogs with hearts <i class="bi bi-suit-heart-fill" aria-hidden="true"></i> the size of the ocean!</p>
    <img src="/images/chandra and skipper.png" alt="two great dogs - chandra and skipper"  class="img-monterey-hero rounded mx-auto d-block shadow">
  </div>
</section>


<!--  DOG PROFILES  -->
<section class="wyp-section">
  <div class="container">

    <div class="text-center mb-5">
      <span class="section-eyebrow">Our Beloved Companions</span>
      <h2 class="section-title">The Dynamic Duo</h2>
      <hr class="section-divider">
      <p class="story-body story-body--wide mx-auto">
        Every wag of a tail, every gleaming pair of eyes at breakfast time, and every
        cozy nap on the couch — Chandra and Skipper fill our days with joy and chaos. Here's a
        little more about who they are.
      </p>
    </div>

    <div class="row g-5 justify-content-center">

      <!-- ── CHANDRA ── -->
      <div class="col-lg-5 col-md-6">
        <div class="dog-profile-card wyp-info-card p-4 text-center h-100">

          <div class="dog-card-top-stripe"></div>

          <h3 class="dog-name">Chandra</h3>
          <p class="dog-catchphrase">"Princess of the House"</p>

          <img src='/images/chandra.jpg' alt='chandra' class='img-fluid rounded mx-auto d-block'>

          <div class="my-3">
            <span class="dog-stat-chip"><i class="bi bi-gender-female" aria-hidden="true"></i> Female</span>
            <span class="dog-stat-chip"><span aria-hidden="true">🐾</span> Chihuahua</span>
            <span class="dog-stat-chip"><span aria-hidden="true">📍</span> Monterey, CA</span>
          </div>

          <p class="dog-bio">
            Chandra is a purebred Chihuahua with all the charm and confidence the breed
            is famous for. Despite her petite frame, she commands every room she enters
            with her bold personality and expressive eyes. She loves sunny spots by the
            window, belly rubs, and is fiercely devoted to Millie.
          </p>

          <ul class="trait-list text-start">
            <li>Spirited, bold, and full of confidence</li>
            <li>Loves warm cuddles and afternoon naps</li>
            <li>Fiercely loyal and protective of her home</li>
            <li>Adores walks along the local neighborhood</li>
            <li>Favorite toy: her plush teddy bear <span aria-hidden="true">🧸</span></li>
          </ul>

          <div class="dog-avatar-frame mt-4">
            <img src='/images/chandra icon 55x55.png' alt="chandra bust icon" width=55 height=55 aria-hidden="true">
          </div>

          <div class="dog-breed-badge">Chihuahua</div>

        </div>
      </div>

      <!-- ── SKIPPER ── -->
      <div class="col-lg-5 col-md-6">
        <div class="dog-profile-card wyp-info-card p-4 text-center h-100">

          <div class="dog-card-top-stripe dog-card-top-stripe--skipper"></div>

          <h3 class="dog-name">Skipper</h3>
          <p class="dog-catchphrase">"The Little Explorer"</p>

          <img src='/images/skipper on couch.jpg' alt='skipper' class='img-fluid rounded mx-auto d-block'>

          <div class="my-3">
            <span class="dog-stat-chip"><i class="bi bi-gender-male" aria-hidden="true"></i> Male</span>
            <span class="dog-stat-chip"><span aria-hidden="true">🐾</span> Chi-Jack</span>
            <span class="dog-stat-chip"><span aria-hidden="true">📍</span> Monterey, CA</span>
          </div>

          <p class="dog-bio">
            Skipper is a Chihuahua-Jack Russell Terrier hybrid, which means he has
            double the energy and triple the curiosity! He's always on the move,
            sniffing out every corner of the neighborhood. Witty, fast, and endlessly
            entertaining, Skipper brings laughter to every moment of the day.
          </p>

          <ul class="trait-list text-start">
            <li>Boundless energy and a nose for adventure</li>
            <li>Quick learner — loves to show off his tricks</li>
            <li>Best friends with Chandra (most of the time <span aria-hidden="true">🤣</span>)</li>
            <li>Loves splashing near the water's edge</li>
            <li>Favorite activity: zoomies in the condo <span aria-hidden="true">🏡</span></li>
          </ul>

          <div class="dog-avatar-frame mt-4">
            <img src='/images/skipper-icon-50x42.png' alt="skipper cartoon icon" width=50 height=42 aria-hidden="true">
          </div>

          <div class="dog-breed-badge">Chihuahua and Jack Russell</div>

        </div>
      </div>

    </div>
  </div>
</section>

<!--  TOGETHER SECTION  -->
<section class="wyp-section wyp-section-sm wyp-section-accent">
  <div class="container">
    <div class="row align-items-center g-5">

      <div class="col-lg-6 text-center">
        <img src='/images/chandra and skipper in bed.jpg' alt='sleeping chandra and skipper in bed' class='img-fluid rounded mx-auto d-block'>
      </div>

      <div class="col-lg-6">
        <span class="section-eyebrow">Together, Always</span>
        <h2 class="section-title mb-3">The Best of Friends</h2>
        <hr class="section-divider">
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
<section class="wyp-section wyp-section-alt">
  <div class="container">

    <div class="text-center mb-5">
      <span class="section-eyebrow">Breed Spotlight</span>
      <h2 class="section-title">About Their Breeds</h2>
      <hr class="section-divider">
    </div>

    <div class="row g-4">

      <div class="col-md-6">
        <div class="wyp-card wyp-info-card h-100 meet-the-pups-border-left">
          <div class="p-4">
            <h3 class="breed-fact-heading">
              <img src='/images/chandra icon 55x55.png' alt="chandra bust icon" width=55 height=55 aria-hidden="true"> Chihuahua
            </h3>
            <ul class="trait-list">
              <li>World's smallest recognized dog breed</li>
              <li>Lifespan: typically 12 to 20 years</li>
              <li>Weight: usually 2 to 6 lbs (0.9 to 2.7 kg)</li>
              <li>Known for fierce loyalty and big personality</li>
              <li>Alert, confident, and highly adaptable</li>
              <li>Originally from the Mexican state of Chihuahua</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="wyp-card wyp-info-card h-100 meet-the-pups-border-left">
          <div class="p-4">
            <h3 class="breed-fact-heading">
              <img src='/images/skipper-icon-50x42.png' alt="skipper cartoon icon" width=50 height=42 aria-hidden="true"> Chihuahua and Jack Russell Terrier
            </h3>
            <ul class="trait-list">
              <li>Affectionately known as a "Jack Chi" or "Chi-Jack"</li>
              <li>Inherits the terrier's energy and chi's loyalty</li>
              <li>Weight: typically 8 to 18 lbs (3.6 to 8 kg)</li>
              <li>Highly intelligent and easy to train with positive reinforcement</li>
              <li>Energetic, playful, and excellent with active families</li>
              <li>Coat and color can vary widely from pup to pup</li>
            </ul>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>