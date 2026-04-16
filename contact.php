<?php
/**
 * contact.php — Contact Us
 * wipeyourpaws.net · PHP 8.5 · Bootstrap 5.3.8 · WCAG 2.1 AA rev.2
 *
 * WCAG / Bug fixes applied:
 *   W8  — Required-field asterisks paired with aria-hidden + sr-only text
 *   W9  — Required indicators are not colour-only (text label "required" added)
 *   W10 — "Fields marked * are required" note above the form
 *   W11 — aria-required="true" on all required fields
 *   W12 — autocomplete attributes on all form fields (WCAG 1.3.5 — AA)
 *   W14 — aria-hidden on Bootstrap icon in submit button
 *   B4  — PII no longer passed through URL; flash data uses PHP sessions
 *   R1  — CSRF token generated and validated
 *   R8  — HTML required attribute added to the name field
 */

session_start();

$page_id = 'contact';
require_once 'includes/header.php';

// ── Read flash data from session then immediately clear it ────────────
$flash_sent       = $_SESSION['form_sent']   ?? false;
$flash_mail_error = $_SESSION['form_error']  ?? false;
$flash_errors     = $_SESSION['form_errors'] ?? [];
$old_values       = $_SESSION['form_values'] ?? [];

unset(
    $_SESSION['form_sent'],
    $_SESSION['form_error'],
    $_SESSION['form_errors'],
    $_SESSION['form_values']
);

