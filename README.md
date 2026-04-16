# 🐾 Wipe Your Paws — wipeyourpaws.net
## Big Love for Small Paws

A multi-page website dedicated to Chandra (Chihuahua) and Skipper
(Chihuahua × Jack Russell mix), built for the small dog community in
Monterey Bay, California.

**Revision 4 — WCAG 2.1 AA compliant, security hardened.**

---

## Technology Stack

| Layer       | Technology                                   |
|-------------|----------------------------------------------|
| Server      | PHP 8.0+                                     |
| HTML        | HTML5 (landmark elements, ARIA)              |
| CSS         | CSS3 + Custom Properties                     |
| Framework   | Bootstrap 5.3.8                              |
| Fonts       | Google Fonts — Pacifico, Nunito, Playfair Display |
| Icons       | Bootstrap Icons 1.11.3                       |

---

## File Structure

```
wipeyourpaws/
├── index.php              — Homepage
├── intro.php              — Meet the Chihuahuas
├── monterey.php           — Why Monterey?
├── contact.php            — Contact Us (session-based, CSRF-protected)
├── contact_submit.php     — Form POST handler
├── gallery.php            — Media Gallery (placeholder grid)
├── 404.php                — Custom 404 error page
├── .htaccess              — Apache: HTTPS, CSP, 404, security headers
├── robots.txt             — Crawler rules + sitemap reference
├── sitemap.xml            — All public pages with priorities
│
├── includes/
│   ├── header.php         — HTML head, skip link, <main>, navbar
│   └── footer.php         — </main>, footer, Bootstrap JS, scroll JS
│
├── css/
│   └── style.css          — All custom styles (WCAG 2.1 AA, Rev.4)
│
└── images/                — Drop site images here (see §5 below)
    ├── favicon-32.png     — (to be added — 32×32 px)
    ├── favicon-16.png     — (to be added — 16×16 px)
    ├── apple-touch-icon.png — (to be added — 180×180 px)
    └── og-image.jpg       — (to be added — 1200×630 px)
```

---

## Pages

| File             | URL         | Description                                    |
|------------------|-------------|------------------------------------------------|
| `index.php`      | `/`         | Homepage: hero, features, quote, map teaser    |
| `intro.php`      | `/intro.php`| Chandra & Skipper profiles + breed facts       |
| `monterey.php`   | `/monterey.php` | Why Monterey is great for dogs             |
| `contact.php`    | `/contact.php` | Contact form + map embed                    |
| `gallery.php`    | `/gallery.php` | Photo gallery (placeholder, ready for images) |
| `404.php`        | auto-loaded | Custom not-found page with quick-nav           |

---

## Deployment Checklist

### 1. Upload files
Upload the entire `wipeyourpaws/` directory to your host's `public_html`
(or `www/` / `htdocs/` — whichever your host uses).

### 2. PHP version
Confirm **PHP 8.0+** is active. The code uses `random_bytes`, `hash_equals`,
`str_contains`, and named match expressions — all PHP 8.x features.

### 3. SSL / HTTPS
Ensure an SSL certificate is installed. The `.htaccess` redirects all HTTP
traffic to HTTPS. Most shared hosts provide a free Let's Encrypt cert via cPanel.

### 4. Email / Contact form
The form uses PHP's built-in `mail()` function.

**Option A — Host SMTP (simplest):** Many shared hosts route `mail()` through
their own SMTP automatically. Test after deployment.

**Option B — PHPMailer + SMTP (recommended for production):**
```bash
composer require phpmailer/phpmailer
```
Replace the `@mail(...)` call in `contact_submit.php` with PHPMailer. See the
inline comment in that file for a complete drop-in snippet.

### 5. Add images
Create an `images/` directory inside `wipeyourpaws/` and add:

| File                   | Size         | Purpose                       |
|------------------------|--------------|-------------------------------|
| `favicon-32.png`       | 32×32 px     | Browser tab icon              |
| `favicon-16.png`       | 16×16 px     | Fallback tab icon             |
| `apple-touch-icon.png` | 180×180 px   | iOS home-screen icon          |
| `og-image.jpg`         | 1200×630 px  | Social-share preview image    |

Then uncomment the `<link>` and `<meta property="og:image">` lines in
`includes/header.php` (search for "Favicon").

### 6. Gallery images
Replace the placeholder tiles in `gallery.php` with real `<img>` tags. Always
include descriptive `alt` text (WCAG 1.1.1):

```php
<img src="images/chandra-beach.jpg"
     alt="Chandra the Chihuahua playing on Carmel Beach"
     class="img-fluid" loading="lazy"
     style="width:100%; height:100%; object-fit:cover;
            border-radius:var(--radius-lg);">
```

### 7. Sitemap
Update `<lastmod>` dates in `sitemap.xml` after content changes, then submit
to Google Search Console and Bing Webmaster Tools.

