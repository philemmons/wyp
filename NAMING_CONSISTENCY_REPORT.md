# Naming Consistency Report

## Scope
Project-wide naming audit and refactor applied to PHP request handlers, shared includes, diagnostics endpoints, JavaScript modules, and naming-related documentation.

## Renamed Concepts

| Old Name | New Name | Area | Reason |
|---|---|---|---|
| `contact_page.js` | `contact_form_experience.js` | JS module/file | Clarifies module responsibility is contact form UX + validation flow. |
| `gallery_lightbox_modal.js` | `gallery_lightbox_controller.js` | JS module/file | Uses controller vocabulary for modal/carousel behavior orchestration. |
| `back_to_top_button.js` | `back_to_top_control.js` | JS module/file | Focuses on behavior/control rather than element type. |
| `scroll_reveal_animations.js` | `scroll_reveal_controller.js` | JS module/file | Clarifies script manages reveal lifecycle logic. |
| `$status` | `$formSubmissionStatus` | `contact.php` | Explicitly conveys status domain and scope. |
| `$statusMsg` | `$formStatusMessage` | `contact.php` | Removes abbreviation and clarifies message role. |
| `$fieldErrors` | `$fieldErrorMessages` | `contact.php` | Clarifies contents and data shape. |
| `$postData` | `$submittedFieldValues` | `contact.php` | Indicates this is user-submitted form state. |
| `$readPostedValue` | `$readTrimmedPostValue` | `contact.php` | Verb phrase communicates read + trim behavior. |
| `$sanitizeHeaderValue` | `$sanitizeMailHeaderValue` | `contact.php` | Domain-specific sanitization target is now explicit. |
| `$verifyRecaptcha` | `$verifyRecaptchaTokenWithGoogle` | `contact.php` | Behavior and external dependency are explicit. |
| `$siteKey` / `$secretKey` | `$recaptchaSiteKey` / `$recaptchaSecretKey` | `contact.php` | Removes ambiguity across configuration values. |
| `$formConfigurationIssues` | `$contactFormConfigurationIssues` | `contact.php` | Adds domain context and improves searchability. |
| `$isFormConfigured` | `$isContactFormConfigured` | `contact.php` | Clear boolean intent and scope. |
| `id="myForm"` | `id="contactForm"` | `contact.php` + JS | Replaces generic identifier with domain-specific name. |
| `id="resetFormButton"` | `id="resetContactFormButton"` | `contact.php` + JS | Clarifies action target. |
| `id="formErrorSummary"` | `id="contactFormStatusSummary"` | `contact.php` + JS | Clarifies semantic purpose. |
| `formatGalleryDateFromFilename()` | `parseCapturedDateFromFilename()` | `gallery.php` | Verb + domain language (`captured`) improves intent. |
| `buildGalleryAltText()` | `composeGalleryImageAltText()` | `gallery.php` | Communicates deterministic composition behavior. |
| `thumbnailFilenameToFullFilename()` | `deriveFullSizeFilenameFromThumbnail()` | `gallery.php` | Clarifies source/target relation. |
| `$galleryPathIssues` | `$galleryAssetIssues` | `gallery.php` | Broader and more accurate issue category. |
| `$vagueAltTextFilenames` | `$genericAltTextFilenames` | `gallery.php` | Stronger adjective and easier search intent. |
| `$showSlideIndicators` | `$shouldRenderSlideIndicators` | `gallery.php` | Boolean naming normalized to `should...`. |
| `$pagePathByKey` | `$canonicalPagePathByKey` | `includes/header.php` | Aligns variable name with canonical URL purpose. |
| `$lines` | `$dotenvLines` | `includes/init.php` | Names data source precisely. |
| `$navigationCards` / `$navigationCard` | `$quickLinkCards` / `$quickLinkCard` | `403.php`, `404.php` | Aligns variable names with UI concept (“Quick Links”). |
| `$featuredDogFriendlySpots` | `$featuredDogFriendlyLocations` | `monterey.php` | Domain noun better matches geographic entities. |
| `$featuredSpot` | `$dogFriendlyLocation` | `monterey.php` | Removes generic placeholder naming. |
| indexed location arrays | associative keys (`icon_class`, `name`, `description`) | `monterey.php` | Removes positional ambiguity and improves readability. |
| `$readEnvironmentValue` | `$resolveEnvironmentValue` | diagnostics | Normalizes retrieval verb vocabulary (`resolve`). |
| `$expectedAccessKey` / `$providedAccessKey` | `$configuredDiagnosticAccessKey` / `$requestedDiagnosticAccessKey` | diagnostics | Distinguishes config vs request origin. |
| `$isJsonResponseRequested` | `$shouldReturnJson` | diagnostics | Boolean naming normalized to `should...`. |
| `$diagnosticReport` | `$mailDiagnosticReport` / `$smtpDiagnosticReport` | diagnostics | Makes report context explicit. |
| `$probeSmtpPort` | `$testSmtpPortConnectivity` | diagnostics | Verb + domain behavior clarified. |
| `$smtpSettings` | `$smtpConfiguration` | diagnostics | Consistent configuration vocabulary across app. |

## Vocabulary Normalization

- Retrieval actions normalized around `read`, `resolve`, and `parse` by responsibility.
- Boolean flags normalized to intent-driven `is...`, `has...`, or `should...` prefixes.
- Form domain terms standardized around `contactForm`, `submission`, `fieldValues`, and `validation`.
- Gallery domain terms standardized around `thumbnail`, `fullSize`, `caption`, and `carousel`.
- Diagnostics domain terms standardized around `diagnostic`, `configuration`, `report`, and `connectivity`.

## Ambiguous Names Removed

- Generic single-purpose names removed from public/local scope (`myForm`, `statusMsg`, `postData`, `featuredSpot`, `navigationCard`, `probeSmtpPort`).
- Abbreviations reduced where ambiguity harmed readability (`subj`, `em`, `ta` were retained only where they are external field keys to preserve compatibility).
- Positional array access for featured locations replaced with keyed associative arrays.

## Domain Naming Improvements

- Contact flow now speaks in form-domain language (submission status, validation errors, recaptcha token verification).
- Gallery flow now speaks in media-domain language (captured date parsing, full-size derivation, generic alt text detection).
- Diagnostics flow now speaks in operational mail-domain language (SMTP connectivity testing, diagnostic access keys, structured reports).

## Compatibility Notes

- Existing external-facing form field names (`contact-em`, `contact-subj`, `contact-ta`, `beeName`) were preserved intentionally to avoid breaking submissions and integrations.
- Behavior remained unchanged; this refactor targeted naming clarity and maintainability.

## Validation Performed

- Ran `php -l` across all PHP files in the repository: no syntax errors.
- Ran project-wide reference search to ensure old JavaScript module names were fully replaced in source and docs.
