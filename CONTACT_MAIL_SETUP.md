# Contact Mail Setup (PHPMailer + SMTP)

## Canonical endpoint naming

- Contact form page and submit target: `/contact.php`
- Legacy `/contact_submit.php` is redirected for compatibility.

## 1) Install PHPMailer

Use Composer in the project root:

```bash
composer require phpmailer/phpmailer
```

This creates `vendor/autoload.php`, which you can load from `contact.php` if you switch from native `mail()` to PHPMailer.

## 2) SMTP environment variables

Set these on your server (`.env` for local/shared-hosting or cPanel Environment Variables):

```text
WYP_SMTP_ENABLED=1
WYP_SMTP_HOST=smtp.yourprovider.com
WYP_SMTP_PORT=587
WYP_SMTP_ENCRYPTION=tls
WYP_SMTP_AUTH=1
WYP_SMTP_USERNAME=noreply@wipeyourpaws.net
WYP_SMTP_PASSWORD=your_app_password
WYP_SMTP_TIMEOUT=15
WYP_SMTP_DEBUG=0
```

Contact form + reCAPTCHA variables:

```text
WYP_EMAIL=admin@wipeyourpaws.net
WYP_FORM_FROM_EMAIL=noreply@wipeyourpaws.net
GOOGLE_RECAPTCHA_SITE_KEY=your_recaptcha_site_key_here
GOOGLE_RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key_here
```

Optional local test only:

```text
WYP_SMTP_ALLOW_SELF_SIGNED=1
```

## 3) Optional DKIM signing

```text
WYP_DKIM_ENABLED=1
WYP_DKIM_DOMAIN=wipeyourpaws.net
WYP_DKIM_SELECTOR=default
WYP_DKIM_PRIVATE_KEY_PATH=/absolute/path/to/dkim_private.pem
WYP_DKIM_IDENTITY=noreply@wipeyourpaws.net
WYP_DKIM_PASSPHRASE=
```

## 4) Delivery behavior

- Current `contact.php` sends an admin notification using native `mail()`.
- If you add PHPMailer SMTP delivery, keep admin notification as the primary send path.
- Consider a native `mail()` fallback only if your host supports it and you need resilience.

## 5) Logging

Log file:

```text
logs/contact-mail.log
```

Contains:

- validation failures
- SMTP handshake/debug details (when enabled)
- fallback events
- send result per channel (`admin_notification`, `user_autoreply`)

Sensitive secrets are redacted from logs.

## 6) DNS recommendations

- SPF: include your actual outbound SMTP sender(s).
- DKIM: publish selector TXT and sign outgoing mail.
- DMARC: publish at `_dmarc.wipeyourpaws.net` (start with `p=none` and reporting).

Example starter DMARC:

```text
v=DMARC1; p=none; rua=mailto:dmarc@wipeyourpaws.net; fo=1; adkim=s; aspf=s
```

After confirming alignment and reports, raise policy to `p=quarantine` or `p=reject`.