### 8. Content Security Policy
The CSP in `.htaccess` covers Bootstrap CDN and Google Maps. If you add
third-party scripts (analytics, chat), add their domains to `script-src` /
`connect-src` as needed.

---

## WCAG 2.1 AA Compliance — Full Audit Trail

All issues identified across four audit passes have been resolved.

### Colour Variable Reference

| Variable         | Hex       | Contrast on cream | Safe for        |
|------------------|-----------|-------------------|-----------------|
| `--text-dark`    | `#3A1A00` | 15.02:1           | All text sizes  |
| `--text-mid`     | `#6B3500` | 9.34:1            | All text sizes  |
| `--text-light`   | `#9A5520` | 5.40:1            | All text sizes  |
| `--text-orange`  | `#B83918` | 5.48:1            | All text sizes  |
| `--text-mauve`   | `#823C64` | 7.43:1 (warm-white)| All text sizes |
| `--orange-deep`  | `#D94820` | 4.07:1            | **Large text only** (≥24px normal / ≥18.67px bold) |
| `--orange-primary`| `#F07822` | 2.69:1           | **Never as text on light backgrounds** |
| `--yellow-bright`| `#F8CE10` | 1.52:1 on white   | **Dark backgrounds only** |
| `--pink-mauve`   | `#C87FAA` | 2.97:1 on white   | **Never as text on light backgrounds** |

---

### Rev.1–2 — Accessibility & Security (original build)

| ID  | Criterion | Fix |
|-----|-----------|-----|
| W1  | 2.4.1 Bypass Blocks — A | Skip-to-main link added |
| W2  | 1.3.1 Info & Relationships — A | `<main id="main-content" tabindex="-1">` |
| W3  | 4.1.2 Name/Role/Value — A | `aria-current="page"` on active nav link |
| W4  | 4.1.2 — A | `aria-label` on both `<nav>` elements |
| W5  | 2.4.7 Focus Visible — AA | `outline:none` removed; `:focus-visible` restored throughout |
| W6  | 2.4.7 — AA | High-contrast focus rings on buttons, nav, form controls, footer |
| W7  | 2.3.3 Animation | `prefers-reduced-motion` media query + JS `matchMedia` check |
| W8–10 | 1.4.1 / 3.3.2 / 1.3.1 | Required-field asterisks with sr-only text + note above form |
| W11 | 4.1.2 — A | `aria-required="true"` on all required fields |
| W12 | 1.3.5 Input Purpose — AA | `autocomplete="name"` and `autocomplete="email"` |
| W13 | 1.4.3 Contrast — AA | Six contrast failures corrected |
| W14 | 1.1.1 Non-text Content — A | `aria-hidden="true"` on all decorative icons and emojis |
| W15 | 1.3.1 — A | Honeypot uses CSS offscreen + `aria-hidden` |
| B1–5 | Security bugs | CSRF token, header injection fix, PII-free URLs, single font load |
| H1  | Heading hierarchy | Corrected h4/h5 skipping across all pages |

---

### Rev.3 — Typography & Contrast (CSS audit pass 1)

**Gradient Backgrounds — all hero/navbar/footer stops now ≥4.5:1 for white normal text, or ≥3:1 for confirmed large-text headings:**

| ID  | Element | Was | Fixed to | Min ratio |
|-----|---------|-----|----------|-----------|
| C1  | Navbar | ended at #F5A240 (2.07:1) | `#7A1E00→#A83010→#C53C14` | 5.21:1 ✅ |
| C2  | Hero card overlay | `rgba(255,255,255,0.18)` (≤1.13:1 worst) | `rgba(30,5,0,0.60)` | 6.02:1 ✅ |
| C3  | Monterey-hero | ended at pink-mauve (2.97:1) | `#D94820→#A83010→#702060` | 4.29:1 (large h1) ✅ |
| C4  | Contact-hero | pink-mauve→orange-primary→yellow-bright (all fail) | `#6B2060→#A83010→#D94820` | 4.29:1 (large h1) ✅ |
| C5  | Gallery-hero | started at #F07822 (2.83:1) | `#A83010→#D94820→#7A1E00` | 4.29:1 (large h1) ✅ |
| C6  | Footer | ended at #F5A240 (2.07:1) | `#7A1E00→#C53C14→#B83918` | 5.21:1 ✅ |
| C7  | Footer tagline | `--yellow-light` (3.48:1 FAIL) | `rgba(255,255,255,0.95)` | ≥5.21:1 ✅ |
| C8  | Footer links resting | `rgba(white,0.92)` (4.47:1) | `#fff` | ≥5.21:1 ✅ |
| C9  | Footer a:hover | `--yellow-bright` (fails mid-stops) | `#fff + underline` | ≥5.21:1 ✅ |
| C10 | btn-submit | ended at yellow-bright (1.52:1) | `#7A1E00→#C53C14` | 5.21:1 ✅ |
| C11 | .section-eyebrow | `--orange-deep` at 0.72rem (4.07:1) | `--text-orange` | 5.48:1 ✅ |
| C12 | .monterey-category-card h3 | `--orange-deep` at 1.1rem (4.07:1) | `--text-orange` | 5.68:1 ✅ |
| C13 | .contact-info-box h3 (CSS) | `--orange-deep` (4.07:1) | `--text-orange` | 5.68:1 ✅ |
| C14 | New variable | — | `--text-orange: #B83918` | 5.48:1 on cream |

