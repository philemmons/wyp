<?php
/**
 * contact.php — Contact Us
 * wipeyourpaws.net · PHP 8.5 · Bootstrap 5.3.8 · WCAG 2.2 AAA
 */

$page_id = 'contact';
require_once 'includes/header.php';

if (isset($_GET['edit'])) {
  unset($_SESSION['form_review']);
}

$flash_sent       = $_SESSION['form_sent']   ?? false;
$flash_mail_error = $_SESSION['form_error']  ?? false;
$flash_errors     = $_SESSION['form_errors'] ?? [];
$review_data      = $_SESSION['form_review'] ?? null;
$old_values       = $_SESSION['form_values'] ?? [];

unset(
  $_SESSION['form_sent'],
  $_SESSION['form_error'],
  $_SESSION['form_errors']
);

if ($flash_sent) {
  unset($_SESSION['form_review'], $_SESSION['form_values']);
  $review_data = null;
}

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$show_review = is_array($review_data) && !$flash_sent;

$old_name    = htmlspecialchars($old_values['name']    ?? '', ENT_QUOTES, 'UTF-8');
$old_email   = htmlspecialchars($old_values['email']   ?? '', ENT_QUOTES, 'UTF-8');
$old_subject = htmlspecialchars($old_values['subject'] ?? '', ENT_QUOTES, 'UTF-8');
$old_msg     = htmlspecialchars($old_values['message'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<section class="contact-hero">
  <div class="container text-center page-hero-z">
    <span class="page-hero-emoji" aria-hidden="true">✉️🐾</span>
    <h1 class="page-hero-h1">Say Hello</h1>
    <p class="page-hero-tagline">
      Send us a note in two safe steps: review first, then confirm.
    </p>
  </div>
</section>

<section class="page-section-sm bg-cream">
  <div class="container">
    <details class="simple-summary">
      <summary>Simple summary of this page</summary>
      <p>
        Fill out the form, review your message, and then confirm before it is sent.
      </p>
    </details>
  </div>
</section>

<section class="page-section bg-cream">
  <div class="container">
    <div class="row g-5 justify-content-center">

      <div class="col-lg-7">

        <?php if ($flash_sent): ?>
        <div class="wyp-alert wyp-alert-success mb-4" role="status" aria-live="polite">
          <strong>Message sent.</strong> Thank you — we will reply soon.
        </div>
        <?php elseif ($flash_mail_error): ?>
        <div class="wyp-alert wyp-alert-error mb-4" role="alert">
          <strong>We could not send your message.</strong>
          Please try again or email us directly at
          <a href="mailto:admin@wipeyourpaws.net">admin@wipeyourpaws.net</a>.
        </div>
        <?php elseif (!empty($flash_errors)): ?>
        <div class="wyp-alert wyp-alert-error mb-4" role="alert">
          <strong>Please correct the following:</strong>
          <ul class="mb-0 mt-1">
            <?php foreach ($flash_errors as $err): ?>
            <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <?php if ($show_review): ?>
          <div class="wyp-form">
            <h2 class="section-title mb-2">Review Your Message</h2>
            <p class="required-note mb-3">
              Step 2 of 2: confirm this information before sending.
            </p>

            <dl class="review-list">
              <dt>Name</dt>
              <dd><?= htmlspecialchars($review_data['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd>
              <dt>Email Address</dt>
              <dd><?= htmlspecialchars($review_data['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd>
              <dt>Subject</dt>
              <dd><?= htmlspecialchars(($review_data['subject'] ?? '') !== '' ? $review_data['subject'] : 'No subject entered', ENT_QUOTES, 'UTF-8') ?></dd>
              <dt>Message</dt>
              <dd class="review-message"><?= nl2br(htmlspecialchars($review_data['message'] ?? '', ENT_QUOTES, 'UTF-8')) ?></dd>
            </dl>

            <div class="d-flex flex-wrap gap-3 mt-3">
              <form action="contact_submit.php" method="post">
                <input type="hidden" name="csrf_token"
                      value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="confirm_send" value="1">
                <button type="submit" class="btn-submit">Confirm and Send Message</button>
              </form>
              <a href="contact.php?edit=1" class="btn-wyp btn-wyp-outline-light">
                Edit Message Before Sending
              </a>
            </div>
          </div>
        <?php else: ?>
          <div class="wyp-form">
            <h2 class="section-title mb-1">Send Us a Message</h2>

            <p class="required-note" id="form-required-note">
              Fields marked with
              <span class="required-asterisk" aria-hidden="true">*</span>
              <span class="sr-only">an asterisk</span>
              are required.
            </p>

            <details class="form-help-box" id="contact-help">
              <summary>Need help writing your message?</summary>
              <p>
                Share the topic, where you are located, and what answer you need.
                We usually reply with practical, simple tips.
              </p>
            </details>

            <form action="contact_submit.php" method="post" novalidate
                  aria-describedby="form-required-note contact-help">
              <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="review_step" value="1">

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
                        maxlength="120" required aria-required="true">
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
                        inputmode="email"
                        maxlength="254" required aria-required="true">
                </div>

                <div class="col-12">
                  <label for="contact-subject" class="form-label">Subject</label>
                  <p class="form-help-text" id="subject-help">
                    Example: Dog-friendly trail question
                  </p>
                  <input type="text" class="form-control" id="contact-subject"
                        name="subject" value="<?= $old_subject ?>"
                        placeholder="Example: Dog-friendly trail question"
                        autocomplete="off" maxlength="200"
                        aria-describedby="subject-help">
                </div>

                <div class="col-12">
                  <label for="contact-message" class="form-label">
                    Message
                    <span class="required-asterisk" aria-hidden="true">*</span>
                    <span class="sr-only">(required)</span>
                  </label>
                  <p class="form-help-text" id="message-help">
                    Include enough detail so we can give a useful reply.
                  </p>
                  <textarea class="form-control" id="contact-message"
                            name="message" rows="6"
                            placeholder="Tell us about your dogs, ask a question, or say hello."
                            autocomplete="off"
                            required aria-required="true"
                            aria-describedby="message-help"><?= $old_msg ?></textarea>
                </div>

                <div class="col-12 mt-2">
                  <button type="submit" class="btn-submit">
                    Review Message Before Sending
                  </button>
                  <p class="contact-form-hint">
                    Your information is only used to respond to your message.
                  </p>
                </div>
              </div>
            </form>
          </div>
        <?php endif; ?>

      </div>

      <div class="col-lg-5">
        <aside class="contact-info-box mb-4" aria-label="Contact information">
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
                <span class="contact-info-value">Monterey Bay, <abbr title="California">CA</abbr></span>
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
            Ask a question, share your pup story, or say hello.
            We are always happy to hear from the small dog community.
          </p>
        </aside>

        <div class="contact-paw-box">
          <div class="contact-paw-box__emoji" aria-hidden="true">🐕🐶</div>
          <p class="contact-paw-box__text">
            “Chandra and Skipper are always ready for a virtual paw-shake.”
          </p>
        </div>
      </div>

    </div>
  </div>
</section>

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
