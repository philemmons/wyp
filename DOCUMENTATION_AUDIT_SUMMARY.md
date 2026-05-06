# Documentation Audit Summary

Audit date: 2026-05-05
Scope: All Markdown files in repository (`*.md`)

## Files Reviewed

- `README.md`
- `CONTACT_MAIL_SETUP.md`
- `WCAG_2.1_READABILITY_CHECKLIST.md`
- `diagnostics/DELIVERABILITY_CHECKLIST.md`

## Files Modified

- `README.md`
- `CONTACT_MAIL_SETUP.md`
- `WCAG_2.1_READABILITY_CHECKLIST.md`
- `diagnostics/DELIVERABILITY_CHECKLIST.md`

## Files Added

- `DOCUMENTATION_AUDIT_SUMMARY.md`

## Outdated Items Corrected

- Removed stale references to a non-existent `config/` mail configuration file.
- Removed stale references to a non-existent diagnostics PHPMailer factory file.
- Corrected project structure listings to match current repository tree.
- Corrected mail flow descriptions to match production behavior (`mail()` only, no sender auto-reply).
- Updated contact form field documentation to reflect current names and constraints:
  - optional combined name field
  - no phone field
  - required email/subject/message
- Updated validation flow documentation to match current CSRF, honeypot, reCAPTCHA, and server-side checks.
- Updated environment variable sections to match keys used by current code.
- Reworked deployment notes to match `.cpanel.yml` behavior.
- Normalized Markdown formatting, heading flow, code fences, and terminology.

## Assumptions Made

- Shared hosting target remains cPanel/LiteSpeed style deployment.
- Contact delivery in production continues to use native PHP `mail()` unless code changes.
- Manual accessibility verification remains the current process (no automated test harness present).

## Unresolved Mismatches (Documented, Not Auto-fixed)

- `includes/init.php` currently hard-requires `/.env` and throws when missing. This limits production secret-management options and forces `.env` provisioning in runtime filesystem.
- `.htaccess` contains a legacy rewrite target for a diagnostics PHPMailer factory path, but that target file is not present in current repository.

## Recommended Future Documentation Improvements

1. Add a short architecture decision record if `.env` bootstrap behavior is changed to support server-only env vars.
2. Add a dedicated runbook for removing diagnostics routes after production troubleshooting.
3. Add an accessibility evidence artifact template (contrast checks, keyboard pass notes, SR spot checks).
4. Add a concise route map documenting extensionless rewrite behavior and legacy redirects.
