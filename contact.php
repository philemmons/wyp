<?php

/**
 * contact.php - Contact Us
 * wipeyourpaws.net - PHP 8.5 - Bootstrap 5.3.8 - WCAG 2.1 AA
 */

declare(strict_types=1);

// Start session storage so CSRF tokens, flash messages, and anti-spam timers persist between requests.
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

$activePageKey = 'contact';



require_once 'includes/header.php';
?>

<!--  PAGE HERO  -->
<section class="contact-hero">
  <div class="container text-center page-hero-z">
    <h1 class="page-hero-h1">Say Hello!</h1>
    <p class="page-hero-tagline">
      We'd love to hear from fellow small dog lovers - send us a note!
    </p>
    <img src='/images/dog-overlay.png' alt='Many dogs looking up' class='img-fluid mx-auto rounded d-block shadow-lg bg-warning-subtle'>
  </div>
</section>

<!--  MAIN CONTACT SECTION  -->
<section class="wyp-section wyp-section-alt">
  <div class="container">
    <div class="row g-5 justify-content-center">

      <div class="col-lg-7">
        <?php

        /**
         * https://www.codexworld.com/new-google-recaptcha-with-php/
         */

        if (empty($_SESSION['csrf_token'])) {
          $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $readEnvironmentValue = static function (array $keys): string {
          foreach ($keys as $key) {
            $value = getenv($key);
            if ($value !== false) {
              $value = trim((string) $value);
              if ($value !== '') {
                return $value;
              }
            }
          }

          return '';
        };

        // Prefer standardized variable names first, then legacy fallbacks.
        $secretKey = $readEnvironmentValue([
          'GOOGLE_RECAPTCHA_SECRET_KEY',
          'RECAPTCHA_SECRET_KEY',
          'G_SECRET_KEY',
          'g-secret-key',
        ]);
        $siteKey = $readEnvironmentValue([
          'GOOGLE_RECAPTCHA_SITE_KEY',
          'RECAPTCHA_SITE_KEY',
          'G_SITE_KEY',
          'g-site-key',
        ]);

        // Email settings
        $recipientEmail = $readEnvironmentValue(['WYP_EMAIL', 'CONTACT_RECIPIENT_EMAIL', 'wyp-email']);

        // If the form is submitted 
        $postData = $statusMsg = '';
        $status = 'error';

        if (isset($_POST['submit'])) {
          $postData = $_POST;

          if (
            empty($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
          ) {
            $statusMsg = 'Your session has expired. Please refresh and try again.';
          } else {
            // Validate form input fields
            if (
              !empty($_POST['contact-fn']) &&
              !empty($_POST['contact-ln']) &&
              !empty($_POST['contact-em']) &&
              !empty($_POST['contact-subj']) &&
              !empty($_POST['contact-ta']) &&
              empty($_POST['beeName'])
            ) {

              // Validate reCAPTCHA checkbox
              if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
                if ($secretKey === '' || $recipientEmail === '') {
                  $statusMsg = 'Contact form configuration is incomplete. Please try again later.';
                } else {
                  // Verify reCAPTCHA with a fully encoded query to avoid malformed requests.
                  $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify?secret='
                    . rawurlencode($secretKey)
                    . '&response='
                    . rawurlencode((string) $_POST['g-recaptcha-response']);

                  $verifyResponse = @file_get_contents($verifyUrl);
                  $responseData = is_string($verifyResponse) ? json_decode($verifyResponse) : null;
                  $isRecaptchaValid = is_object($responseData) && !empty($responseData->success);

                  // If the reCAPTCHA API response is valid
                  if ($isRecaptchaValid) {
                  // Retrieve value from the form input fields 
                  $firstName = !empty($_POST['contact-fn']) ? htmlspecialchars($_POST['contact-fn']) : '';
                  $lastName = !empty($_POST['contact-ln']) ? htmlspecialchars($_POST['contact-ln']) : '';
                  $email = !empty($_POST['contact-em']) ? htmlspecialchars($_POST['contact-em']) : '';
                  $phone = !empty($_POST['contact-phone']) ? htmlspecialchars($_POST['contact-phone']) : '';
                  $contactSubj = !empty($_POST['contact-subj']) ? htmlspecialchars($_POST['contact-subj']) : '';
                  $contactMess = !empty($_POST['contact-ta']) ? htmlspecialchars($_POST['contact-ta']) : '';

                  // Send email notification to the site admin 
                  $to = $recipientEmail;
                  $subject = 'WYP Contact Us Submitted';
                  $htmlContent = " 
                    <h4>Wipe Your Paw's Contact Us Form - EN</h4> 
                    <p><b>Name: </b>" . $firstName . " " . $lastName . "</p> 
                    <p><b>Email: </b>" . $email . "</p> 
                    <p><b>Phone: </b>" . $phone . "</p> 
                    <p><b>Subject: </b>" . $contactSubj . "</p> 
                    <p><b>Message: </b>" . $contactMess . "</p> 
                ";

                  // Always set content-type when sending HTML email 
                  $headers = "MIME-Version: 1.0" . "\r\n";
                  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                  // More headers 
                  $headers .= 'From:' . $firstName . ' ' . $lastName . '<' . $email . '>' . "\r\n";

                  // Send email
                  $emailWasSent = @mail($to, $subject, $htmlContent, $headers);
                  if ($emailWasSent) {
                    $status = 'success';
                    $statusMsg = 'Thank you! Please allow up to 48 hours for a response.';
                    $postData = '';
                  } else {
                    $statusMsg = 'Your message could not be delivered right now. Please try again later.';
                  }
                } else {
                  $statusMsg = 'We apologize, reCaptcha verification failed, and  please try again.';
                }
                }
              } else {
                $statusMsg = 'Please check the reCAPTCHA checkbox to prove your human.';
              }
            } else {
              $statusMsg = 'There wa one or more mandatory fields missing.';
              if (!empty($_POST['beeName'])) {
                $statusMsg = 'Are you Agent Smith?';
              }
            }
          }
        }

        ?>

        <div id="contact-us"></div>
        <?php if (!empty($statusMsg)) { ?>
          <div class="col-xl-8 col-lg-8 col-md-12 mb-5">
            <div class="p-3 text-center text-bg-light hero-text-border" title="We are listening.">
              <p id="formErrorSummary" tabindex="-1" class="mb-5 h5 status-msg <?php echo $status; ?>"><?php echo $statusMsg; ?></p>
            </div>
          </div>
        <?php } ?>

        <div class="col-xl-10 col-lg-10 col-md-12 mb-5">
          <div class="p-3 text-bg-light hero-text-border" title="Millie's Crazy Flowers Contact Us Form.">

            <form action="contact.php" method="POST" class="row g-3 needs-validation" id="myForm" novalidate>
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

              <p class="fw-bold">We're open for any suggestion or just to have a chat.</p>

              <div class="col-md-6">
                <label for="beeName" aria-hidden="true" class="visually-hidden">Sunflower Name</label>
                <input type="text" name="beeName" id="beeName" style="display:none">

                <label for="contact-fn" class="form-label">First Name (Required)</label>
                <input type="text" class="form-control" name="contact-fn" id="contact-fn" required>
                <div class="invalid-feedback">
                  Please enter your first name.
                </div>
              </div>

              <div class="col-md-6">
                <label for="contact-ln" class="form-label">Last Name (Required)</label>
                <input type="text" class="form-control" name="contact-ln" id="contact-ln" required>
                <div class="invalid-feedback">
                  Please enter your last name.
                </div>
              </div>

              <div class="col-md-6">
                <label for="contact-em" class="form-label">Email (Required)</label>
                <input type="email" class="form-control" name="contact-em" id="contact-em" required>
                <div class="invalid-feedback">
                  Please enter your email.
                </div>
              </div>

              <div class="col-md-6">
                <label for="contact-phone" class="form-label">Phone (xxx.xxx.xxxx)</label>
                <input type="tel" class="form-control" name="contact-phone" id="contact-phone" pattern="^(\+\d{1,2}\s?)?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$" placeholder="555.867.5309">
                <div class="invalid-feedback">
                  Optional, please enter a valid phone number.
                </div>
              </div>

              <div class="col-md-12">
                <label for="contact-subj" class="form-label">Subject (Required)</label>
                <input type="text" class="form-control" name="contact-subj" id="contact-subj" required>
                <div class="invalid-feedback">
                  Please enter a subject.
                </div>
              </div>

              <div class="col-md-12">
                <label for="contact-ta" class="form-label">Question, Feedback or Improvement (Required)</label>
                <textarea class="form-control" name="contact-ta" id="contact-ta" required></textarea>
                <div class="invalid-feedback">
                  Please type your message.
                </div>
              </div>

              <div class="col-md-12">
                <?php if ($siteKey !== '') { ?>
                  <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($siteKey, ENT_QUOTES, 'UTF-8') ?>"></div>
                  <p id="recaptchaLoadError" class="form-error-text d-none mb-0" role="status" aria-live="polite">
                    reCAPTCHA could not be loaded. Please refresh and try again.
                  </p>
                <?php } else { ?>
                  <p class="form-error-text mb-0" role="status" aria-live="polite">
                    reCAPTCHA is currently unavailable due to a server configuration issue.
                  </p>
                <?php } ?>
              </div>

              <div class="col-md-6 text-center">
                <button type="submit" class="btn-wyp btn-wyp-primary" name="submit" <?= $siteKey === '' ? 'disabled aria-disabled="true"' : '' ?>>Submit Message</button>
              </div>

              <div class="col-md-6 text-center">
                <button type="reset" id="resetFormButton" class="btn-wyp btn-wyp-outline" name="reset" value="reset" aria-labelledby="reset">Reset Form</button>
                <div class="sr-only" id="reset" role="alert" aria-live="assertive" aria-atomic="true">
                  <p>(A pop up will confirm your reset form)</p>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="col-xl-8 col-lg-8 col-md-12 mb-5">
          <div class="p-3 text-center hero-text-border banner" title="Please contact us with any questions, suggestions, or concerns.">
            <section aria-label="Talk to Us">
              <h2 class="h5 mb-6 px-3 px-md-0">Please allow us up to 48 hours to respond, and if you need assistance sooner, please email <?= htmlspecialchars($recipientEmail, ENT_QUOTES, 'UTF-8') ?>
              </h2>
            </section>
          </div>
        </div>

      </div>

      <div class="col-lg-5">

        <div class="contact-info-box mb-4">
          <h3>Get in Touch <span aria-hidden="true">&#128062;</span></h3>

          <address class="address-reset">

            <div class="contact-info-row">
              <div class="contact-info-icon" aria-hidden="true">&#9993;&#65039;</div>
              <div>
                <strong class="contact-info-label">Email</strong>
                <a href="mailto:admin@wipeyourpaws.net" class="contact-info-link">
                  admin@wipeyourpaws.net
                </a>
              </div>
            </div>

            <div class="contact-info-row">
              <div class="contact-info-icon" aria-hidden="true">&#128205;</div>
              <div>
                <strong class="contact-info-label">Location</strong>
                <span class="contact-info-value">Monterey Bay, California</span>
              </div>
            </div>

            <div class="contact-info-row">
              <div class="contact-info-icon" aria-hidden="true">&#127760;</div>
              <div>
                <strong class="contact-info-label">Website</strong>
                <span class="contact-info-value">wipeyourpaws.net</span>
              </div>
            </div>

          </address>

          <hr class="contact-info-divider">

          <p class="contact-info-note">
            Whether you have questions about small dog care, want to share your own
            pup&rsquo;s story, or just want to say hi &mdash; we love hearing from
            the small dog community! <i class="bi bi-suit-heart-fill" aria-hidden="true"></i><span class="visually-hidden">LOVE</span>
          </p>
        </div>

        <div class="contact-paw-box">

          <div class="d-flex align-items-start gap-4">
            <div class="dog-avatar-frame">
              <img src="/images/skipper-icon-50x42.png" alt="skipper cartoon icon" class="mx-2" width="50" height="42" aria-hidden="true">
            </div>
            <p class="contact-paw-box__text">
              Skipper and Chandra are eagerly awaiting your message &mdash; and are ready to give you a virtual paw-shake in return!
            </p>
            <div class="dog-avatar-frame">
              <img src="/images/chandra icon 55x55.png" alt="chandra bust icon" class="mx-2" width="55" height="55" aria-hidden="true">
            </div>
          </div>

        </div>

      </div>
    </div>
</section>

<!--  MAP  -->
<section class="wyp-section wyp-section-sm">
  <div class="container">
    <div class="text-center mb-4">
      <span class="section-eyebrow">Where to Find Us</span>
      <h2 class="section-title">Monterey Bay, California</h2>
      <hr class="section-divider">
    </div>
    <div class="map-wrapper ratio ratio-16x9">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d51729.2!2d-121.9177!3d36.6002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808de15c59e1e2fd%3A0xeabe3a9b9c9b1efc!2sMonterey%2C%20CA!5e0!3m2!1sen!2sus"
        allowfullscreen
        loading="lazy" referrerpolicy="no-referrer-when-downgrade"
        title="Google map showing Monterey Bay, California">
      </iframe>
    </div>
  </div>
</section>

<script src="/js/contact_page.js?v=<?= filemtime(__DIR__ . '/js/contact_page.js'); ?>" defer></script>

<?php require_once 'includes/footer.php';
