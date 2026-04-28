<?php
ob_start();
session_start();
/**
 * includes/header.php
 * Shared header + navbar — wipeyourpaws.net
 * PHP 8.5 · Bootstrap 5.3.8 · WCAG 2.2 AAA
 */

// $page_id must be set before including this file
$page_id = $page_id ?? 'home';

$nav_items = [
  'home'     => ['href' => '/',            'label' => 'Home'],
  'intro'    => ['href' => 'intro.php',    'label' => 'Meet the Pups'],
  'monterey' => ['href' => 'monterey.php', 'label' => 'Why Monterey'],
  'contact'  => ['href' => 'contact.php',  'label' => 'Contact Us'],
  'gallery'  => ['href' => 'gallery.php',  'label' => 'Gallery'],
];

$page_titles = [
  'home'     => 'Wipe Your Paws - Big Love for Small Paws',
  'intro'    => 'Meet the Pups | Wipe Your Paws',
  'monterey' => 'Why Monterey | Wipe Your Paws',
  'contact'  => 'Contact Us | Wipe Your Paws',
  'gallery'  => 'Media Gallery | Wipe Your Paws',
  'error403' => '403 Forbidden | Wipe Your Paws',
  'error404' => '404 Not Found | Wipe Your Paws',
];

$page_descriptions = [
  'home'     => 'Wipe Your Paws — Big Love for Small Paws. Home of Chandra and Skipper in beautiful Monterey Bay, California.',
  'intro'    => 'Meet Chandra the Chihuahua and Skipper the Jack Chi mix — two small dogs with enormous hearts in Monterey Bay, California.',
  'monterey' => 'Discover why Monterey Bay, California is a paradise for small dog lovers: beaches, trails, pet-friendly culture, and more.',
  'contact'  => 'Get in touch with the Wipe Your Paws team. We love hearing from fellow small dog enthusiasts.',
  'gallery'  => 'Media gallery coming soon — adorable photos of Chandra and Skipper exploring Monterey Bay.',
  'error403' => 'You do not have permission to access this page.',
  'error404' => 'The page you requested could not be found.',
];

$breadcrumb_labels = [
  'intro'    => 'Meet the Pups',
  'monterey' => 'Why Monterey',
  'contact'  => 'Contact Us',
  'gallery'  => 'Media Gallery',
  'error403' => 'Forbidden',
  'error404' => 'Page Not Found',
];

$title       = $page_titles[$page_id]       ?? 'Wipe Your Paws';
$description = $page_descriptions[$page_id] ?? 'Wipe Your Paws - Big Love for Small Paws.';

$base_url = 'https://wipeyourpaws.net';
$script_path = basename(parse_url($_SERVER['PHP_SELF'] ?? '', PHP_URL_PATH) ?: '');
$canonical_path = ($page_id === 'home')
  ? ''
  : ($nav_items[$page_id]['href'] ?? $script_path);
$canonical = rtrim($base_url, '/') . '/' . ltrim($canonical_path, '/');

$show_breadcrumb = ($page_id !== 'home');
$current_crumb = $breadcrumb_labels[$page_id] ?? 'Current Page';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($description) ?>">
  <meta name="author" content="wipeyourpaws.net">
  <meta name="theme-color" content="#F07822">

  <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Wipe Your Paws">
  <meta property="og:title" content="<?= htmlspecialchars($title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
  <meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">

  <title><?= htmlspecialchars($title) ?></title>

  <link rel="icon" type="image/png" sizes="32x32" href="images/favicons/favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="images/favicons/favicon-16.png">
  <link rel="icon" type="image/x-icon" href="/images/favicons/favicon.ico">
  <link rel="apple-touch-icon" href="images/favicons/apple-touch-icon.png">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
    rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
    crossorigin="anonymous">

  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Berkshire+Swash&family=Nunito:wght@300;400;600;700;900&family=Playfair+Display:ital,wght@0,600;1,400&display=swap"
    rel="stylesheet">

  <link rel="stylesheet" href="css/style.css">
</head>

<body id="toTop">

  <a class="skip-link" href="#main-content">Skip to main content</a>

  <header class="site-header">
    <div class="swirl-strip" aria-hidden="true" role="presentation"></div>

    <nav class="navbar navbar-expand-lg wyp-navbar" aria-label="Main navigation">
      <div class="container">

        <a class="navbar-brand d-flex align-items-center" href="/">
          <img class="paw-brand-icon" src="/images/white-paw.png" alt="" aria-hidden="true">
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
                <a class="nav-link <?= $is_active ? 'active' : '' ?>"
                  href="<?= htmlspecialchars($item['href']) ?>"
                  <?= $is_active ? 'aria-current="page"' : '' ?>>
                  <?= htmlspecialchars($item['label']) ?>
                </a>
                <?= $is_active ? '<span class="visually-hidden">(current page)</span>' : '' ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

      </div>
    </nav>
  </header>

  <main id="main-content" tabindex="-1">
    <?php if ($show_breadcrumb): ?>
      <nav class="wyp-breadcrumb-wrap" aria-label="Breadcrumb">
        <div class="container">
          <ol class="breadcrumb wyp-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($current_crumb) ?></li>
          </ol>
        </div>
      </nav>
    <?php endif; ?>

    <section class="reading-tools" aria-label="Reading preferences">
      <div class="container reading-tools__inner">
        <p class="reading-tools__label">Reading preferences</p>
        <div class="reading-tools__actions" role="group" aria-label="Display preference toggles">
          <button type="button" class="reading-pref-btn" data-pref-toggle="contrast" aria-pressed="false">
            High Contrast
          </button>
          <button type="button" class="reading-pref-btn" data-pref-toggle="spacing" aria-pressed="false">
            Comfortable Spacing
          </button>
          <button type="button" class="reading-pref-btn" data-pref-toggle="width" aria-pressed="false">
            Narrow Reading Width
          </button>
        </div>
      </div>
    </section>