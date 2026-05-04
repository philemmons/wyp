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

// Create one CSRF token per session so forged cross-site POST requests fail token validation.
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$requestMethod = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
if ($requestMethod !== 'GET' && $requestMethod !== 'POST') {
  http_response_code(405);
  header('Allow: GET, POST');
  $requestMethod = 'GET';
}

// Centralize anti-spam thresholds so behavior is easy to tune without changing validation logic.
$minSubmitSeconds = 3;
$submitCooldownSeconds = 30;
$ipWindowSeconds = 600;
$ipMaxSubmissions = 5;

// Read Google reCAPTCHA keys from environment so secrets are not hardcoded in source control.
$recaptchaSiteKey = trim((string) getenv('g-site-key'));
$recaptchaSecretKey = trim((string) getenv('g-secret-key'));
$recaptchaEnabled = $recaptchaSiteKey !== '' && $recaptchaSecretKey !== '';
$recaptchaNotice = '';

if (($recaptchaSiteKey !== '' || $recaptchaSecretKey !== '') && !$recaptchaEnabled) {
  $recaptchaNotice = 'Captcha is temporarily unavailable due to incomplete configuration. You can still submit the form.';
}

// Initialize default form and flash state so both GET and POST flows always render predictable values.
$defaultPostData = [
  'contact-name' => '',
  'contact-em' => '',
  'contact-subj' => '',
  'contact-ta' => '',
];

$status = '';
$statusMsg = '';
$statusList = [];
$fieldErrors = [];
$postData = $defaultPostData;

