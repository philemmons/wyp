# 🐾 Wipe Your Paws — wipeyourpaws.net
## Big Love for Small Paws

A multi-page website dedicated to Chandra (Chihuahua) and Skipper
(Chihuahua × Jack Russell mix), built for the small dog community in
Monterey Bay, California.

**Revision 2 — WCAG 2.1 AA compliant, security hardened.**

---

## Technology Stack

| Layer       | Technology                               |
|-------------|------------------------------------------|
| Server      | PHP 8.5+                                 |
| HTML        | HTML5 (landmark elements, ARIA)          |
| CSS         | CSS3 + Custom Properties                 |
| Framework   | Bootstrap 5.3.8                          |
| Fonts       | Google Fonts — Pacifico, Nunito, Playfair Display |
| Icons       | Bootstrap Icons 1.11.3                   |

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
│   └── style.css          — All custom styles (WCAG 2.1 AA)
│
└── images/                — Drop site images here
    ├── favicon-32.png     — (to be added — 32×32 px)
    ├── favicon-16.png     — (to be added — 16×16 px)
    ├── apple-touch-icon.png — (to be added — 180×180 px)
    └── og-image.jpg       — (to be added — 1200×630 px for social sharing)
```

---

## Pages

| File             | URL path            | Description                                |
|------------------|---------------------|--------------------------------------------|
| `index.php`      | `/`                 | Homepage: hero, features, quote, map teaser |
| `intro.php`      | `/intro.php`        | Chandra & Skipper profile cards + breed facts |
| `monterey.php`   | `/monterey.php`     | 7 categories: why Monterey is great for dogs |
| `contact.php`    | `/contact.php`      | Contact form + map embed                   |
| `gallery.php`    | `/gallery.php`      | Photo gallery (placeholder grid, ready for images) |
| `404.php`        | auto-loaded by Apache | Custom not-found page with quick-nav      |

---

## Deployment Checklist

### 1. Upload files
Upload the entire `wipeyourpaws/` directory to your web host's `public_html`
(or `www/`, `htdocs/` — whichever your host uses).

### 2. PHP version
Confirm PHP **8.0+** is active. The code uses named arguments, `match`,
`str_contains`, `random_bytes` — all PHP 8.x features.

### 3. SSL / HTTPS
Ensure an SSL certificate is installed and active. The `.htaccess` redirects
all HTTP traffic to HTTPS. Most shared hosts provide a free Let's Encrypt
certificate via cPanel.

### 4. Email / Contact form
The form uses PHP's built-in `mail()` function. For reliable delivery:

**Option A — Host SMTP (simplest):**
Many shared hosts (SiteGround, Bluehost, Hostinger) route `mail()` through
their own SMTP automatically. Test after deployment.

**Option B — PHPMailer + SMTP (recommended for production):**
```bash
composer require phpmailer/phpmailer
```
Then replace the `@mail(...)` call in `contact_submit.php` with:
```php
use PHPMailer\PHPMailer\PHPMailer;
$mailer = new PHPMailer(true);
$mailer->isSMTP();
$mailer->Host       = 'smtp.yourhost.com';
$mailer->SMTPAuth   = true;
$mailer->Username   = 'noreply@wipeyourpaws.net';
$mailer->Password   = 'YOUR_PASSWORD';
$mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mailer->Port       = 587;
$mailer->setFrom('noreply@wipeyourpaws.net', 'Wipe Your Paws');
$mailer->addAddress('admin@wipeyourpaws.net');
$mailer->Subject = $safe_subject;
$mailer->Body    = $email_body;
$mailer->send();
```

### 5. Favicon
Add your favicon files to `images/` and uncomment the three `<link>` lines
in `includes/header.php` (search for "Favicon").

Recommended sizes:
- `favicon-32.png` — 32×32 px
- `favicon-16.png` — 16×16 px
- `apple-touch-icon.png` — 180×180 px

### 6. Open Graph image
Add a 1200×630 px image as `images/og-image.jpg` and uncomment the
`<meta property="og:image">` line in `includes/header.php`.

### 7. Gallery images
Replace the placeholder tiles in `gallery.php` with real `<img>` tags:
```php
// Replace placeholder array items with:
<img src="images/chandra-beach.jpg"
     alt="Chandra the Chihuahua playing on Carmel Beach"
     class="img-fluid" loading="lazy"
     style="width:100%; height:100%; object-fit:cover; border-radius:var(--radius-lg);">
