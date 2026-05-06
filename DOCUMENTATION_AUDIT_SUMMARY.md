# Documentation Audit Summary

Audit date: 2026-05-05
Scope: all Markdown files in repository.

## Files Reviewed

- `README.md`
- `CONTACT_MAIL_SETUP.md`
- `NAMING_CONSISTENCY_REPORT.md`
- `WCAG_2.1_READABILITY_CHECKLIST.md`
- `diagnostics/DELIVERABILITY_CHECKLIST.md`

## Files Modified

- `README.md`
- `CONTACT_MAIL_SETUP.md`
- `NAMING_CONSISTENCY_REPORT.md`
- `WCAG_2.1_READABILITY_CHECKLIST.md`
- `diagnostics/DELIVERABILITY_CHECKLIST.md`
- `DOCUMENTATION_AUDIT_SUMMARY.md` (new)

## Outdated Items Corrected

- Removed assumptions that contact form currently uses PHPMailer or SMTP in production.
- Corrected contact form field model to a single optional name field (`contact-name`) with required email, subject, and message, and no phone field.
- Added accurate CSRF, honeypot, reCAPTCHA, and server-side validation flow details.
- Corrected environment-variable guidance to match code paths, including required contact variables, `CONTACT_RECIPIENT_EMAIL` fallback, optional SMTP/DKIM variables, and `WYP_DIAG_KEY` handling.
- Rewrote naming report to match current identifiers and routes.
- Aligned deployment notes with `.cpanel.yml` behavior and manual `.env` upload requirement.
- Standardized Markdown structure and code blocks across docs.

## Assumptions Made

- PHP runtime target remains PHP 8.x as indicated by code comments and syntax, without pinning to a specific deployed patch version.
- SMTP and DKIM settings are considered optional because production contact submission currently uses native `mail()`.
- Accessibility conformance statements are based on static code review and manual-check guidance, not automated test tooling output.

## Unresolved Mismatches

- `js/scroll_reveal_animations.js` expects `[data-animate]` elements, but current templates do not include those attributes.
- `config/contact_mail.php` defines `logs/contact-mail.log`, but current `contact.php` does not write to that log path.
- Diagnostics scripts rely on direct `getenv('WYP_DIAG_KEY')` and do not bootstrap `.env` through `includes/init.php`; this is documented but may surprise maintainers.

## Recommended Future Documentation Improvements

1. Add a short architecture decision note if/when SMTP replaces native `mail()` in production contact handling.
2. Add a small docs section for encoding standards (UTF-8 without BOM) to prevent character-garbled comments/text.
3. If scroll reveal effects are intended, document and implement a canonical `data-animate` usage pattern in templates.
4. Add a repeatable manual QA checklist that combines accessibility, contact form, and deployment smoke tests.
