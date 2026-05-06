# Wipe Your Paws

Multi-page PHP site for wipeyourpaws.net, built with shared PHP templates, Bootstrap 5.3.8, and a single canonical stylesheet.

## Overview

- Public pages: `index.php`, `intro.php`, `monterey.php`, `gallery.php`, `contact.php`
- Custom error pages: `403.php`, `404.php`
- Shared layout includes: `includes/header.php`, `includes/footer.php`
- Contact bootstrap: `includes/init.php` (loads `.env` and exposes `wyp_env()`)
- Canonical CSS: `css/style.css`
- JavaScript modules: `js/analytics.js`, `js/back_to_top_button.js`, `js/scroll_reveal_animations.js`, `js/contact_page.js`

## Current Project Structure

```text
/
|-- .cpanel.yml
|-- .env.example
|-- .htaccess
|-- 403.php
|-- 404.php
|-- contact.php
|-- gallery.php
|-- index.php
|-- intro.php
|-- monterey.php
|-- robots.txt
|-- sitemap.xml
|-- config/
|   `-- contact_mail.php
|-- css/
|   `-- style.css
|-- diagnostics/
|   |-- mail_delivery_diagnostic.php
|   |-- smtp_delivery_test.php
|   |-- phpmailer_production_mailer_factory.php
|   `-- DELIVERABILITY_CHECKLIST.md
|-- includes/
|   |-- footer.php
|   |-- header.php
|   `-- init.php
|-- js/
|   |-- analytics.js
|   |-- back_to_top_button.js
|   |-- contact_page.js
|   `-- scroll_reveal_animations.js
`-- images/
```

## Runtime Architecture

- `includes/header.php` starts output buffering, ensures session start, renders `<head>`, navbar, skip link, and opens `<main id="main-content">`.
- `includes/footer.php` closes `</main>`, renders footer, and loads JS bundles with cache-busting `filemtime()` query strings.
- `contact.php` calls `includes/init.php` first so environment variables are available before form processing.
- `includes/init.php` treats `.env` as mandatory and throws `RuntimeException` when missing or unreadable.

## Contact Form Behavior

`contact.php` currently handles both display and submission.

- `contact-name` is optional and acts as a single combined name field.
- `contact-em`, `contact-subj`, and `contact-ta` are required.
- `beeName` is a hidden honeypot and must remain empty.
- CSRF token is stored in `$_SESSION['csrf_token']` and verified with `hash_equals()`.
- Email uses `FILTER_VALIDATE_EMAIL`; subject max length is `150`; message max length is `5000`.
- reCAPTCHA response is required and verified server-side at `https://www.google.com/recaptcha/api/siteverify`.
- Frontend script `js/contact_page.js` applies Bootstrap validation, blocks submit without reCAPTCHA token, and focuses the first invalid control.
- Submission currently sends via native PHP `mail()` with visitor email in `Reply-To`.
- If configuration values are missing or invalid, submit is disabled and the form shows a setup message.

## Environment Configuration

Copy `.env.example` to `.env` in the project root (same level as `contact.php`).

### Required for contact form

```text
WYP_EMAIL=admin@example.com
GOOGLE_RECAPTCHA_SITE_KEY=your_recaptcha_site_key_here
GOOGLE_RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key_here
```

### Optional for contact form

```text
WYP_FORM_FROM_EMAIL=noreply@example.com
CONTACT_RECIPIENT_EMAIL=admin@example.com
```

### Optional for diagnostics and SMTP experiments

```text
WYP_SMTP_ENABLED=0
WYP_SMTP_HOST=smtp.example.com
WYP_SMTP_PORT=587
WYP_SMTP_ENCRYPTION=tls
WYP_SMTP_AUTH=1
WYP_SMTP_USERNAME=noreply@example.com
WYP_SMTP_PASSWORD=replace_with_smtp_password
WYP_SMTP_TIMEOUT=15
WYP_DKIM_ENABLED=0
WYP_DKIM_DOMAIN=example.com
WYP_DKIM_SELECTOR=default
WYP_DKIM_PRIVATE_KEY_PATH=/absolute/path/to/private.pem
WYP_DKIM_IDENTITY=noreply@example.com
WYP_DKIM_PASSPHRASE=
```