// ── CSRF token: generate once per session ─────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── Pre-fill helpers (HTML-escaped) ──────────────────────────────────
$old_name    = htmlspecialchars($old_values['name']    ?? '', ENT_QUOTES, 'UTF-8');
$old_email   = htmlspecialchars($old_values['email']   ?? '', ENT_QUOTES, 'UTF-8');
$old_subject = htmlspecialchars($old_values['subject'] ?? '', ENT_QUOTES, 'UTF-8');
$old_msg     = htmlspecialchars($old_values['message'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<!-- ░░ PAGE HERO ░░ -->
<section class="contact-hero">
  <div class="container text-center position-relative" style="z-index:2;">
    <span style="font-size:3.5rem; display:block; margin-bottom:0.5rem;" aria-hidden="true">✉️🐾</span>
    <h1 style="font-family:var(--font-display); color:#fff; font-size:clamp(2rem,5vw,3.5rem); text-shadow:3px 4px 12px rgba(0,0,0,0.25); margin-bottom:0.5rem;">
      Say Hello!
    </h1>
    <!-- W13 FIX: was color:var(--yellow-light) ~3:1 — now white ~5.5:1 -->
    <p style="font-family:var(--font-accent); font-style:italic; color:rgba(255,255,255,0.95); font-size:1.1rem; max-width:500px; margin:0 auto;">
      We&rsquo;d love to hear from fellow small dog lovers &mdash; send us a note!
    </p>
  </div>
</section>

<!-- ░░ MAIN CONTACT SECTION ░░ -->
<section class="page-section" style="background:var(--cream);">
  <div class="container">
    <div class="row g-5 justify-content-center">

      <!-- ── Contact Form ── -->
      <div class="col-lg-7">

        <!--
          Flash messages: role="alert" ensures screen readers announce them
          immediately without needing focus (WCAG 4.1.3 Status Messages)
        -->
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
        <div class="wyp-alert wyp-alert-error mb-4" role="alert" aria-live="assertive">
          <strong>Please correct the following errors:</strong>
          <ul class="mb-0 mt-1">
            <?php foreach ($flash_errors as $err): ?>
            <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <div class="wyp-form">
          <h2 style="font-family:var(--font-display); color:var(--orange-deep); font-size:1.6rem; margin-bottom:0.25rem;">
            Send Us a Message
          </h2>

          <!-- W10: Required-field note BEFORE the form fields (WCAG 3.3.2) -->
          <p class="required-note" id="form-required-note">
            Fields marked with
            <span class="required-asterisk" aria-hidden="true">*</span>
            <span class="sr-only">an asterisk</span>
            are required.
          </p>

          <form action="contact_submit.php" method="post" novalidate
                aria-describedby="form-required-note">

            <!-- R1: CSRF token (hidden, session-bound) -->
            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <!-- W15 / B1 honeypot: CSS offscreen + tabindex="-1"; no aria-hidden
                 on container so AT can still read; input itself has aria-hidden -->
            <div style="position:absolute; left:-9999px; width:1px; height:1px; overflow:hidden;">
              <label for="website" aria-hidden="true">Leave this field blank</label>
              <input type="text" id="website" name="website"
                     tabindex="-1" autocomplete="off" aria-hidden="true">
            </div>

            <div class="row g-3">

              <!-- Name — R8: HTML required added; W11: aria-required -->
              <div class="col-sm-6">
                <label for="contact-name" class="form-label">
                  Your Name
                  <!-- W8/W9: aria-hidden on visible *, sr-only announces it to AT -->
                  <span class="required-asterisk" aria-hidden="true">*</span>
                  <span class="sr-only">(required)</span>
                </label>
                <input type="text"
                       class="form-control"
                       id="contact-name"
                       name="name"
                       value="<?= $old_name ?>"
                       placeholder="Jane Smith"
                       autocomplete="name"
                       maxlength="120"
                       required
                       aria-required="true">
              </div>

              <!-- Email — W11/W12 -->
              <div class="col-sm-6">
                <label for="contact-email" class="form-label">
                  Email Address
                  <span class="required-asterisk" aria-hidden="true">*</span>
                  <span class="sr-only">(required)</span>
                </label>
                <input type="email"
                       class="form-control"
                       id="contact-email"
                       name="email"
                       value="<?= $old_email ?>"
                       placeholder="you@example.com"
                       autocomplete="email"
                       maxlength="254"
                       required
                       aria-required="true">
              </div>

              <!-- Subject — W12 -->
              <div class="col-12">
                <label for="contact-subject" class="form-label">Subject</label>
                <input type="text"
                       class="form-control"
                       id="contact-subject"
                       name="subject"
                       value="<?= $old_subject ?>"
                       placeholder="e.g. Dog-friendly trail tips in Monterey!"
                       autocomplete="off"
                       maxlength="200">
              </div>

              <!-- Message — W11/W12 -->
              <div class="col-12">
                <label for="contact-message" class="form-label">
                  Message
                  <span class="required-asterisk" aria-hidden="true">*</span>
                  <span class="sr-only">(required)</span>
                </label>
                <textarea class="form-control"
                          id="contact-message"
                          name="message"
                          rows="6"
                          placeholder="Tell us about your furry friends, ask a question, or just say hi! We love hearing from fellow small dog enthusiasts."
                          autocomplete="off"
                          required
                          aria-required="true"><?= $old_msg ?></textarea>
              </div>

              <!-- Submit -->
              <div class="col-12 mt-2">
                <button type="submit" class="btn-submit">
                  <!-- W14: decorative icon aria-hidden; button text is the accessible label -->
                  <i class="bi bi-send-fill" aria-hidden="true"></i>
                  Send Message
                </button>
                <p style="font-size:0.75rem; color:var(--text-light); text-align:center; margin-top:0.75rem;">
                  <i class="bi bi-lock-fill me-1" aria-hidden="true"></i>
                  Your information will only be used to respond to your message.
                </p>
              </div>

            </div><!-- /.row -->
          </form>
        </div><!-- /.wyp-form -->

      </div><!-- /.col-lg-7 -->

      <!-- ── Sidebar Info ── -->
      <div class="col-lg-5">

        <div class="contact-info-box mb-4">
          <h3 style="font-family:var(--font-display); color:var(--orange-deep); font-size:1.1rem; margin-bottom:1rem;">
            Get in Touch <span aria-hidden="true">🐾</span>
          </h3>

          <address style="font-style:normal;">

            <div class="contact-info-row">
              <div class="contact-info-icon" aria-hidden="true">✉️</div>
              <div>
                <strong style="font-size:0.82rem; display:block; color:var(--text-mid); text-transform:uppercase; letter-spacing:0.06em;">
                  Email
                </strong>
                <a href="mailto:admin@wipeyourpaws.net"
                   style="color:var(--orange-deep); font-weight:700; text-decoration:none;">
                  admin@wipeyourpaws.net
                </a>
              </div>
            </div>

            <div class="contact-info-row">
              <div class="contact-info-icon" aria-hidden="true">📍</div>
              <div>
                <strong style="font-size:0.82rem; display:block; color:var(--text-mid); text-transform:uppercase; letter-spacing:0.06em;">
                  Location
                </strong>
                <span style="color:var(--orange-deep); font-weight:700;">
                  Monterey Bay, California
                </span>
              </div>
            </div>

            <div class="contact-info-row">
              <div class="contact-info-icon" aria-hidden="true">🌐</div>
              <div>
                <strong style="font-size:0.82rem; display:block; color:var(--text-mid); text-transform:uppercase; letter-spacing:0.06em;">
                  Website
                </strong>
                <span style="color:var(--orange-deep); font-weight:700;">
                  wipeyourpaws.net
                </span>
              </div>
            </div>

          </address>

          <hr style="border-color:var(--orange-pale); margin:1rem 0;">

          <p style="font-size:0.85rem; color:var(--text-mid); line-height:1.65; margin:0;">
            Whether you have questions about small dog care, want to share your own
            pup&rsquo;s story, or just want to say hi &mdash; we love hearing from
            the small dog community! <span aria-hidden="true">🐶</span>
          </p>
        </div>

        <!-- Chandra & Skipper note -->
        <div style="background:linear-gradient(135deg, var(--orange-pale), var(--yellow-soft));
                    border-radius:var(--radius-lg); padding:1.5rem;
                    border:2px solid var(--yellow-bright); text-align:center;">
          <div style="font-size:3rem; margin-bottom:0.5rem;" aria-hidden="true">🐕🐶</div>
          <p style="font-family:var(--font-accent); font-style:italic; font-size:1rem;
                    color:var(--orange-deep); line-height:1.65; margin:0;">
            &ldquo;Chandra and Skipper are eagerly awaiting your message &mdash; and are
            ready to give you a virtual paw-shake in return!&rdquo;
          </p>
        </div>

      </div><!-- /.col-lg-5 -->

    </div><!-- /.row -->
  </div><!-- /.container -->
</section>

<!-- ░░ MAP ░░ -->
<section class="page-section-sm" style="background:var(--warm-white);">
  <div class="container">
    <div class="text-center mb-4">
      <span class="section-eyebrow">Where to Find Us</span>
      <h2 class="section-title">Monterey Bay, California</h2>
      <hr class="section-divider">
    </div>
    <div class="map-wrapper">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d51729.2!2d-121.9177!3d36.6002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808de15c59e1e2fd%3A0xeabe3a9b9c9b1efc!2sMonterey%2C%20CA!5e0!3m2!1sen!2sus"
        width="100%" height="380" style="border:0; display:block;"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        title="Interactive map showing Monterey Bay, California">
      </iframe>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