**Font sizes fixed (all were sub-12px):**

| ID  | Element | Was | Fixed |
|-----|---------|-----|-------|
| F1  | .navbar-brand span | 0.65rem (10.4px) | 0.75rem |
| F2  | .section-eyebrow | 0.72rem (11.5px) | 0.8rem |
| F3  | .dog-breed-badge | 0.72rem (11.5px) | 0.8rem |
| F4  | .gallery-coming-badge | 0.62rem (9.9px) | 0.75rem |
| F5  | .footer-bottom | 0.78rem | 0.8rem |
| F6  | .wyp-form .form-label | 0.82rem | 0.85rem |

**Typography base:**

| ID  | Fix |
|-----|-----|
| L1  | `html { font-size: 100% }` — anchors rem to user browser font preference |
| L2  | `body { line-height: 1.5 }` — WCAG 1.4.12 minimum |
| L3  | `body { word-spacing: normal }` — explicit baseline |
| L4  | `p { line-height: 1.6; margin-bottom: 1.5rem }` — above Bootstrap default |
| L5  | `.wyp-hero h1 line-height: 1.1 → 1.2` |

**Letter-spacing reduced on small uppercase elements:**

| ID  | Element | Was | Fixed |
|-----|---------|-----|-------|
| LS1 | .navbar-brand span | 0.14em | 0.10em |
| LS2 | .section-eyebrow | 0.18em | 0.12em |
| LS3 | .dog-breed-badge | 0.12em | 0.06em |
| LS4 | .gallery-coming-badge | 0.10em | 0.06em |

**Relative units (px → rem):**

| ID  | Element | Was | Fixed |
|-----|---------|-----|-------|
| U1  | html | no font-size | `font-size: 100%` |
| U2  | .dog-avatar-frame | 180px / 6px border | 11.25rem / 0.375rem |
| U3  | .dog-avatar-frame responsive | 130px | 8.125rem |
| U4  | .social-circle | 40px | 2.5rem |
| U5  | .monterey-num-badge | 32px | 2rem |
| U6  | .contact-info-icon | 36px | 2.25rem |
| U7  | textarea min-height | 140px | 9rem |
| U8  | navbar toggler padding | 4px 8px | 0.25rem 0.5rem |

**Utility and selector:**

| ID  | Fix |
|-----|-----|
| LN1 | Added `.text-content { max-width: 75ch }` utility (WCAG 1.4.8 AAA guidance) |
| BX1 | `.contact-info-box h5 → h3, h5` selector (heading changed in Rev.2) |

---

### Rev.4 — Typography & Contrast (CSS audit pass 2 + PHP inline styles)

**Critical contrast failures found and fixed:**

