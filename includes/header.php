<?php

/**
 * includes/header.php
 * Shared header + navbar â€” wipeyourpaws.net
 * PHP 8.5 Â· Bootstrap 5.3.8 Â· WCAG 2.1 AA rev.2
 *
 * WCAG fixes applied:
 *   W1  â€” Skip-to-main link
 *   W2  â€” <main> landmark opened here (closed in footer.php)
 *   W3  â€” aria-current="page" on active nav item
 *   W4  â€” aria-label="Main navigation" on <nav>
 *   B5  â€” Google Fonts loaded via <link> only (CSS @import removed)
 *   R3  â€” og:url, og:type, og:image placeholders added
 *   R4  â€” <link rel="canonical"> added
 */

// $page_id must be set before including this file
$page_id = $page_id ?? 'home';

$nav_items = [
  'home'     => ['href' => 'index.php',    'label' => 'Home'],
  'intro'    => ['href' => 'intro.php',    'label' => 'Meet the Pups'],
  'monterey' => ['href' => 'monterey.php', 'label' => 'Why Monterey'],
  'contact'  => ['href' => 'contact.php',  'label' => 'Contact Us'],
  'gallery'  => ['href' => 'gallery.php',  'label' => 'Gallery'],
];

$page_titles = [
  'home'     => 'Wipe Your Paws â€” Big Love for Small Paws',
  'intro'    => 'Meet the Pups | Wipe Your Paws',
  'monterey' => 'Why Monterey | Wipe Your Paws',
  'contact'  => 'Contact Us | Wipe Your Paws',
  'gallery'  => 'Media Gallery | Wipe Your Paws',
];

$page_descriptions = [
  'home'     => 'Wipe Your Paws â€” Big Love for Small Paws. Home of Chandra and Skipper, devoted to small dog lovers in beautiful Monterey Bay, CA.',
  'intro'    => 'Meet Chandra the Chihuahua and Skipper the Chi-Jack mix â€” two small dogs with enormous hearts living in Monterey Bay, CA.',
  'monterey' => 'Discover why Monterey Bay, California is a paradise for small dog lovers â€” beaches, trails, pet-friendly culture and more.',
  'contact'  => 'Get in touch with the Wipe Your Paws team. We love hearing from fellow small dog enthusiasts!',
  'gallery'  => 'Media gallery coming soon â€” adorable photos of Chandra and Skipper exploring Monterey Bay.',
];

$title       = $page_titles[$page_id]       ?? 'Wipe Your Paws';
$description = $page_descriptions[$page_id] ?? 'Wipe Your Paws â€” Big Love for Small Paws.';

// Build canonical URL (update BASE_URL when deploying)
$base_url    = 'https://wipeyourpaws.net';
$canonical   = $base_url . '/' . ($page_id === 'home' ? '' : ($nav_items[$page_id]['href'] ?? ''));
?>

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
  <!-- og:image â€” add when image is available: -->
  <!-- <meta property="og:image" content="<?= $base_url ?>/images/og-image.jpg"> -->

  <title><?= htmlspecialchars($title) ?></title>

  <!-- Favicon â€” to be added -->
  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32.png">
       <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16.png">
       <link rel="apple-touch-icon"       href="img/apple-touch-icon.png">

  <!-- Bootstrap 5.3.8 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">


  <!-- Bootstrap Icons 1.11.3 -->
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Google Fonts (B5 FIX: single load via <link> only â€” CSS @import removed) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Nunito:wght@300;400;600;700;900&family=Playfair+Display:ital,wght@0,600;1,400&display=swap"
    rel="stylesheet">

  <!-- Site stylesheet -->
  <link rel="stylesheet" href="css/styles.css">
</head>

<body>

  <!-- W1: Skip navigation â€” first focusable element on page (WCAG 2.4.1) -->
  <a class="skip-link" href="#main-content">Skip to main content</a>

  <!-- Animated color strip â€” purely decorative -->
  <div class="swirl-strip" aria-hidden="true" role="presentation"></div>

  <!-- W4: aria-label="Main navigation" distinguishes this <nav> from footer nav -->
  <nav class="navbar navbar-expand-lg wyp-navbar" aria-label="Main navigation">
    <div class="container">

      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <!-- W14: decorative emoji is aria-hidden -->
        <span class="paw-brand-icon" aria-hidden="true">🐾</span>
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

  <!-- W2: <main> landmark â€” target for skip link; tabindex="-1" allows programmatic focus -->
  <main id="main-content" tabindex="-1">