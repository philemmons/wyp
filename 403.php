<?php

/**
 * 403.php — Custom Forbidden page
 * wipeyourpaws.net · PHP 8.x · Bootstrap 5.3.8
 * Triggered by .htaccess: ErrorDocument 403 /403.php
 */
http_response_code(403);
$activePageKey = 'error403';
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
                <a href="/" class="btn btn-wyp btn-wyp-primary">
                    <span aria-hidden="true">🏠</span> Back to Home
                </a>
                <a href="/contact.php" class="btn btn-wyp btn-wyp-outline">
                    Contact Us
                </a>
            </div>

        </div>
    </div>
</section>

<!--  QUICK LINKS STRIP  -->
<section class="quicklinks-section wyp-section-alt">
    <div class="container">
        <h2 class="quicklinks-title text-center">Where would you like to go?</h2>
        <nav aria-label="Error page navigation">
            <div class="row g-3 justify-content-center">
                <?php
                $quickLinkCards = [
                    ['index.php',    '🏠', 'Home',         'Start at the beginning'],
                    ['intro.php',    '🐶', 'Meet the Pups', 'Get to know Chandra &amp; Skipper'],
                    ['monterey.php', '🌊', 'Why Monterey',  'Discover our beautiful home'],
                    ['gallery.php',  '📸', 'Gallery',       'Photos coming soon!'],
                    ['contact.php',  '✉️', 'Contact Us',     'Say hello'],
                ];
                foreach ($quickLinkCards as $quickLinkCard): ?>
                    <div class="col-sm-4 col-md-2">
                        <a href="<?= $quickLinkCard[0] ?>" class="quicklink-card">
                            <span class="quicklink-icon" aria-hidden="true"><?= $quickLinkCard[1] ?></span>
                            <?= $quickLinkCard[2] ?>
                            <span class="quicklink-subtitle"><?= $quickLinkCard[3] ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </nav>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>