| ID  | Element | Was | Fixed | New ratio |
|-----|---------|-----|-------|-----------|
| C-R4-1 | `.dedication-banner` gradient | pink-mauve→orange-primary (2.97:1 / 2.83:1 — **fails even 3:1 large-text**) | `#6B2060→#A03018` | 10.5:1 / 7.17:1 ✅ |
| C-R4-2 | `.required-asterisk` | `--orange-deep` on warm-white (4.22:1) | `--text-orange` | 5.68:1 ✅ |
| C-R4-3 | index.php CTA strip | gradient ended at yellow-bright (white 1.52:1) | `#7A1E00→--orange-deep→#A83010` | ≥6.78:1 ✅ |
| C-R4-4 | index.php CTA btn (white bg) | `--orange-deep` on #fff (4.29:1) | `--text-orange` | 5.48:1 ✅ |
| C-R4-5 | index.php 3× feature card h3 (1.2rem) | `--orange-deep` (4.22:1) | `--text-orange` | 5.68:1 ✅ |
| C-R4-6 | index.php blockquote p (1.4rem italic, pastel bg) | `--orange-deep` (3.28:1 on orange-pale) | `--text-mid` | 9.34:1 ✅ |
| C-R4-7 | intro.php 2× dog subtitle p (1rem italic) | `--pink-mauve` (2.92:1) | `--text-mauve` | 7.43:1 ✅ |
| C-R4-8 | intro.php Say Hello btn-outline (transparent bg) | `--orange-deep` on cream (4.07:1) | `--text-orange` | 5.48:1 ✅ |
| C-R4-9 | intro.php 2× breed fact h3 (1.2rem) | `--orange-deep` (4.22:1) | `--text-orange` | 5.68:1 ✅ |
| C-R4-10 | monterey.php spot name `<strong>` (0.88rem) | `--orange-deep` (4.07:1) | `--text-orange` | 5.48:1 ✅ |
| C-R4-11 | gallery.php 2× story links (0.82rem) | `--orange-primary` (2.69:1) | `--text-orange` | 5.48:1 ✅ |
| C-R4-12 | contact.php info h3 inline (1.1rem, overrides CSS) | `--orange-deep` (4.07:1) | `--text-orange` | 5.68:1 ✅ |
| C-R4-13 | contact.php email link | `--orange-deep` on pale-orange (4.07:1) | `--text-orange` | 5.48:1 ✅ |
| C-R4-14 | contact.php location + website spans | `--orange-deep` (4.07:1) | `--text-orange` | 5.48:1 ✅ |
| C-R4-15 | intro.php page hero inline background | pink-mauve/orange-primary override (2.97:1/2.83:1 — fails 3:1 large-text) | dark mahogany gradient matching CSS class | ≥4.29:1 (large h1) ✅ |
| C-R4-16 | intro.php Skipper badge inline background | orange-primary→orange-deep (text-dark 3.69:1 at dark end) | Removed inline override → default CSS pink (4.9–7.9:1) | 4.90:1 ✅ |

**New CSS variable:**

| Variable       | Hex       | Contrast (warm-white) | Use |
|----------------|-----------|-----------------------|-----|
| `--text-mauve` | `#823C64` | 7.43:1                | Mauve/pink text at any size on light backgrounds |

**Additional typography fixes:**

| ID      | Fix |
|---------|-----|
| L-R4-1  | `.hero-tagline line-height: 1.4 → 1.5` (was below WCAG 1.4.12 minimum) |
| LH-R4-1 | `.section-title line-height: 1.15 → 1.2` (better for multi-line headings) |

**Remaining px → rem conversions:**

| Element | Was | Fixed |
|---------|-----|-------|
| `.card-header-band` height | 8px | 0.5rem |
| Nav active dot width/height/bottom | 6px / 6px / 2px | 0.375rem / 0.375rem / 0.125rem |
| Hero/section `::after` scallops (×3) | 60px | 3.75rem |
| `.wyp-footer::before` stripe | 4px | 0.25rem |
| `.swirl-strip` | 12px | 0.75rem |
| `.gallery-coming-badge` position | 12px | 0.75rem |
| `.map-wrapper` border | 4px | 0.25rem |
| `.hero-copy` max-width | 560px | 60ch (character-based, scales) |

---

## Confirmed Passing — All Elements

After Rev.4, every text element in the site has been individually verified:

| Category | Criterion | Status |
|----------|-----------|--------|
| Skip link | 2.4.1 Bypass Blocks — A | ✅ |
| Focus indicators — all controls | 2.4.7 Focus Visible — AA | ✅ |
| Colour contrast — all text | 1.4.3 Contrast — AA | ✅ |
| Text resize 200% | 1.4.4 Resize Text — AA | ✅ |
| Reflow at 320px | 1.4.10 Reflow — AA | ✅ |
| Text spacing overrides | 1.4.12 Text Spacing — AA | ✅ |
| Images of text | 1.4.5 — AA | ✅ (no images of text used) |
| Form labels & instructions | 3.3.2 Labels — A | ✅ |
| ARIA roles & landmarks | 4.1.2 Name/Role/Value — A | ✅ |
| Keyboard navigation | 2.1.1 Keyboard — A | ✅ |
| Reduced motion | 2.3.3 Animation — AAA | ✅ |

---

## Known Limitations / Pending Items

- **Gallery images:** Grid uses placeholder tiles. Replace with `<img>` elements
  including descriptive `alt` text as real photos become available (WCAG 1.1.1).
- **PHPMailer:** The `mail()` function is unreliable on many hosts. Switch to
  PHPMailer + SMTP for reliable production delivery.
- **Favicon:** Three sizes plus OG image need to be created. Uncomment the
  `<link>` tags in `header.php` once files are in place.
- **Analytics:** No tracking installed. Add Google Analytics or Plausible by
  including their script and updating `script-src` in `.htaccess`.
- **Rate limiting:** The contact form regenerates a CSRF token per submission.
  For high-traffic scenarios, add IP-based throttling via `$_SESSION`.

---

*Made with 🧡 for Chandra & Skipper · wipeyourpaws.net · WCAG 2.1 AA Rev.4*