### Development-only values

Use only in local or controlled troubleshooting environments.

```text
WYP_SMTP_DEBUG=0
WYP_SMTP_ALLOW_SELF_SIGNED=0
```

Do not enable verbose SMTP debug output or self-signed TLS bypass in production.

### Production-only recommendation

```text
WYP_DIAG_KEY=replace_with_temporary_random_key
```

`WYP_DIAG_KEY` is read directly with `getenv()` in diagnostics scripts and is best set as a server environment variable in cPanel, not committed to `.env`.

## Security Posture

- `.htaccess` blocks `.env` access.
- `.htaccess` sets `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, and `Referrer-Policy: strict-origin-when-cross-origin`.
- Contact form defenses include CSRF validation, honeypot, required-field validation, header newline stripping, output escaping, and reCAPTCHA verification.
- Deployment and canonical URLs assume HTTPS (`https://wipeyourpaws.net`).
- No CSP header is currently configured in `.htaccess`.
- No nonce-based CSP flow is currently implemented.

## Accessibility Baseline (WCAG 2.1 AA)

- Skip link to `#main-content`
- Keyboard-visible focus styling via `:focus-visible`
- Form labels for all inputs and explicit required-field text (no required asterisk dependence)
- Error feedback and invalid states using Bootstrap + ARIA (`aria-invalid`, `role="alert"`, `aria-live`)
- Reduced motion handling in CSS (`prefers-reduced-motion`) and JS scrolling behavior
- Semantic landmarks (`<nav>`, `<main>`, `<footer>`, `<address>`)

## Asset and Frontend Conventions

- Keep shared styles in `css/style.css`.
- Keep page behavior in dedicated files under `js/`.
- Use cache-busting query strings from `filemtime()` in include templates.
- Keep Bootstrap classes plus project classes (`wyp-*`) consistent across pages.

## Deployment Workflow (cPanel/shared hosting)

`.cpanel.yml` currently deploys with:

- `cp -R * $DEPLOYPATH`
- `cp .htaccess $DEPLOYPATH/.htaccess`

Important implications:

- Dotfiles other than explicitly copied `.htaccess` are not included by `*`.
- Upload `.env` manually in cPanel File Manager after first deployment or environment changes.
- Confirm file permissions allow Apache/PHP to read `.env`.

## Local Development

1. Use Apache/PHP from the project root.
2. Create `.env` from `.env.example`.
3. Open `/contact.php` and verify reCAPTCHA keys are configured.
4. Optional lint checks:

```bash
php -l contact.php
php -l includes/init.php
php -l includes/header.php
php -l includes/footer.php
```

## Diagnostics and Deliverability

- See `diagnostics/DELIVERABILITY_CHECKLIST.md` for operational checks.
- Remove diagnostics scripts from production after troubleshooting.

## Troubleshooting

- Contact form shows "temporarily unavailable": verify required environment values and valid email format in `WYP_EMAIL`.
- Runtime exception about `.env`: ensure `.env` exists and is readable at project root.
- reCAPTCHA fails: verify site/secret keys match the deployed domain and do not contain extra whitespace.
- SMTP diagnostics report `phpmailer_not_found`: install PHPMailer before SMTP send tests.

## Maintenance Notes

- Keep documentation aligned with current file names and routing rules in `.htaccess`.
- Update `sitemap.xml` `<lastmod>` when content changes.
- Review docs after any change to form fields, validation, deployment scripts, or security headers.
- See `DOCUMENTATION_AUDIT_SUMMARY.md` for the latest documentation audit record.

