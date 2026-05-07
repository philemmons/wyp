<?php

/**
 * monterey.php — Why Monterey?
 * wipeyourpaws.net · PHP 8.x · Bootstrap 5.3.8
 */
$activePageKey = 'monterey';
require_once 'includes/header.php';

$montereyHighlightCategories = [
  [
    'icon'  => '<i class="bi bi-sunrise"></i>',
    'title' => 'Outdoor Access and Natural Beauty',
    'theme' => 'monterey-theme-primary',
    'aria'  => 'Natural-Beauty',
    'body'  => 'Monterey\'s coastal landscape makes it an exceptional destination for dogs and their owners. Scenic spots like Carmel Beach, Garrapata State Park, and the Monterey Bay Coastal Recreation Trail offer ample space for walks and outdoor adventures. The area\'s mild, temperate climate means these spaces are enjoyable year-round, without the discomfort of extreme heat or cold.',
  ],
  [
    'icon'  => '<i class="bi bi-cup-hot"></i>',
    'title' => 'Dog-Friendly Culture and Community',
    'theme' => 'monterey-theme-mauve',
    'aria'  => 'Dog-Culture',
    'body'  => 'Monterey has a genuinely welcoming attitude toward dogs across daily life. Restaurants, cafés, and shops regularly accommodate pets, and many go the extra mile with water bowls and treats. The local dog community is active and well-organized, with clubs, training groups, and regular meetups that make it easy for owners to connect and for dogs to socialize.',
  ],
  [
    'icon'  => '<i class="bi bi-calendar-event"></i>',
    'title' => 'Accommodations and Events',
    'theme' => 'monterey-theme-deep',
    'aria'  => 'Dog-Events',
    'body'  => 'Travelers with dogs are well catered to in Monterey. A solid range of hotels and vacation rentals accept pets, removing the usual stress of finding suitable lodging. Throughout the year, the area also hosts dog-centric events, including parades, shows, and adoption gatherings, that bring the community together around a shared love of dogs.',
  ],
  [
    'icon'  => '<i class="bi bi-house-heart"></i>',
    'title' => 'Services, Amenities, and Local Character',
    'theme' => 'monterey-theme-light',
    'aria'  => 'Dog-Services',
    'body'  => 'Practical needs are well covered, with quality veterinary care, grooming, boarding, and training services readily available. Several off-leash parks and beaches give dogs the freedom to run and explore. Beyond the amenities, Monterey\'s broader character adds to its appeal — its environmental ethic keeps public spaces clean and safe, historic sites like Cannery Row welcome leashed dogs, and the locals themselves are known for being genuinely warm toward visiting pets and their owners.',
  ],
];
?>

<!--  PAGE HERO  -->
<section class="monterey-hero image-header" aria-label="Why Monterey hero">
  <div class="container text-center page-hero-z">
    <div class='visually-hidden'>
      <h1>Why Monterey? A paradise where small dogs and their people thrive.</h1>
    </div>
    <img src='images/why-monterey-header.png' alt='Why Monterey? A paradise where small dogs and their people thrive' class='img-fluid rounded mx-auto d-block shadow'>
  </div>
</section>

<!--  INTRO PARAGRAPH  -->
<section class="wyp-section wyp-section-sm wyp-section-alt" aria-label="Monterey introduction">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <span class="section-eyebrow">Our Home</span>
        <h2 class="section-title mb-3">A Haven for Dog Lovers</h2>
        <hr class="section-divider">
        <p class="monterey-intro__copy">
          Monterey, California, is a unique place for dog lovers due to a combination of its natural beauty,
          dog-friendly culture, and a variety of amenities catering to dogs and their owners.
          Below are the factors that make Monterey particularly special for dog enthusiasts — and why
          Chandra and Skipper are two very lucky pups! <i class="bi bi-heart-fill" aria-hidden="true"></i>
        </p>
      </div>
    </div>
  </div>
</section>

<!--  CATEGORIES  -->
<aside>
<div class="wyp-section" aria-label="Monterey highlights">
  <div class="container">
    <div class="row g-4">
      <?php foreach ($montereyHighlightCategories as $highlightCategory): ?>
        <div class="col-12 col-md-6">
          <div class="monterey-category-card <?= htmlspecialchars($highlightCategory['theme']) ?> h-100">
            <div class="d-flex align-items-start gap-3">
              <span class="category-icon" aria-hidden="true"><?= $highlightCategory['icon'] ?></span>
              <div class="flex-grow-1">
                <section aria-label= <?= htmlspecialchars($highlightCategory['aria']) ?> >
                <h2 class="monterey-cat-heading section-title fs-3"><?= htmlspecialchars($highlightCategory['title']) ?></h2>
                <p class="category-item-body mt-2 mb-0"><?= htmlspecialchars($highlightCategory['body']) ?></p>
                </section>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
      </div>
</aside>

<!--  SUMMARY CALLOUT  -->
<section class="section-cta wyp-section-accent" aria-label="Monterey summary">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="wyp-card wyp-info-card">
          <div class="p-4 p-lg-5 text-center">
            <img src='images/gold paw award-small.png' alt='' class="img-fluid rounded mx-auto d-block" aria-hidden="true">
            <h2 class="spots-heading">The Bottom Line</h2>
            <hr class="section-divider">
            <p class="spots-intro">
              Overall, Monterey, California, stands out as a haven for dog lovers due to its picturesque
              setting, welcoming community, and abundance of dog-friendly amenities and activities.
              It&rsquo;s no wonder Chandra and Skipper feel right at home here!
            </p>
            <div>
              <a href="contact.php" class="btn btn-wyp btn-wyp-primary">
                We&rsquo;d Love to Hear from You
                <i class="bi bi-envelope-open-heart" aria-hidden="true"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!--  INTERACTIVE MAP  -->
<section class="wyp-section wyp-section-alt" aria-label="Monterey map and featured spots">
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
      $featuredDogFriendlyLocations = [
        ['icon_class' => 'bi-umbrella', 'name' => 'Carmel Beach', 'description' => 'One of California\'s most beautiful dog-friendly beaches'],
        ['icon_class' => 'bi-tree', 'name' => 'Garrapata State Park', 'description' => 'Stunning coastal trails where leashed dogs are welcome'],
        ['icon_class' => 'bi-person-walking', 'name' => 'Monterey Bay Coastal Trail', 'description' => '18-mile multi-use path along the scenic bay'],
        ['icon_class' => 'bi-heart-fill', 'name' => 'Carmel City Beach', 'description' => 'Off-leash beach access for well-behaved dogs'],
        ['icon_class' => 'bi-water', 'name' => 'Cannery Row', 'description' => 'Historic waterfront with dog-welcoming shops & eateries'],
        ['icon_class' => 'bi-stars', 'name' => 'Monterey Bay Aquarium', 'description' => 'Leashed dogs welcome in outdoor areas'],
      ];
      foreach ($featuredDogFriendlyLocations as $dogFriendlyLocation): ?>
        <div class="col-md-4 col-sm-6">
          <div class="wyp-card wyp-feature-card p-3 d-flex align-items-start gap-3 h-100 monterey-location-border-left">
            <i class="bi <?= htmlspecialchars($dogFriendlyLocation['icon_class']) ?> emoji-md" aria-hidden="true"></i>
            <div>
              <strong class="spot-name"><?= htmlspecialchars($dogFriendlyLocation['name']) ?></strong>
              <p class="spot-desc"><?= htmlspecialchars($dogFriendlyLocation['description']) ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>