<?php

/**
 * contact.php - Contact Us
 * wipeyourpaws.net - PHP 8.x - Bootstrap 5.3.8 - WCAG 2.1 AA
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

// Start session storage so CSRF tokens, flash messages, and anti-spam timers persist between requests.
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

$activePageKey = 'contact';

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Remove line-break characters to prevent header injection in any header-bound value.
 */
$sanitizeHeaderValue = static function (string $value): string {
  $cleanValue = str_replace(["\r", "\n"], ' ', $value);
  return trim(preg_replace('/\s+/', ' ', $cleanValue) ?? '');
};

/**
 * Read and trim a posted value from the request body.
 */
$readPostedValue = static function (string $key): string {
  $rawValue = $_POST[$key] ?? '';
  return trim((string) $rawValue);
};

/**
 * Verify a reCAPTCHA token with Google using server-side secret key.
 */
$verifyRecaptcha = static function (string $secretKey, string $responseToken): bool {
  $requestBody = http_build_query([
    'secret' => $secretKey,
    'response' => $responseToken,
  ], '', '&', PHP_QUERY_RFC3986);

  $streamContext = stream_context_create([
    'http' => [
      'method' => 'POST',
      'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
        . 'Content-Length: ' . strlen($requestBody) . "\r\n",
      'content' => $requestBody,
      'timeout' => 10,
      'ignore_errors' => true,
    ],
  ]);

  $responseBody = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $streamContext);
  if (!is_string($responseBody) || $responseBody === '') {
    return false;
  }

  $decoded = json_decode($responseBody, true);
  return is_array($decoded) && !empty($decoded['success']);
};

// Pull runtime configuration once so request handling stays deterministic and testable.
// `CONTACT_RECIPIENT_EMAIL` acts as a compatibility fallback for older deployments.
$siteKey = wyp_env('GOOGLE_RECAPTCHA_SITE_KEY');
$secretKey = wyp_env('GOOGLE_RECAPTCHA_SECRET_KEY');
$recipientEmail = wyp_env('WYP_EMAIL');
if ($recipientEmail === '') {
  $recipientEmail = wyp_env('CONTACT_RECIPIENT_EMAIL');
}

$formFromEmail = wyp_env('WYP_FORM_FROM_EMAIL');

$formConfigurationIssues = [];
if ($siteKey === '') {
  $formConfigurationIssues[] = 'GOOGLE_RECAPTCHA_SITE_KEY';
}
if ($secretKey === '') {
  $formConfigurationIssues[] = 'GOOGLE_RECAPTCHA_SECRET_KEY';
}
if ($recipientEmail === '' || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
  $formConfigurationIssues[] = 'WYP_EMAIL';
}
if ($formFromEmail !== '' && !filter_var($formFromEmail, FILTER_VALIDATE_EMAIL)) {
  $formConfigurationIssues[] = 'WYP_FORM_FROM_EMAIL';
}

$isFormConfigured = $formConfigurationIssues === [];
if (!$isFormConfigured) {
  error_log(
    'Contact form configuration issue. Missing or invalid environment values: '
      . implode(', ', $formConfigurationIssues)
  );
}

$supportEmailForDisplay = filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)
  ? $recipientEmail
  : 'admin@wipeyourpaws.net';

$status = 'error';
$statusMsg = '';
$fieldErrors = [
  'contact-em' => '',
  'contact-subj' => '',
  'contact-ta' => '',
  'recaptcha' => '',
];

$postData = [
  'contact-name' => '',
  'contact-em' => '',
  'contact-subj' => '',
  'contact-ta' => '',
];

