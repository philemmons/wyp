<?php

/**
 * gallery.php - Media Gallery
 * wipeyourpaws.net | PHP 8.x | Bootstrap 5.3.8
 */
$activePageKey = 'gallery';

/**
 * Convert the first 8 consecutive digits in a filename into MM-DD-YYYY.
 * Example: 20230319_123456.jpg -> 03-19-2023
 */
function formatGalleryDateFromFilename(string $fileBaseName): ?string
{
  if (!preg_match('/(\d{8})/', $fileBaseName, $matches)) {
    return null;
  }

  $digits = $matches[1];
  $year = (int) substr($digits, 0, 4);
  $month = (int) substr($digits, 4, 2);
  $day = (int) substr($digits, 6, 2);

  if (!checkdate($month, $day, $year)) {
    return null;
  }

  return sprintf('%02d-%02d-%04d', $month, $day, $year);
}

/**
 * Build alt text from filename components plus date when available.
 * Returns [altText, isVagueFilename].
 */
function buildGalleryAltText(string $fileBaseName): array
{
  $formattedDate = formatGalleryDateFromFilename($fileBaseName);
  $normalized = preg_replace('/\(\d+\)/', ' ', $fileBaseName) ?? $fileBaseName;
  $normalized = preg_replace('/\d{8}/', ' ', $normalized) ?? $normalized;
  $normalized = preg_replace('/\d{6}/', ' ', $normalized) ?? $normalized;
  $normalized = str_replace(['_', '-'], ' ', $normalized);

  preg_match_all('/[A-Za-z]+/', $normalized, $wordMatches);
  $words = array_values(array_filter(array_map('strtolower', $wordMatches[0] ?? [])));
  $isVagueFilename = ($words === []);

  if ($isVagueFilename) {
    $altText = 'Chandra and Skipper gallery photo';
    if ($formattedDate !== null) {
      $altText .= ' from ' . $formattedDate;
    }
    return [$altText, true];
  }

  $descriptor = ucwords(implode(' ', array_slice($words, 0, 6)));
  $altText = $descriptor . ' with Chandra and Skipper';
  if ($formattedDate !== null) {
    $altText .= ' on ' . $formattedDate;
  }

  return [$altText, false];
}

$expectedImageCount = 76;
$galleryDirectoryAbsolutePath = __DIR__ . '/images/gallery';
$galleryDirectoryWebPath = '/images/gallery';
$supportedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'avif'];

$galleryItems = [];
$galleryPathIssues = [];
$vagueAltTextFilenames = [];

if (!is_dir($galleryDirectoryAbsolutePath)) {
  $galleryPathIssues[] = 'Gallery directory not found: ' . $galleryDirectoryAbsolutePath;
} else {
  // Discover images from /images/gallery so we avoid hardcoding image blocks.
  $directoryEntries = scandir($galleryDirectoryAbsolutePath);
  if ($directoryEntries === false) {
    $galleryPathIssues[] = 'Unable to read gallery directory: ' . $galleryDirectoryAbsolutePath;
  } else {
    foreach ($directoryEntries as $entry) {
      if ($entry === '.' || $entry === '..') {
        continue;
      }

      $absoluteImagePath = $galleryDirectoryAbsolutePath . DIRECTORY_SEPARATOR . $entry;
      if (!is_file($absoluteImagePath)) {
        continue;
      }

      $extension = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
      if (!in_array($extension, $supportedExtensions, true)) {
        continue;
      }

      if (!is_readable($absoluteImagePath)) {
        $galleryPathIssues[] = 'Unreadable file skipped: ' . $entry;
        continue;
      }

      $dimensions = @getimagesize($absoluteImagePath);
      if ($dimensions === false) {
        $galleryPathIssues[] = 'Invalid image metadata skipped: ' . $entry;
        continue;
      }

      $fileBaseName = pathinfo($entry, PATHINFO_FILENAME);
      // Build filename-based alt text and date-aware caption for every valid image.
      [$altText, $isVagueFilename] = buildGalleryAltText($fileBaseName);
      if ($isVagueFilename) {
        $vagueAltTextFilenames[] = $entry;
      }

      $formattedDate = formatGalleryDateFromFilename($fileBaseName);
      $captionText = $formattedDate !== null
        ? ('Captured on ' . $formattedDate)
        : 'Captured date unavailable';

      $galleryItems[] = [
        'filename' => $entry,
        'src' => $galleryDirectoryWebPath . '/' . rawurlencode($entry),
        'alt' => $altText,
        'caption' => $captionText,
        'width' => (int) $dimensions[0],
        'height' => (int) $dimensions[1],
      ];
    }
  }
}

$validImageCount = count($galleryItems);
if ($galleryItems !== []) {
  shuffle($galleryItems);
}
$showSlideIndicators = ($validImageCount > 1 && $validImageCount <= 12);

require_once 'includes/header.php';
?>

