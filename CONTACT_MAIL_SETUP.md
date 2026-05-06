# Contact Mail Setup

This document describes the current mail behavior in the repository and the optional SMTP diagnostics configuration.

## Current Production Behavior

- Form endpoint: `contact.php`
- Mail transport used by form submission: native PHP `mail()`
- Trigger: POST to `contact.php` with `submit`
- Recipient source: `WYP_EMAIL` first, then `CONTACT_RECIPIENT_EMAIL` fallback.
- From source: `WYP_FORM_FROM_EMAIL` when valid; otherwise recipient address.
- Security checks before sending: CSRF validation, honeypot check (`beeName`), required field validation, email format validation, server-side reCAPTCHA verification, and header newline stripping.

## Important Clarification

- `config/contact_mail.php` defines SMTP and DKIM settings for diagnostics and future extension.
- `contact.php` does not currently load `config/contact_mail.php` or PHPMailer for production form submission.
- `logs/contact-mail.log` is defined in config but not actively written by `contact.php` in the current implementation.

## Environment Variables

### Required now (contact form availability)

```text
WYP_EMAIL=admin@example.com
GOOGLE_RECAPTCHA_SITE_KEY=your_recaptcha_site_key_here
GOOGLE_RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key_here
```

### Optional now

```text
WYP_FORM_FROM_EMAIL=noreply@example.com
CONTACT_RECIPIENT_EMAIL=admin@example.com
```

### Optional SMTP and DKIM variables (diagnostics/future SMTP integration)

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

### Development-only SMTP flags

```text
WYP_SMTP_DEBUG=0
WYP_SMTP_ALLOW_SELF_SIGNED=0
```

Keep both disabled in production unless actively troubleshooting in a controlled environment.

### Production-only recommendation for diagnostics endpoints

```text
WYP_DIAG_KEY=replace_with_temporary_random_key
```

## PHPMailer Status

- There is no `vendor/` directory in the current repository snapshot.
- SMTP test route `diagnostics/smtp_delivery_test.php` can probe SMTP connectivity without sending.
- To perform SMTP send tests, install PHPMailer:

```bash
composer require phpmailer/phpmailer
```

## Shared Hosting Notes (cPanel)

- `.cpanel.yml` copies `*` and explicitly copies `.htaccess`.
- Dotfiles like `.env` are not deployed by wildcard copy.
- Upload `.env` manually in cPanel File Manager.

## DNS and Deliverability Baseline

- Publish SPF for all outbound senders.
- Publish DKIM if SMTP relay supports signing.
- Publish DMARC at `_dmarc.your-domain`.

Example starter DMARC:

```text
v=DMARC1; p=none; rua=mailto:dmarc@example.com; fo=1; adkim=s; aspf=s
```

Raise policy only after reviewing aggregate report data.

