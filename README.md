# Wipe Your Paws

Multi-page PHP site for `wipeyourpaws.net`, built with shared PHP includes, Bootstrap 5.3.8, modular JavaScript, and a single canonical stylesheet.

## Overview

- Public pages: `index.php`, `intro.php`, `monterey.php`, `gallery.php`, `contact.php`
- Custom error pages: `403.php`, `404.php`
- Shared includes: `includes/header.php`, `includes/footer.php`, `includes/init.php`
- Canonical CSS: `css/style.css`
- JavaScript modules: `js/analytics.js`, `js/back_to_top_control.js`, `js/scroll_reveal_controller.js`, `js/contact_form_experience.js`, `js/gallery_lightbox_controller.js`
- Operational diagnostics: `diagnostics/mail_delivery_diagnostic.php`, `diagnostics/smtp_delivery_test.php`

## Current Project Structure

```text
/
|-- .cpanel.yml
|-- .env
|-- .env.example
|-- .htaccess
|-- 403.php
|-- 404.php
|-- contact.php
|-- CONTACT_MAIL_SETUP.md
|-- gallery.php
|-- index.php
|-- intro.php
|-- monterey.php
|-- README.md
|-- robots.txt
|-- sitemap.xml
|-- WCAG_2.1_READABILITY_CHECKLIST.md
|-- css/
|   |-- index.php
|   `-- style.css
|-- diagnostics/
|   |-- DELIVERABILITY_CHECKLIST.md
|   |-- mail_delivery_diagnostic.php
|   `-- smtp_delivery_test.php
|-- images/
|-- includes/
|   |-- footer.php
|   |-- header.php
|   |-- index.php
|   `-- init.php
`-- js/
    |-- analytics.js
    |-- back_to_top_control.js
    |-- contact_form_experience.js
    |-- gallery_lightbox_controller.js
    |-- index.php
    `-- scroll_reveal_controller.js
```

## Runtime Architecture

- Every page sets `$activePageKey` and includes `includes/header.php` and `includes/footer.php`.
- `includes/header.php` starts output buffering, starts session if needed, renders metadata/nav, and opens `<main id="main-content">`.
- `includes/footer.php` closes `</main>`, renders footer, and loads Bootstrap + site JavaScript.
- `contact.php` includes `includes/init.php` before form logic.
- `includes/init.php` hard-loads `/.env` and throws `RuntimeException` if unreadable.
- `wyp_env()` is the shared environment accessor used across app and diagnostics scripts.

## Contact Form Behavior

`contact.php` handles both form rendering and submission.

### Fields and current names

- `contact-name`: optional combined name field (`maxlength=120`)
- `contact-em`: required email
- `contact-subj`: required subject (`maxlength=150`)
- `contact-ta`: required message (`maxlength=5000`)
- `beeName`: hidden honeypot field (must remain empty)

### Validation flow (server-side)

1. Checks CSRF token via `hash_equals()`.
2. Checks honeypot (`beeName`) is empty.
3. Checks runtime configuration values are valid.
4. Validates required fields and formats (`FILTER_VALIDATE_EMAIL`, length limits).
5. Verifies Google reCAPTCHA response server-side.
6. Sends message with native PHP `mail()` only when validation passes.

### Validation flow (client-side)

- `js/contact_form_experience.js` applies Bootstrap validation (`needs-validation`/`was-validated`).
- Prevents submit when HTML validity fails.
- Loads reCAPTCHA script dynamically and blocks submit until token is present.
- Focuses first invalid control on failure and focuses summary message after server response.
- Reset button uses `window.confirm()` before clearing inputs and validation state.

### Email behavior

- Recipient: `WYP_EMAIL`, fallback `CONTACT_RECIPIENT_EMAIL`
- From: `WYP_FORM_FROM_EMAIL` when valid, otherwise resolved recipient address
- Reply path: visitor email is placed in `Reply-To`
- Auto-reply: not implemented
- Transport: native `mail()` (PHPMailer is not used by production contact submission)

## Environment Configuration

### Current implementation note

`includes/init.php` currently requires a `.env` file at repository root. Even when server-level environment variables exist, missing `.env` causes a hard runtime error in current code.

### Required for contact form runtime

At least one recipient variable must resolve to a valid email. `WYP_EMAIL` is checked first, then `CONTACT_RECIPIENT_EMAIL`.

```text
WYP_EMAIL=admin@example.com
CONTACT_RECIPIENT_EMAIL=admin@example.com
GOOGLE_RECAPTCHA_SITE_KEY=your_recaptcha_site_key_here
GOOGLE_RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key_here
```

### Optional for contact form runtime

```text
CONTACT_RECIPIENT_EMAIL=admin@example.com
WYP_FORM_FROM_EMAIL=noreply@example.com
```

### Required for diagnostics endpoint access

```text
WYP_DIAG_KEY=replace_with_temporary_random_key
```

### Optional for SMTP diagnostics

```text
WYP_SMTP_HOST=smtp.example.net
WYP_SMTP_PORT=587
WYP_SMTP_ENCRYPTION=tls
WYP_SMTP_AUTH=1
WYP_SMTP_USERNAME=noreply@example.net
WYP_SMTP_PASSWORD=replace_with_smtp_password
WYP_SMTP_TIMEOUT=15
```

Template-only keys currently present in `.env.example`:

```text
WYP_SMTP_ENABLED=1
WYP_SMTP_DEBUG=0
```

These two keys are not consumed by the current contact form send path.

### Optional for mail diagnostics domain checks

```text
WYP_DKIM_SELECTOR=default
```

### Secret handling guidance

- Never commit `.env` or secrets (`.gitignore` already excludes them).
- Keep `.env` file permissions least-privilege on shared hosting.
- Remove temporary diagnostic secrets (`WYP_DIAG_KEY`) when troubleshooting ends.

## Security Posture

- `.htaccess` blocks direct access to `.env` files.
- `.htaccess` sets `X-Content-Type-Options`, `X-Frame-Options`, and `Referrer-Policy`.
- Contact form uses CSRF token validation, honeypot trap, input validation, output escaping, and reCAPTCHA verification.
- Header values for outbound mail are newline-sanitized.
- Canonical URLs assume HTTPS (`https://wipeyourpaws.net`).
- No CSP header is currently configured.
- No nonce-based CSP workflow is currently implemented.
- No explicit server-side rate limiter exists in this repo; spam mitigation relies on honeypot + reCAPTCHA + validation.

