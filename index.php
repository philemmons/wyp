<?php

/**
 * index.php — Homepage
 * wipeyourpaws.net · PHP 8.X · Bootstrap 5.3.8
 */
$activePageKey = 'home';
require_once 'includes/header.php';
?>

<!--  HERO SECTION  -->
<section class="wyp-hero">

  <span class="paw-float" aria-hidden="true">🐾</span>
  <span class="paw-float" aria-hidden="true">🐾</span>
  <span class="paw-float" aria-hidden="true">🐾</span>
  <span class="paw-float" aria-hidden="true">🐾</span>
  <span class="paw-float" aria-hidden="true">🐾</span>

  <div class="container hero-content">
    <div class="row align-items-end g-5">

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
            <a href="intro.php" class="btn btn-wyp btn-wyp-primary">
              Meet Chandra &amp; Skipper
            </a>
            <a href="monterey.php" class="btn btn-wyp btn-wyp-outline">
              Explore Monterey
            </a>
          </div>
        </div>
      </div>

      <div class="col-lg-5 text-center d-none d-lg-block">
        <img src="images/chandra-headshot.png" alt="Portrait of Chandra the Chihuahua" width="250" height="252" class="mb-3">
        <div class="hero-card hero-tagline">Chandra &amp; Skipper say hello!</div>
      </div>

    </div>
  </div>
</section>

<!--  WELCOME FEATURE TILES  -->
<section class="wyp-section wyp-section-alt">
  <div class="container">

    <div class="text-center mb-5">
      <span class="section-eyebrow">Welcome to Our World</span>
      <h2 class="section-title">Small Dogs, Big Hearts</h2>
      <hr class="section-divider">
    </div>

    <div class="row g-4">

      <div class="col-md-4">
        <div class="wyp-card wyp-feature-card h-100">
          <div class="p-4 text-center">
            <div class="card-header-band"></div>
            <img src='images/chandra-left-small.png' class="feature-card-icon" alt="" aria-hidden="true">
            <h3 class="feature-card-heading">Meet the Pups</h3>
            <p class="feature-card-body">
              Say hello to Chandra, our spirited Chihuahua, and Skipper, our energetic
              Chihuahua-Jack Russell mix. Two tiny dogs with the biggest hearts you'll
              ever encounter.
            </p>
            <a href="intro.php" class="btn btn-wyp btn-wyp-primary btn-wyp-sm mt-3">
              Their Story <i class="bi bi-chevron-double-right" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="wyp-card wyp-feature-card h-100">
          <div class="p-4 text-center">
            <div class="card-header-band"></div>
            <img src='images/ocean icon final.png' alt="" class="feature-card-icon" aria-hidden="true">
            <h3 class="feature-card-heading">Monterey Bay Life</h3>
            <p class="feature-card-body">
              Monterey, CA is a paradise for small dog lovers — dog-friendly beaches,
              coastal trails, welcoming cafés, and a community that truly adores furry
              companions of every size.
            </p>
            <a href="monterey.php" class="btn btn-wyp btn-wyp-primary btn-wyp-sm mt-3">
              Explore <i class="bi bi-chevron-double-right" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="wyp-card wyp-feature-card h-100">
          <div class="p-4 text-center">
            <div class="card-header-band"></div>
            <img src='images/nikon-small.png' class="feature-card-icon" alt="" aria-hidden="true">
            <h3 class="feature-card-heading">Media Gallery</h3>
            <p class="feature-card-body">
              Our photo gallery is coming soon! We're busy snapping adorable pictures
              of Chandra and Skipper. Check back soon to see all their cute adventures.
            </p>
            <a href="gallery.php" class="btn btn-wyp btn-wyp-primary btn-wyp-sm mt-3">
              Peek Inside <i class="bi bi-chevron-double-right" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!--  QUOTE STRIP  -->
<section class="section-quote">
  <div class="container text-center">
    <p class="section-quote__text">
      "The world would be a nicer place if everyone had the ability to love as unconditionally as a dog."
    </p>
    <p class="section-quote__attr">— M.K. Clinton</p>
  </div>
</section>

<!--  LOCATION TEASER  -->
<section class="wyp-section section-teaser">
  <div class="container">
    <div class="row align-items-center g-5">

      <div class="col-lg-6">
        <span class="section-eyebrow">Our Home Base</span>
        <h2 class="section-title mb-3">Monterey Bay, California</h2>
        <p class="section-teaser__copy">
          Nestled along California's stunning central coast, Monterey Bay is one of the
          most dog-welcoming destinations in the country. With miles of coastal trails,
          dog-friendly beaches, and a community that loves four-legged friends, it's the
          perfect home for small paw adventurers like Chandra and Skipper.
        </p>
        <a href="monterey.php" class="btn btn-wyp btn-wyp-primary mt-2">
          Why We Love It Here
        </a>
      </div>

      <div class="col-lg-6">
        <div class="map-wrapper">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d51729.2!2d-121.9177!3d36.6002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808de15c59e1e2fd%3A0xeabe3a9b9c9b1efc!2sMonterey%2C%20CA!5e0!3m2!1sen!2sus!4v1699999999"
            width="100%" height="320" allowfullscreen=""
            loading="lazy" referrerpolicy="no-referrer-when-downgrade"
            title="Interactive map showing Monterey Bay, California">
          </iframe>
        </div>
      </div>

    </div>
  </div>
</section>

<!--  CTA STRIP  -->
<section class="section-cta wyp-section-accent">
  <div class="container">
    <h2 class="section-cta__h2">
      Want to Say Hi? <span aria-hidden="true">🐾</span>
    </h2>
    <p class="section-cta__p hero-tagline">
      We'd love to hear from fellow small dog lovers! Drop us a message anytime.
    </p>
    <a href="contact.php" class="btn btn-wyp btn-wyp-outline">
      <div>
        Get in Touch
        <i class="bi bi-envelope-open-heart" aria-hidden="true"></i>
      </div>
    </a>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

