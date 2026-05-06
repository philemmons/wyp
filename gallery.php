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

/**
 * Convert a thumbnail filename to its full-size filename by removing
 * a trailing "_thumb" segment before the extension.
 * Example: dog-beach_thumb.webp -> dog-beach.webp
 */
function thumbnailFilenameToFullFilename(string $thumbnailFilename): string
{
  $extension = pathinfo($thumbnailFilename, PATHINFO_EXTENSION);
  $baseName = pathinfo($thumbnailFilename, PATHINFO_FILENAME);
  $fullBaseName = preg_replace('/_thumb$/i', '', $baseName) ?? $baseName;

  if ($extension === '') {
    return $fullBaseName;
  }

  return $fullBaseName . '.' . $extension;
}

$expectedImageCount = 76;
$galleryThumbnailDirectoryAbsolutePath = __DIR__ . '/images/gallery-thumbnails';
$galleryThumbnailDirectoryWebPath = '/images/gallery-thumbnails';
$galleryFullDirectoryAbsolutePath = __DIR__ . '/images/gallery';
$galleryFullDirectoryWebPath = '/images/gallery';
$supportedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'avif'];

$galleryItems = [];
$galleryPathIssues = [];
$vagueAltTextFilenames = [];

if (!is_dir($galleryThumbnailDirectoryAbsolutePath)) {
  $galleryPathIssues[] = 'Gallery thumbnail directory not found: ' . $galleryThumbnailDirectoryAbsolutePath;
} elseif (!is_dir($galleryFullDirectoryAbsolutePath)) {
  $galleryPathIssues[] = 'Gallery full-size directory not found: ' . $galleryFullDirectoryAbsolutePath;
} else {
  // Discover thumbnails from /images/gallery-thumbnails so carousel images stay optimized.
  $directoryEntries = scandir($galleryThumbnailDirectoryAbsolutePath);
  if ($directoryEntries === false) {
    $galleryPathIssues[] = 'Unable to read thumbnail directory: ' . $galleryThumbnailDirectoryAbsolutePath;
  } else {
    foreach ($directoryEntries as $entry) {
      if ($entry === '.' || $entry === '..') {
        continue;
      }

      $thumbnailAbsolutePath = $galleryThumbnailDirectoryAbsolutePath . DIRECTORY_SEPARATOR . $entry;
      if (!is_file($thumbnailAbsolutePath)) {
        continue;
      }

      $extension = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
      if (!in_array($extension, $supportedExtensions, true)) {
        continue;
      }

      if (!is_readable($thumbnailAbsolutePath)) {
        $galleryPathIssues[] = 'Unreadable thumbnail skipped: ' . $entry;
        continue;
      }

      $fullSizeFilename = thumbnailFilenameToFullFilename($entry);
      $fullSizeAbsolutePath = $galleryFullDirectoryAbsolutePath . DIRECTORY_SEPARATOR . $fullSizeFilename;
      if (!is_file($fullSizeAbsolutePath)) {
        $galleryPathIssues[] = 'Missing matching full-size image for thumbnail: ' . $entry . ' -> ' . $fullSizeFilename;
        continue;
      }

      if (!is_readable($fullSizeAbsolutePath)) {
        $galleryPathIssues[] = 'Unreadable full-size image skipped: ' . $fullSizeFilename;
        continue;
      }

      $dimensions = @getimagesize($thumbnailAbsolutePath);
      if ($dimensions === false) {
        $galleryPathIssues[] = 'Invalid thumbnail metadata skipped: ' . $entry;
        continue;
      }

      $fileBaseName = pathinfo($fullSizeFilename, PATHINFO_FILENAME);
      // Build filename-based alt text and date-aware caption for every valid image.
      [$altText, $isVagueFilename] = buildGalleryAltText($fileBaseName);
      if ($isVagueFilename) {
        $vagueAltTextFilenames[] = $fullSizeFilename;
      }

      $formattedDate = formatGalleryDateFromFilename($fileBaseName);
      $captionText = $formattedDate !== null
        ? ('Captured on ' . $formattedDate)
        : 'Captured date unavailable';

      $galleryItems[] = [
        'filename' => $entry,
        'thumb_src' => $galleryThumbnailDirectoryWebPath . '/' . rawurlencode($entry),
        'full_src' => $galleryFullDirectoryWebPath . '/' . rawurlencode($fullSizeFilename),
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

<!-- Page hero: gallery-specific layered header component -->
<section class="monterey-hero image-header">
  <div class="container text-center page-hero-z">
    <header class="wyp-gallery-hero shadow" aria-labelledby="galleryHeroTitle">
      <div class="wyp-gallery-hero-accent-top" aria-hidden="true"></div>
      <div class="wyp-gallery-hero-accent-bottom" aria-hidden="true"></div>

      <div class="wyp-gallery-hero-photo" aria-hidden="true">
        <picture>
          <img
            src="/images/gallery-header.jpg"
            alt=""
            width="1920"
            height="720"
            loading="eager"
            decoding="sync"
            fetchpriority="high">
        </picture>
      </div>

      <div class="wyp-gallery-hero-overlay" aria-hidden="true"></div>

      <div class="wyp-gallery-hero-content">
        <div class="wyp-gallery-hero-eyebrow">
          <span class="wyp-gallery-hero-eyebrow-line" aria-hidden="true"></span>
          <span>Chandra &amp; Skipper</span>
        </div>
        <h1 id="galleryHeroTitle">Media Gallery</h1>
        <p>We're busy snapping adorable photos of Chandra and Skipper exploring Monterey Bay! This gallery will soon be filled with their cutest moments. Check back soon — it'll be paw-some!</p>
      </div>

    </header>
  </div>
</section>



<!--  FULL GALLERY CAROUSEL (uses existing gallery preview styles as canonical structure)  -->
<section class="wyp-section">
  <div class="container">

    <div class="text-center mb-5">
      <span class="section-eyebrow">Photo Collection</span>
      <h2 class="section-title">Full Gallery Carousel</h2>
      <hr class="section-divider">
      <p class="gallery-tip-text gallery-carousel-intro-tip">
        Tap or press Enter on any card for a larger view.
      </p>
    </div>

    <?php if ($validImageCount !== $expectedImageCount): ?>
      <!-- TODO: Gallery image count mismatch. Expected 76 thumbnails in /images/gallery-thumbnails, found <?= (int) $validImageCount ?> valid images. -->
    <?php endif; ?>

    <?php if ($vagueAltTextFilenames !== []): ?>
      <!-- TODO: Replace generic alt text with scene-specific alt text for these vague filenames: <?= htmlspecialchars(implode(', ', $vagueAltTextFilenames), ENT_QUOTES, 'UTF-8') ?> -->
    <?php endif; ?>

    <?php if ($galleryPathIssues !== []): ?>
      <!-- TODO: Fix gallery path/image issues: <?= htmlspecialchars(implode(' | ', $galleryPathIssues), ENT_QUOTES, 'UTF-8') ?> -->
    <?php endif; ?>

    <?php if ($validImageCount > 0): ?>
      <p id="galleryCarouselStatus" class="visually-hidden" role="status" aria-live="polite" aria-atomic="true"></p>
      <p id="galleryCarouselInstructions" class="visually-hidden">
        Use left and right arrow keys to move between images. Press Enter to open the selected image in a dialog.
      </p>
      <div
        id="galleryPhotoCarousel"
        class="carousel slide gallery-carousel-shell"
        data-bs-ride="false"
        data-bs-interval="false"
        data-bs-keyboard="true"
        data-bs-touch="true"
        tabindex="0"
        aria-roledescription="carousel"
        aria-describedby="galleryCarouselInstructions galleryCarouselStatus"
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
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" aria-label="Slide <?= (int) ($index + 1) ?> of <?= (int) $validImageCount ?>">
              <div class="gallery-placeholder-item gallery-carousel-card">
                <button
                  type="button"
                  class="gallery-photo-item gallery-carousel-trigger gallery-lightbox-trigger"
                  data-bs-toggle="modal"
                  data-bs-target="#galleryLightboxModal"
                  data-fullsrc="<?= htmlspecialchars($galleryItem['full_src'], ENT_QUOTES, 'UTF-8') ?>"
                  data-full-src="<?= htmlspecialchars($galleryItem['full_src'], ENT_QUOTES, 'UTF-8') ?>"
                  data-alt="<?= htmlspecialchars($galleryItem['alt'], ENT_QUOTES, 'UTF-8') ?>"
                  data-caption="<?= htmlspecialchars($galleryItem['caption'], ENT_QUOTES, 'UTF-8') ?>"
                  aria-label="Open larger gallery image <?= (int) ($index + 1) ?>: <?= htmlspecialchars($galleryItem['alt'], ENT_QUOTES, 'UTF-8') ?>">
                  <img
                    src="<?= htmlspecialchars($galleryItem['thumb_src'], ENT_QUOTES, 'UTF-8') ?>"
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
  data-bs-keyboard="true"
  tabindex="-1"
  aria-labelledby="galleryLightboxTitle"
  aria-describedby="galleryLightboxCaption"
  aria-modal="true">
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

<!--  ABOUT THE DOGS MINI SECTION  -->
<section class="section-gallery-story wyp-section-accent">
  <div class="container">
    <div class="row g-4 justify-content-center">

      <div class="col-md-5">
        <div class="wyp-card wyp-info-card text-center p-4">
          <div class="card-header-band"></div>
          <div class="gallery-dog-card-icon" aria-hidden="true">
            <div class="dog-avatar-frame">
              <img src="/images/chandra icon 55x55.png" alt="" class="mx-2" width="55" height="55" aria-hidden="true">
            </div>
          </div>
          <h3 class="section-title">Chandra</h3>
          <p class="gallery-dog-teaser">
            Our spirited Chihuahua princess - her gallery photos showcase
            her signature sunlit poses and diva energy.
          </p>
          <a href="intro.php" class="gallery-dog-link">
            Read Chandra's Story <span aria-hidden="true">→</span>
          </a>
        </div>
      </div>

      <div class="col-md-5">
        <div class="wyp-card wyp-info-card text-center p-4">
          <div class="card-header-band dog-card-top-stripe--skipper"></div>
          <div class="gallery-dog-card-icon" aria-hidden="true">
            <div class="dog-avatar-frame">
              <img src="/images/skipper-icon-50x42.png" alt="" class="mx-2" width="50" height="42" aria-hidden="true">
            </div>
          </div>
          <h3 class="section-title">Skipper</h3>
          <p class="gallery-dog-teaser">
            Our adventurous Jack Chi explorer - expect candid action shots of
            beach zoomies and trail-sniffing expeditions.
          </p>
          <a href="intro.php" class="gallery-dog-link">
            Read Skipper's Story <span aria-hidden="true">→</span>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<script src="/js/gallery_lightbox_modal.js?v=<?= filemtime(__DIR__ . '/js/gallery_lightbox_modal.js'); ?>" defer></script>

<?php require_once 'includes/footer.php'; ?>
