<?php
/**
 * 404.php — Custom Not Found page
 * wipeyourpaws.net · PHP 8.5 · Bootstrap 5.3.8
 * Triggered by .htaccess: ErrorDocument 404 /404.php
 */
http_response_code(404);
$page_id = 'home'; // keeps "Home" as active in nav
require_once 'includes/header.php';
?>

<section style="
  min-height: 75vh;
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, var(--orange-deep) 0%, var(--orange-primary) 50%, var(--yellow-bright) 100%);
  position: relative;
  overflow: hidden;">

  <!-- Decorative paw floats -->
  <span class="paw-float" aria-hidden="true">🐾</span>
  <span class="paw-float" aria-hidden="true">🐾</span>
  <span class="paw-float" aria-hidden="true">🐾</span>

  <div class="container text-center position-relative" style="z-index:2;">
    <div style="
      background: rgba(255,255,255,0.18);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,0.35);
      border-radius: var(--radius-xl);
      padding: 3rem 2.5rem;
      max-width: 580px;
      margin: 0 auto;
      box-shadow: 0 20px 60px rgba(217,72,32,0.3);">

      <div style="font-size: 5rem; margin-bottom: 0.5rem;" aria-hidden="true">🐾</div>

      <h1 style="
        font-family: var(--font-display);
        color: #fff;
        font-size: clamp(3rem, 10vw, 6rem);
        line-height: 1;
        text-shadow: 3px 4px 12px rgba(0,0,0,0.25);
        margin-bottom: 0.25rem;">
        404
      </h1>

      <p style="
        font-family: var(--font-accent);
        font-style: italic;
        color: rgba(255,255,255,0.95);
        font-size: 1.3rem;
        margin-bottom: 0.75rem;">
        Oops &mdash; this page ran off like Skipper on a zoomie!
      </p>

      <p style="
        color: rgba(255,255,255,0.88);
        font-size: 0.98rem;
        line-height: 1.75;
        max-width: 420px;
        margin: 0 auto 2rem;">
        The page you&rsquo;re looking for doesn&rsquo;t exist, may have moved,
        or perhaps Chandra buried it somewhere in the garden.
        Let&rsquo;s get you back on the right trail!
      </p>

      <div style="display:flex; flex-wrap:wrap; gap:1rem; justify-content:center;">
        <a href="index.php" class="btn-wyp btn-wyp-primary">
          <span aria-hidden="true">🏠</span> Back to Home
        </a>
        <a href="contact.php" class="btn-wyp btn-wyp-outline">
          Contact Us
        </a>
      </div>

    </div>
  </div>
</section>

<!-- Quick links strip -->
<section style="background: var(--cream); padding: 3rem 0;">
  <div class="container">
    <p class="text-center" style="font-family:var(--font-display); color:var(--orange-deep); font-size:1.1rem; margin-bottom:1.5rem;">
      Where would you like to go?
    </p>
    <nav aria-label="Error page navigation">
      <div class="row g-3 justify-content-center">
        <?php
        $links = [
          ['index.php',    '🏠', 'Home',          'Start at the beginning'],
          ['intro.php',    '🐶', 'Meet the Pups',  'Get to know Chandra &amp; Skipper'],
          ['monterey.php', '🌊', 'Why Monterey',   'Discover our beautiful home'],
          ['gallery.php',  '📸', 'Gallery',        'Photos coming soon!'],
          ['contact.php',  '✉️', 'Contact Us',      'Say hello'],
        ];
        foreach ($links as $l): ?>
        <div class="col-sm-4 col-md-2">
          <a href="<?= $l[0] ?>" style="
            display:block; text-align:center;
            background: var(--warm-white);
            border: 2px solid var(--orange-pale);
            border-radius: var(--radius-lg);
            padding: 1.2rem 0.75rem;
            text-decoration: none;
            color: var(--orange-deep);
            font-weight: 700;
            font-size: 0.82rem;
            transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
            box-shadow: var(--shadow-card);"
            onmouseover="this.style.borderColor='var(--orange-primary)';this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(240,120,34,0.25)'"
            onmouseout="this.style.borderColor='var(--orange-pale)';this.style.transform='';this.style.boxShadow='var(--shadow-card)'">
            <span style="font-size:1.8rem; display:block; margin-bottom:0.4rem;" aria-hidden="true"><?= $l[1] ?></span>
            <?= $l[2] ?>
            <span style="display:block; font-size:0.72rem; color:var(--text-light); font-weight:400; margin-top:0.2rem;"><?= $l[3] ?></span>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </nav>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
