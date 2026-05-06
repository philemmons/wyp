# WCAG 2.1 Readability and Typography Checklist

Last updated: 2026-05-05
Applies to: `css/style.css`, shared includes, and page templates.

## Scope

This checklist tracks WCAG 2.1 AA-oriented readability, form accessibility, and keyboard interaction patterns currently implemented.

## Verified Implementation

- Shared skip link in `includes/header.php`: `a.skip-link` to `#main-content`
- Landmark structure: `<nav>`, `<main id="main-content">`, `<footer>`
- Active nav indication: `aria-current="page"` in main navigation
- Focus styles: global `:focus-visible` ring in `css/style.css`
- Reduced motion: CSS `@media (prefers-reduced-motion: reduce)` limits animation and smooth scrolling, and JS respects reduced motion in back-to-top behavior.
- Contact form semantics in `contact.php`: labels map to controls, required fields are text-labeled, errors use `aria-live` and `role="alert"`, and invalid controls use `aria-invalid="true"` when server validation fails.

## WCAG 2.1 AA Checklist

- [x] 1.3.1 Info and Relationships
- Labels, landmarks, and heading hierarchy are present in shared templates and form markup.
- [x] 1.4.3 Contrast (Minimum)
- Brand palette and text colors are defined via tokens and tuned in shared stylesheet.
- [x] 1.4.4 Resize Text
- Typography uses scalable units (`rem`, `clamp`) and root font size remains browser-scalable.
- [x] 1.4.10 Reflow
- Responsive breakpoints are implemented for major layout blocks.
- [x] 1.4.12 Text Spacing
- No restrictive text clipping rules were found in contact/page body content areas.
- [x] 2.1.1 Keyboard
- Navigation, form controls, and back-to-top interaction are keyboard-operable.
- [x] 2.4.1 Bypass Blocks
- Skip link is first focusable element in body.
- [x] 2.4.7 Focus Visible
- Focus indication is visible via shared `:focus-visible` styling.
- [x] 3.3.1 Error Identification
- Contact form surfaces field-level and summary-level errors.
- [x] 3.3.2 Labels or Instructions
- Required field behavior is communicated by explicit text, not asterisks alone.

## Current Gaps and Observations

- `js/scroll_reveal_animations.js` targets `[data-animate]`, but no current templates include `data-animate` attributes. This is non-breaking but currently inactive behavior.
- There is no automated accessibility test suite in this repository; verification is manual.

## Manual Regression Pass

1. Test each page at 100%, 200%, and 400% zoom.
2. Tab through header nav, page CTAs, form controls, and footer links.
3. Confirm skip link appears on keyboard focus and moves focus into `<main>`.
4. Submit contact form with missing values and verify clear error messaging.
5. Enable reduced-motion OS setting and verify animations/transitions are minimized.
6. Recheck contrast whenever palette tokens are changed in `css/style.css`.

## Authoring Rules

- Keep semantic labels and instructions aligned with form field IDs/names.
- Preserve keyboard focus visibility for all interactive elements.
- Keep required-field communication explicit in text.
- Prefer scalable units and avoid fixed pixel text sizing for body content.
- Preserve reduced-motion behavior in both CSS and JavaScript changes.
