# WCAG 2.1 Readability and Accessibility Checklist

Last audited: 2026-05-06  
Applies to: `includes/header.php`, `index.php`, `intro.php`, `contact.php`, `gallery.php`, `css/style.css`, `js/contact_page.js`, `js/gallery_lightbox_modal.js`

## Scope

This checklist tracks implemented WCAG 2.1 AA-oriented patterns for readability, keyboard interaction, semantic structure, forms, dialogs, and carousel behavior.

## Implemented Accessibility Fixes (Current State)

- Skip-link accessible name now matches visible label text (removed extra `aria-label` override).
- Landmark structure remains consistent (`<nav>`, `<main>`, `<footer>`, `<address>`), with shared header/footer usage.
- Improved meaningful image alternative text in content sections (`index.php`, `intro.php`).
- Contact form now uses explicit form heading association via `aria-labelledby`.
- Required controls include explicit required semantics (`required` + `aria-required="true"` where applicable).
- reCAPTCHA region is now grouped and programmatically labeled/described (`role="group"`, `aria-labelledby`, `aria-describedby`) with invalid state signaling.
- Form live-region focus behavior improved: error summary receives focus only for error outcomes, not success status.
- reCAPTCHA JS validation now toggles `aria-invalid` state and respects reduced-motion when scrolling to error area.
- Gallery carousel now includes keyboard instructions, focusability (`tabindex="0"`), keyboard support, and live announcements.
- Gallery modal behavior improved for focus return and Escape-key close robustness.
- Focus-visible structural improvements added for skip link, footer links, quick links, and gallery story links.
- Carousel controls and thumbnail presentation were adjusted for better small-screen/touch interaction without redesign.

## Before/After Change Notes

1. Skip link
- Before: visible text and programmatic label were different.
- After: single aligned label text for better Label in Name consistency.

2. Contact form semantics
- Before: form had no explicit heading association; required communication partly implicit.
- After: form is explicitly associated with its heading, and required fields include `aria-required`.

3. reCAPTCHA error accessibility
- Before: error messaging existed but was not grouped with a clearly labeled recaptcha control region.
- After: recaptcha has an accessible group label/description and error state is programmatically exposed.

4. Live-region focus behavior
- Before: status summary could receive focus for both error and success.
- After: focus is moved only when status is an error condition.

5. Carousel keyboard behavior
- Before: carousel depended primarily on framework default behavior.
- After: explicit keyboard instructions and key handling (`ArrowLeft`, `ArrowRight`, `Home`, `End`) are implemented.

6. Modal interaction
- Before: modal focus handling existed but trigger tracking depended only on click handlers.
- After: focus return is reinforced through modal `show` event source tracking and Escape close fallback.

7. Focus visibility and reflow/touch structure
- Before: some interactive link/card focus styles were inconsistent; carousel trigger height was rigid on small viewports.
- After: added structural focus styles and relaxed mobile carousel constraints to improve readability and operation.

## WCAG 2.1 AA Mapping (Implemented Patterns)

- [x] 1.1.1 Non-text Content
- [x] 1.3.1 Info and Relationships
- [ ] 1.4.3 Contrast (Minimum) (manual review required; see section below)
- [x] 1.4.4 Resize Text
- [x] 1.4.10 Reflow
- [x] 1.4.12 Text Spacing (structural compatibility pass; manual verification pending)
- [x] 2.1.1 Keyboard
- [x] 2.1.2 No Keyboard Trap
- [x] 2.4.1 Bypass Blocks
- [x] 2.4.3 Focus Order (implementation pass; manual walkthrough pending)
- [x] 2.4.7 Focus Visible (structural pass; color strength review pending)
- [x] 3.3.1 Error Identification
- [x] 3.3.2 Labels or Instructions
- [x] 4.1.2 Name, Role, Value

## Color Contrast Failures — Manual Review Required

1. File name: `css/style.css`  
Selector/element: `.wyp-form .form-control::placeholder`  
Text color: `#e7d2be`  
Background color: `#ffffff`  
Border/outline/focus indicator color: input border `#1d0e07`; shared focus ring `#55b7ff`  
Current measured ratio: `1.46:1`  
Minimum WCAG AA requirement: `4.5:1` (normal text)  
Preferred target ratio: `7:1` where practical  
Related criterion: `1.4.3`, `1.4.6`  
Why it fails or is borderline: placeholder text is significantly too light against white input background  
Suggested adjustment direction: darker placeholder text  
Human visual/manual verification required: Yes