## Accessibility Baseline (WCAG 2.1 AA)

- Skip link to `#main-content`
- Semantic landmarks: `<nav>`, `<main>`, `<footer>`, `<address>`
- `aria-current="page"` on active primary navigation item
- Keyboard-visible focus styles (`:focus-visible`) in `css/style.css`
- Required form fields communicated with explicit text (not asterisk-only)
- Field-level error feedback with `invalid-feedback`, `aria-invalid`, `role`/`aria-live`
- Reduced-motion behavior in CSS and in JS motion handlers

See `WCAG_2.1_READABILITY_CHECKLIST.md` for detailed manual regression checks.

## Frontend and Assets

- Shared design tokens and component styles live in `css/style.css`.
- JavaScript responsibilities are split by concern:
  - `analytics.js`: Google Analytics initialization
  - `back_to_top_control.js`: back-to-top visibility + behavior
  - `scroll_reveal_controller.js`: IntersectionObserver reveal behavior
  - `contact_form_experience.js`: contact form UX and reCAPTCHA flow
  - `gallery_lightbox_controller.js`: gallery modal and carousel keyboard behavior
- CSS/JS assets are loaded with `filemtime()` cache-busting query strings.

## Deployment Workflow (cPanel/shared hosting)

`.cpanel.yml` deploy tasks:

- `cp -R * $DEPLOYPATH`
- `cp .htaccess $DEPLOYPATH/.htaccess`

Implications:

- Dotfiles are not copied by `*` (except explicit `.htaccess` copy).
- You must provision `.env` separately in deployment target for current runtime behavior.
- Confirm deployed web root has correct file permissions for runtime reads.

## Local Development

1. Serve project with Apache + PHP from repository root.
2. Copy `.env.example` to `.env` and fill placeholders.
3. Open `/contact.php` and verify reCAPTCHA keys are set.
4. Optional lint checks:

```bash
php -l contact.php
php -l includes/init.php
php -l includes/header.php
php -l includes/footer.php
php -l diagnostics/mail_delivery_diagnostic.php
php -l diagnostics/smtp_delivery_test.php
```

## Diagnostics and Deliverability

- Primary checklist: `diagnostics/DELIVERABILITY_CHECKLIST.md`
- `mail_delivery_diagnostic.php` validates DNS/mail() assumptions and optional mail send test.
- `smtp_delivery_test.php` validates SMTP connectivity/auth using PHPMailer when available.
- Remove diagnostics scripts from production after troubleshooting.

## Troubleshooting

- Contact form shows "temporarily unavailable": validate `WYP_EMAIL` (or fallback recipient), reCAPTCHA keys, and email formats.
- Runtime exception about `.env`: ensure `.env` exists and is readable at repo root.
- reCAPTCHA fails: confirm key/domain match and no extra whitespace.
- `smtp_delivery_test.php` returns `phpmailer_not_found`: install `phpmailer/phpmailer` under `vendor/`.
- Form sends but no inbox delivery: run diagnostics checklist and inspect host mail logs.

## Maintenance Notes

- Keep docs aligned with code after any field, route, header, deployment, or env changes.
- Update `sitemap.xml` `<lastmod>` values when page content changes.
- Re-run manual WCAG checks when updating typography, color tokens, form markup, or interaction scripts.
- Review `DOCUMENTATION_AUDIT_SUMMARY.md` after documentation updates.