// Process only explicit form submissions; plain GET requests render the page.
if (isset($_POST['submit'])) {
  // Persist posted values so the user does not lose input when validation fails.
  $postData['contact-name'] = $readPostedValue('contact-name');
  $postData['contact-em'] = $readPostedValue('contact-em');
  $postData['contact-subj'] = $readPostedValue('contact-subj');
  $postData['contact-ta'] = $readPostedValue('contact-ta');

  $csrfTokenFromPost = (string) ($_POST['csrf_token'] ?? '');
  $honeypotValue = $readPostedValue('beeName');
  $recaptchaResponse = $readPostedValue('g-recaptcha-response');

  // Fast-fail security checks first to avoid expensive work on invalid or bot traffic.
  if ($csrfTokenFromPost === '' || !hash_equals($_SESSION['csrf_token'], $csrfTokenFromPost)) {
    $statusMsg = 'Your session has expired. Please refresh and try again.';
  } elseif ($honeypotValue !== '') {
    $statusMsg = 'Spam protection triggered. Please try again.';
  } elseif (!$isFormConfigured) {
    $statusMsg = 'The contact form is temporarily unavailable due to server configuration. Please try again later.';
  } else {
    // Domain validation runs only after security gates pass, so messages stay user-actionable.
    if ($postData['contact-em'] === '') {
      $fieldErrors['contact-em'] = 'Email is required.';
    } elseif (!filter_var($postData['contact-em'], FILTER_VALIDATE_EMAIL)) {
      $fieldErrors['contact-em'] = 'Please enter a valid email address.';
    }

    if ($postData['contact-subj'] === '') {
      $fieldErrors['contact-subj'] = 'Subject is required.';
    } elseif (strlen($postData['contact-subj']) > 150) {
      $fieldErrors['contact-subj'] = 'Subject must be 150 characters or fewer.';
    }

    if ($postData['contact-ta'] === '') {
      $fieldErrors['contact-ta'] = 'Message is required.';
    } elseif (strlen($postData['contact-ta']) > 5000) {
      $fieldErrors['contact-ta'] = 'Message must be 5000 characters or fewer.';
    }

    if ($recaptchaResponse === '') {
      $fieldErrors['recaptcha'] = 'Please complete reCAPTCHA before submitting.';
    } elseif (!$verifyRecaptcha($secretKey, $recaptchaResponse)) {
      $fieldErrors['recaptcha'] = 'reCAPTCHA verification failed. Please try again.';
    }

    // Aggregate field-level errors into one decision point for clearer control flow.
    $hasFieldErrors = false;
    foreach ($fieldErrors as $fieldError) {
      if ($fieldError !== '') {
        $hasFieldErrors = true;
        break;
      }
    }

    if ($hasFieldErrors) {
      $statusMsg = 'Please review the highlighted fields and try again.';
    } else {
      // Sanitize header-bound values separately from HTML output escaping.
      // Headers need newline stripping; HTML needs entity escaping.
      $submittedName = $sanitizeHeaderValue($postData['contact-name']);
      $submittedEmail = filter_var($postData['contact-em'], FILTER_VALIDATE_EMAIL) ?: '';
      $submittedSubject = $sanitizeHeaderValue($postData['contact-subj']);
      $submittedMessage = trim($postData['contact-ta']);

      $safeName = htmlspecialchars($submittedName, ENT_QUOTES, 'UTF-8');
      $safeEmail = htmlspecialchars((string) $submittedEmail, ENT_QUOTES, 'UTF-8');
      $safeSubject = htmlspecialchars($submittedSubject, ENT_QUOTES, 'UTF-8');
      $safeMessage = nl2br(htmlspecialchars($submittedMessage, ENT_QUOTES, 'UTF-8'));

      $mailSubject = 'WYP Contact Us Submitted: ' . substr($submittedSubject, 0, 120);
      $mailFromEmail = $formFromEmail !== '' ? $formFromEmail : $recipientEmail;
      $safeMailFromEmail = $sanitizeHeaderValue($mailFromEmail);

      // Keep content HTML-formatted for readability in inboxes while preserving escaped user input.
      $htmlContent = "
        <h4>Wipe Your Paws Contact Form Submission</h4>
        <p><b>Name:</b> " . ($safeName !== '' ? $safeName : 'Not provided') . "</p>
        <p><b>Email:</b> " . $safeEmail . "</p>
        <p><b>Subject:</b> " . $safeSubject . "</p>
        <p><b>Message:</b><br>" . $safeMessage . "</p>
        <p><b>reCAPTCHA:</b> Verified</p>
      ";

      $headers = [
        'MIME-Version: 1.0',
        'Content-type:text/html;charset=UTF-8',
        'From: Wipe Your Paws Contact Form <' . $safeMailFromEmail . '>',
        'Reply-To: ' . $safeEmail,
        'X-Mailer: PHP/' . phpversion(),
      ];

      // `@mail()` suppresses transport warnings from leaking to users;
      // operations visibility still comes from server logs/error tracking.
      $emailWasSent = @mail($recipientEmail, $mailSubject, $htmlContent, implode("\r\n", $headers));

      if ($emailWasSent) {
        $status = 'success';
        $statusMsg = 'Thank you! Please allow up to 48 hours for a response.';
        // Clear state after success to prevent accidental duplicate resubmissions from stale values.
        $postData = [
          'contact-name' => '',
          'contact-em' => '',
          'contact-subj' => '',
          'contact-ta' => '',
        ];
      } else {
        $statusMsg = 'Your message could not be delivered right now. Please try again later.';
      }
    }
  }
}

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

        <div id="contact-us"></div>
        <?php if ($statusMsg !== '') { ?>
          <div class="mb-4">
            <div class="wyp-alert <?= $status === 'success' ? 'wyp-alert-success' : 'wyp-alert-error' ?>" title="We are listening.">
              <p id="formErrorSummary" tabindex="-1" data-form-status="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" class="mb-0 h6 status-msg"><?php echo htmlspecialchars($statusMsg, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
          </div>
        <?php } ?>

        <div class="wyp-form" title="Wipe Your Paws Contact Us Form.">


          <form action="contact.php" method="POST" class="row g-3 needs-validation" id="myForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">


            <h2 class="section-title">We're open for any suggestion or just to have a chat.</h2>


            <?php if (!$isFormConfigured) { ?>
              <p class="form-error-text mb-0" role="status" aria-live="polite">
                Contact form is temporarily unavailable due to server configuration.
              </p>
              <p class="form-help mb-0" role="status" aria-live="polite">
                Developer setup required: add valid values for <?php echo htmlspecialchars(implode(', ', $formConfigurationIssues), ENT_QUOTES, 'UTF-8'); ?> in your environment.
              </p>
            <?php } ?>


            <div class="sunFlower-wrap" aria-hidden="true">
              <label for="beeName" class="visually-hidden">Leave this field empty</label>
              <input type="text" name="beeName" id="beeName" tabindex="-1" autocomplete="off">
            </div>


            <div class="col-md-12">
              <label for="contact-name" class="form-label">Name (optional)</label>
              <input
                type="text"
                class="form-control"
                name="contact-name"
                id="contact-name"
                maxlength="120"
                value="<?= htmlspecialchars($postData['contact-name'], ENT_QUOTES, 'UTF-8') ?>">
            </div>


            <div class="col-md-12">
              <label for="contact-em" class="form-label">Email (required)</label>
              <input
                type="email"
                class="form-control <?= $fieldErrors['contact-em'] !== '' ? 'is-invalid' : '' ?>"
                name="contact-em"
                id="contact-em"
                required
                aria-describedby="contact-em-error"
                <?= $fieldErrors['contact-em'] !== '' ? 'aria-invalid="true"' : '' ?>
                value="<?= htmlspecialchars($postData['contact-em'], ENT_QUOTES, 'UTF-8') ?>">
              <div id="contact-em-error" class="invalid-feedback">
                <?= $fieldErrors['contact-em'] !== ''
                  ? htmlspecialchars($fieldErrors['contact-em'], ENT_QUOTES, 'UTF-8')
                  : 'Please enter a valid email address.' ?>
              </div>
            </div>


            <div class="col-md-12">
              <label for="contact-subj" class="form-label">Subject (required)</label>
              <input
                type="text"
                class="form-control <?= $fieldErrors['contact-subj'] !== '' ? 'is-invalid' : '' ?>"
                name="contact-subj"
                id="contact-subj"
                required
                maxlength="150"
                aria-describedby="contact-subj-error"
                <?= $fieldErrors['contact-subj'] !== '' ? 'aria-invalid="true"' : '' ?>
                value="<?= htmlspecialchars($postData['contact-subj'], ENT_QUOTES, 'UTF-8') ?>">
              <div id="contact-subj-error" class="invalid-feedback">
                <?= $fieldErrors['contact-subj'] !== ''
                  ? htmlspecialchars($fieldErrors['contact-subj'], ENT_QUOTES, 'UTF-8')
                  : 'Please enter a subject.' ?>
              </div>
            </div>


            <div class="col-md-12">
              <label for="contact-ta" class="form-label">Message (required)</label>
              <textarea
                class="form-control <?= $fieldErrors['contact-ta'] !== '' ? 'is-invalid' : '' ?>"
                name="contact-ta"
                id="contact-ta"
                required
                maxlength="5000"
                aria-describedby="contact-ta-error"
                <?= $fieldErrors['contact-ta'] !== '' ? 'aria-invalid="true"' : '' ?>><?= htmlspecialchars($postData['contact-ta'], ENT_QUOTES, 'UTF-8') ?></textarea>
              <div id="contact-ta-error" class="invalid-feedback">
                <?= $fieldErrors['contact-ta'] !== ''
                  ? htmlspecialchars($fieldErrors['contact-ta'], ENT_QUOTES, 'UTF-8')
                  : 'Please type your message.' ?>
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
              <p id="recaptchaValidationError" class="form-error-text mb-0 <?= $fieldErrors['recaptcha'] === '' ? 'd-none' : '' ?>" role="alert" aria-live="assertive">
                <?= htmlspecialchars($fieldErrors['recaptcha'], ENT_QUOTES, 'UTF-8') ?>
              </p>
            </div>


            <div class="col-md-6 text-center">
              <button type="submit" class="btn-wyp btn-wyp-primary" name="submit" <?= $isFormConfigured ? '' : 'disabled aria-disabled="true"' ?>>Submit Message</button>
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
            pup&rsquo;s story, or just want to say hi - we love hearing from
            the small dog community! <i class="bi bi-suit-heart-fill" aria-hidden="true"></i><span class="visually-hidden">LOVE</span>
          </p>
        </div>

        <div class="contact-paw-box">

          <div class="d-flex align-items-start gap-4">
            <div class="dog-avatar-frame">
              <img src="/images/skipper-icon-50x42.png" alt="skipper cartoon icon" class="mx-2" width="50" height="42" aria-hidden="true">
            </div>
            <p class="contact-paw-box__text">
              Skipper and Chandra are eagerly awaiting your message - and are ready to give you a virtual paw-shake in return!
            </p>
            <div class="dog-avatar-frame">
              <img src="/images/chandra icon 55x55.png" alt="chandra bust icon" class="mx-2" width="55" height="55" aria-hidden="true">
            </div>
          </div>

        </div>

        <div class="wyp-card p-3 mt-4">
          <section aria-label="Talking to Us">
            <p class="fs-4 px-3 px-md-0">Please allow us up to 48 hours to respond, as we are walking the dogs.</p>
          </section>
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
