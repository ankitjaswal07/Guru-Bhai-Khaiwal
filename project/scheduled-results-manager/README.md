# Guru Bhai Khaiwal

A redesigned classic WordPress theme and a compatible **data-preserving upgrade** of the supplied Scheduled Results Manager. Informational schedules and source-reviewed historical archives only—no wagering, payments, predictions or payout functions.

**Release:** 2.0.0 · 26 October 2026  
**Readiness:** installable and integration-tested in WordPress Playground. Complete the hosting acceptance checklist below before a production launch. This is not a claim of validation on an unknown hosting environment.

## Deliverables

| File | Purpose |
|---|---|
| `releases/guru-bhai-khaiwal-theme.zip` | Uploadable theme; folder `guru-bhai-khaiwal` |
| `releases/guru-bhai-khaiwal-results-manager.zip` | Uploadable plugin; **folder and entry point remain `scheduled-results-manager/scheduled-results-manager.php`** for in-place upgrades |
| `historical-data-import.csv` | **Partial** dataset: 523 source-checked records across 11 games, 2026-08-26–2026-10-26; imports as drafts |
| `CHANGELOG.md` | Changes relative to the supplied code |
| `TESTING-REPORT.md` | Tests, limitations and production acceptance gates |
| `docs/MIGRATION.md` | Schema, compatibility boundaries and rollback |
| `docs/HISTORICAL-SOURCES.md` | Evidence, mappings, verification meaning and missing coverage |
| `releases/SHA256SUMS.txt` | Checksums of the installation ZIPs and companion delivery files |

Original `satta20.zip` and `scheduled-results-manager.zip` are retained unchanged at the repository root. Their hashes are in `docs/INPUT-SHA256.txt`. Development source is in `project/`; original extracted inspection copies are excluded from releases.

## Requirements

- WordPress **6.7 or newer**. Use a currently maintained WordPress release for production. Functional compatibility was tested on 6.7.2, not every later release.
- PHP **8.1+ with mbstring**, a supported WordPress database, UTF-8/utf8mb4 and database CREATE/ALTER permissions for activation/upgrades. Prefer a currently security-supported PHP release such as 8.3+; the 8.1 test is compatibility evidence, not a deployment recommendation.
- HTTPS, ordinary WordPress upload permissions and adequate disk/database backup space.
- **No ACF or ACF PRO license required.** No Elementor, Composer, Node, external font service, API key or third-party runtime dependency is required on the hosting server.
- Single-site deployment tested. **Multisite/network activation is not supported by this release**; do not network-activate it.

## Clean installation

1. Back up the database and `wp-content`. Use a staging site first.
2. **Plugins → Add New → Upload Plugin:** upload `guru-bhai-khaiwal-results-manager.zip`, install and activate.
3. **Appearance → Themes → Add New → Upload Theme:** upload `guru-bhai-khaiwal-theme.zip`, install and activate.
4. **Settings → General:** set title to **Guru Bhai Khaiwal**, edit the tagline, set the appropriate timezone (normally `Asia/Kolkata` for this project), and choose date/time formats. The release deliberately does not overwrite an existing site's identity or timezone.
5. Create a published **Home** page and set it as the static homepage in **Settings → Reading**. The homepage uses the theme's section renderer. Homepage body sections are edited in the Customizer rather than the page body.
6. Create a published **Historical archives** page with the shortcode `[gbk_archive]`. Add a short editorial explanation of sources and missing records. Choose this page in **Appearance → Customize → Guru Bhai Khaiwal → Brand, contact & SEO → Archive page**.
7. Optional: create an **Articles** page and assign it as the Posts page under Reading. WordPress supplies normal paginated post archives. Alternatively use the Informational Articles page template.
8. **Appearance → Menus:** assign header and footer menus. Without a menu, working homepage anchor links are shown. Update menus if you disable their destination sections.
9. **Settings → Permalinks:** choose a descriptive structure, save once, and confirm `/archives/`, post pages and `/wp-sitemap.xml` work with your hosting rewrite rules.
10. Review all branding, notices, contacts and FAQs before launch. Add your own contact details; no third-party phone numbers or invented business address are prefilled.

The plugin inserts the 14 requested schedules **only if the games table is empty**. No historical results are seeded. The theme adds four editable informational FAQ entries once, only when no FAQ content exists. It does not generate fake blog posts.

## Upgrading an existing installation

**Do not activate both old and new copies.** The plugin ZIP intentionally retains its original directory and main PHP filename. Use WordPress's “Replace current with uploaded” workflow on staging, or deactivate the original plugin and replace that directory by SFTP. Do not click Delete on the old plugin: its old uninstall setting may erase data.

