# Testing report — Guru Bhai Khaiwal 2.2.0

27 October 2026. Revision: supplied table layouts/styles, distinct Hindi-first homepage, complete editable starter-page setup, private provenance and explicit historical import/review.

## Environment and limits

Real WordPress 6.7.2, PHP-WASM 8.1.34/8.3.33, SQLite integration; Chromium/Playwright/axe. **No licensed ACF PRO package was available locally.** The owner has PRO remotely. Registration/storage tests do not certify its real editor, repeater/media picker or save UI. No production MySQL/SMTP/CDN/hosting certification. Test fixtures exist only in disposable runtimes; delivered history is not auto-approved.

## Validation

- PHP syntax: **86 PHP files** parse on PHP 8.1.
- Repository: **56 assertions**, retaining privacy, review, dates/timezones, legacy migration and duplicate-safe CSV behavior.
- Restoration: **319 assertions**, including retention of all **278 original field/layout keys** and original administration/settings contracts.
- Structure revision: **32 assertions**: two-column game table; single four-column daily table; current/prior month bounds; times and filter controls; independent previous month; fixed-game constraint; unique IDs; invalid filters; 24 starter routes/14 game mappings; preserved edited content/front-page selection; shortcode flexible layout; bounded/signed/confirmed approval; stale/tampered/anonymous rejection; private provenance; byte-identical protected CSV bundle; 523-row draft import and duplicate-safe repeat.
- WordPress template loader: all starter page/article routes, the assigned WordPress posts page, and all eight templates, plus article/category/search/sitemap; homepage visibility/order; plugin-disabled fallback; no PHP debug errors.
- Browser: **76 checks passed**, zero tested axe violations and no browser JavaScript errors. Homepage at **1440/768/390px**, table rows, no viewport overflow/broken assets, mobile menu/Escape, axe checks; populated fixture archive; all restored admin screens; daily AJAX review and unpublish; plugin settings; CSV upload/export; new setup and history approval through actual forms; bad nonce and GET rejection; page-section rendering on requested pages; fixed-game filtering and no public source links. No genuine historical rows approved in tests.
- Fresh ZIP installation is tested, not only source mounts. Evidence: `docs/QA-STRUCTURE-PHP81.txt`, `docs/QA-STRUCTURE-PHP83.txt`, `docs/QA-STRUCTURE-BROWSER.txt`.

Earlier `QA-ACF-*` evidence describes 2.1, not this revision. Failed development runs exposed outdated card selectors, a Hindi font fallback issue and contrast issues; those were corrected before final validation. Browser test expectations now assert table contracts rather than the rejected 2.1 card layout.

## Data and publication

The historical CSV remains **523 records / 11 games**, 26 August–26 October 2026, incomplete. It is stored as a PHP-guarded payload in the plugin and imports only via explicit action. No Model Town/Punjab/HUDA history or fabricated fillers are added. Public tables can be empty until a human manager checks and approves the records. Today is outside the delivered historical window. Admin/export keeps provenance; public table HTML and REST omit it.

## Required staging acceptance

Test licensed ACF PRO: all original option groups; clear/save fields; reorder homepage; add text/image/shortcode sections on every template; media/logo; styles; contact details. Review all Hindi starter prose, real mail delivery, plugin permissions, source approvals, public unpublish/cache purge, MySQL and backups. Bulk approval is bounded and snapshot-checked but not a fully transactional locking system. Automated axe checks are not full accessibility certification.
