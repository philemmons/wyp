# Wipe Your Paws (wipeyourpaws.net)

Big Love for Small Paws.

A multi-page PHP website for the small dog community in Monterey Bay, California, featuring Chandra (Chihuahua) and Skipper (Chihuahua x Jack Russell mix).

## Current Status

This codebase now uses a centralized, canonical design system and shared template structure:

- Canonical stylesheet: `/css/style.css`
- Legacy `/assets/` CSS path is deprecated
- Shared layout includes: `includes/header.php` and `includes/footer.php`
- Session-safe include behavior (`session_start` guarded against duplicate start)
- Inline `style=""` removed from templates in favor of reusable classes

## Technology Stack

- PHP 8.0+
- HTML5 + ARIA landmarks
- CSS Custom Properties (design tokens)
- Bootstrap 5.3.8
- Bootstrap Icons 1.11.3
- Google Fonts: Berkshire Swash, Nunito, Playfair Display

## Project Structure

```text
wipeyourpaws/
|-- index.php
|-- intro.php
|-- monterey.php
|-- gallery.php
|-- contact.php
|-- contact_submit.php
|-- 403.php
|-- 404.php
|-- .htaccess
|-- robots.txt
|-- sitemap.xml
|-- includes/
|   |-- header.php
|   `-- footer.php
|-- css/
|   `-- style.css
|-- js/
|   |-- app.js
|   `-- backToTop.js
`-- images/
```

## Design System Notes

The canonical design source was standardized across templates using `wyp-*` classes.

### Key canonical classes

- Buttons: `.btn-wyp`, `.btn-wyp-primary`, `.btn-wyp-secondary`, `.btn-wyp-outline`, `.btn-wyp-ghost`, `.btn-submit`
- Sections: `.wyp-section`, `.wyp-section-sm`, `.wyp-section-alt`, `.wyp-section-accent`
- Cards: `.wyp-card`, `.wyp-feature-card`, `.wyp-info-card`
- Layout: `.wyp-grid-2`, `.wyp-grid-3`, `.wyp-grid-auto`, `.wyp-stack`
- Utilities: `.text-balance`, `.flow`, `.container-narrow`, `.shadow-hover`, `.radius-brand`

### Token system

Core tokens are defined in `:root` within `/css/style.css`, including:

- Color aliases (`--color-primary`, `--color-secondary`, `--color-accent`, etc.)
- Spacing aliases (`--space-xs` ... `--space-xl`)
- Radius aliases (`--radius-brand`, `--radius-pill`)
- Motion aliases (`--transition-fast`, `--transition-base`, `--transition-slow`)

## Apache (.htaccess)

Current `.htaccess` behavior:

- `DirectoryIndex index.php`
- Canonical redirect: `/index.php` -> `/`
- Extensionless PHP fallback routing (if matching `.php` exists)
- Custom error documents:
  - `ErrorDocument 403 /403.php`
  - `ErrorDocument 404 /404.php`
- Security headers:
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: SAMEORIGIN`
  - `Referrer-Policy: strict-origin-when-cross-origin`
- Static asset/browser caching rules for CSS/JS/images/fonts

## CSS Location Standard

- Main stylesheet: `/css/style.css`
- Legacy `/assets/` CSS paths are deprecated
- New pages should reference `/css/style.css`
- CSS should remain centralized unless a page-specific stylesheet is explicitly justified

## Accessibility and UX

The site includes:

- Skip link (`#main-content`)
- Visible `:focus-visible` states
- Reduced-motion support (`prefers-reduced-motion`)
- Semantic landmarks and heading structure
- Keyboard-friendly navigation patterns

## Contact Form Security

`contact_submit.php` includes:

- CSRF token validation (`hash_equals`)
- Honeypot check
- Input validation and length guards
- Header injection protections for email headers
- PRG pattern with session-based flash messages

## Local Development

1. Serve the project from your web root (Apache recommended).
2. Confirm PHP 8.0+.
3. Visit `/` for homepage.
4. Syntax check templates (optional):

```bash
php -l index.php
```

## Deployment Notes

- Ensure `/css/style.css` is deployed (this is the active stylesheet).
- If switching to SMTP delivery, replace `mail()` in `contact_submit.php` with PHPMailer.

---

Maintained for wipeyourpaws.net.
