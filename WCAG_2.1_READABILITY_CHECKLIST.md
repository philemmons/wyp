# WCAG 2.1 Readability And Typography Checklist

Last updated: 2026-04-16
Applies to: `css/style.css` and page templates in this repo.

## Scope
This checklist focuses on WCAG 2.1 text readability and visual presentation topics:
- Fonts and text style
- Line spacing and paragraph spacing
- Letter spacing and word spacing
- Color contrast
- Relative units and text resizing
- Line length and alignment

## Current Implementation Snapshot
- Base readable typography is set in `body` and shared text rules.
- Most body copy now uses a readable measure (`ch`-based width limits).
- Hero and dark-surface text contrast was strengthened using darker gradients and overlays.
- Small-text tokens were increased in key UI areas.
- Motion reduction behavior remains in place (`prefers-reduced-motion`).

## WCAG Criteria Checklist

### A/AA Criteria

- [x] **1.4.3 Contrast (Minimum)**
  - Primary body and UI text combinations are tuned for >= 4.5:1 for normal-size text.
  - Dark-surface sections (nav/footer/cta/heroes) were updated to improve white/yellow text contrast.

- [x] **1.4.4 Resize Text**
  - Text uses relative sizing (`rem`, `clamp`) rather than fixed px text sizes.
  - `html` keeps browser text scaling support (`font-size: 100%`, `-webkit-text-size-adjust: 100%`).

- [x] **1.4.10 Reflow**
  - Mobile breakpoints and grid adjustments are present.
  - Long text blocks use constrained measures and wrapping safeguards.

- [x] **1.4.12 Text Spacing**
  - No restrictive fixed text containers for paragraph content.
  - Readability rules allow increased line/word/letter spacing without clipping.

### AAA-Oriented Readability Preferences (1.4.8 Visual Presentation guidance)

- [x] Line length generally constrained to readable measures (about 45-76ch depending on context).
- [x] Paragraph line-height raised for body copy and long-form text.
- [x] Long informational text is left-aligned in most sections.
- [x] Paragraph spacing is standardized via shared spacing tokens.
- [x] Decorative/script fonts reduced in some information-heavy headings.

## Selector-Level Quick Audit
Use this section as a fast maintenance checklist when editing styles.

- [x] `body`, `main p`, `main li` keep readable defaults.
- [x] `.wyp-navbar`, `.wyp-footer`, `.home-cta-strip` maintain dark enough backgrounds for light text.
- [x] `.wyp-hero::before`, `.monterey-hero::before`, `.contact-hero::before`, `.gallery-hero::before`, `.error-hero::before` preserve text contrast over gradients.
- [x] `.feature-tile-copy`, `.intro-duo-copy`, `.intro-together-copy`, `.monterey-intro-copy`, `.summary-callout-copy`, `.gallery-notice-copy`, `.gallery-upload-copy`, `.contact-sidebar-copy`, `.error-hero-copy` stay measure-limited and readable.
- [x] `.required-note`, `.contact-privacy-note`, `.footer-bottom`, `.error-link-meta` remain legible (avoid shrinking below practical small-text sizes).

## Regression Test Procedure
Run this manual check after major visual edits:

1. Open each page at 100%, 200%, and 400% zoom.
2. Confirm no text overlaps/clipping in nav, cards, forms, footer, and hero areas.
3. Verify keyboard focus visibility on links/buttons/inputs.
4. Check contrast of any newly introduced color pair against WCAG targets.
5. Confirm long text remains readable (line length and alignment) on desktop and mobile widths.

## Authoring Rules For Future Edits
- Prefer `rem`, `%`, `clamp`, `ch` for typography and layout.
- Keep normal text contrast >= 4.5:1 (AA minimum).
- Use `max-inline-size` for long copy blocks.
- Avoid tiny uppercase text with heavy letter spacing.
- If adding bright gradients behind text, add a contrast-preserving overlay or switch to darker text/background pair.
