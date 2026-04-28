<?php
/**
 * contact.php — Contact Us
 * wipeyourpaws.net · PHP 8.5 · Bootstrap 5.3.8 · WCAG 2.1 AA
 */

session_start();

$page_id = 'contact';
require_once 'includes/header.php';

$flash_sent       = $_SESSION['form_sent']   ?? false;
$flash_mail_error = $_SESSION['form_error']  ?? false;
$flash_errors     = $_SESSION['form_errors'] ?? [];
$flash_field_errors = $_SESSION['form_field_errors'] ?? [];
$old_values       = $_SESSION['form_values'] ?? [];

unset(
    $_SESSION['form_sent'],
    $_SESSION['form_error'],
    $_SESSION['form_errors'],
    $_SESSION['form_field_errors'],
    $_SESSION['form_values']
);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$old_name    = htmlspecialchars($old_values['name']    ?? '', ENT_QUOTES, 'UTF-8');
$old_email   = htmlspecialchars($old_values['email']   ?? '', ENT_QUOTES, 'UTF-8');
$old_subject = htmlspecialchars($old_values['subject'] ?? '', ENT_QUOTES, 'UTF-8');
$old_msg     = htmlspecialchars($old_values['message'] ?? '', ENT_QUOTES, 'UTF-8');

$name_error = $flash_field_errors['name'] ?? '';
$email_error = $flash_field_errors['email'] ?? '';
$message_error = $flash_field_errors['message'] ?? '';
$form_describedby = !empty($flash_errors)
    ? 'form-required-note form-error-summary'
    : 'form-required-note';
?>

<!--  PAGE HERO  -->
<section class="contact-hero">
  <div class="container text-center page-hero-z">
    <span class="page-hero-emoji" aria-hidden="true">✉️🐾</span>
    <h1 class="page-hero-h1">Say Hello!</h1>
    <p class="page-hero-tagline">
      We&rsquo;d love to hear from fellow small dog lovers &mdash; send us a note!
    </p>
  </div>
</section>

<!--  MAIN CONTACT SECTION  -->
<section class="page-section bg-cream">
  <div class="container">
    <div class="row g-5 justify-content-center">

      <!-- ── Contact Form ── -->
      <div class="col-lg-7">

        <?php if ($flash_sent): ?>
        <div class="wyp-alert wyp-alert-success mb-4" role="alert" aria-live="assertive">
          <strong><span aria-hidden="true">🎉</span> Message sent!</strong>
          Thank you so much &mdash; we&rsquo;ll get back to you soon!
          Chandra and Skipper send tail wags your way! <span aria-hidden="true">🐾</span>
        </div>
        <?php elseif ($flash_mail_error): ?>
        <div class="wyp-alert wyp-alert-error mb-4" role="alert" aria-live="assertive">
          <strong>Something went wrong.</strong> Please try again, or email us directly at
          <a href="mailto:admin@wipeyourpaws.net">admin@wipeyourpaws.net</a>.
        </div>
        <?php elseif (!empty($flash_errors)): ?>
        <div class="wyp-alert wyp-alert-error mb-4" id="form-error-summary" role="alert" aria-live="assertive" tabindex="-1">
          <strong>Please correct the following errors:</strong>
          <ul class="mb-0 mt-1">
            <?php if ($name_error): ?>
            <li><a href="#contact-name"><?= htmlspecialchars($name_error, ENT_QUOTES, 'UTF-8') ?></a></li>
            <?php endif; ?>
            <?php if ($email_error): ?>
            <li><a href="#contact-email"><?= htmlspecialchars($email_error, ENT_QUOTES, 'UTF-8') ?></a></li>
            <?php endif; ?>
            <?php if ($message_error): ?>
            <li><a href="#contact-message"><?= htmlspecialchars($message_error, ENT_QUOTES, 'UTF-8') ?></a></li>
            <?php endif; ?>
          </ul>
        </div>
        <?php endif; ?>

        <div class="wyp-form">
          <!-- 1.6rem = 25.6px Berkshire Swash — large text → orange-deep 4.07:1 passes 3:1 ✅ -->
          <h2 class="section-title mb-1">Send Us a Message</h2>

          <p class="required-note" id="form-required-note">
            Fields marked with
            <span class="required-asterisk" aria-hidden="true">*</span>
            <span class="sr-only">an asterisk</span>
            are required.
          </p>

          <form action="contact_submit.php" method="post" novalidate
                aria-describedby="<?= $form_describedby ?>">

            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <!-- Honeypot: offscreen container, AT-accessible wrapper, hidden input -->
            <div class="honeypot-wrap">
              <label for="website" aria-hidden="true">Leave this field blank</label>
              <input type="text" id="website" name="website"
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
                       name="name" value="<?= $old_name ?>"
                       placeholder="Jane Smith" autocomplete="name"
                       maxlength="120" required aria-required="true"
                       <?= $name_error ? 'aria-invalid="true" aria-describedby="contact-name-error"' : '' ?>>
                <?php if ($name_error): ?>
                <p class="form-error-text mt-2 mb-0" id="contact-name-error">
                  <?= htmlspecialchars($name_error, ENT_QUOTES, 'UTF-8') ?>
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
                       name="email" value="<?= $old_email ?>"
                       placeholder="you@example.com" autocomplete="email"
                       maxlength="254" required aria-required="true"
                       <?= $email_error ? 'aria-invalid="true" aria-describedby="contact-email-error"' : '' ?>>
                <?php if ($email_error): ?>
                <p class="form-error-text mt-2 mb-0" id="contact-email-error">
                  <?= htmlspecialchars($email_error, ENT_QUOTES, 'UTF-8') ?>
                </p>
                <?php endif; ?>
              </div>

              <div class="col-12">
                <label for="contact-subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="contact-subject"
                       name="subject" value="<?= $old_subject ?>"
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
                          <?= $message_error ? 'aria-invalid="true" aria-describedby="contact-message-error"' : '' ?>><?= $old_msg ?></textarea>
                <?php if ($message_error): ?>
                <p class="form-error-text mt-2 mb-0" id="contact-message-error">
                  <?= htmlspecialchars($message_error, ENT_QUOTES, 'UTF-8') ?>
                </p>
                <?php endif; ?>
              </div>

              <div class="col-12 mt-2">
                <button type="submit" class="btn-submit">
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

      <!-- ── Sidebar Info ── -->
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

<!--  MAP  -->
<section class="page-section-sm bg-warm-white">
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
