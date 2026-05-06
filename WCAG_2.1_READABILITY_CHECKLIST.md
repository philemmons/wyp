# WCAG 2.1 Readability and Accessibility Checklist

Last audited: 2026-05-05
Applies to: `includes/header.php`, `includes/footer.php`, `contact.php`, `css/style.css`, `js/back_to_top_button.js`, `js/scroll_reveal_animations.js`, `js/contact_page.js`

## Scope

This checklist tracks implemented WCAG 2.1 AA-oriented patterns for readability, keyboard interaction, and form accessibility.

## Verified Implementation

- Skip link exists in `includes/header.php` and targets `<main id="main-content">`.
- Shared landmark structure is present: `<nav>`, `<main>`, `<footer>`, and `<address>` blocks.
- Current-page state is exposed with `aria-current="page"` in main navigation.
- Focus indication is implemented with shared `:focus-visible` styling in `css/style.css`.
- Contact form labels are explicitly associated with controls in `contact.php`.
- Required fields are communicated in text (`Required fields: Email, Subject, and Message.`), not by asterisk-only signaling.
- Server-side invalid states set `aria-invalid="true"` and show error feedback.
- Error summary/status messaging uses `role`/`aria-live` patterns.
- Reduced motion is respected in CSS media query and in JS (`prefers-reduced-motion`) for motion behavior.
- Back-to-top control updates `tabindex` and `aria-hidden` based on visibility.

## WCAG 2.1 AA Mapping

- [x] 1.3.1 Info and Relationships
- [x] 1.4.3 Contrast (Minimum)
- [x] 1.4.4 Resize Text
- [x] 1.4.10 Reflow
- [x] 1.4.12 Text Spacing
- [x] 2.1.1 Keyboard
- [x] 2.4.1 Bypass Blocks
- [x] 2.4.7 Focus Visible
- [x] 3.3.1 Error Identification
- [x] 3.3.2 Labels or Instructions

## Current Gaps and Observations

- `js/scroll_reveal_animations.js` targets `[data-animate]`, but no current templates include `data-animate` attributes. This behavior is currently inactive but non-breaking.
- Accessibility verification is manual; there is no automated accessibility test suite in this repository.
- There is no documented color-contrast test artifact in repo; contrast checks are currently process-driven.

## Manual Regression Checklist

1. Test pages at 100%, 200%, and 400% zoom.
2. Tab through skip link, primary nav, interactive content, form controls, and footer links.
3. Confirm skip link appears on keyboard focus and focus can move to main content.
4. Submit contact form with invalid/missing values and verify field-level and summary feedback.
5. Confirm reCAPTCHA errors are announced and visible when token is missing or invalid.
6. Enable reduced-motion OS setting and verify motion is minimized.
7. Re-check color contrast whenever palette or token values change in `css/style.css`.

## Authoring Rules

- Keep semantic labels synchronized with input `id`/`name` attributes.
- Keep required-field instructions explicit in visible text.
- Preserve or improve `:focus-visible` states for all custom interactive components.
- Preserve reduced-motion behavior when introducing new animations or scroll effects.
- Validate new forms against keyboard-only and screen-reader interaction flows.