// Escape output once through a helper so every render path is consistently XSS-safe.
$esc = static function (string $value): string {
  return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$getPostValue = static function (string $field): string {
  $value = $_POST[$field] ?? '';
  return is_string($value) ? $value : '';
};

// Strip control bytes because they can obfuscate payloads and can also break logs, headers, or mail output.
$stripControlChars = static function (string $value): string {
  $sanitized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
  return is_string($sanitized) ? $sanitized : $value;
};

$normalizeSpaces = static function (string $value): string {
  $normalized = preg_replace('/\s+/u', ' ', $value);
  return is_string($normalized) ? trim($normalized) : trim($value);
};

$normalizeLineEndings = static function (string $value): string {
  return str_replace(["\r\n", "\r"], "\n", $value);
};

// Normalize message formatting so validation, storage, and outgoing email all process the same canonical text.
$normalizeMessage = static function (string $value) use ($stripControlChars, $normalizeLineEndings): string {
  $value = $stripControlChars($normalizeLineEndings($value));
  $lines = explode("\n", $value);

  foreach ($lines as &$line) {
    $line = preg_replace('/[ \t]+/u', ' ', $line);
    $line = is_string($line) ? trim($line) : '';
  }
  unset($line);

  $normalized = trim(implode("\n", $lines));
  $normalized = preg_replace("/\n{3,}/", "\n\n", $normalized);
  return is_string($normalized) ? $normalized : trim($value);
};

// Detect header-injection tokens early so user input cannot create extra mail headers.
$hasHeaderInjection = static function (string $value): bool {
  return preg_match('/\r|\n|%0a|%0d|content-type:|bcc:|cc:|to:/i', $value) === 1;
};

$sanitizeHeader = static function (string $value): string {
  $value = str_replace(["\r", "\n"], '', $value);
  $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
  return is_string($value) ? trim($value) : '';
};

// Branch once on request method so write logic stays in POST and render logic stays in GET.
$isPost = $requestMethod === 'POST';

if ($isPost) {
  // Collect raw form input first, then normalize it once so validation and email use identical sanitized values.
  $submittedCsrfToken = trim($getPostValue('csrf_token'));
  $sessionCsrfToken = (string) ($_SESSION['csrf_token'] ?? '');

  $rawContactName = $getPostValue('contact-name');
  $rawContactEmail = $getPostValue('contact-em');
  $rawContactSubj = $getPostValue('contact-subj');
  $rawContactTa = $getPostValue('contact-ta');
  $littlebee = trim($getPostValue('littlebee'));
  $recaptchaToken = trim($getPostValue('g-recaptcha-response'));

  $contactName = $normalizeSpaces(strip_tags($stripControlChars($rawContactName)));
  $contactEmail = strtolower($normalizeSpaces($stripControlChars($rawContactEmail)));
  $contactSubj = $normalizeSpaces(strip_tags($stripControlChars($rawContactSubj)));
  $contactTa = $normalizeMessage($rawContactTa);

  $postData = [
    'contact-name' => $contactName,
    'contact-em' => $contactEmail,
    'contact-subj' => $contactSubj,
    'contact-ta' => $contactTa,
  ];

  $status = 'error';
  $statusMsg = 'Please correct the highlighted issues and try again.';

  $successMsg = 'Thanks for reaching out. Your message has been received, and we will respond as soon as we can.';

  $now = time();
  $formStartedAt = (int) ($_SESSION['contact_form_started_at'] ?? 0);
  $lastSubmitAt = (int) ($_SESSION['contact_last_submit_at'] ?? 0);

  $remoteIp = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
  $ipHash = hash('sha256', $remoteIp !== '' ? $remoteIp : 'unknown');

  $ipRateStore = $_SESSION['contact_ip_rate_limit'] ?? [];
  if (!is_array($ipRateStore)) {
    $ipRateStore = [];
  }

  $ipTimestamps = $ipRateStore[$ipHash] ?? [];
  if (!is_array($ipTimestamps)) {
    $ipTimestamps = [];
  }

  $ipTimestamps = array_values(array_filter(
    $ipTimestamps,
    static fn($ts): bool => is_int($ts) && ($now - $ts) <= $ipWindowSeconds
  ));

  $submitTooFast = $formStartedAt > 0 && ($now - $formStartedAt) < $minSubmitSeconds;
  $submitCoolingDown = $lastSubmitAt > 0 && ($now - $lastSubmitAt) < $submitCooldownSeconds;
  $ipRateLimited = count($ipTimestamps) >= $ipMaxSubmissions;

  // Examine Sunflower Name; return silent success so users cannot tune around detection.
  if ($littlebee !== '') {
    $status = 'success';
    $statusMsg = $successMsg;
    $statusList = [$successMsg];
    $fieldErrors = [];
    $postData = $defaultPostData;
  } else {
    // Validate CSRF with hash_equals to prevent timing leaks and block forged form submissions.
    if (
      $submittedCsrfToken === '' ||
      $sessionCsrfToken === '' ||
      !hash_equals($sessionCsrfToken, $submittedCsrfToken)
    ) {
      $statusList[] = 'Your session expired. Please refresh the page and try again.';
    }

    if (
      $hasHeaderInjection($rawContactName) ||
      $hasHeaderInjection($rawContactEmail) ||
      $hasHeaderInjection($rawContactSubj)
    ) {
      $statusList[] = 'Invalid input detected. Please remove line breaks from name, email, and subject.';
    }

    // Run field validation only after anti-forgery checks so we avoid exposing unnecessary validation detail.
    // Anti-spam timing/rate controls return silent success to reduce feedback loops for scripted abuse.
    if (empty($statusList) && ($submitTooFast || $submitCoolingDown || $ipRateLimited)) {
      $status = 'success';
      $statusMsg = $successMsg;
      $statusList = [$successMsg];
      $fieldErrors = [];
      $postData = $defaultPostData;
    } else {
      if ($contactName === '') {
        $fieldErrors['contact-name'] = 'Please enter your name.';
      } elseif (mb_strlen($contactName) > 120) {
        $fieldErrors['contact-name'] = 'Name must be 120 characters or fewer.';
      }

      if ($contactEmail === '') {
        $fieldErrors['contact-em'] = 'Please enter your email address.';
      } elseif (mb_strlen($contactEmail) > 254) {
        $fieldErrors['contact-em'] = 'Email must be 254 characters or fewer.';
      } elseif (preg_match('/\.\./', $contactEmail) === 1 || filter_var($contactEmail, FILTER_VALIDATE_EMAIL) === false) {
        $fieldErrors['contact-em'] = 'Please enter a valid email address, for example name@example.com.';
      }

      if ($contactSubj !== '' && mb_strlen($contactSubj) > 200) {
        $fieldErrors['contact-subj'] = 'Subject must be 200 characters or fewer.';
      }

      if ($contactTa === '') {
        $fieldErrors['contact-ta'] = 'Please enter a message so we know how to help.';
      } elseif (mb_strlen($contactTa) < 10) {
        $fieldErrors['contact-ta'] = 'Message must be at least 10 characters.';
      } elseif (mb_strlen($contactTa) > 5000) {
        $fieldErrors['contact-ta'] = 'Message must be 5,000 characters or fewer.';
      }

      // Verify reCAPTCHA server-side because client-side checks alone can be bypassed by direct POST requests.
      if ($recaptchaEnabled) {
        if ($recaptchaToken === '') {
          $fieldErrors['contact-recaptcha'] = 'Please complete the captcha challenge before submitting.';
        } else {
          $verifyBody = http_build_query([
            'secret' => $recaptchaSecretKey,
            'response' => $recaptchaToken,
            'remoteip' => $remoteIp,
          ]);

          $verifyResponse = '';

          if (function_exists('curl_init')) {
            $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
            if ($ch !== false) {
              curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $verifyBody,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
              ]);
              $curlResponse = curl_exec($ch);
              if (is_string($curlResponse)) {
                $verifyResponse = $curlResponse;
              }
              curl_close($ch);
            }
          }

          if ($verifyResponse === '') {
            $streamContext = stream_context_create([
              'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => $verifyBody,
                'timeout' => 10,
              ],
            ]);

            $streamResponse = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $streamContext);
            if (is_string($streamResponse)) {
              $verifyResponse = $streamResponse;
            }
          }

          if ($verifyResponse === '') {
            $statusList[] = 'Captcha verification is temporarily unavailable. Please try again shortly.';
          } else {
            $verifyJson = json_decode($verifyResponse, true);
            $verifyOk = is_array($verifyJson) && !empty($verifyJson['success']);
            if (!$verifyOk) {
              $fieldErrors['contact-recaptcha'] = 'Captcha verification failed. Please try again.';
            }
          }
        }
      }

      foreach ($fieldErrors as $fieldErrorMsg) {
        $statusList[] = $fieldErrorMsg;
      }

      // Load mail config at send time so environment-specific addresses stay configurable outside app code.
      if (empty($statusList)) {
        $contactMailConfig = [];
        $configPath = __DIR__ . '/config/contact_mail.php';

        if (is_file($configPath)) {
          $loadedConfig = require $configPath;
          if (is_array($loadedConfig)) {
            $contactMailConfig = $loadedConfig;
          }
        }

        $siteName = $sanitizeHeader((string) ($contactMailConfig['site']['name'] ?? 'Wipe Your Paws'));
        $toEmail = (string) ($contactMailConfig['site']['admin_email'] ?? 'admin@wipeyourpaws.net');
        $fromEmail = (string) ($contactMailConfig['site']['from_email'] ?? 'noreply@wipeyourpaws.net');

        if (filter_var($toEmail, FILTER_VALIDATE_EMAIL) === false) {
          $toEmail = 'admin@wipeyourpaws.net';
        }
        if (filter_var($fromEmail, FILTER_VALIDATE_EMAIL) === false) {
          $fromEmail = 'noreply@wipeyourpaws.net';
        }

        $mailSubject = $contactSubj !== '' ? $contactSubj : 'New message from contact form';
        $mailSubject = '[wipeyourpaws.net] ' . $sanitizeHeader($mailSubject);

        $mailBody = "New contact form submission\n\n";
        $mailBody .= "Name: {$contactName}\n";
        $mailBody .= "Email: {$contactEmail}\n";
        $mailBody .= "Subject: {$contactSubj}\n\n";
        $mailBody .= "Message:\n{$contactTa}\n";

        // Keep From as site-owned for SPF/DMARC alignment, and use Reply-To for visitor address.
        $mailHeaders = [
          'MIME-Version: 1.0',
          'Content-Type: text/plain; charset=UTF-8',
          'Content-Transfer-Encoding: 8bit',
          "From: {$siteName} <{$fromEmail}>",
          'Reply-To: ' . $sanitizeHeader($contactEmail),
          'X-Mailer: PHP/' . PHP_VERSION,
        ];

        $mailSent = @mail($toEmail, $mailSubject, $mailBody, implode("\r\n", $mailHeaders));

        if ($mailSent) {
          $status = 'success';
          $statusMsg = $successMsg;
          $statusList = [$successMsg];
          $fieldErrors = [];
          $postData = $defaultPostData;
        } else {
          $status = 'error';
          $statusMsg = 'Something went wrong while sending your message. Please try again, or email us directly at admin@wipeyourpaws.net.';
          $statusList = [$statusMsg];
        }
      }
    }

    if (!empty($statusList) && $status !== 'success') {
      $status = 'error';
      $statusMsg = 'Please correct the highlighted issues and try again.';
    }
  }

  // Store outcome in session flash so PRG can render status on GET without repeating the POST action.
  $_SESSION['contact_form_flash'] = [
    'status' => $status,
    'statusMsg' => $statusMsg,
    'statusList' => $statusList,
    'fieldErrors' => $fieldErrors,
    'postData' => $postData,
  ];

  $ipTimestamps[] = $now;
  $ipRateStore[$ipHash] = $ipTimestamps;
  $_SESSION['contact_ip_rate_limit'] = $ipRateStore;
  $_SESSION['contact_last_submit_at'] = $now;

  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

  // Redirect after POST (PRG) so refresh repeats a safe GET page render instead of resubmitting email.
  header('Location: /contact.php', true, 303);
  exit;
}