<!--  PAGE HERO  -->
<section class="gallery-hero">
  <div class="container text-center page-hero-z">
    <span class="page-hero-emoji" aria-hidden="true">&#128248;&#128062;&#10024;</span>
    <h1 class="page-hero-h1">Media Gallery</h1>
    <p class="page-hero-tagline">
      Beautiful moments with Chandra &amp; Skipper.
    </p>
  </div>
</section>

<section class="wyp-section wyp-section-sm wyp-section-alt">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-7 text-center">
        <div class="gallery-coming-soon">
          <div class="gallery-coming-soon__icon" aria-hidden="true">&#128062;</div>
          <h2 class="section-title">Photo Highlights</h2>
          <p class="gallery-coming-soon__body">
            Enjoy candid adventures and cozy moments from Monterey Bay with Chandra and Skipper.
            Select any image to open a larger view.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!--  FULL GALLERY CAROUSEL (uses existing gallery preview styles as canonical structure)  -->
<section class="wyp-section">
  <div class="container">

    <div class="text-center mb-5">
      <span class="section-eyebrow">Photo Collection</span>
      <h2 class="section-title">Gallery Preview</h2>
      <hr class="section-divider">
      <p class="gallery-tip-text">
        Tap or press Enter on any card for a larger view.
      </p>
    </div>

    <?php if ($validImageCount !== $expectedImageCount): ?>
      <!-- TODO: Gallery image count mismatch. Expected 76 files in /images/gallery, found <?= (int) $validImageCount ?> valid images. -->
    <?php endif; ?>

    <?php if ($vagueAltTextFilenames !== []): ?>
      <!-- TODO: Replace generic alt text with scene-specific alt text for these vague filenames: <?= htmlspecialchars(implode(', ', $vagueAltTextFilenames), ENT_QUOTES, 'UTF-8') ?> -->
    <?php endif; ?>

    <?php if ($galleryPathIssues !== []): ?>
      <!-- TODO: Fix gallery path/image issues: <?= htmlspecialchars(implode(' | ', $galleryPathIssues), ENT_QUOTES, 'UTF-8') ?> -->
    <?php endif; ?>

    <?php if ($validImageCount > 0): ?>
      <div
        id="galleryPhotoCarousel"
        class="carousel slide gallery-carousel-shell"
        data-bs-ride="false"
        data-bs-interval="false"
        data-bs-touch="true"
        aria-label="Chandra and Skipper photo carousel">

        <?php if ($showSlideIndicators): ?>
          <div class="carousel-indicators">
            <?php foreach ($galleryItems as $index => $galleryItem): ?>
              <button
                type="button"
                data-bs-target="#galleryPhotoCarousel"
                data-bs-slide-to="<?= (int) $index ?>"
                class="<?= $index === 0 ? 'active' : '' ?>"
                <?= $index === 0 ? 'aria-current="true"' : '' ?>
                aria-label="Go to slide <?= (int) ($index + 1) ?>"></button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="carousel-inner">
          <?php foreach ($galleryItems as $index => $galleryItem): ?>
            <!-- Each slide uses the existing gallery card styling and opens the Bootstrap modal lightbox. -->
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
              <div class="gallery-placeholder-item gallery-carousel-card">
                <span class="gallery-coming-badge">View Full</span>
                <button
                  type="button"
                  class="gallery-photo-item gallery-carousel-trigger gallery-lightbox-trigger"
                  data-bs-toggle="modal"
                  data-bs-target="#galleryLightboxModal"
                  data-full-src="<?= htmlspecialchars($galleryItem['src'], ENT_QUOTES, 'UTF-8') ?>"
                  data-alt="<?= htmlspecialchars($galleryItem['alt'], ENT_QUOTES, 'UTF-8') ?>"
                  data-caption="<?= htmlspecialchars($galleryItem['caption'], ENT_QUOTES, 'UTF-8') ?>"
                  aria-label="Open larger gallery image <?= (int) ($index + 1) ?>: <?= htmlspecialchars($galleryItem['alt'], ENT_QUOTES, 'UTF-8') ?>">
                  <img
                    src="<?= htmlspecialchars($galleryItem['src'], ENT_QUOTES, 'UTF-8') ?>"
                    class="gallery-photo-thumb gallery-carousel-image"
                    alt="<?= htmlspecialchars($galleryItem['alt'], ENT_QUOTES, 'UTF-8') ?>"
                    width="<?= (int) $galleryItem['width'] ?>"
                    height="<?= (int) $galleryItem['height'] ?>"
                    loading="lazy"
                    decoding="async">
                </button>
                <p class="spot-name mt-2 mb-0">Chandra &amp; Skipper</p>
                <p class="gallery-tip-text mb-0"><?= htmlspecialchars($galleryItem['caption'], ENT_QUOTES, 'UTF-8') ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if ($validImageCount > 1): ?>
          <button
            class="carousel-control-prev"
            type="button"
            data-bs-target="#galleryPhotoCarousel"
            data-bs-slide="prev"
            aria-label="Previous gallery image">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          </button>
          <button
            class="carousel-control-next"
            type="button"
            data-bs-target="#galleryPhotoCarousel"
            data-bs-slide="next"
            aria-label="Next gallery image">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
          </button>
        <?php endif; ?>

      </div>
    <?php else: ?>
      <div class="gallery-coming-soon text-center">
        <p class="gallery-coming-soon__body mb-0">
          Gallery images are currently unavailable. Please check back soon.
        </p>
      </div>
    <?php endif; ?>

  </div>
