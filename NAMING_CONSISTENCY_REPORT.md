# Naming Consistency Report

This file records the naming conventions that are currently true in the repository.

## Confirmed Naming Conventions

- PHP variables and helper names use `camelCase`.
- File names use snake_case where a file was intentionally renamed (`back_to_top_button.js`, `scroll_reveal_animations.js`, `mail_delivery_diagnostic.php`).
- Page context key is `$activePageKey`.
- Shared navigation arrays in `includes/header.php` use intent-revealing names: `$primaryNavigationLinks`, `$pageTitleByKey`, `$pageDescriptionByKey`, `$pagePathByKey`, `$canonicalUrl`.

## Canonical Route and Script Names

- Contact page and submit target: `contact.php`
- Back-to-top script: `js/back_to_top_button.js`
- Reveal animation script: `js/scroll_reveal_animations.js`
- Mail diagnostics script: `diagnostics/mail_delivery_diagnostic.php`
- SMTP diagnostics script: `diagnostics/smtp_delivery_test.php`
- PHPMailer factory example: `diagnostics/phpmailer_production_mailer_factory.php`

## Legacy Compatibility Redirects

`.htaccess` keeps 301 redirects for legacy names:

- `contact_submit.php` to `contact.php`
- `js/backToTop.js` to `js/back_to_top_button.js`
- `js/app.js` to `js/scroll_reveal_animations.js`
- `diagnostics/mail_diagnostic.php` to `diagnostics/mail_delivery_diagnostic.php`
- `diagnostics/smtp_test.php` to `diagnostics/smtp_delivery_test.php`
- `diagnostics/PHPMailer_PRODUCTION_TEMPLATE.php` to `diagnostics/phpmailer_production_mailer_factory.php`

## Current Intentional Exceptions

- CSRF session key remains `$_SESSION['csrf_token']` in `contact.php`.
- Honeypot field name is `beeName`.
- Form field names remain hyphenated for frontend compatibility: `contact-name`, `contact-em`, `contact-subj`, `contact-ta`.

## Notes for Future Refactors

- If session keys or form field names are renamed, update `contact.php`, `js/contact_page.js`, and related docs in `README.md` and `CONTACT_MAIL_SETUP.md`.
- Keep route redirects in `.htaccess` during transition windows.