The new theme is a separate theme folder. Activating it does not delete the Satta20 theme, ACF options, pages or posts. Existing custom page-template paths are retained, but branding and homepage settings need a deliberate manual review/migration to native Customizer controls. Old payment/promotional content and third-party contacts are not copied automatically.

**Important publication change:** old result IDs, dates, values and `published`/`pending` statuses are preserved. New provenance columns initially mark legacy rows **unverified**, so those rows are hidden publicly until reviewed. This is intentional, not lost data. Existing schedules are never silently overwritten by the new defaults. See `docs/MIGRATION.md` before upgrading.

## Administration guide

### Branding and homepage

Open **Appearance → Customize**:

- **Site Identity:** site title/tagline, uploaded logo and site icon/favicon.
- **Guru Bhai Khaiwal → Brand, contact & SEO:** colors, system/serif heading style, fallback logo initials, hero text/buttons, informational notices, contact details, two social URLs/labels, archive page, SEO default description, about/contact text and footer copy.
- **Each numbered homepage section:** show/hide checkbox, numeric display order, heading, description and optional Media Library image. Lower order numbers appear first; equal values retain the default sequence. Use the dedicated Hero title/introduction fields for its H1 and main copy; the Hero section heading is its accessible section label.
- Image descriptions/alt text are managed in **Media**. No generated reference-site banners are used.
- **FAQs:** edit questions as titles and answers as content in the WordPress FAQs menu; use page order to rearrange them. Unpublish to remove. Up to 50 published FAQs render; no thin individual FAQ URLs are exposed.
- **Posts:** publish real informational articles with excerpts and optional featured images. The homepage shows three latest published articles.

Default colors passed automated contrast checks. Re-test contrast after changing colors. English interface text and bilingual game names are supported; a complete Hindi translation of all admin/system labels is not supplied.

### Schedule management

**Guru Bhai Records → Daily schedule**:

- Add/edit primary and Hindi names, local time, display order and Active visibility.
- The CSV slug is shown in the table. Slugs are stable when a game is renamed.
- Uncheck Active to deactivate without deleting history. Destructive game/result deletion is intentionally absent from the review desk; unpublish records instead.
- All 14 defaults, including **Disawar at 1:00 AM**, live only in the plugin's database seed—not scattered through templates.
- Times use **WordPress Settings → General**. “Next scheduled entry” is computed chronologically, independently of display order, using actual local dates. An entry after midnight belongs to that calendar date, not automatically to yesterday. Historical source date labels are never shifted to match schedule order.
- These are schedules, not promises that a result has been or will be published at a particular time. There is no automatic result fetch or countdown polling.

### Add, review and publish a record

**Guru Bhai Records → Records & review**:

1. Choose a game and actual source calendar date.
2. Enter the exact historical value, preserving leading zeroes. Values accept digits with optional numeric group separators (`-`, space or `/`), up to 50 characters; formulas and arbitrary markup are rejected.
3. Enter the **original game name on the source**, a valid HTTP(S) source URL and optional private verification notes.
4. Save as Draft/Pending if not yet checked. Source verification and publication are separate states.
5. Open **Review / edit**, visit the source, verify game/date/value, select **Verified against source**, select **Published**, check the explicit review confirmation and save.
6. Publication requires the source, verification, reviewer ID and UTC review timestamp. Revising a published record requires renewed confirmation; unpublishing removes it from public components immediately at the application level.

Public tables, shortcodes and REST responses exclude drafts, unverified/disputed records, inactive games and future-dated records. Use the date, month/year, game and status filters in the desk. Lists paginate at 30 rows. Exact date takes precedence over month. A stale edit form is detected and asks you to reload.

Only administrators or explicitly trusted users with `manage_scheduled_results` may use this desk. **Legacy game-scoped operators are not automatically permitted** to publish, import, export, or manage schedules in 2.0. Their user metadata is retained. Do not grant the full management capability to an untrusted user merely to restore old scoped access.

### CSV import and export

**Guru Bhai Records → CSV & import logs** provides the current empty template, file upload, date-range export and paginated import history.

Required UTF-8 header:

```csv
date,game,result,status,original_game_name,source_url,verification_status,verification_notes
```

Rules:

