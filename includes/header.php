<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($description) ?>">
  <meta name="author" content="wipeyourpaws.net">
  <meta name="theme-color" content="#F07822">

  <!-- R4: Canonical URL -->
  <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">

  <!-- Open Graph (R3) -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Wipe Your Paws">
  <meta property="og:title" content="<?= htmlspecialchars($title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
  <meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
  <!-- og:image add when image is available: -->
  <!-- <meta property="og:image" content="<?= $base_url ?>/images/og-image.jpg"> -->

  <title><?= htmlspecialchars($title) ?></title>

  <!-- Favicon to be added -->
  <link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16.png">
  <link rel="apple-touch-icon" href="/img/favicons/apple-touch-icon.png">

  <!-- Bootstrap 5.3.8 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">


  <!-- Bootstrap Icons 1.11.3 -->
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Google Fonts (B5 FIX: single load via <link> only CSS @import removed) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Nunito:wght@300;400;600;700;900&family=Playfair+Display:ital,wght@0,600;1,400&display=swap"
    rel="stylesheet">

  <!-- Site stylesheet -->
  <link rel="stylesheet" href="css/styles.css">
</head>

<body>

  <!-- W1: Skip navigation first focusable element on page (WCAG 2.4.1) -->
  <a class="skip-link" href="#main-content">Skip to main content</a>

  <!-- Animated color strip purely decorative -->
  <div class="swirl-strip" aria-hidden="true" role="presentation"></div>

  <!-- W4: aria-label="Main navigation" distinguishes this <nav> from footer nav -->
  <nav class="navbar navbar-expand-lg wyp-navbar" aria-label="Main navigation">
    <div class="container">

      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <!-- W14: decorative emoji is aria-hidden -->
        <span class="paw-brand-icon" aria-hidden="true"><img src='/img/chandra-graphic.png' alt='chandra graphic'></span>
        <div>
          Wipe Your Paws
          <span>Big Love for Small Paws</span>
        </div>
      </a>

      <button class="navbar-toggler" type="button"
        data-bs-toggle="collapse"
        data-bs-target="#wypNav"
        aria-controls="wypNav"
        aria-expanded="false"
        aria-label="Toggle navigation menu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="wypNav">
        <ul class="navbar-nav ms-auto gap-1">
          <?php foreach ($nav_items as $key => $item):
            $is_active = ($page_id === $key);
          ?>
            <li class="nav-item">
              <!-- W3: aria-current="page" communicates active page to screen readers -->
              <a class="nav-link <?= $is_active ? 'active' : '' ?>"
                href="<?= htmlspecialchars($item['href']) ?>"
                <?= $is_active ? 'aria-current="page"' : '' ?>>
                <?= htmlspecialchars($item['label']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

    </div>
  </nav>

  <!-- W2: <main> landmark target for skip link; tabindex="-1" allows programmatic focus -->
  <main id="main-content" tabindex="-1">