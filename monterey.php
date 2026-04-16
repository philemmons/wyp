<?php
/**
 * monterey.php — Why Monterey?
 * wipeyourpaws.net · PHP 8.5 · Bootstrap 5.3.8
 */
$page_id = 'monterey';
require_once 'includes/header.php';

// Category data — structured content from the brief
$categories = [
  [
    'icon'  => '🌅',
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
    'icon'  => '☕',
    'title' => 'Dog-Friendly Culture',
    'color' => 'var(--pink-mauve)',
    'items' => [
      [
        'title' => 'Pet-Friendly Establishments',
        'body'  => 'Many restaurants, cafés, and shops in Monterey are pet-friendly, allowing dogs to accompany their owners. Some establishments even provide <strong>water bowls and treats</strong> for dogs.',
      ],
      [
        'title' => 'Dog-Friendly Lodging',
        'body'  => 'There are numerous hotels and vacation rentals that welcome dogs, making it easy for travelers with pets to find comfortable accommodations.',
      ],
    ],
  ],
  [
    'icon'  => '🎉',
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
    'icon'  => '🏥',
    'title' => 'Amenities & Services',
    'color' => 'var(--orange-light)',
    'items' => [
      [
        'title' => 'Veterinary Care',
        'body'  => 'The availability of high-quality veterinary care and pet services — including grooming, boarding, and training — ensures that dogs in Monterey are well taken care of.',
      ],
      [
        'title' => 'Off-Leash Areas',
        'body'  => 'Several off-leash dog parks and beaches allow dogs to run and play freely — particularly appealing for owners who want to give their pets freedom to explore and socialise.',
      ],
    ],
  ],
  [
    'icon'  => '🌿',
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
    'icon'  => '⚓',
    'title' => 'Unique Experiences',
    'color' => 'var(--pink-mauve)',
    'items' => [
      [
        'title' => 'Historic & Cultural Sites',
        'body'  => "Monterey's rich history and cultural sites — such as <strong>Cannery Row</strong> and the <strong>Monterey Bay Aquarium</strong> — often welcome leashed dogs, allowing owners to enjoy these attractions without leaving their pets behind.",
      ],
    ],
  ],
  [
    'icon'  => '😊',
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

<!-- ░░ PAGE HERO ░░ -->
<section class="monterey-hero">
  <div class="container text-center position-relative" style="z-index:2;">
    <span style="font-size:3.5rem; display:block; margin-bottom:0.5rem;" aria-hidden="true">🌊🐾🌉</span>
    <h1 style="font-family:var(--font-display); color:#fff; font-size:clamp(2rem,5vw,3.5rem); text-shadow:3px 4px 12px rgba(0,0,0,0.25); margin-bottom:0.5rem;">
      Why Monterey?
    </h1>
    <p style="font-family:var(--font-accent); font-style:italic; color:rgba(255,255,255,0.95); font-size:1.1rem; max-width:560px; margin:0 auto;">
      A paradise on California&rsquo;s Central Coast &mdash; where small dogs and their people thrive
    </p>
  </div>
</section>

<!-- ░░ INTRO PARAGRAPH ░░ -->
<section class="page-section-sm" style="background:var(--cream);">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <span class="section-eyebrow">Our Home</span>
        <h2 class="section-title mb-3">A Haven for Dog Lovers</h2>
        <hr class="section-divider">
        <p style="font-size:1.05rem; color:var(--text-mid); line-height:1.85;">
          Monterey, California, is a unique place for dog lovers due to a combination of its natural beauty,
          dog-friendly culture, and a variety of amenities catering to dogs and their owners.
          Below are the factors that make Monterey particularly special for dog enthusiasts — and why
          Chandra and Skipper are two very lucky pups! <span aria-hidden="true">🐾</span>
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ░░ CATEGORIES ░░ -->
<section class="page-section" style="background:var(--warm-white);">
  <div class="container">

    <div class="row g-4">
      <?php foreach ($categories as $cat): ?>
      <div class="col-12">
        <div class="monterey-category-card" style="border-left-color: <?= $cat['color'] ?>;">
          <div class="d-flex align-items-start gap-3">
            <span class="category-icon" style="line-height:1;" aria-hidden="true"><?= $cat['icon'] ?></span>
            <div class="flex-grow-1">
              <h3 class="monterey-cat-heading"><?= htmlspecialchars($cat['title']) ?></h3>
              <div class="row g-3 mt-1">
                <?php foreach ($cat['items'] as $i => $item): ?>
                <div class="col-md-6">
                  <div class="d-flex align-items-start gap-2">
                    <span class="monterey-num-badge"
                          style="background: linear-gradient(135deg, <?= $cat['color'] ?>, var(--yellow-bright));"
                          aria-hidden="true">
                      <?= $i + 1 ?>
                    </span>
                    <div>
                      <strong style="font-size:0.9rem; color:var(--text-dark); display:block; margin-bottom:0.3rem;">
                        <?= htmlspecialchars($item['title']) ?>
                      </strong>
                      <p style="font-size:0.88rem; color:var(--text-mid); line-height:1.65; margin:0;">
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

<!-- ░░ SUMMARY CALLOUT ░░ -->
<section style="background: linear-gradient(135deg, var(--orange-pale), var(--pink-pale)); padding: 4rem 0;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="wyp-card">
          <div class="card-header-band"></div>
          <div class="p-4 p-lg-5 text-center">
            <span style="font-size:3rem;" aria-hidden="true">🏆</span>
            <h2 style="font-family:var(--font-display); color:var(--orange-deep); font-size:1.6rem; margin:1rem 0 0.75rem;">
              The Bottom Line
            </h2>
            <p style="font-size:1rem; color:var(--text-mid); line-height:1.8; max-width:560px; margin:0 auto 1.5rem;">
              Overall, Monterey, California, stands out as a haven for dog lovers due to its picturesque
              setting, welcoming community, and abundance of dog-friendly amenities and activities.
              It&rsquo;s no wonder Chandra and Skipper feel right at home here!
            </p>
            <a href="contact.php" class="btn-wyp btn-wyp-primary">
              We&rsquo;d Love to Hear from You <span aria-hidden="true">🐾</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ░░ INTERACTIVE MAP ░░ -->
<section class="page-section" style="background:var(--cream);">
  <div class="container">
    <div class="text-center mb-4">
      <span class="section-eyebrow">Find Us Here</span>
      <h2 class="section-title">Monterey Bay, California</h2>
      <hr class="section-divider">
    </div>
    <div class="map-wrapper">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d51729.2!2d-121.9177!3d36.6002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808de15c59e1e2fd%3A0xeabe3a9b9c9b1efc!2sMonterey%2C%20CA!5e0!3m2!1sen!2sus!4v1699999999"
        width="100%" height="420" style="border:0; display:block;" allowfullscreen=""
        loading="lazy" referrerpolicy="no-referrer-when-downgrade"
        title="Interactive map showing Monterey Bay, California">
      </iframe>
    </div>

    <!-- Dog-friendly spots -->
    <div class="row g-3 mt-4">
      <?php
      $spots = [
        ['🏖️', 'Carmel Beach',                 'One of California\'s most beautiful dog-friendly beaches'],
        ['🌿', 'Garrapata State Park',           'Stunning coastal trails where leashed dogs are welcome'],
        ['🚶', 'Monterey Bay Coastal Trail',     '18-mile multi-use path along the scenic bay'],
        ['🐾', 'Carmel City Beach',              'Off-leash beach access for well-behaved dogs'],
        ['⚓', 'Cannery Row',                    'Historic waterfront with dog-welcoming shops & eateries'],
        ['🦦', 'Monterey Bay Aquarium',          'Leashed dogs welcome in outdoor areas'],
      ];
      foreach ($spots as $spot): ?>
      <div class="col-md-4 col-sm-6">
        <div class="wyp-card p-3 d-flex align-items-start gap-3">
          <span style="font-size:1.6rem;" aria-hidden="true"><?= $spot[0] ?></span>
          <div>
            <strong style="font-size:0.88rem; color:var(--orange-deep);"><?= htmlspecialchars($spot[1]) ?></strong>
            <p style="font-size:0.8rem; color:var(--text-mid); margin:0; line-height:1.5;"><?= htmlspecialchars($spot[2]) ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
