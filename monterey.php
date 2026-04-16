<?php
/**
 * monterey.php - Why Monterey?
 * wipeyourpaws.net - PHP 8.5 - Bootstrap 5.3.8
 */
$page_id = 'monterey';
require_once 'includes/header.php';

// Category data - structured content from the brief
$categories = [
  [
    'icon'  => '&#127754;',
    'title' => 'Natural Beauty & Outdoor Activities',
    'color' => 'var(--orange-primary)',
    'items' => [
      [
        'title' => 'Scenic Locations',
        'body'  => 'Monterey offers stunning coastal views, beaches, and parks that are perfect for dog walking and outdoor activities. Popular spots include <strong>Carmel Beach</strong>, <strong>Garrapata State Park</strong>, and the <strong>Monterey Bay Coastal Recreation Trail</strong>.',
      ],
      [
        'title' => 'Mild Climate',
        'body'  => 'The moderate climate in Monterey is ideal for outdoor activities year-round, ensuring that dogs can enjoy the outdoors without the extremes of hot or cold weather.',
      ],
    ],
  ],
  [
    'icon'  => '&#9749;',
    'title' => 'Dog-Friendly Culture',
    'color' => 'var(--pink-mauve)',
    'items' => [
      [
        'title' => 'Pet-Friendly Establishments',
        'body'  => 'Many restaurants, cafes, and shops in Monterey are pet-friendly, allowing dogs to accompany their owners. Some establishments even provide <strong>water bowls and treats</strong> for dogs.',
      ],
      [
        'title' => 'Dog-Friendly Lodging',
        'body'  => 'There are numerous hotels and vacation rentals that welcome dogs, making it easy for travelers with pets to find comfortable accommodations.',
      ],
    ],
  ],
  [
    'icon'  => '&#127881;',
    'title' => 'Community & Events',
    'color' => 'var(--orange-deep)',
    'items' => [
      [
        'title' => 'Dog-Centric Events',
        'body'  => 'Monterey hosts various dog-friendly events and festivals, such as pet parades, dog shows, and adoption events. These gatherings help foster a strong sense of community among dog lovers.',
      ],
      [
        'title' => 'Active Dog Community',
        'body'  => 'Numerous dog clubs and groups in the area organise meetups, training sessions, and social events for dogs and their owners.',
      ],
    ],
  ],
  [
    'icon'  => '&#127973;',
    'title' => 'Amenities & Services',
    'color' => 'var(--orange-light)',
    'items' => [
      [
        'title' => 'Veterinary Care',
        'body'  => 'The availability of high-quality veterinary care and pet services - including grooming, boarding, and training - ensures that dogs in Monterey are well taken care of.',
      ],
      [
        'title' => 'Off-Leash Areas',
        'body'  => 'Several off-leash dog parks and beaches allow dogs to run and play freely - particularly appealing for owners who want to give their pets freedom to explore and socialise.',
      ],
    ],
  ],
  [
    'icon'  => '&#127807;',
    'title' => 'Environmental Awareness',
    'color' => 'var(--orange-primary)',
    'items' => [
      [
        'title' => 'Conservation Efforts',
        'body'  => 'Monterey is known for its commitment to environmental conservation, which extends to its pet-friendly policies. Many areas are maintained to be clean and safe for both humans and animals.',
      ],
    ],
  ],
  [
    'icon'  => '&#9875;',
    'title' => 'Unique Experiences',
    'color' => 'var(--pink-mauve)',
    'items' => [
      [
        'title' => 'Historic & Cultural Sites',
        'body'  => "Monterey's rich history and cultural sites - such as <strong>Cannery Row</strong> and the <strong>Monterey Bay Aquarium</strong> - often welcome leashed dogs, allowing owners to enjoy these attractions without leaving their pets behind.",
      ],
    ],
  ],
  [
    'icon'  => '&#128522;',
    'title' => 'Local Attitude',
    'color' => 'var(--orange-deep)',
    'items' => [
      [
        'title' => 'Friendly Locals',
        'body'  => 'The residents of Monterey are known for being welcoming and accommodating to dogs, contributing to a relaxed and inclusive atmosphere for dog lovers.',
      ],
    ],
  ],
];
?>

<!-- PAGE HERO -->
<section class="monterey-hero">
  <div class="container text-center position-relative page-hero-content">
    <span class="page-hero-emoji" aria-hidden="true">&#127754;&#128062;&#127753;</span>
    <h1 class="page-hero-title">
      Why Monterey?
    </h1>
    <p class="page-hero-tagline page-hero-tagline-wide">
      A paradise on California&rsquo;s Central Coast &mdash; where small dogs and their people thrive
    </p>
  </div>
