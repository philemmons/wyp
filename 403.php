<?php

/**
 * 404.php — Custom Not Found page
 * wipeyourpaws.net · PHP 8.5 · Bootstrap 5.3.8
 * Triggered by .htaccess: ErrorDocument 404 /404.php
 */
http_response_code(403);
$page_id = 'home';
require_once 'includes/header.php';
?>

<!--  ERROR HERO  -->
<section class="error-hero">

    <span class="paw-float" aria-hidden="true">🐾</span>
    <span class="paw-float" aria-hidden="true">🐾</span>
    <span class="paw-float" aria-hidden="true">🐾</span>

    <div class="container page-hero-z">
        <div class="error-card">

            <div class="emoji-xl" aria-hidden="true">🐾</div>

            <h1 class="error-404-heading">403</h1>

            <p class="error-tagline">
                Error 403: Chandra ate this page.
            </p>

            <p class="error-body">
                Chandra has already performed a 'perpetual sniff test' on your request and found zero traces of bacon or authorization, but chewed it up anyways.
                Return to the homepage before Skipper wakes up.
            </p>

            <div class="error-btn-row">
                <a href="/" class="btn-wyp btn-wyp-primary">
                    <span aria-hidden="true">🏠</span> Back to Home
                </a>
                <a href="contact.php" class="btn-wyp btn-wyp-outline">
                    Contact Us
                </a>
            </div>

        </div>
    </div>
</section>

<!--  QUICK LINKS STRIP  -->
<section class="quicklinks-section">
    <div class="container">
        <p class="quicklinks-title text-center">Where would you like to go?</p>
        <nav aria-label="Error page navigation">
            <div class="row g-3 justify-content-center">
                <?php
                $links = [
                    ['index.php',    '🏠', 'Home',         'Start at the beginning'],
                    ['intro.php',    '🐶', 'Meet the Pups', 'Get to know Chandra &amp; Skipper'],
                    ['monterey.php', '🌊', 'Why Monterey',  'Discover our beautiful home'],
                    ['gallery.php',  '📸', 'Gallery',       'Photos coming soon!'],
                    ['contact.php',  '✉️', 'Contact Us',     'Say hello'],
                ];
                foreach ($links as $l): ?>
                    <div class="col-sm-4 col-md-2">
                        <a href="<?= $l[0] ?>" class="quicklink-card">
                            <span class="quicklink-icon" aria-hidden="true"><?= $l[1] ?></span>
                            <?= $l[2] ?>
                            <span class="quicklink-subtitle"><?= $l[3] ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </nav>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>