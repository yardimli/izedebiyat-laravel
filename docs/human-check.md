# Country reading challenge

The web middleware challenges visitors from CN, SG, VN, BR and HK before route bindings and page controllers run. Local loopback requests bypass it. API routes and static public files are outside this gate.

Each page load selects one of ten Turkish passages and one of ten word-position questions. The passage is a committed static PNG served directly from public/human-check-images by the web server; the HTML contains neither the passage nor the answer. One correct answer unlocks the current session for two hours, tied to its resolved IP. Questions expire after ten minutes and are consumed on submission. Reloading replaces the previous question (including in another tab).

## Production setup

- Run `composer install --no-dev --optimize-autoloader`.
- Install/update MaxMind's GeoLite2 Country database at `/usr/share/GeoIP/GeoLite2-Country.mmdb`, or set `GEOIP_DATABASE` to its readable path.
- PHP mbstring is required for answer normalization. This feature does not use GD on the server: all ten PNGs are committed in `public/human-check-images/`.
- Set `HUMAN_CHECK_TRUSTED_PROXIES` to your actual reverse proxy IPs/CIDRs, comma-separated. The list is deliberately empty by default. Only trusted peers may supply CF-Connecting-IP; configure that edge to overwrite this header. Otherwise X-Forwarded-For is walked from the right through trusted hops. Do not list arbitrary visitors or wildcard ranges.
- Use a persistent session backend (file, database, or Redis) and a shared cache for rate limiting on multi-server deployments. Match SESSION_DOMAIN to the hostname; use secure session cookies on HTTPS.
- Keep the CDN/reverse proxy from caching dynamic HTML and the `/human-check` form. Static `/human-check-images/*.png` files may be cached normally. PHP cannot protect an already cached page served directly by a CDN.
- Refresh Laravel's configuration cache after deployment.

`HUMAN_CHECK_ENABLED=true` enables the gate. Set it to false to disable it. Country codes, challenge lifetime, verification lifetime are in `config/human-check.php`.

Missing/unreadable databases and unknown country results challenge all non-local visitors (fail closed). This deliberately avoids silently disabling protection. Monitor database availability operationally.

## Privacy and behavior

The standalone Blade view does not extend the site's analytics layouts. Its content security policy excludes scripts and third-party resources. Challenge HTML responses are private/no-store and noindex. Paragraph PNGs are public static assets, independent of visitor sessions. No quiz answers or visits are explicitly sent to analytics or application logs; ordinary web server access logs still apply. Normal page analytics resume after successful verification.

Failed submissions receive a fresh question. Verification is limited to ten attempts per IP per minute. Laravel CSRF protection remains enabled. A safe same-origin GET destination is saved before the challenge; blocked POST requests are not replayed automatically.

A raster image prevents ordinary text selection, but is not OCR-resistant. This image-only Turkish challenge is not an accessible CAPTCHA for people who cannot read the image; consider an alternative verification path if needed. The small passage bank also limits bot resistance.

## Local preview and checks

Open `/human-check` directly, even on localhost. Use the hostname matching SESSION_DOMAIN so form submission shares the challenge session. Image requests do not use PHP or sessions when the web server serves existing public files normally.

Run `php artisan test --filter=HumanCheckTest`.

Desktop and narrow-window browser captures are saved locally under `storage/app/human-check-preview/` (ignored by Git).

## Updating the committed images

The paragraph catalog lives in `resources/human-check/paragraphs.php`, outside the public directory. To change passages, edit that catalog and run `php scripts/generate-human-check-images.php` locally with GD/FreeType enabled. Commit the updated catalog and all resulting PNGs together. Purge cached PNGs after replacing existing image files. The builder checks line widths to avoid clipped text.

The generation script is an offline developer command, never called by a route or request handler. Deployment only requires pulling the committed PNGs and code; no image generation command is needed on the server. Clear/rebuild route and view caches as usual so the removed dynamic image route and updated template take effect.