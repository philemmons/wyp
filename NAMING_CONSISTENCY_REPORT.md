# Naming Consistency Report

## Scope
- Completed a project-wide naming refactor across PHP templates, shared includes, contact form handling, diagnostics routes, JavaScript modules, and setup documentation.
- Preserved behavior while normalizing identifier and file naming to Clean Code intent-revealing standards.

## Renamed Concepts
- Page context variable: `$page_id` -> `$activePageKey`.
- Navigation metadata variables in shared header renamed to intent-revealing names:
  - `$nav_items` -> `$primaryNavigationLinks`
  - `$page_titles` -> `$pageTitleByKey`
  - `$page_descriptions` -> `$pageDescriptionByKey`
  - `$base_url` -> `$siteBaseUrl`
  - `$page_paths` -> `$pagePathByKey`
  - `$canonical` -> `$canonicalUrl`
- Contact form state/session naming normalized:
  - `form_sent` -> `contact_form_submission_succeeded`
  - `form_confirmation_sent` -> `contact_form_confirmation_sent`
  - `form_error` -> `contact_form_submission_failed`
  - `form_errors` -> `contact_form_error_messages`
  - `form_field_errors` -> `contact_form_field_errors`
  - `form_values` -> `contact_form_previous_values`
  - `csrf_token` -> `contact_form_csrf_token`
  - `contact_submit_times` -> `contact_form_submission_timestamps`
- Contact form honeypot field renamed for clarity:
  - `website` -> `contact_website`.
- Contact submission handler helper names refactored to clear responsibilities:
  - `$maskEmailAddress`, `$logContactFormEvent`, `$readPostField`, `$normalizeWhitespace`, `$resolveClientIpAddress`, `$sanitizeHeaderValue`, `$loadPhpMailer`, `$sendViaNativeMailTransport`, `$sendViaSmtpTransport`, `$sendWithPreferredMailTransport`, etc.

## File and Module Renames
- `contact_submit.php` -> `process_contact_form_submission.php`
- `js/backToTop.js` -> `js/back_to_top_button.js`
- `js/app.js` -> `js/scroll_reveal_animations.js`
- `diagnostics/mail_diagnostic.php` -> `diagnostics/mail_delivery_diagnostic.php`
- `diagnostics/smtp_test.php` -> `diagnostics/smtp_delivery_test.php`
- `diagnostics/PHPMailer_PRODUCTION_TEMPLATE.php` -> `diagnostics/phpmailer_production_mailer_factory.php`

## Vocabulary Normalization
- Standardized mail/delivery vocabulary around:
  - delivery, confirmation, recipient, outbound, transport, diagnostics.
- Standardized contact form vocabulary around:
  - submission, field errors, previous values, CSRF token, rate limits.
- Standardized page context vocabulary around:
  - active page key, navigation links, canonical URL.

## Ambiguous Names Removed
- Replaced ambiguous loop names like `$l`, `$ph`, `$cat`, and `$i` with descriptive alternatives:
  - `$navigationCard`, `$previewCard`, `$highlightCategory`, `$itemIndex`, `$highlightItem`, `$featuredSpot`.
- Replaced short generic JS identifiers:
  - `ready` -> `runWhenDomReady`
  - `elements` -> `animatedElements`
  - `observer` -> `animationObserver`
  - `backToTopBtn` -> `backToTopButton`

## Domain Naming Improvements
- Contact workflow identifiers now directly match user-visible behavior and business purpose:
  - submission succeeded/failed
  - confirmation sent
  - previous form values
  - mail delivery diagnostics
- Diagnostics scripts now communicate operational intent by file name and variable naming.

## Validation
- Ran PHP syntax checks across all PHP files.
- Result: `ALL_PHP_LINT_OK`