</section>

<!-- Bootstrap lightbox modal -->
<div
  class="modal fade"
  id="galleryLightboxModal"
  tabindex="-1"
  aria-labelledby="galleryLightboxTitle"
  aria-describedby="galleryLightboxCaption"
  aria-modal="true"
  role="dialog">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="h5 mb-0 section-title" id="galleryLightboxTitle">Expanded Gallery Image</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close enlarged image"></button>
      </div>
      <div class="modal-body">
        <img id="galleryLightboxImage" class="gallery-lightbox-img" src="" alt="">
        <p id="galleryLightboxCaption" class="gallery-tip-text gallery-lightbox-caption mb-0 mt-3"></p>
      </div>
    </div>
  </div>
</div>

<!--  UPLOAD CTA  -->
<section class="section-gallery-story wyp-section-accent">
  <div class="container">
    <div class="row g-4 align-items-center">

      <div class="col-lg-8">
        <h3 class="gallery-story-heading">
          Have Photos of Your Small Pups? <span aria-hidden="true">&#128062;</span>
        </h3>
        <p class="gallery-story-body">
          We'd love to feature photos from our community of small dog lovers!
          Reach out to us through our contact form and share the joy your furry
          family members bring to your world.
        </p>
      </div>

      <div class="col-lg-4 text-lg-end">
        <a href="contact.php" class="btn-wyp btn-wyp-primary">
          Share Your Pup <span aria-hidden="true">&#128248;</span>
        </a>
      </div>

    </div>
  </div>
</section>

<!--  ABOUT THE DOGS MINI SECTION  -->
<section class="wyp-section wyp-section-sm wyp-section-alt">
  <div class="container">
    <div class="row g-4 justify-content-center">

      <div class="col-md-5">
        <div class="wyp-card wyp-info-card text-center p-4">
          <div class="card-header-band"></div>
          <div class="gallery-dog-card-icon" aria-hidden="true">&#128021;</div>
          <h3 class="section-title">Chandra</h3>
          <p class="gallery-dog-teaser">
            Our spirited Chihuahua princess - her gallery photos showcase
            her signature sunlit poses and diva energy.
          </p>
          <a href="intro.php" class="gallery-dog-link">
            Read Chandra&rsquo;s Story <span aria-hidden="true">&rarr;</span>
          </a>
        </div>
      </div>

      <div class="col-md-5">
        <div class="wyp-card wyp-info-card text-center p-4">
          <div class="card-header-band dog-card-top-stripe--skipper"></div>
          <div class="gallery-dog-card-icon" aria-hidden="true">&#128054;</div>
          <h3 class="section-title">Skipper</h3>
          <p class="gallery-dog-teaser">
            Our adventurous Jack Chi explorer - expect candid action shots of
            beach zoomies and trail-sniffing expeditions.
          </p>
          <a href="intro.php" class="gallery-dog-link">
            Read Skipper&rsquo;s Story <span aria-hidden="true">&rarr;</span>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Populate the Bootstrap modal with the image selected from the carousel.
    var modalElement = document.getElementById('galleryLightboxModal');
    var modalImage = document.getElementById('galleryLightboxImage');
    var modalCaption = document.getElementById('galleryLightboxCaption');
    var galleryTriggers = document.querySelectorAll('.gallery-lightbox-trigger');
    var lastFocusedTrigger = null;

    galleryTriggers.forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        lastFocusedTrigger = trigger;
        var fullSrc = trigger.getAttribute('data-full-src') || trigger.getAttribute('href') || '';
        var altText = trigger.getAttribute('data-alt') || '';
        var captionText = trigger.getAttribute('data-caption') || '';

        modalImage.setAttribute('src', fullSrc);
        modalImage.setAttribute('alt', altText);
        modalCaption.textContent = captionText;
      });
    });

    if (modalElement) {
      modalElement.addEventListener('hidden.bs.modal', function () {
        modalImage.setAttribute('src', '');
        modalImage.setAttribute('alt', '');
        modalCaption.textContent = '';

        if (lastFocusedTrigger) {
          lastFocusedTrigger.focus();
        }
      });
    }
  });
</script>

<?php require_once 'includes/footer.php'; ?>
