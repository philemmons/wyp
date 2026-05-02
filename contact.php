<?php
/**
 * contact.php - Contact Us
 * wipeyourpaws.net - PHP 8.5 - Bootstrap 5.3.8 - WCAG 2.1 AA
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$activePageKey = 'contact';
require_once 'includes/header.php';

$didSubmissionSucceed = $_SESSION['contact_form_submission_succeeded'] ?? false;
$wasConfirmationEmailSent = $_SESSION['contact_form_confirmation_sent'] ?? null;
$didSubmissionFail = $_SESSION['contact_form_submission_failed'] ?? false;
$formErrorMessages = $_SESSION['contact_form_error_messages'] ?? [];
$fieldErrorMessagesByField = $_SESSION['contact_form_field_errors'] ?? [];
$previousFormValues = $_SESSION['contact_form_previous_values'] ?? [];

unset(
    $_SESSION['contact_form_submission_succeeded'],
    $_SESSION['contact_form_confirmation_sent'],
    $_SESSION['contact_form_submission_failed'],
    $_SESSION['contact_form_error_messages'],
    $_SESSION['contact_form_field_errors'],
    $_SESSION['contact_form_previous_values']
);

if (empty($_SESSION['contact_form_csrf_token'])) {
    $_SESSION['contact_form_csrf_token'] = bin2hex(random_bytes(32));
}

$escapedPreviousName = htmlspecialchars($previousFormValues['name'] ?? '', ENT_QUOTES, 'UTF-8');
$escapedPreviousEmail = htmlspecialchars($previousFormValues['email'] ?? '', ENT_QUOTES, 'UTF-8');
$escapedPreviousSubject = htmlspecialchars($previousFormValues['subject'] ?? '', ENT_QUOTES, 'UTF-8');
$escapedPreviousMessage = htmlspecialchars($previousFormValues['message'] ?? '', ENT_QUOTES, 'UTF-8');

$nameErrorMessage = $fieldErrorMessagesByField['name'] ?? '';
$emailErrorMessage = $fieldErrorMessagesByField['email'] ?? '';
$messageErrorMessage = $fieldErrorMessagesByField['message'] ?? '';
$formAriaDescribedBy = !empty($formErrorMessages)
    ? 'form-required-note form-error-summary'
    : 'form-required-note';
?>

<section class="contact-hero">
  <div class="container text-center page-hero-z">
    <span class="page-hero-emoji" aria-hidden="true">✉️🐾</span>
    <h1 class="page-hero-h1">Say Hello!</h1>
    <p class="page-hero-tagline">
      We&rsquo;d love to hear from fellow small dog lovers &mdash; send us a note!
    </p>
  </div>
</section>

<section class="wyp-section wyp-section-alt">
  <div class="container">
    <div class="row g-5 justify-content-center">
      <div class="col-lg-7">

        <?php if ($didSubmissionSucceed): ?>
        <div class="wyp-alert wyp-alert-success mb-4" role="alert" aria-live="assertive">
          <?php if ($wasConfirmationEmailSent === true): ?>
          <strong>Thanks &mdash; we received your message. A confirmation email has been sent.</strong>
          <?php else: ?>
          <strong>Thanks &mdash; we received your message. Email confirmation could not be delivered.</strong>
          <?php endif; ?>
        </div>
        <?php elseif ($didSubmissionFail): ?>
        <div class="wyp-alert wyp-alert-error mb-4" role="alert" aria-live="assertive">
          <strong>Something went wrong.</strong> Please try again, or email us directly at
          <a href="mailto:admin@wipeyourpaws.net">admin@wipeyourpaws.net</a>.
        </div>
        <?php elseif (!empty($formErrorMessages)): ?>
        <div class="wyp-alert wyp-alert-error mb-4" id="form-error-summary" role="alert" aria-live="assertive" tabindex="-1">
          <strong>Please correct the following errors:</strong>
          <ul class="mb-0 mt-1">
            <?php foreach ($formErrorMessages as $validationMessage): ?>
              <?php
                $safeValidationMessage = htmlspecialchars((string) $validationMessage, ENT_QUOTES, 'UTF-8');
                $fieldAnchorTarget = '';
                if ($validationMessage === $nameErrorMessage) {
                    $fieldAnchorTarget = '#contact-name';
                } elseif ($validationMessage === $emailErrorMessage) {
                    $fieldAnchorTarget = '#contact-email';
                } elseif ($validationMessage === $messageErrorMessage) {
                    $fieldAnchorTarget = '#contact-message';
                }
              ?>
            <li>
              <?php if ($fieldAnchorTarget !== ''): ?>
              <a href="<?= $fieldAnchorTarget ?>"><?= $safeValidationMessage ?></a>
              <?php else: ?>
              <?= $safeValidationMessage ?>
              <?php endif; ?>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <div class="wyp-form">
          <h2 class="section-title mb-1">Send Us a Message</h2>

          <p class="required-note" id="form-required-note">
            Fields marked with
            <span class="required-asterisk" aria-hidden="true">*</span>
            <span class="sr-only">an asterisk</span>
            are required.
          </p>

          <form action="process_contact_form_submission.php" method="post" novalidate
                aria-describedby="<?= $formAriaDescribedBy ?>">

            <input type="hidden" name="contact_form_csrf_token"
                   value="<?= htmlspecialchars($_SESSION['contact_form_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="honeypot-wrap">
              <label for="contact-website" aria-hidden="true">Leave this field blank</label>
              <input type="text" id="contact-website" name="contact_website"
                     tabindex="-1" autocomplete="off" aria-hidden="true">
            </div>

            <div class="row g-3">
              <div class="col-sm-6">
                <label for="contact-name" class="form-label">
                  Your Name
                  <span class="required-asterisk" aria-hidden="true">*</span>
                  <span class="sr-only">(required)</span>
                </label>
                <input type="text" class="form-control" id="contact-name"
                       name="name" value="<?= $escapedPreviousName ?>"
                       placeholder="Jane Smith" autocomplete="name"
                       maxlength="120" required aria-required="true"
                       <?= $nameErrorMessage ? 'aria-invalid="true" aria-describedby="contact-name-error"' : '' ?>>
                <?php if ($nameErrorMessage): ?>
                <p class="form-error-text mt-2 mb-0" id="contact-name-error">
                  <?= htmlspecialchars($nameErrorMessage, ENT_QUOTES, 'UTF-8') ?>
                </p>
                <?php endif; ?>
              </div>

              <div class="col-sm-6">
                <label for="contact-email" class="form-label">
                  Email Address
                  <span class="required-asterisk" aria-hidden="true">*</span>
                  <span class="sr-only">(required)</span>
                </label>
                <input type="email" class="form-control" id="contact-email"
                       name="email" value="<?= $escapedPreviousEmail ?>"
                       placeholder="you@example.com" autocomplete="email"
                       maxlength="254" required aria-required="true"
                       <?= $emailErrorMessage ? 'aria-invalid="true" aria-describedby="contact-email-error"' : '' ?>>
                <?php if ($emailErrorMessage): ?>
                <p class="form-error-text mt-2 mb-0" id="contact-email-error">
                  <?= htmlspecialchars($emailErrorMessage, ENT_QUOTES, 'UTF-8') ?>
                </p>
                <?php endif; ?>
              </div>

              <div class="col-12">
                <label for="contact-subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="contact-subject"
                       name="subject" value="<?= $escapedPreviousSubject ?>"
                       placeholder="e.g. Dog-friendly trail tips in Monterey!"
                       autocomplete="off" maxlength="200">
              </div>

              <div class="col-12">
                <label for="contact-message" class="form-label">
                  Message
                  <span class="required-asterisk" aria-hidden="true">*</span>
                  <span class="sr-only">(required)</span>
                </label>
                <textarea class="form-control" id="contact-message"
                          name="message" rows="6"
                          placeholder="Tell us about your furry friends, ask a question, or just say hi!"
                          autocomplete="off"
                          required aria-required="true"
                          <?= $messageErrorMessage ? 'aria-invalid="true" aria-describedby="contact-message-error"' : '' ?>><?= $escapedPreviousMessage ?></textarea>
                <?php if ($messageErrorMessage): ?>
                <p class="form-error-text mt-2 mb-0" id="contact-message-error">
                  <?= htmlspecialchars($messageErrorMessage, ENT_QUOTES, 'UTF-8') ?>
                </p>
                <?php endif; ?>
              </div>

              <div class="col-12 mt-2">
                <button type="submit" class="btn-wyp btn-wyp-primary btn-submit">
                  <i class="bi bi-send-fill" aria-hidden="true"></i>
                  Send Message
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
          <h3>Get in Touch <span aria-hidden="true">🐾</span></h3>

          <address class="address-reset">
            <div class="contact-info-row">
              <div class="contact-info-icon" aria-hidden="true">✉️</div>
              <div>
                <strong class="contact-info-label">Email</strong>
                <a href="mailto:admin@wipeyourpaws.net" class="contact-info-link">
                  admin@wipeyourpaws.net
                </a>
              </div>
            </div>

            <div class="contact-info-row">
              <div class="contact-info-icon" aria-hidden="true">📍</div>
              <div>
                <strong class="contact-info-label">Location</strong>
                <span class="contact-info-value">Monterey Bay, California</span>
              </div>
            </div>

            <div class="contact-info-row">
              <div class="contact-info-icon" aria-hidden="true">🌐</div>
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
            the small dog community! <span aria-hidden="true">🐶</span>
          </p>
        </div>

        <div class="contact-paw-box">
          <div class="contact-paw-box__emoji" aria-hidden="true">🐕🐶</div>
          <p class="contact-paw-box__text">
            &ldquo;Chandra and Skipper are eagerly awaiting your message &mdash; and are
            ready to give you a virtual paw-shake in return!&rdquo;
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="wyp-section wyp-section-sm">
  <div class="container">
    <div class="text-center mb-4">
      <span class="section-eyebrow">Where to Find Us</span>
      <h2 class="section-title">Monterey Bay, California</h2>
      <hr class="section-divider">
    </div>
    <div class="map-wrapper">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d51729.2!2d-121.9177!3d36.6002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808de15c59e1e2fd%3A0xeabe3a9b9c9b1efc!2sMonterey%2C%20CA!5e0!3m2!1sen!2sus"
        width="100%" height="380" allowfullscreen=""
        loading="lazy" referrerpolicy="no-referrer-when-downgrade"
        title="Interactive map showing Monterey Bay, California">
      </iframe>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>




