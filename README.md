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
- Canonical file naming is snake_case for renamed handlers/scripts
- Legacy renamed routes are redirected to canonical targets in `.htaccess`

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
|-- 403.php
|-- 404.php
|-- .htaccess
|-- .env.example
|-- robots.txt
|-- sitemap.xml
|-- includes/
|   |-- init.php
|   |-- header.php
|   `-- footer.php
|-- css/
|   `-- style.css
|-- js/
|   |-- scroll_reveal_animations.js
|   `-- back_to_top_button.js
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
- Blocks direct access to `.env` files
- Legacy redirect compatibility for renamed files (for example `contact_submit.php` -> `contact.php`)
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

`contact.php` includes:

- CSRF token validation (`hash_equals`)
- Honeypot check
- Input validation and length guards
- Header injection protections for email headers
- Server-side Google reCAPTCHA verification
- Environment-based key loading (`GOOGLE_RECAPTCHA_SITE_KEY`, `GOOGLE_RECAPTCHA_SECRET_KEY`)

## Environment Variables (.env)

Copy `.env.example` to `.env` and set real values:

```text
WYP_EMAIL=admin@example.com
WYP_FORM_FROM_EMAIL=noreply@example.com
GOOGLE_RECAPTCHA_SITE_KEY=your_recaptcha_site_key_here
GOOGLE_RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key_here
```

Notes:

- `.env` is ignored by git and should never be committed.
- `GOOGLE_RECAPTCHA_SECRET_KEY` is used server-side only and is never rendered to the browser.
- If required values are missing, the contact form disables submit and logs a developer-facing configuration error.

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
- Ensure `includes/init.php` and `.env.example` are deployed.
- Create a real `.env` file on the server (do not commit it).

### FastComet/cPanel `.env` setup

1. In cPanel File Manager, navigate to your site root (`public_html` or addon domain document root).
2. Create `.env` in that directory (same level as `contact.php`) or one directory above if your hosting layout allows it.
3. Copy values from `.env.example` and replace placeholders with real credentials.
4. Confirm `.htaccess` with the `.env` deny rule is deployed.
5. Because `.cpanel.yml` deploys `*` and not dotfiles, upload `.env` manually in cPanel after each first-time environment setup.

---

Maintained for wipeyourpaws.net.

