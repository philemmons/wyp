<?php

/**
 * includes/footer.php
 * Shared footer - wipeyourpaws.net
 * PHP 8.x - Bootstrap 5.3.8 - WCAG 2.1 AA
 *
 * WCAG fixes:
 *   W2  - </main> closed here
 *   W4  - aria-label="Footer navigation" on footer <nav>
 *   W7  - prefers-reduced-motion handled in scroll_reveal_controller.js
 *   W14 - aria-hidden="true" on decorative Bootstrap icons
 *   R2  - <noscript> style restores element visibility if JS disabled
 */
?>

<!-- Back to Top Button -->
<a href="#toTop"
  class="btn btn-primary back-to-top"
  id="back-to-top-link"
  title="Back to Top"
  tabindex="-1"
  aria-hidden="true"
  aria-label="Back to Top">
  <i class="bi bi-chevron-double-up" aria-hidden="true"></i>
</a>

</main>

<!--  FOOTER  -->
<footer class="wyp-footer mt-auto">
  <div class="container">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-4 g-4 align-items-start">

      <!-- Brand column -->
      <div class="col">
        <div class="footer-brand mb-1">
          Wipe Your Paws
        </div>
        <div class="footer-tagline mb-3">
          Big Love for Small Paws <span aria-hidden="true">??</span>
        </div>
        <p class="footer-desc">
          Celebrating the joy of small dogs with Chandra &amp; Skipper <i class="bi bi-suit-heart-fill" aria-hidden="true"></i>
          your cozy corner of the internet for small paw enthusiasts.
        </p>
        <div class="mt-3">
          <a href="https://www.facebook.com/" class="social-circle" aria-label="Visit Facebook">
            <i class="bi bi-facebook" aria-hidden="true"></i>
          </a>
          <a href="https://www.instagram.com/" class="social-circle" aria-label="Visit Instagram">
            <i class="bi bi-instagram" aria-hidden="true"></i>
          </a>
          <a href="https://www.tiktok.com/" class="social-circle" aria-label="Visit TikTok">
            <i class="bi bi-tiktok" aria-hidden="true"></i>
          </a>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="col">
        <h2 class="footer-col-heading">Quick Links</h2>
        <nav aria-label="Footer navigation" class="footer-nav d-flex flex-column gap-2">
          <a href="index.php">Home</a>
          <a href="intro.php">Meet the Pups</a>
          <a href="monterey.php">Why Monterey</a>
          <a href="contact.php">Contact Us</a>
          <a href="gallery.php">Gallery</a>
        </nav>
      </div>

      <!-- Contact -->
      <div class="col">
        <h2 class="footer-col-heading">Get in Touch</h2>
        <address class="footer-contact-address d-flex flex-column gap-2">
          <div>
            <i class="bi bi-envelope-fill me-2 footer-icon" aria-hidden="true"></i>
            <a href="mailto:admin@wipeyourpaws.net" class="text-break">admin@wipeyourpaws.net</a>
          </div>
          <div>
            <i class="bi bi-geo-alt-fill me-2 footer-icon" aria-hidden="true"></i>
            Monterey Bay, CA
          </div>
        </address>
      </div>

      <!-- Fun fact -->
      <div class="col">
        <h2 class="footer-col-heading">
          <span>
            Did You Know?
            <img src='/images/skipper-icon.png' alt="" width=35 height=30 aria-hidden="true">
          </span>
        </h2>
        <p class="footer-col-body">
          Chihuahuas are the world's smallest dog breed but are known for having
          some of the biggest personalities! Despite their tiny stature, they are
          fiercely loyal and love to cuddle.
        </p>
      </div>

    </div>

    <hr class="footer-divider">

    <div class="row footer-bottom gy-2 align-items-center">
      <div class="col-12 col-md-auto">
        <span>&copy; <?= date('Y') ?> wipeyourpaws.net &mdash; All rights reserved.</span>
      </div>
      <div class="col-12 col-md-auto">
        <span>
          Made with <i class="bi bi-suit-heart-fill" aria-hidden="true"></i><span class="visually-hidden">LOVE</span> for Chandra &amp; Skipper
          <span aria-hidden="true">🐾</span>
        </span>
      </div>
    </div>

  </div>
</footer>


<!-- JavaScript (CSP-compliant: external only) -->

<!-- Bootstrap Bundle -->
<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
  crossorigin="anonymous"
  defer>
</script>

<!-- Back to Top -->
<script src="/js/back_to_top_control.js?v=<?= filemtime(__DIR__ . '/../js/back_to_top_control.js'); ?>" defer></script>

<!-- App JS (replaces inline animation script) -->
<script src="/js/scroll_reveal_controller.js?v=<?= filemtime(__DIR__ . '/../js/scroll_reveal_controller.js'); ?>" defer></script>

</body>

</html>
