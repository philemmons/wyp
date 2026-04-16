<?php
/**
 * index.php — Homepage
 * wipeyourpaws.net · PHP 8.5 · Bootstrap 5.3.8
 */
$page_id = 'home';
require_once 'includes/header.php';
?>

<!-- ░░ HERO SECTION ░░ -->
<section class="wyp-hero">

  <!-- Floating paw decorations -->
  <span class="paw-float" aria-hidden="true">🐾</span>
  <span class="paw-float" aria-hidden="true">🐾</span>
  <span class="paw-float" aria-hidden="true">🐾</span>
  <span class="paw-float" aria-hidden="true">🐾</span>
  <span class="paw-float" aria-hidden="true">🐾</span>

  <!-- SVG swirl background -->
  <svg class="hero-swirl-bg" viewBox="0 0 1400 800" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
    <g fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="3">
      <path d="M 100 600 Q 200 400 400 350 Q 600 300 700 150 Q 800 0 1000 100"/>
      <path d="M 0 400 Q 150 300 300 400 Q 450 500 600 350 Q 750 200 900 300 Q 1050 400 1200 250"/>
      <path d="M 200 800 Q 350 650 500 700 Q 650 750 800 600 Q 950 450 1100 550 Q 1250 650 1400 500"/>
    </g>
    <g fill="rgba(255,255,255,0.08)">
      <circle cx="1100" cy="150" r="180"/>
      <circle cx="150" cy="650" r="120"/>
      <circle cx="700" cy="700" r="90"/>
    </g>
    <g fill="rgba(248,206,16,0.12)">
      <circle cx="900" cy="400" r="220"/>
      <circle cx="300" cy="200" r="100"/>
    </g>
  </svg>

  <div class="container hero-content">
    <div class="row align-items-center g-5">

      <div class="col-lg-7">
        <div class="hero-card">
          <h1>Wipe Your Paws</h1>
          <p class="hero-tagline">Big Love for Small Paws 🐾</p>
          <p class="hero-copy">
            Welcome to wipeyourpaws.net, your go-to haven for small dog enthusiasts!
            Our cozy, pet-friendly space is designed to celebrate the joy of having a
            furry companion. Come in and let's cherish the love for our small pawed
            friends together!
          </p>
          <div class="d-flex flex-wrap gap-3">
            <a href="intro.php" class="btn-wyp btn-wyp-primary">
              Meet Chandra &amp; Skipper 🐶
            </a>
            <a href="monterey.php" class="btn-wyp btn-wyp-outline">
              Explore Monterey
            </a>
          </div>
        </div>
      </div>

      <div class="col-lg-5 text-center d-none d-lg-block">
        <div style="font-size: 11rem; line-height:1; filter: drop-shadow(0 12px 32px rgba(0,0,0,0.2));" aria-hidden="true">
          🐕
        </div>
        <div style="font-family:var(--font-display); color:rgba(255,255,255,0.9); font-size:1.2rem; margin-top:1rem; text-shadow:2px 2px 8px rgba(0,0,0,0.2);">
          Chandra &amp; Skipper say hello!
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ░░ WELCOME FEATURE TILES ░░ -->
<section class="page-section bg-cream">
  <div class="container">

    <div class="text-center mb-5">
      <span class="section-eyebrow">Welcome to Our World</span>
      <h2 class="section-title">Small Dogs, Big Hearts</h2>
      <hr class="section-divider">
    </div>

    <div class="row g-4">

      <!-- Card 1 -->
      <div class="col-md-4">
        <div class="wyp-card h-100">
          <div class="card-header-band"></div>
          <div class="p-4 text-center">
            <div style="font-size:3.5rem; margin-bottom:1rem;" aria-hidden="true">🐕‍🦺</div>
            <h3 style="font-family:var(--font-display); color:var(--orange-deep); font-size:1.2rem; margin-bottom:0.75rem;">
              Meet the Pups
            </h3>
            <p style="font-size:0.92rem; color:var(--text-mid); line-height:1.7;">
              Say hello to Chandra, our spirited Chihuahua, and Skipper, our energetic
              Chihuahua–Jack Russell mix. Two tiny dogs with the biggest hearts you'll
              ever encounter.
            </p>
            <a href="intro.php" class="btn-wyp btn-wyp-primary mt-3" style="font-size:0.82rem; padding:0.6rem 1.5rem;">
              Their Story <span aria-hidden="true">→</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="col-md-4">
        <div class="wyp-card h-100">
          <div class="card-header-band"></div>
          <div class="p-4 text-center">
            <div style="font-size:3.5rem; margin-bottom:1rem;" aria-hidden="true">🌊</div>
            <h3 style="font-family:var(--font-display); color:var(--orange-deep); font-size:1.2rem; margin-bottom:0.75rem;">
              Monterey Bay Life
            </h3>
            <p style="font-size:0.92rem; color:var(--text-mid); line-height:1.7;">
              Monterey, CA is a paradise for small dog lovers — dog-friendly beaches,
              coastal trails, welcoming cafés, and a community that truly adores furry
              companions of every size.
            </p>
            <a href="monterey.php" class="btn-wyp btn-wyp-primary mt-3" style="font-size:0.82rem; padding:0.6rem 1.5rem;">
              Explore <span aria-hidden="true">→</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="col-md-4">
        <div class="wyp-card h-100">
          <div class="card-header-band"></div>
          <div class="p-4 text-center">
            <div style="font-size:3.5rem; margin-bottom:1rem;" aria-hidden="true">📸</div>
            <h3 style="font-family:var(--font-display); color:var(--orange-deep); font-size:1.2rem; margin-bottom:0.75rem;">
              Media Gallery
            </h3>
            <p style="font-size:0.92rem; color:var(--text-mid); line-height:1.7;">
              Our photo gallery is coming soon! We're busy snapping adorable pictures
              of Chandra and Skipper. Check back soon to see all their cute
              adventures.
            </p>
            <a href="gallery.php" class="btn-wyp btn-wyp-primary mt-3" style="font-size:0.82rem; padding:0.6rem 1.5rem;">
              Peek Inside <span aria-hidden="true">→</span>
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ░░ QUOTE STRIP ░░ -->
<section style="background: linear-gradient(135deg, var(--orange-pale), var(--yellow-soft), var(--pink-pale)); padding: 4rem 0;">
  <div class="container text-center">
    <p style="font-family:var(--font-accent); font-style:italic; font-size:clamp(1.4rem, 3vw, 2rem); color:var(--orange-deep); max-width:700px; margin:0 auto; line-height:1.5;">
      "The world would be a nicer place if everyone had the ability to love as unconditionally as a dog."
    </p>
    <p style="font-family:var(--font-body); font-weight:700; font-size:0.85rem; letter-spacing:0.1em; text-transform:uppercase; color:var(--text-mid); margin-top:1rem;">
      — M.K. Clinton
    </p>
  </div>