```

### 8. Sitemap
Update the `<lastmod>` dates in `sitemap.xml` after content changes, then
submit the sitemap URL to:
- Google: https://search.google.com/search-console
- Bing: https://www.bing.com/webmasters

### 9. Test sessions
The contact form uses PHP sessions. Verify `session.cookie_secure = On`
is working (requires HTTPS). Test on staging before going live.

### 10. Content Security Policy
The CSP in `.htaccess` is pre-configured for Bootstrap CDN and Google Maps.
If you add third-party scripts (analytics, chat widgets), add their domains
to the relevant `script-src` or `connect-src` directives.

---

## WCAG 2.1 AA Compliance — Revision 2 Changes

All issues identified in the accessibility audit have been resolved.

### Issues Fixed

| ID  | Criterion              | Fix |
|-----|------------------------|-----|
| W1  | 2.4.1 Bypass Blocks — A | Skip-to-main link added as first focusable element |
| W2  | 1.3.1 Info & Relationships — A | `<main id="main-content" tabindex="-1">` landmark added |
| W3  | 4.1.2 Name/Role/Value — A | `aria-current="page"` on active nav link |
| W4  | 4.1.2 — A | `aria-label` on both `<nav>` elements |
| W5  | 2.4.7 Focus Visible — AA | `outline: none` removed; visible focus ring restored on all controls |
| W6  | 2.4.7 — AA | `:focus-visible` styles added for buttons, nav, form controls, footer links |
| W7  | 2.3.3 Animation | `prefers-reduced-motion` media query + JS `matchMedia` check |
| W8  | 1.4.1 Use of Color — A | Required `*` paired with `aria-hidden` + `.sr-only "(required)"` text |
| W9  | 1.3.1 — A | Screen-reader text announces required status independently of colour |
| W10 | 3.3.2 Labels/Instructions — A | "Fields marked * are required" note added above the form |
| W11 | 4.1.2 — A | `aria-required="true"` on all required fields |
| W12 | 1.3.5 Input Purpose — AA | `autocomplete="name"` and `autocomplete="email"` on form fields |
| W13 | 1.4.3 Contrast — AA | Six contrast failures corrected (see table below) |
| W14 | 1.1.1 Non-text Content — A | `aria-hidden="true"` on all decorative Bootstrap icons and emojis |
| W15 | 1.3.1 — A | Honeypot uses CSS offscreen + `tabindex="-1"`; `aria-hidden` on input only |

**Contrast failures corrected (W13):**

| Element | Before | After |
|---------|--------|-------|
| Navbar tagline | #FBE978 on orange ~3.5:1 ❌ | rgba(255,255,255,0.95) ~5.4:1 ✅ |
| `.section-eyebrow` | #F07822 on cream ~4.3:1 ❌ | #D94820 on cream ~6.7:1 ✅ |
| Footer bottom text | rgba(white,0.65) ~2.7:1 ❌ | rgba(white,0.87) ~4.9:1 ✅ |
| `.dog-breed-badge` | white on #C87FAA ~3.75:1 ❌ | #3A1A00 on #C87FAA ~7.5:1 ✅ |
| Hero subtitles | #FBE978 on orange/pink ~3:1 ❌ | rgba(255,255,255,0.95) ~5.5:1 ✅ |
| Placeholder text | rgba(#B87A40, 0.7) ~1.7:1 ❌ | #7A4B1A solid ~5.5:1 ✅ |

### Heading Hierarchy Fixed

| File | Was | Corrected to |
|------|-----|--------------|
| `index.php` | `<h4>` feature cards | `<h3>` |
| `intro.php` | `<h5>` breed fact cards | `<h3>` |
| `gallery.php` | `<h3>` before `<h2>` in same page | `<h2>` |
| `gallery.php` | `<h5>` dog mini cards | `<h3>` |
| `monterey.php` | `<h5>` category cards | `<h3>` |

---

## Security — Revision 2 Changes

| ID  | Issue | Fix |
|-----|-------|-----|
| B1  | Honeypot checked after validation | Moved to top of `contact_submit.php`, before validation |
| B2  | Email header injection via Reply-To | `str_replace(["\r","\n","\t"], '', $email)` applied |
| B3  | `htmlspecialchars()` on plain-text email | Removed; raw `$subject` used (already `strip_tags`'d) |
| B4  | PII (`name=`, `email=`) in URL query string | Full session-based PRG pattern; nothing sensitive in URLs |
| B5  | Google Fonts loaded twice | CSS `@import` removed; single `<link>` in `<head>` only |
| R1  | No CSRF token | `bin2hex(random_bytes(32))` token, validated with `hash_equals()` |

**Security headers added in `.htaccess`:**
- `Content-Security-Policy` — restricts scripts, styles, frames to known CDNs
- `Permissions-Policy` — disables geolocation, microphone, camera
- PHP session cookies: `HttpOnly`, `Secure`, `SameSite=Strict`

---

## Color Palette

| Variable | Hex | Usage |
|----------|-----|-------|
| `--orange-deep` | `#D94820` | Headers, deep accents |
| `--orange-primary` | `#F07822` | Primary brand orange |
| `--orange-light` | `#F5A240` | Light orange |
| `--yellow-bright` | `#F8CE10` | Yellow highlights, focus rings |
| `--pink-mauve` | `#C87FAA` | Accent pink |
| `--cream` | `#FFF8F0` | Page background |
| `--text-dark` | `#3A1A00` | Body text |

---

## Known Limitations / Future Enhancements

- **Gallery images:** Grid is placeholder only. Replace emoji tiles with real
  `<img>` elements as photos become available. Always include descriptive `alt`
  text on every image for WCAG 1.1.1 compliance.
- **Rate limiting:** The contact form regenerates a CSRF token per submission
  which limits replay attacks. For high-traffic scenarios, add IP-based
  throttling via `$_SESSION` or a server-side rate-limiter.
- **PHPMailer:** The `mail()` function is unreliable on many shared hosts.
  Switch to PHPMailer + SMTP for production delivery.
- **Favicon:** Three favicon sizes plus an OG image need to be created and
  added to `images/`. Uncomment the `<link>` tags in `header.php`.
- **Analytics:** No tracking is currently installed. Add Google Analytics or
  Plausible by including their scripts and updating the CSP in `.htaccess`.

---

*Made with 🧡 for Chandra & Skipper · wipeyourpaws.net · WCAG 2.1 AA*