2. File name: `css/style.css`  
Selector/element: `.form-help`  
Text color: `#a67c52`  
Background color: `#ffffff` to `#fff7eb` (context dependent)  
Border/outline/focus indicator color: n/a  
Current measured ratio: `3.73:1` on `#ffffff`; `3.51:1` on `#fff7eb`  
Minimum WCAG AA requirement: `4.5:1` (normal text)  
Preferred target ratio: `7:1` where practical  
Related criterion: `1.4.3`, `1.4.6`  
Why it fails or is borderline: helper text appears below minimum contrast for normal-sized instructional text  
Suggested adjustment direction: darker helper text  
Human visual/manual verification required: Yes

3. File name: `css/style.css`  
Selector/element: `.wyp-navbar .navbar-brand span`  
Text color: `#c86f3d`  
Background color: approximately `#fff9ef` (navbar gradient area)  
Border/outline/focus indicator color: n/a  
Current measured ratio: `3.46:1`  
Minimum WCAG AA requirement: `4.5:1` (normal text)  
Preferred target ratio: `7:1` where practical  
Related criterion: `1.4.3`, `1.4.6`  
Why it fails or is borderline: small tagline text has insufficient contrast against light navbar background  
Suggested adjustment direction: darker tagline text or clearer light/dark separation behind tagline  
Human visual/manual verification required: Yes

4. File name: `css/style.css`  
Selector/element: global focus ring (`--focus-ring: #55b7ff`) on light surfaces  
Text color: n/a  
Background color: `#ffffff` and similar light surfaces  
Border/outline/focus indicator color: `#55b7ff` (`--focus-ring`)  
Current measured ratio: `2.19:1` against white  
Minimum WCAG AA requirement: `3:1` for non-text indicators  
Preferred target ratio: clearly above minimum, target toward `7:1` where practical  
Related criterion: `1.4.11`, `2.4.11`  
Why it fails or is borderline: focus indicator may be too subtle on pale backgrounds  
Suggested adjustment direction: stronger/darker focus ring color and/or enhanced ring thickness  
Human visual/manual verification required: Yes

5. File name: `css/style.css`  
Selector/element: border tokens used for visual boundaries (`--border-mid`, `--border-soft`)  
Text color: n/a  
Background color: `#ffffff`  
Border/outline/focus indicator color: `#e7c8a5` (`--border-mid`), `#f2ddc7` (`--border-soft`)  
Current measured ratio: `1.59:1` (`--border-mid`), `1.32:1` (`--border-soft`)  
Minimum WCAG AA requirement: `3:1` where boundary conveys meaningful UI state/structure  
Preferred target ratio: stronger than minimum where practical  
Related criterion: `1.4.11`  
Why it fails or is borderline: subtle boundary colors can be insufficient when relied upon for perceivable component boundaries  
Suggested adjustment direction: stronger border contrast for meaningful boundaries/states  
Human visual/manual verification required: Yes

## Remaining Manual Accessibility Checks

1. Keyboard-only walkthrough across all templates and shared components.
2. Screen-reader pass (NVDA/JAWS/VoiceOver) for form flow, recaptcha messages, and gallery announcements.
3. Zoom/reflow checks at 200% and 400%, including 320px viewport scenarios.
4. Text-spacing override test for `1.4.12` with browser/bookmarklet overrides.
5. Full contrast pass across all states: default, hover, focus-visible, active, disabled, error, placeholder, icon-only, and borders/separators.

## Components Requiring Human Visual Inspection

1. Global focus indicator visibility across light gradients and cards.
2. Navbar tagline readability at all breakpoints.
3. Form helper and placeholder readability in realistic display conditions.
4. Boundary contrast where light borders communicate grouping/state.
5. Carousel controls and touch-target comfort on mobile devices.

## Compliance Status

Do not declare full WCAG compliance yet. Manual verification is still required.