</section>

<!-- ░░ LOCATION TEASER ░░ -->
<section class="page-section" style="background: var(--warm-white);">
  <div class="container">
    <div class="row align-items-center g-5">

      <div class="col-lg-6">
        <span class="section-eyebrow">Our Home Base</span>
        <h2 class="section-title mb-3">Monterey Bay, California</h2>
        <p style="font-size:1rem; color:var(--text-mid); line-height:1.75; max-width:460px;">
          Nestled along California's stunning central coast, Monterey Bay is one of the
          most dog-welcoming destinations in the country. With miles of coastal trails,
          dog-friendly beaches, and a community that loves four-legged friends, it's the
          perfect home for small paw adventurers like Chandra and Skipper.
        </p>
        <a href="monterey.php" class="btn-wyp btn-wyp-primary mt-2">
          Why We Love It Here
        </a>
      </div>

      <div class="col-lg-6">
        <div class="map-wrapper">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d51729.2!2d-121.9177!3d36.6002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808de15c59e1e2fd%3A0xeabe3a9b9c9b1efc!2sMonterey%2C%20CA!5e0!3m2!1sen!2sus!4v1699999999"
            width="100%" height="320" style="border:0; display:block;" allowfullscreen=""
            loading="lazy" referrerpolicy="no-referrer-when-downgrade"
            title="Interactive map showing Monterey Bay, California">
          </iframe>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ░░ CTA STRIP ░░ -->
<section style="background: linear-gradient(135deg, var(--orange-deep), var(--orange-primary), var(--yellow-bright)); padding:4rem 0; text-align:center;">
  <div class="container">
    <h2 style="font-family:var(--font-display); color:#fff; font-size:clamp(1.6rem, 4vw, 2.5rem); text-shadow:2px 3px 10px rgba(0,0,0,0.2); margin-bottom:0.75rem;">
      Want to Say Hi? <span aria-hidden="true">🐾</span>
    </h2>
    <p style="color:rgba(255,255,255,0.88); font-size:1rem; margin-bottom:1.75rem; max-width:480px; margin-left:auto; margin-right:auto;">
      We'd love to hear from fellow small dog lovers! Drop us a message anytime.
    </p>
    <a href="contact.php" class="btn-wyp" style="background:#fff; color:var(--orange-deep); font-weight:900; padding:0.85rem 2.5rem; box-shadow:0 6px 24px rgba(0,0,0,0.15);">
      Get in Touch <span aria-hidden="true">✉️</span>
    </a>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