// Read and clear flash on GET so messages are shown once and form-start timestamp resets for fill-time checks.
$flash = $_SESSION['contact_form_flash'] ?? null;
if (is_array($flash)) {
  $status = (string) ($flash['status'] ?? '');
  $statusMsg = (string) ($flash['statusMsg'] ?? '');
  $statusList = isset($flash['statusList']) && is_array($flash['statusList']) ? $flash['statusList'] : [];
  $fieldErrors = isset($flash['fieldErrors']) && is_array($flash['fieldErrors']) ? $flash['fieldErrors'] : [];
  $postData = isset($flash['postData']) && is_array($flash['postData']) ? array_merge($defaultPostData, $flash['postData']) : $defaultPostData;
}

unset($_SESSION['contact_form_flash']);
$_SESSION['contact_form_started_at'] = time();

$contactNameError = (string) ($fieldErrors['contact-name'] ?? '');
$contactEmError = (string) ($fieldErrors['contact-em'] ?? '');
$contactSubjError = (string) ($fieldErrors['contact-subj'] ?? '');
$contactTaError = (string) ($fieldErrors['contact-ta'] ?? '');
$contactRecaptchaError = (string) ($fieldErrors['contact-recaptcha'] ?? '');

$formDescribedBy = 'formRequiredNote';
if ($status === 'error' && !empty($statusList)) {
  $formDescribedBy .= ' formErrorSummary';
}
if ($recaptchaNotice !== '') {
  $formDescribedBy .= ' recaptchaNote';
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

        <?php if ($status === 'success' && !empty($statusList)): ?>
          <div class="wyp-alert wyp-alert-success mb-4" role="status" aria-live="polite">
            <strong>Message received!</strong>
            <?= $esc((string) $statusList[0]) ?>
          </div>
        <?php elseif ($status === 'error' && !empty($statusList)): ?>
          <!-- Error summary provides one focusable list so keyboard and screen-reader users can jump to each invalid field. -->
          <div class="wyp-alert wyp-alert-error mb-4" id="formErrorSummary" role="alert" aria-live="assertive" tabindex="-1">
            <strong>Please correct the following:</strong>
            <ul class="mb-0 mt-1">
              <?php foreach ($statusList as $errorMsg): ?>
                <?php
                $target = '';
                if ($errorMsg === $contactNameError) {
                  $target = '#contact-name';
                } elseif ($errorMsg === $contactEmError) {
                  $target = '#contact-em';
                } elseif ($errorMsg === $contactSubjError) {
                  $target = '#contact-subj';
                } elseif ($errorMsg === $contactTaError) {
                  $target = '#contact-ta';
                } elseif ($errorMsg === $contactRecaptchaError) {
                  $target = '#contact-recaptcha';
                }
                ?>
                <li>
                  <?php if ($target !== ''): ?>
                    <a href="<?= $esc($target) ?>"><?= $esc((string) $errorMsg) ?></a>
                  <?php else: ?>
                    <?= $esc((string) $errorMsg) ?>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <div class="wyp-form">
          <h2 class="section-title mb-1">Send Us a Message</h2>

          <p class="required-note" id="formRequiredNote">
            Fields marked with
            <span class="required-asterisk" aria-hidden="true">*</span>
            <span class="visually-hidden">an asterisk</span>
            are required.
          </p>

          <?php if ($recaptchaNotice !== ''): ?>
            <p class="form-help" id="recaptchaNote" role="status" aria-live="polite">
              <?= $esc($recaptchaNotice) ?>
            </p>
          <?php endif; ?>

          <form action="/contact.php" method="post" novalidate class="needs-validation contact-form" id="myForm"
            aria-describedby="<?= $esc(trim($formDescribedBy)) ?>">

            <input type="hidden" name="csrf_token" value="<?= $esc((string) $_SESSION['csrf_token']) ?>">

            <div class="visually-hidden" aria-hidden="true">
              <label for="littlebee">Sunflower Name</label>
              <input type="text" id="littlebee" name="littlebee" tabindex="-1" autocomplete="off" aria-hidden="true">
            </div>

            <div class="row g-3">

              <div class="col-sm-6">
                <label for="contact-name" class="form-label">
                  Your Name
                  <span class="required-asterisk" aria-hidden="true">*</span>
                  <span class="visually-hidden">(required)</span>
                </label>
                <input type="text" class="form-control" id="contact-name"
                  name="contact-name" value="<?= $esc((string) $postData['contact-name']) ?>"
                  placeholder="Jane Smith" autocomplete="name"
                  maxlength="120" required aria-required="true"
                  <?= $contactNameError !== '' ? 'aria-invalid="true" aria-describedby="contact-name-error"' : '' ?>>
                <?php if ($contactNameError !== ''): ?>
                  <p class="form-error-text mt-2 mb-0" id="contact-name-error">
                    <?= $esc($contactNameError) ?>
                  </p>
                <?php endif; ?>
              </div>

              <div class="col-sm-6">
                <label for="contact-em" class="form-label">
                  Email Address
                  <span class="required-asterisk" aria-hidden="true">*</span>
                  <span class="visually-hidden">(required)</span>
                </label>
                <input type="email" class="form-control" id="contact-em"
                  name="contact-em" value="<?= $esc((string) $postData['contact-em']) ?>"
                  placeholder="you@example.com" autocomplete="email"
                  maxlength="254" required aria-required="true"
                  <?= $contactEmError !== '' ? 'aria-invalid="true" aria-describedby="contact-em-error"' : '' ?>>
                <?php if ($contactEmError !== ''): ?>
                  <p class="form-error-text mt-2 mb-0" id="contact-em-error">
                    <?= $esc($contactEmError) ?>
                  </p>
                <?php endif; ?>
              </div>

              <div class="col-12">
                <label for="contact-subj" class="form-label">Subject</label>
                <input type="text" class="form-control" id="contact-subj"
                  name="contact-subj" value="<?= $esc((string) $postData['contact-subj']) ?>"
                  placeholder="e.g. Dog-friendly trail tips in Monterey!"
                  autocomplete="off" maxlength="200"
                  <?= $contactSubjError !== '' ? 'aria-invalid="true" aria-describedby="contact-subj-error"' : '' ?>>
                <?php if ($contactSubjError !== ''): ?>
                  <p class="form-error-text mt-2 mb-0" id="contact-subj-error">
                    <?= $esc($contactSubjError) ?>
                  </p>
                <?php endif; ?>
              </div>

              <div class="col-12">
                <label for="contact-ta" class="form-label">
                  Message
                  <span class="required-asterisk" aria-hidden="true">*</span>
                  <span class="visually-hidden">(required)</span>
                </label>
                <textarea class="form-control" id="contact-ta"
                  name="contact-ta" rows="6" maxlength="5000"
                  placeholder="Tell us about your furry friends, ask a question, or just say hi!"
                  autocomplete="off" required aria-required="true"
                  <?= $contactTaError !== '' ? 'aria-invalid="true" aria-describedby="contact-ta-error"' : '' ?>><?= $esc((string) $postData['contact-ta']) ?></textarea>
                <?php if ($contactTaError !== ''): ?>
                  <p class="form-error-text mt-2 mb-0" id="contact-ta-error">
                    <?= $esc($contactTaError) ?>
                  </p>
                <?php endif; ?>
              </div>

              <?php if ($recaptchaEnabled): ?>
                <div class="col-12">
                  <div id="contact-recaptcha" class="g-recaptcha" data-sitekey="<?= $esc($recaptchaSiteKey) ?>"></div>
                  <?php if ($contactRecaptchaError !== ''): ?>
                    <p class="form-error-text mt-2 mb-0" id="contact-recaptcha-error">
                      <?= $esc($contactRecaptchaError) ?>
                    </p>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <div class="col-12 mt-2">
                <button type="submit" class="btn-wyp btn-wyp-primary btn-submit me-2">
                  <i class="bi bi-send-fill" aria-hidden="true"></i>
                  Send Message
                </button>
                <button type="reset" class="btn-wyp btn-wyp-outline btn-submit" id="resetFormButton">
                  Reset Form
                </button>
                <p class="contact-form-hint">
                  <i class="bi bi-lock-fill me-1" aria-hidden="true"></i>
                  Your information will only be used to respond to your message.
                </p>
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

<?php if ($recaptchaEnabled): ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
<script src="/js/contact_page.js?v=<?= filemtime(__DIR__ . '/js/contact_page.js'); ?>" defer></script>

<?php require_once 'includes/footer.php';