- ISO dates only (`YYYY-MM-DD`), real calendar dates, no future dates. No locale-ambiguous dates.
- Use a stored slug (recommended), exact primary name or exact Hindi name. Whitespace/case normalization is applied; ambiguous names are rejected. Legacy `event` header is accepted as `game`, but provenance columns are now mandatory.
- `status`: `draft`, `pending` or `published` accepted in the file; **all imported rows are saved as pending drafts regardless**.
- `verification_status`: `unverified`, `verified` or `disputed`. A file's `verified` claim does not populate a reviewer or authorize publication.
- **2 MB / 5,000 rows maximum** per upload. Header, encoding, column count, date, source, game and value validation happen before each row is stored. Oversized row-count files are rejected before writes.
- A unique `(event_id, result_date)` constraint prevents duplicates. Repeated imports never overwrite an existing row—even if the incoming value differs. Resolve corrections explicitly in the review desk.
- Log counts distinguish imported, skipped blank, duplicate and invalid rows; each log stores UTC time, user ID, file SHA256 and up to 100 row-level errors. Mixed-validity files import valid rows and log rejected ones; they are **not all-or-nothing transactions**.
- CSVs are processed from PHP's temporary upload location, not published to the Media Library. Raw uploads are not retained; retain your source file and evidence in your private backup process.
- Exports include drafts, original source names, source URLs and verification fields. Spreadsheet formula-leading text is prefixed with an apostrophe. Import the result column as **Text** in spreadsheet software so it does not convert `00` or `07` into numbers.

### Delivered historical CSV

`historical-data-import.csv` contains **523 source-checked rows**, not a complete two-month dataset. The 62-day inclusive window is 26 August–26 October 2026. 154 included cells also match the A1 October chart; others were checked against a single displayed source. This checks what those websites displayed, **not an independent certification of the underlying events**.

No rows are provided for Model Town, Punjab Matka or HUDA City. Four other games have October-only coverage. Missing cells and an unresolved 1 October Disawar entry are omitted. Do not substitute Dwarka for HUDA City, HR Satta for HR Matka, or Delhi Matka for Punjab Matka. Source schedules differ from the client schedule; source times are not imported.

Import this CSV only after reading `docs/HISTORICAL-SOURCES.md`, checking permitted reuse and rechecking the original sources. Importing it does not publish it. It is not automatically loaded on activation.

## Pages, shortcodes and SEO

| Shortcode | Use |
|---|---|
| `[srm_active_games]` | Bilingual daily schedule cards |
| `[srm_next_game]` | Next calendar-aware informational schedule entry |
| `[gbk_latest_records]` | Six most recently dated published records |
| `[srm_today_results]`, `[srm_yesterday_results]` | Verified records for the site's local dates |
| `[srm_today_yesterday_results]` | Both date groups |
| `[srm_monthly_results month="10" year="2026"]` | Source-linked monthly grid; attributes optional |
| `[srm_previous_month_results]` | Previous month relative to WordPress's current date |
| `[gbk_archive]` | Date/month/game filters and paginated history |
| `[srm_game_history game_id="1"]` | Game-scoped, filtered archive |

The old plural/singular active-game aliases, previous-month aliases and `srm_event_history` remain registered. Legacy countdown/live aliases now render informational components without polling. Some display attributes and markup differ; see migration notes. Multiple independent search forms should not be placed on the same page because they share the same filter query parameters.

The original eight custom page-template paths remain available: archives, today, yesterday, monthly, game history, contact, FAQ and blog. The Single Game History template has a native **game dropdown** in the page editor. A page's excerpt can supply its meta description.

- Native WordPress titles, language attributes, breadcrumbs, canonical handling, descriptions, Open Graph metadata and WebSite JSON-LD.
- Core XML sitemap compatibility; use native published Pages for indexable, descriptive archive landing URLs. Do not generate hundreds of empty game/month pages.
- Filtered result URLs and internal search are `noindex`; the unfiltered archive page remains indexable. Core handles admin indexing protections; public plugin REST routes add `X-Robots-Tag: noindex`.
- Theme head metadata defers to detected Yoast, Rank Math, AIOSEO or SEOPress to avoid duplicates. When using an SEO plugin, explicitly preserve noindex rules for filter/search parameters in that plugin and verify its generated canonicals/robots. These integrations were not all installed in this test environment.
- Legacy REST namespace `/wp-json/srm/v1/` retains `games`, `events`, `today`, `monthly` and `current`. Monthly legacy `games`/`rows` keys are retained, with an additional source-aware `results` collection. Public results never include private verification notes or reviewer identity. No write REST endpoint is exposed.

## Backups, update and rollback

