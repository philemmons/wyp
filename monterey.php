<?php
/**
 * monterey.php — Why Monterey?
 * wipeyourpaws.net · PHP 8.5 · Bootstrap 5.3.8
 */
$page_id = 'monterey';
require_once 'includes/header.php';

$categories = [
  [
    'icon'  => '🌅',
    'title' => 'Natural Beauty and Outdoor Activities',
    'color' => 'var(--orange-primary)',
    'items' => [
      ['title' => 'Scenic Locations',
       'body'  => 'Monterey has beautiful beaches, parks, and coastal trails, including Carmel Beach, Garrapata State Park, and the Monterey Bay Coastal Recreation Trail.'],
      ['title' => 'Mild Climate',
       'body'  => 'The weather is moderate through the year, which makes outdoor time easier for people and pets.'],
    ],
  ],
  [
    'icon'  => '☕',
    'title' => 'Dog-Friendly Culture',
    'color' => 'var(--pink-mauve)',
    'items' => [
      ['title' => 'Pet-Friendly Places',
       'body'  => 'Many restaurants, cafes, and shops welcome leashed dogs and provide water bowls.'],
      ['title' => 'Dog-Friendly Lodging',
       'body'  => 'Travelers can find many hotels and rentals that allow dogs.'],
    ],
  ],
  [
    'icon'  => '🎉',
    'title' => 'Community and Events',
    'color' => 'var(--orange-deep)',
    'items' => [
      ['title' => 'Dog Events',
       'body'  => 'The area hosts dog-friendly events like parades, shows, and adoption activities.'],
      ['title' => 'Local Dog Groups',
       'body'  => 'Clubs and groups organize meetups, training sessions, and social events for dogs and owners.'],
    ],
  ],
  [
    'icon'  => '🏥',
    'title' => 'Amenities and Services',
    'color' => 'var(--orange-light)',
    'items' => [
      ['title' => 'Veterinary Care',
       'body'  => 'Monterey offers strong veterinary support and pet services such as grooming and training.'],
      ['title' => 'Off-Leash Areas',
       'body'  => 'There are off-leash dog areas and beaches where dogs can run and play.'],
    ],
  ],
  [
    'icon'  => '🌿',
    'title' => 'Environmental Awareness',
    'color' => 'var(--orange-primary)',
    'items' => [
      ['title' => 'Conservation Efforts',
       'body'  => 'Monterey keeps many areas clean and safe with strong local conservation efforts.'],
    ],
  ],
  [
    'icon'  => '⚓',
    'title' => 'Unique Experiences',
    'color' => 'var(--pink-mauve)',
    'items' => [
      ['title' => 'Historic and Cultural Sites',
       'body'  => 'Historic areas like Cannery Row often allow leashed dogs in outdoor spaces.'],
    ],
  ],
  [
    'icon'  => '😊',
    'title' => 'Local Attitude',
    'color' => 'var(--orange-deep)',
    'items' => [
      ['title' => 'Friendly Locals',
       'body'  => 'Local residents are known for being welcoming and inclusive toward dogs.'],
    ],
  ],
];
?>

<section class="monterey-hero">
  <div class="container text-center page-hero-z">
    <span class="page-hero-emoji" aria-hidden="true">🌊🐾🌉</span>
    <h1 class="page-hero-h1">Why Monterey?</h1>
    <p class="page-hero-tagline">
      A great place for small dogs and their people
    </p>
  </div>
</section>

<section class="page-section-sm bg-cream">
  <div class="container">
    <details class="simple-summary">
      <summary>Simple summary of this page</summary>
      <p>
        Monterey Bay is dog-friendly because of beaches, trails, local services, and welcoming community spaces.
      </p>
    </details>
  </div>
</section>

<section class="page-section-sm bg-cream">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <span class="section-eyebrow">Our Home</span>
        <h2 class="section-title mb-3">A Haven for Dog Lovers</h2>
        <hr class="section-divider">
        <p class="monterey-intro__copy">
          Monterey Bay, California offers natural beauty, dog-friendly spaces,
          and local services that support healthy, active pet life.
        </p>
      </div>
    </div>
  </div>
</section>

<section class="page-section bg-warm-white">
  <div class="container">
    <div class="row g-4">
      <?php foreach ($categories as $cat): ?>
      <div class="col-12">
        <div class="monterey-category-card" style="--border-color: <?= htmlspecialchars($cat['color']) ?>;">
          <div class="d-flex align-items-start gap-3">
            <span class="category-icon" aria-hidden="true"><?= $cat['icon'] ?></span>
            <div class="flex-grow-1">
              <h3 class="monterey-cat-heading"><?= htmlspecialchars($cat['title']) ?></h3>
              <div class="row g-3 mt-1">
                <?php foreach ($cat['items'] as $i => $item): ?>
                <div class="col-md-6">
                  <div class="d-flex align-items-start gap-2">
                    <span class="monterey-num-badge"
                          style="--cat-color: <?= htmlspecialchars($cat['color']) ?>;"
                          aria-hidden="true">
                      <?= $i + 1 ?>
                    </span>
                    <div>
                      <strong class="category-item-title"><?= htmlspecialchars($item['title']) ?></strong>
                      <p class="category-item-body"><?= htmlspecialchars($item['body']) ?></p>
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

<section class="section-spots">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="wyp-card">
          <div class="card-header-band"></div>
          <div class="p-4 p-lg-5 text-center">
            <div class="emoji-lg" aria-hidden="true">🏆</div>
            <h2 class="spots-heading">The Bottom Line</h2>
            <p class="spots-intro">
              Monterey Bay is a strong match for dog lovers thanks to scenery,
              community support, and pet-friendly spaces.
            </p>
            <a href="contact.php" class="btn-wyp btn-wyp-primary">
              Open Contact Page <span aria-hidden="true">🐾</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

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
        width="100%" height="420" allowfullscreen=""
        loading="lazy" referrerpolicy="no-referrer-when-downgrade"
        title="Interactive map showing Monterey Bay, California">
      </iframe>
    </div>

    <div class="row g-3 mt-4">
      <?php
      $spots = [
        ['🏖️', 'Carmel Beach',             'A popular dog-friendly beach in the area'],
        ['🌿', 'Garrapata State Park',       'Scenic coastal trails with leashed dog access'],
        ['🚶', 'Monterey Bay Coastal Trail', 'An 18-mile path with bay views'],
        ['🐾', 'Carmel City Beach',          'Off-leash access for well-behaved dogs'],
        ['⚓', 'Cannery Row',                'Historic waterfront with pet-welcoming businesses'],
        ['🦦', 'Monterey Bay Aquarium',      'Leashed dogs are welcome in outdoor spaces'],
      ];
      foreach ($spots as $spot): ?>
      <div class="col-md-4 col-sm-6">
        <div class="wyp-card p-3 d-flex align-items-start gap-3">
          <span class="emoji-md" aria-hidden="true"><?= $spot[0] ?></span>
          <div>
            <strong class="spot-name"><?= htmlspecialchars($spot[1]) ?></strong>
            <p class="spot-desc"><?= htmlspecialchars($spot[2]) ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
