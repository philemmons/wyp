<?php
/**
 * includes/footer.php
 * Shared footer — wipeyourpaws.net
 * PHP 8.5 · Bootstrap 5.3.8 · WCAG 2.2 AAA
 */
?>

<a href="#toTop"
  class="btn btn-primary back-to-top"
  id="back-to-top-link"
  title="Back to top"
  aria-label="Back to top">
  <i class="bi bi-chevron-double-up" aria-hidden="true"></i>
</a>

</main>

<noscript>
  <style>
    .wyp-card,
    .dog-profile-card,
    .monterey-category-card,
    .gallery-placeholder-item,
    .dedication-banner,
    .contact-info-box,
    .wyp-form {
      opacity: 1 !important;
      transform: none !important;
    }
  </style>
</noscript>

<footer class="wyp-footer mt-auto">
  <div class="container">
    <div class="row g-4">

      <div class="col-md-4">
        <p class="footer-brand mb-1">Wipe Your Paws</p>
        <p class="footer-tagline mb-3">
          Big Love for Small Paws <span aria-hidden="true">🐾</span>
        </p>
        <p class="footer-desc">
          Celebrating the joy of small dogs with Chandra and Skipper —
          your cozy corner of the internet for small paw enthusiasts.
        </p>
        <div class="mt-3">
          <a href="https://www.facebook.com/" class="social-circle" target="_blank" rel="noopener noreferrer">
            <span class="sr-only">Follow us on Facebook</span>
            <i class="bi bi-facebook" aria-hidden="true"></i>
          </a>
          <a href="https://www.instagram.com/" class="social-circle" target="_blank" rel="noopener noreferrer">
            <span class="sr-only">Follow us on Instagram</span>
            <i class="bi bi-instagram" aria-hidden="true"></i>
          </a>
          <a href="https://www.tiktok.com/" class="social-circle" target="_blank" rel="noopener noreferrer">
            <span class="sr-only">Follow us on TikTok</span>
            <i class="bi bi-tiktok" aria-hidden="true"></i>
          </a>
        </div>
      </div>

      <div class="col-md-2 col-6">
        <h2 class="footer-col-heading">Quick Links</h2>
        <nav aria-label="Footer navigation" class="footer-nav">
          <a href="index.php">Home</a>
          <a href="intro.php">Meet the Pups</a>
          <a href="monterey.php">Why Monterey</a>
          <a href="contact.php">Contact Us</a>
          <a href="gallery.php">Gallery</a>
        </nav>
      </div>

      <div class="col-md-3 col-6">
        <h2 class="footer-col-heading">Get in Touch</h2>
        <address class="footer-contact-address">
          <div>
            <i class="bi bi-envelope-fill me-2 footer-icon" aria-hidden="true"></i>
            <a href="mailto:admin@wipeyourpaws.net">admin@wipeyourpaws.net</a>
          </div>
          <div>
            <i class="bi bi-geo-alt-fill me-2 footer-icon" aria-hidden="true"></i>
            Monterey Bay, <abbr title="California">CA</abbr>
          </div>
        </address>
      </div>

      <div class="col-md-3">
        <h2 class="footer-col-heading">
          Did You Know?
          <img src="/images/skipper-icon.png" alt="" width="35" height="30" aria-hidden="true">
        </h2>
        <p class="footer-col-body">
          Chihuahuas are the world’s smallest dog breed and often have big personalities.
          They are loyal, alert, and they love to cuddle.
        </p>
      </div>

    </div>

    <hr class="footer-divider">

    <div class="d-flex flex-wrap justify-content-between align-items-center footer-bottom">
      <span>&copy; <?= date('Y') ?> wipeyourpaws.net — All rights reserved.</span>
      <span>
        Made with <span aria-hidden="true">🧡</span><span class="sr-only">love</span> for Chandra and Skipper
        <span aria-hidden="true">🐾</span>
      </span>
    </div>

  </div>
</footer>

<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
  crossorigin="anonymous"
  defer>
</script>

<script src="/js/backToTop.js?v=<?= filemtime(__DIR__ . '/../js/backToTop.js'); ?>" defer></script>
<script src="/js/app.js?v=<?= filemtime(__DIR__ . '/../js/app.js'); ?>" defer></script>
<script src="/js/accessibilityPrefs.js?v=<?= filemtime(__DIR__ . '/../js/accessibilityPrefs.js'); ?>" defer></script>

</body>

</html>