1. Take a full database export (including `*_srm_events`, `*_srm_results`, `*_srm_import_logs`, options, posts and user metadata), a copy of `wp-content`, and the original ZIPs before any change.
2. Verify restoration on staging. Database credentials belong in the hosting environment, never in these packages or Git.
3. Replace the plugin in place, activate the new theme, review legacy records and test all workflows. Deploy only after the hosting acceptance checklist passes.
4. For rollback, put the site in maintenance mode, restore the matching database **and** file backup, then clear PHP opcode/page/CDN caches and recheck URLs. A CSV export alone is not a full rollback backup and omits some system metadata.
5. Do not delete the old plugin through WordPress while its original “delete on uninstall” option is enabled. **The new plugin always retains data on deactivation/uninstall**, deliberately ignoring that destructive old option. Permanent data removal requires an intentional, backed-up DBA operation.
6. Reverting to the original plugin can restore its old draft-disclosure and unsourced-seeding behavior. Prefer fixing forward or rolling back privately; do not assume the original is secure just because it is retained.

## Troubleshooting

- **Empty results after upgrade:** check verification, source, explicit approval, publication status, active game and date. Legacy rows are not lost; they are unverified until reviewed.
- **Only old schedules appear:** this is preservation by design. Edit retained rows and add missing client games in the desk; do not reset IDs or delete tables.
- **CSV unknown game:** use the schedule's displayed slug. Add missing games first on an existing install. Original source names belong in `original_game_name`, not an assumed alias mapping.
- **Database migration warning:** verify CREATE/ALTER privileges and database logs. The plugin does not mark a failed migration complete; restore staging backup if needed. Do not bypass schema checks.
- **Wrong time/day:** set the named WordPress timezone; do not change the PHP server clock to compensate. Date labels follow the source.
- **Changes not visible:** clear host page/CDN caches. This plugin intentionally has no shared result transient cache. Exclude frequently changing schedule/archive pages from long-lived full-page caching, or configure your cache plugin's purge policy.
- **No contact form:** intentional. Publish your actual email/telephone/social channel. A vetted form plugin may be added to a normal Contact page if needed, with your own privacy/spam policy.
- **Fonts/branding:** Hindi glyphs use a self-hosted Noto Sans Devanagari subset; other text uses system fonts. Logo/site icon override the bundled typographic fallback. Clear browser caches after replacing assets.
- **404s on clean URLs:** save Permalinks and check Apache/Nginx rewrite configuration.

## Development and reproducible tests

Node/Python tooling is **development only**, never included in either installable ZIP. See `tools/` and `tests/`. Install with `npm ci`. Download WordPress 6.7.2 from the official WordPress GitHub mirror into `.runtime/wordpress.zip` (see `tools/prepare-runtime.mjs`). Then:

```sh
node tools/prepare-runtime.mjs
npm test
npm run test:php81
npm run preview
npm run test:browser   # against a fresh disposable preview; see tools/prepare-runtime.mjs for Chromium libraries
python tools/build-history.py
npm run package
PHP_VERSION=8.1 node tools/runtime.mjs --from-zips
```

The preview/testing database is disposable and in-memory. Tests create and remove clearly labeled fixture records, never put invented data in the release CSV, and must **not** be run against production. Runtime archives, credentials, screenshots and databases are ignored by Git. See the testing report for runtime versions and transport adapters used for sandbox browser tests.

## Production acceptance checklist

- [ ] Restore-tested database/files backup and rollback owner assigned.
- [ ] Install/update on the actual supported PHP + MySQL/MariaDB host; no PHP/SQL errors.
- [ ] HTTPS, rewrite rules, uploads, security headers and caches verified.
- [ ] Real title, logo/site icon, contact details, notices and menus approved.
- [ ] Existing ACF/page content manually reviewed and migrated where appropriate.
- [ ] Existing-user capability policy approved; no unintended full-manager grants.
- [ ] CSV provenance/reuse rights checked and each record reviewed before publication.
- [ ] Real-device/browser and assistive-technology checks, including custom brand colors.
- [ ] SEO-plugin metadata, sitemap and filtered-page noindex checked if applicable.
- [ ] Site owner's legal/privacy review completed for the intended jurisdiction and informational use.

## Attribution and licensing

Derived from the client-supplied Satta20 theme (author credit: Satta20, GPL v2 or later) and Scheduled Results Manager 1.1.0 (GPL-2.0-or-later). The release code is GPL-2.0-or-later; see `LICENSE`. New branding is independent, not an affiliation with any reference site. The self-hosted Noto Sans Devanagari font is under the SIL Open Font License, bundled alongside the font. Historical source excerpts contain factual table cells for traceability; no reference-site advertising text, images, logos or contact details are republished.
