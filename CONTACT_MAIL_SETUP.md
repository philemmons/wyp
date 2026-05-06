# Contact Mail Setup

This file documents the **current** contact form mail behavior and the diagnostics-only SMTP tooling in this repository.

## Production Contact Form Path

- Endpoint: `contact.php`
- Trigger: `POST` with submit button named `submit`
- Transport: native PHP `mail()`
- Recipient resolution: `WYP_EMAIL`, fallback `CONTACT_RECIPIENT_EMAIL`
- From resolution: valid `WYP_FORM_FROM_EMAIL`, fallback resolved recipient
- Reply handling: visitor email is set as `Reply-To`
- Auto-reply to sender: not implemented

## Contact Form Security and Validation Flow

Before sending, `contact.php` enforces:

1. CSRF token validation using `hash_equals()`.
2. Honeypot validation (`beeName` must be empty).
3. Configuration validation (required env keys present and valid).
4. Field validation: email required/valid, subject required with max 150 chars, and message required with max 5000 chars.
5. Server-side reCAPTCHA verification.
6. Header value newline stripping to reduce header-injection risk.
7. HTML escaping before reflecting content in message body.

## Contact Form Field Model

- `contact-name`: optional combined name field (`maxlength=120`)
- `contact-em`: required email
- `contact-subj`: required subject (`maxlength=150`)
- `contact-ta`: required message (`maxlength=5000`)
- `beeName`: hidden honeypot field

Removed/absent in current implementation:

- No phone field.
- No split first/last name fields.

## Environment Variables

### Runtime requirement

Current bootstrap (`includes/init.php`) requires a root `.env` file and will throw if it is missing or unreadable.

### Required for contact form

```text
WYP_EMAIL=admin@example.com
CONTACT_RECIPIENT_EMAIL=admin@example.com
GOOGLE_RECAPTCHA_SITE_KEY=your_recaptcha_site_key_here
GOOGLE_RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key_here
```

### Optional for contact form

```text
CONTACT_RECIPIENT_EMAIL=admin@example.com
WYP_FORM_FROM_EMAIL=noreply@example.com
```

### Required for diagnostics access

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

These keys are currently not consumed by production contact form submission.

### Optional for DKIM selector lookups in diagnostics

```text
WYP_DKIM_SELECTOR=default
```

## Diagnostics vs Production Behavior

- `diagnostics/mail_delivery_diagnostic.php` and `diagnostics/smtp_delivery_test.php` are troubleshooting routes only.
- Production contact submission does **not** use PHPMailer today.
- `smtp_delivery_test.php` loads PHPMailer only if available under `vendor/`.

Install PHPMailer when you need SMTP send diagnostics:

```bash
composer require phpmailer/phpmailer
```

## Shared Hosting Notes (cPanel)

- `.cpanel.yml` copies `*` and explicitly copies `.htaccess`.
- Dotfiles like `.env` are not included by `*` and must be provisioned separately.
- Keep diagnostics routes temporary and remove them after troubleshooting.

## Deliverability Baseline

- Publish SPF for active sender domain.
- Publish DKIM for relay/host selector.
- Publish DMARC (`_dmarc.your-domain`) and tighten policy after monitoring.
- Keep sender domain alignment consistent between `From` and authenticated transport.
