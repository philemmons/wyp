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
$sanitizeMailHeaderValue = static function (string $value): string {
  $cleanValue = str_replace(["\r", "\n"], ' ', $value);
  return trim(preg_replace('/\s+/', ' ', $cleanValue) ?? '');
};

/**
 * Read and trim a posted value from the request body.
 */
$readTrimmedPostValue = static function (string $key): string {
  $rawValue = $_POST[$key] ?? '';
  return trim((string) $rawValue);
};

/**
 * Verify a reCAPTCHA token with Google using server-side secret key.
 */
$verifyRecaptchaTokenWithGoogle = static function (string $recaptchaSecretKey, string $recaptchaResponseToken): bool {
  $requestBody = http_build_query([
    'secret' => $recaptchaSecretKey,
    'response' => $recaptchaResponseToken,
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
$recaptchaSiteKey = wyp_env('GOOGLE_RECAPTCHA_SITE_KEY');
$recaptchaSecretKey = wyp_env('GOOGLE_RECAPTCHA_SECRET_KEY');
$recipientEmail = wyp_env('WYP_EMAIL');
if ($recipientEmail === '') {
  $recipientEmail = wyp_env('CONTACT_RECIPIENT_EMAIL');
}

$formFromEmail = wyp_env('WYP_FORM_FROM_EMAIL');

$contactFormConfigurationIssues = [];
if ($recaptchaSiteKey === '') {
  $contactFormConfigurationIssues[] = 'GOOGLE_RECAPTCHA_SITE_KEY';
}
if ($recaptchaSecretKey === '') {
  $contactFormConfigurationIssues[] = 'GOOGLE_RECAPTCHA_SECRET_KEY';
}
if ($recipientEmail === '' || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
  $contactFormConfigurationIssues[] = 'WYP_EMAIL';
}
if ($formFromEmail !== '' && !filter_var($formFromEmail, FILTER_VALIDATE_EMAIL)) {
  $contactFormConfigurationIssues[] = 'WYP_FORM_FROM_EMAIL';
}

$isContactFormConfigured = $contactFormConfigurationIssues === [];
if (!$isContactFormConfigured) {
  error_log(
    'Contact form configuration issue. Missing or invalid environment values: '
      . implode(', ', $contactFormConfigurationIssues)
  );
}

$formSubmissionStatus = 'error';
$formStatusMessage = '';
$fieldErrorMessages = [
  'contact-em' => '',
  'contact-subj' => '',
  'contact-ta' => '',
  'recaptcha' => '',
];

$submittedFieldValues = [
  'contact-name' => '',
  'contact-em' => '',
  'contact-subj' => '',
  'contact-ta' => '',
];

// Process only explicit form submissions; plain GET requests render the page.
if (isset($_POST['submit'])) {
  // Persist posted values so the user does not lose input when validation fails.
  $submittedFieldValues['contact-name'] = $readTrimmedPostValue('contact-name');
  $submittedFieldValues['contact-em'] = $readTrimmedPostValue('contact-em');
  $submittedFieldValues['contact-subj'] = $readTrimmedPostValue('contact-subj');
  $submittedFieldValues['contact-ta'] = $readTrimmedPostValue('contact-ta');

  $csrfTokenFromPost = (string) ($_POST['csrf_token'] ?? '');
  $honeypotFieldValue = $readTrimmedPostValue('beeName');
  $recaptchaResponseToken = $readTrimmedPostValue('g-recaptcha-response');

  // Fast-fail security checks first to avoid expensive work on invalid or bot traffic.
  if ($csrfTokenFromPost === '' || !hash_equals($_SESSION['csrf_token'], $csrfTokenFromPost)) {
    $formStatusMessage = 'Your session has expired. Please refresh and try again.';
  } elseif ($honeypotFieldValue !== '') {
    $formStatusMessage = 'Spam protection triggered. Please try again.';
  } elseif (!$isContactFormConfigured) {
    $formStatusMessage = 'The contact form is temporarily unavailable due to server configuration. Please try again later.';
  } else {
    // Domain validation runs only after security gates pass, so messages stay user-actionable.
    if ($submittedFieldValues['contact-em'] === '') {
      $fieldErrorMessages['contact-em'] = 'Email is required.';
    } elseif (!filter_var($submittedFieldValues['contact-em'], FILTER_VALIDATE_EMAIL)) {
      $fieldErrorMessages['contact-em'] = 'Please enter a valid email address.';
    }

    if ($submittedFieldValues['contact-subj'] === '') {
      $fieldErrorMessages['contact-subj'] = 'Subject is required.';
    } elseif (strlen($submittedFieldValues['contact-subj']) > 150) {
      $fieldErrorMessages['contact-subj'] = 'Subject must be 150 characters or fewer.';
    }

    if ($submittedFieldValues['contact-ta'] === '') {
      $fieldErrorMessages['contact-ta'] = 'Message is required.';
    } elseif (strlen($submittedFieldValues['contact-ta']) > 5000) {
      $fieldErrorMessages['contact-ta'] = 'Message must be 5000 characters or fewer.';
    }

    if ($recaptchaResponseToken === '') {
      $fieldErrorMessages['recaptcha'] = 'Please complete reCAPTCHA before submitting.';
    } elseif (!$verifyRecaptchaTokenWithGoogle($recaptchaSecretKey, $recaptchaResponseToken)) {
      $fieldErrorMessages['recaptcha'] = 'reCAPTCHA verification failed. Please try again.';
    }

    // Aggregate field-level errors into one decision point for clearer control flow.
    $hasValidationErrors = false;
    foreach ($fieldErrorMessages as $fieldError) {
      if ($fieldError !== '') {
        $hasValidationErrors = true;
        break;
      }
    }

    if ($hasValidationErrors) {
      $formStatusMessage = 'Please review the highlighted fields and try again.';
    } else {
      // Sanitize header-bound values separately from HTML output escaping.
      // Headers need newline stripping; HTML needs entity escaping.
      $submittedName = $sanitizeMailHeaderValue($submittedFieldValues['contact-name']);
      $submittedEmail = filter_var($submittedFieldValues['contact-em'], FILTER_VALIDATE_EMAIL) ?: '';
      $submittedSubject = $sanitizeMailHeaderValue($submittedFieldValues['contact-subj']);
      $submittedMessage = trim($submittedFieldValues['contact-ta']);

      $safeName = htmlspecialchars($submittedName, ENT_QUOTES, 'UTF-8');
      $safeEmail = htmlspecialchars((string) $submittedEmail, ENT_QUOTES, 'UTF-8');
      $safeSubject = htmlspecialchars($submittedSubject, ENT_QUOTES, 'UTF-8');
      $safeMessage = nl2br(htmlspecialchars($submittedMessage, ENT_QUOTES, 'UTF-8'));

      $mailSubject = 'WYP Contact Us Submitted: ' . substr($submittedSubject, 0, 120);
      $mailFromEmail = $formFromEmail !== '' ? $formFromEmail : $recipientEmail;
      $safeMailFromEmail = $sanitizeMailHeaderValue($mailFromEmail);

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
        $formSubmissionStatus = 'success';
        $formStatusMessage = 'Thank you! Please allow up to 48 hours for a response.';
        // Clear state after success to prevent accidental duplicate resubmissions from stale values.
        $submittedFieldValues = [
          'contact-name' => '',
          'contact-em' => '',
          'contact-subj' => '',
          'contact-ta' => '',
        ];
      } else {
        $formStatusMessage = 'Your message could not be delivered right now. Please try again later.';
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
    <img src="/images/dog-overlay.png" alt="Many dogs looking up" class="img-fluid mx-auto rounded d-block shadow-lg bg-warning-subtle">
    <div class="contact-paw-box mt-5">

      <div class="d-flex align-items-start">
        <div class="dog-avatar-frame">
          <img src="/images/skipper-icon-50x42.png" alt="" class="mx-2" width="50" height="42" aria-hidden="true">
        </div>
        <p class="section-eyebrow">
          Skipper and Chandra are eagerly awaiting your message - and are ready to give you a virtual paw-shake in return!
        </p>
        <div class="dog-avatar-frame">
          <img src="/images/chandra icon 55x55.png" alt="" class="mx-2" width="55" height="55" aria-hidden="true">
        </div>
      </div>

    </div>
  </div>
</section>


<!--  MAIN CONTACT SECTION  -->
<section class="wyp-section wyp-section-alt">
  <div class="container">
    <div class="row g-5 justify-content-center">

      <div class="col-lg-7">

        <div id="contact-us"></div>
        <?php if ($formStatusMessage !== '') { ?>
          <div class="mb-4">
            <div class="wyp-alert <?= $formSubmissionStatus === 'success' ? 'wyp-alert-success' : 'wyp-alert-error' ?>">
              <p id="contactFormStatusSummary" tabindex="-1" data-form-status="<?= htmlspecialchars($formSubmissionStatus, ENT_QUOTES, 'UTF-8') ?>" role="<?= $formSubmissionStatus === 'error' ? 'alert' : 'status' ?>" aria-live="<?= $formSubmissionStatus === 'error' ? 'assertive' : 'polite' ?>" aria-atomic="true" class="mb-0 h6 status-msg"><?php echo htmlspecialchars($formStatusMessage, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
          </div>
        <?php } ?>

        <div class="wyp-form">


          <form action="contact.php" method="POST" class="row g-3 needs-validation" id="contactForm" aria-labelledby="contact-form-heading" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

            <h2 id="contact-form-heading" class="section-title">We're open for any suggestion or just to have a chat.</h2>
            <p id="contact-required-note" class="form-help mb-0">Fields marked "(required)" must be completed before submitting.</p>

            <?php if (!$isContactFormConfigured) { ?>
              <p class="form-error-text mb-0" role="status" aria-live="polite">
                Contact form is temporarily unavailable due to server configuration.
              </p>
              <p class="form-help mb-0" role="status" aria-live="polite">
                Developer setup required: add valid values for <?php echo htmlspecialchars(implode(', ', $contactFormConfigurationIssues), ENT_QUOTES, 'UTF-8'); ?> in your environment.
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
                autocomplete="name"
                value="<?= htmlspecialchars($submittedFieldValues['contact-name'], ENT_QUOTES, 'UTF-8') ?>">
            </div>


            <div class="col-md-12">
              <label for="contact-em" class="form-label">Email (required)</label>
              <input
                type="email"
                class="form-control <?= $fieldErrorMessages['contact-em'] !== '' ? 'is-invalid' : '' ?>"
                name="contact-em"
                id="contact-em"
                required
                aria-required="true"
                autocomplete="email"
                aria-describedby="contact-required-note contact-em-error"
                <?= $fieldErrorMessages['contact-em'] !== '' ? 'aria-invalid="true"' : '' ?>
                value="<?= htmlspecialchars($submittedFieldValues['contact-em'], ENT_QUOTES, 'UTF-8') ?>">
              <div id="contact-em-error" class="invalid-feedback">
                <?= $fieldErrorMessages['contact-em'] !== ''
                  ? htmlspecialchars($fieldErrorMessages['contact-em'], ENT_QUOTES, 'UTF-8')
                  : 'Please enter a valid email address.' ?>
              </div>
            </div>


            <div class="col-md-12">
              <label for="contact-subj" class="form-label">Subject (required)</label>
              <input
                type="text"
                class="form-control <?= $fieldErrorMessages['contact-subj'] !== '' ? 'is-invalid' : '' ?>"
                name="contact-subj"
                id="contact-subj"
                required
                aria-required="true"
                maxlength="150"
                aria-describedby="contact-required-note contact-subj-hint contact-subj-error"
                <?= $fieldErrorMessages['contact-subj'] !== '' ? 'aria-invalid="true"' : '' ?>
                value="<?= htmlspecialchars($submittedFieldValues['contact-subj'], ENT_QUOTES, 'UTF-8') ?>">
              <p id="contact-subj-hint" class="form-help mb-0">Subject limit: 150 characters.</p>
              <div id="contact-subj-error" class="invalid-feedback">
                <?= $fieldErrorMessages['contact-subj'] !== ''
                  ? htmlspecialchars($fieldErrorMessages['contact-subj'], ENT_QUOTES, 'UTF-8')
                  : 'Please enter a subject.' ?>
              </div>
            </div>


            <div class="col-md-12">
              <label for="contact-ta" class="form-label">Message (required)</label>
              <textarea
                class="form-control <?= $fieldErrorMessages['contact-ta'] !== '' ? 'is-invalid' : '' ?>"
                name="contact-ta"
                id="contact-ta"
                required
                aria-required="true"
                maxlength="5000"
                aria-describedby="contact-required-note contact-ta-hint contact-ta-error"
                <?= $fieldErrorMessages['contact-ta'] !== '' ? 'aria-invalid="true"' : '' ?>><?= htmlspecialchars($submittedFieldValues['contact-ta'], ENT_QUOTES, 'UTF-8') ?></textarea>
              <p id="contact-ta-hint" class="form-help mb-0">Message limit: 5000 characters.</p>
              <div id="contact-ta-error" class="invalid-feedback">
                <?= $fieldErrorMessages['contact-ta'] !== ''
                  ? htmlspecialchars($fieldErrorMessages['contact-ta'], ENT_QUOTES, 'UTF-8')
                  : 'Please type your message.' ?>
              </div>
            </div>


            <div class="col-md-12">
              <p id="recaptcha-label" class="visually-hidden">Spam protection (required)</p>
              <p id="recaptcha-help" class="visually-hidden">Complete the reCAPTCHA challenge before submitting the form.</p>
              <?php if ($recaptchaSiteKey !== '') { ?>
                <div
                  id="recaptcha-group"
                  role="group"
                  aria-labelledby="recaptcha-label"
                  aria-describedby="recaptcha-help recaptchaLoadError recaptchaValidationError"
                  <?= $fieldErrorMessages['recaptcha'] !== '' ? 'aria-invalid="true"' : '' ?>>
                  <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaSiteKey, ENT_QUOTES, 'UTF-8') ?>"></div>
                </div>
              <?php } else { ?>
                <div id="recaptcha-group" role="group" aria-labelledby="recaptcha-label" aria-describedby="recaptcha-help recaptchaConfigError" aria-invalid="true"></div>
              <?php } ?>
              <p id="recaptchaLoadError" class="form-error-text d-none mb-0" role="status" aria-live="polite">
                reCAPTCHA could not be loaded. Please refresh and try again.
              </p>
              <?php if ($recaptchaSiteKey === '') { ?>
                <p id="recaptchaConfigError" class="form-error-text mb-0" role="status" aria-live="polite">
                  reCAPTCHA is currently unavailable due to a server configuration issue.
                </p>
              <?php } ?>
              <p id="recaptchaValidationError" tabindex="-1" class="form-error-text mb-0 <?= $fieldErrorMessages['recaptcha'] === '' ? 'd-none' : '' ?>" role="alert" aria-live="assertive" aria-atomic="true">
                <?= htmlspecialchars($fieldErrorMessages['recaptcha'], ENT_QUOTES, 'UTF-8') ?>
              </p>
            </div>


            <div class="col-md-6 text-center">
              <button type="submit" class="btn-wyp btn-wyp-primary" name="submit" <?= $isContactFormConfigured ? '' : 'disabled aria-disabled="true"' ?>>Submit Message</button>
            </div>


            <div class="col-md-6 text-center">
              <button type="reset" id="resetContactFormButton" class="btn-wyp btn-wyp-outline" name="reset" value="reset" aria-describedby="reset-help">Reset Form</button>
              <span class="visually-hidden" id="reset-help">A confirmation dialog appears before this form is reset.</span>
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

<script src="/js/contact_form_experience.js?v=<?= filemtime(__DIR__ . '/js/contact_form_experience.js'); ?>" defer></script>

<?php require_once 'includes/footer.php';
