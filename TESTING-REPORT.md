# Testing report — Guru Bhai Khaiwal 2.0.0

Date: 26 October 2026. Scope: supplied-code inspection, revised theme/plugin, partial historical CSV and installation packages.

## Environment

- Clean **WordPress 6.7.2**, obtained from the official WordPress GitHub mirror; archive SHA256 pinned by the development setup script.
- **PHP 8.1.34 and 8.3.33** via WordPress Playground / PHP-WASM, with WordPress's SQLite integration adapter.
- Chromium **138** through Playwright; axe-core **4.10.2** accessibility checks.
- Browser sizes: **1440, 768 and 390 CSS pixels** wide. Populated historical table also tested at 390px.
- No native PHP-FPM, MySQL/MariaDB server or actual hosting credentials/environment were available. SQLite compatibility is not a substitute for hosting acceptance.

## Completed tests

| Area | Result |
|---|---|
| PHP syntax | All 37 release PHP files parse under PHP 8.1 |
| Clean activation | Theme/plugin activate together; 14 schedules, zero seeded historical results |
| Default schedule | All 14 times, bilingual fields and ordering match the client configuration |
| Midnight logic | Before/after midnight and exact 1:00 AM boundary use the correct next local calendar date; noon/midnight parsing and invalid-time rejection pass |
| Record lifecycle | Draft save, source validation, explicit review, publication, source-linked display, unpublish and stale-form detection pass |
| Public privacy | Drafts/unsourced legacy rows are excluded from public getters, current/today/monthly REST routes and tested shortcodes, even with an admin public-page session |
| Authorization | Unauthorized/scoped legacy user writes/imports rejected; private-read flag cannot bypass capability checks; actual bad admin nonce returns HTTP 403; mutation GET returns HTTP 405 |
| CSV validation | Impossible/future dates, formula values, unknown game, incomplete provenance header, duplicate rows and blank rows handled as specified |
| CSV end-to-end | Actual upload handler processes all **523** delivery rows; source fields remain private until approval; actual export includes provenance and preserves leading-zero strings |
| Duplicate protection | Second full CSV import skips **523 duplicates**, imports zero and does not overwrite |
| Import log | Per-file hashes, users, UTC timestamps and imported/skipped/duplicate/invalid counts recorded; mixed-row fixture counts verified |
| Archives | Date/month filtering and pagination work; 523-row fixture gives 18 pages at 30 rows; September filter gives exactly 203 records |
| Migration | Isolated 1.1.0-schema fixture upgraded with dbDelta; original ID, date, leading-zero value and publication status retained, provenance defaults unverified; existing schedules not reseeded |
| Repeat activation | Schedule and record values/IDs preserved |
| Templates | Eight custom page templates, single post, category archive, search and XML sitemap requested successfully through WordPress's actual template loader |
| Homepage controls | Native section order/visibility changes affect public output; Customizer branding/homepage panel loads without the earlier color-control JS error |
| Plugin unavailable | Theme renders a truthful fallback, not a fatal error or invented records |
| Admin browser flows | Login, four management screens, schedule creation/deactivation, draft save, source review/publication and unpublish exercised through real forms |
| Responsive UI | No page-level horizontal overflow at the three viewport widths; populated tables scroll within their own labelled region |
| Keyboard navigation | Mobile menu open/close and Escape focus restoration work; filters and scrollable tables expose keyboard controls |
| Accessibility automation | **Zero detected WCAG 2 A/AA and WCAG 2.1 AA axe violations** on default homepage at all three widths and on the populated archive page; not a full accessibility certification |
| SEO | One homepage H1, single singular canonical, noindex on filtered archive, 404 status retained, core sitemap responds; source-aware public REST has noindex header |
| Logs | No PHP warning/fatal/deprecation in the final integration/template run; no browser JS errors in the completed browser workflow |
| Source data | All 523 rows within requested dates, unique game/date keys, exact string values, 154 cross-source matching cells, one unresolved cell withheld; no invented source data |
| Original files | Both supplied ZIP hashes still match the pre-development baseline |
| ZIP activation | Both packaged ZIPs unpacked into a fresh WordPress installation and activated; the same integration/template suite passed on PHP 8.1 and 8.3 |
| Package structure | One correct WordPress folder per ZIP; no Node modules, runtime DB, test fixtures, credentials or original ZIP nested in releases |