</section>

<!-- INTRO PARAGRAPH -->
<section class="page-section-sm bg-cream">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <span class="section-eyebrow">Our Home</span>
        <h2 class="section-title mb-3">A Haven for Dog Lovers</h2>
        <hr class="section-divider">
        <p class="monterey-intro-copy">
          Monterey, California, is a unique place for dog lovers due to a combination of its natural beauty,
          dog-friendly culture, and a variety of amenities catering to dogs and their owners.
          Below are the factors that make Monterey particularly special for dog enthusiasts - and why
          Chandra and Skipper are two very lucky pups! <span aria-hidden="true">&#128062;</span>
        </p>
      </div>
    </div>
  </div>
</section>

<!-- CATEGORIES -->
<section class="page-section bg-warm-white">
  <div class="container">

    <div class="row g-4">
      <?php foreach ($categories as $cat): ?>
      <div class="col-12">
        <div class="monterey-category-card" style="--cat-color: <?= $cat['color'] ?>;">
          <div class="d-flex align-items-start gap-3">
            <span class="category-icon" aria-hidden="true"><?= $cat['icon'] ?></span>
            <div class="flex-grow-1">
              <h3 class="monterey-cat-heading"><?= htmlspecialchars($cat['title']) ?></h3>
              <div class="row g-3 mt-1">
                <?php foreach ($cat['items'] as $i => $item): ?>
                <div class="col-md-6">
                  <div class="d-flex align-items-start gap-2">
                    <span class="monterey-num-badge" aria-hidden="true">
                      <?= $i + 1 ?>
                    </span>
                    <div>
                      <strong class="monterey-item-title">
                        <?= htmlspecialchars($item['title']) ?>
                      </strong>
                      <p class="monterey-item-copy">
                        <?= $item['body'] ?>
                      </p>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<!-- SUMMARY CALLOUT -->
<section class="summary-callout-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="wyp-card">
          <div class="card-header-band"></div>
          <div class="p-4 p-lg-5 text-center">
            <span class="summary-callout-icon" aria-hidden="true">&#127942;</span>
            <h2 class="summary-callout-title">
              The Bottom Line
            </h2>
            <p class="summary-callout-copy">
              Overall, Monterey, California, stands out as a haven for dog lovers due to its picturesque
              setting, welcoming community, and abundance of dog-friendly amenities and activities.
              It&rsquo;s no wonder Chandra and Skipper feel right at home here!
            </p>
            <a href="contact.php" class="btn-wyp btn-wyp-primary">
              We&rsquo;d Love to Hear from You <span aria-hidden="true">&#128062;</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- INTERACTIVE MAP -->
<section class="page-section bg-cream">
  <div class="container">
    <div class="text-center mb-4">
      <span class="section-eyebrow">Find Us Here</span>
      <h2 class="section-title">Monterey Bay, California</h2>
      <hr class="section-divider">
    </div>
    <div class="map-wrapper">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d51729.2!2d-121.9177!3d36.6002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808de15c59e1e2fd%3A0xeabe3a9b9c9b1efc!2sMonterey%2C%20CA!5e0!3m2!1sen!2sus!4v1699999999"
        width="100%" height="420" class="map-embed" allowfullscreen=""
        loading="lazy" referrerpolicy="no-referrer-when-downgrade"
        title="Interactive map showing Monterey Bay, California">
      </iframe>
    </div>

    <!-- Dog-friendly spots -->
    <div class="row g-3 mt-4">
      <?php
      $spots = [
        ['&#127958;&#65039;', 'Carmel Beach',                 'One of California\'s most beautiful dog-friendly beaches'],
        ['&#127807;', 'Garrapata State Park',           'Stunning coastal trails where leashed dogs are welcome'],
        ['&#128694;', 'Monterey Bay Coastal Trail',     '18-mile multi-use path along the scenic bay'],
        ['&#128062;', 'Carmel City Beach',              'Off-leash beach access for well-behaved dogs'],
        ['&#9875;', 'Cannery Row',                    'Historic waterfront with dog-welcoming shops & eateries'],
        ['&#129445;', 'Monterey Bay Aquarium',          'Leashed dogs welcome in outdoor areas'],
      ];
      foreach ($spots as $spot): ?>
      <div class="col-md-4 col-sm-6">
        <div class="wyp-card p-3 d-flex align-items-start gap-3">
          <span class="monterey-spot-icon" aria-hidden="true"><?= $spot[0] ?></span>
          <div>
            <strong class="monterey-spot-title"><?= htmlspecialchars($spot[1]) ?></strong>
            <p class="monterey-spot-copy"><?= htmlspecialchars($spot[2]) ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
