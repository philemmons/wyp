<?php

/**
 * includes/footer.php
 * Shared footer - wipeyourpaws.net
 * PHP 8.5 - Bootstrap 5.3.8 - WCAG 2.1 AA rev.2
 *
 * WCAG fixes applied:
 *   W2  - </main> closed here
 *   W4  - aria-label="Footer navigation" on footer <nav>
 *   W7  - prefers-reduced-motion checked in JS
 *   W14 - aria-hidden="true" on decorative Bootstrap icons
 */
?>

<!-- W2: Close <main> landmark (opened in header.php) -->
</main>

<!-- FOOTER -->
<footer class="wyp-footer mt-auto">
  <div class="container">
    <div class="row g-4">

      <!-- Brand column -->
      <div class="col-md-4">
        <div class="footer-brand mb-1">
          <span aria-hidden="true">&#128062;</span> Wipe Your Paws
        </div>
        <div class="footer-tagline mb-3">Big Love for Small Paws</div>
        <p class="footer-small-copy">
          Celebrating the joy of small dogs with Chandra &amp; Skipper -
          your cozy corner of the internet for small paw enthusiasts.
        </p>
        <div class="mt-3">
          <!-- W14: icon elements are decorative inside labelled links -->
          <a href="#" class="social-circle" aria-label="Follow us on Facebook">
            <i class="bi bi-facebook" aria-hidden="true"></i>
          </a>
          <a href="#" class="social-circle" aria-label="Follow us on Instagram">
            <i class="bi bi-instagram" aria-hidden="true"></i>
          </a>
          <a href="#" class="social-circle" aria-label="Follow us on TikTok">
            <i class="bi bi-tiktok" aria-hidden="true"></i>
          </a>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="col-md-2 col-6">
        <h2 class="footer-heading">
          Quick Links
        </h2>
        <!-- W4: aria-label distinguishes footer nav from main nav -->
        <nav aria-label="Footer navigation" class="footer-nav">
          <a href="index.php">Home</a>
          <a href="intro.php">Meet the Pups</a>
          <a href="monterey.php">Why Monterey</a>
          <a href="contact.php">Contact Us</a>
          <a href="gallery.php">Gallery</a>
        </nav>
      </div>

      <!-- Contact snippet -->
      <div class="col-md-3 col-6">
        <h2 class="footer-heading">
          Get in Touch
        </h2>
        <address class="footer-contact">
          <div>
            <i class="bi bi-envelope-fill me-2 footer-contact-icon" aria-hidden="true"></i>
            <a href="mailto:admin@wipeyourpaws.net">admin@wipeyourpaws.net</a>
          </div>
          <div>
            <i class="bi bi-geo-alt-fill me-2 footer-contact-icon" aria-hidden="true"></i>
            Monterey Bay, CA
          </div>
        </address>
      </div>

      <!-- Fun fact -->
      <div class="col-md-3">
        <h2 class="footer-heading">
          Did You Know?
          <span aria-hidden="true"> &#128054;</span>
        </h2>
        <p class="footer-fun-fact">
          Chihuahuas are the world's smallest dog breed but are known for having
          some of the biggest personalities! Despite their tiny stature, they are
          fiercely loyal and love to cuddle.
        </p>
      </div>

    </div>

    <hr class="footer-divider">

    <div class="d-flex flex-wrap justify-content-between align-items-center footer-bottom">
      <span>&copy; <?= date('Y') ?> wipeyourpaws.net - All rights reserved.</span>
      <span>Made with <span aria-label="love">&#129505;</span> for Chandra &amp; Skipper
        <span aria-hidden="true">&#128062;</span>
      </span>
    </div>

  </div>
</footer>

<!-- Bootstrap 5.3.8 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>


<!-- Site JS -->
<script>
  (function() {
    'use strict';

    // W7: Respect prefers-reduced-motion (WCAG 2.3.3)
    const prefersReducedMotion = window.matchMedia(
      '(prefers-reduced-motion: reduce)'
    ).matches;

    const targets = document.querySelectorAll(
      '.wyp-card, .dog-profile-card, .monterey-category-card, ' +
      '.gallery-placeholder-item, .dedication-banner, .contact-info-box, .wyp-form'
    );

    // If user prefers reduced motion OR IntersectionObserver unavailable,
    // make all elements immediately visible without animation
    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
      targets.forEach(function(t) {
        t.style.opacity = '1';
        t.style.transform = '';
      });
      return;
    }

    // Fade-in-on-scroll (only when motion is acceptable)
    const io = new IntersectionObserver(function(entries) {
      entries.forEach(function(e) {
        if (e.isIntersecting) {
          e.target.style.opacity = '1';
          e.target.style.transform = '';
          io.unobserve(e.target);
        }
      });
    }, {
      threshold: 0.1
    });

    targets.forEach(function(t) {
      t.style.opacity = '0';
      t.style.transform = 'translateY(28px)';
      t.style.transition = 'opacity 0.55s ease, transform 0.55s ease';
      io.observe(t);
    });
  }());
</script>

</body>

</html>