The integration script contains **56 assertions**, followed by 12 template/sitemap requests and homepage/plugin-disabled/log checks, on both PHP runtimes. Tests create explicitly labelled synthetic fixtures only inside disposable test databases, then remove them. They are not part of the historical CSV or initial plugin data.

## Defects caught and corrected during testing

- A positioned table-scroll region now contains the visually hidden source-link label, preventing that absolute-positioned label from widening mobile documents.
- Default muted label/footer colors below AA contrast were darkened/lightened appropriately.
- WordPress emoji image fallback was disabled for the public theme to avoid remote broken-image arrows in the restricted network environment.
- The Customizer's native color control class is now used so WordPress enqueues its color-picker dependency.
- A one-page blog pagination path now casts an empty pagination result to a string, removing a PHP 8.1 deprecation.
- Browser automation adapters were corrected for redirect interception, multipart file-byte omission in CDP and the DOM `form.action` property being shadowed by the form's action input. These were test-transport issues; the actual upload/export application handlers are exercised after correction.

## Reproducibility

Source: `tests/integration.php`, `tools/lint.mjs`, `tools/runtime.mjs`, `tools/browser-test.mjs`, `tools/build-history.py`, `tools/package.py`. Follow the README for setup and commands. Run browser tests on a **fresh disposable preview**; they deliberately create/update test content.

The sandbox browser proxy adapts redirects and reconstructs only the known CSV fixture's omitted file bytes to reach the local server under the externally proxied HTTPS site URL. The production plugin uses ordinary WordPress upload handling; no proxy adapter is shipped. A live native-host upload/export check remains required.

## Known limitations / release gates

1. **Actual hosting is untested.** Validate a maintained WordPress/PHP release, mbstring, MySQL/MariaDB dbDelta behavior, permissions, HTTPS, rewrite rules, uploads and opcode/page/CDN caches on staging before launch. PHP 8.1 was tested for compatibility, not recommended as a security-supported production target.
2. **Historical dataset incomplete.** No checked rows for Model Town/Punjab Matka/HUDA City; four games have only October data. “Verified” means checked against what a public source displayed, not official/independent certification. All imports remain drafts; every publication requires human review. See source documentation and withheld-cell list.
3. **Existing production database unavailable.** Data retention tested with an original-schema fixture and repeat activation, not a copy of the client's live database. Make and restore-test a full backup first.
4. **No multisite/network activation support.** Network activation is explicitly blocked. Legacy scoped-game editorial delegation is not restored automatically; full review-desk capability is deliberately restricted.
5. **ACF content is not auto-migrated.** Legacy options remain intact, but approved copy/images/menu settings must be migrated manually. Promotional, payment and third-party contact defaults were intentionally excluded.
6. **External integrations need acceptance tests.** Legacy shortcode names/routes remain, but presentation/countdown behavior and some API missing-record semantics changed. Review migration notes. The full Yoast/Rank Math/AIOSEO/SEOPress matrix and custom child themes were not installed/tested.
7. **Not a full security/accessibility/performance certification.** No exhaustive penetration test, PHPCS/WPCS certification, screen-reader audit, Lighthouse benchmark or Safari/Firefox/physical-device matrix was completed. Keyboard, responsive and automated axe checks are reported narrowly.
8. **No source scraper or auto-publication.** Reference sites were readable through the research tool; ordinary sandbox HTTP access to them is restricted. Ongoing source availability, permitted reuse and legal/privacy approval remain site-owner responsibilities.
9. **External caching may delay visible changes.** The plugin avoids its own shared result cache; configure/purge hosting and CDN caches explicitly.
10. **Development tools only:** Node/npm/PHP-WASM/Chromium are not runtime dependencies and are excluded from installation files. The sandbox used Node 22.22.3 asyncify successfully despite newer declared Playground engine preferences. Prefer supported current Node/npm when reproducing development tests. Final npm audit reported zero known dependency vulnerabilities after a qs override; this is not a security guarantee for WordPress or hosting.

**Conclusion:** working installable code with verified local integration/browser coverage. Production launch approval remains conditional on the hosting, editorial, migration and legal checks above; neither complete historical coverage nor actual-host certification is claimed.
