# Guru Bhai Khaiwal — ACF PRO edition

**Version 2.1.0 · 27 October 2026**

An installable classic WordPress theme based on the **supplied Satta20 templates, styles and ACF definitions**, with the supplied Scheduled Results Manager's admin organization restored. Guru Bhai Khaiwal branding, the client schedule, and source-reviewed informational records remain in place.

This revision supersedes the native-Customizer design in 2.0. **Licensed ACF PRO is required for theme content administration; the owner has confirmed it is installed on their website.** ACF PRO and license keys are not bundled. The private live Satta20 dashboard has not been inspected: compatibility refers to the supplied ZIP, not a claim of pixel-identical live-site parity.

## Deliverables

- `releases/guru-bhai-khaiwal-theme.zip` — theme directory `guru-bhai-khaiwal`.
- `releases/guru-bhai-khaiwal-results-manager.zip` — retains `scheduled-results-manager/scheduled-results-manager.php` for replacement upgrades.
- `historical-data-import.csv` — **partial**, 523 source-checked draft rows for 2026-08-26–2026-10-26, across 11 games.
- `CHANGELOG.md`, `TESTING-REPORT.md`, `docs/MIGRATION.md`, `docs/ACF-ADMIN-GUIDE.md`, `docs/HISTORICAL-SOURCES.md`.
- `releases/SHA256SUMS.txt` — release checksums.

The original `satta20.zip` and `scheduled-results-manager.zip` remain unchanged. `docs/INPUT-SHA256.txt` records their hashes. Development sources live under `project/`.

## Requirements and installation

1. Back up the entire database and `wp-content`; use staging first.
2. Use WordPress **6.7+**, PHP **8.1+ with mbstring**, HTTPS, UTF-8/utf8mb4 and a supported WordPress database. Prefer a currently security-supported PHP version. Tests use WP 6.7.2 with PHP-WASM/SQLite, not production MySQL certification.
3. Keep your licensed **ACF PRO** active. Do not install unofficial copies. PHP-registered field groups load automatically; no JSON import is needed.
4. Upload and activate the results-manager plugin ZIP, then the theme ZIP. For upgrades, choose **Replace current with uploaded**. Never activate two plugin copies; never delete the old plugin first, because its original uninstall option may erase data.
5. Set the site timezone in **Settings → General** (normally `Asia/Kolkata`). Set a published Home page as the static front page in **Settings → Reading**, before configuring Homepage options.
6. Create an Archives page containing `[gbk_archive]`, or use the **Results: Result Chart** template. Optional templates cover Today, Yesterday, Monthly, Single Game, FAQ, Contact and Blog. Set a single-game page's shortcode explicitly, for example `[srm_game_history game_id="1"]` using a real game ID.
7. Assign menus under **Appearance → Menus**. Configure the site icon in Site Identity. Save permalinks and check your hosting rewrite rules.
8. Open **Guru Bhai Khaiwal** to edit ACF content; open **Scheduled Results** to manage schedules, entries and plugin settings. The plugin menu label can be renamed in its Settings.
9. Complete `docs/ACF-ADMIN-GUIDE.md` and the staging acceptance checklist before launch.

No build tools are required on hosting. Single-site only; network activation is blocked. ACF deactivation does not delete content: the frontend keeps neutral defaults and reads supported saved values, but the content editor is unavailable until PRO returns.

## Theme management — original ACF organization

The original `s20-settings` parent and nine option-page slugs remain:

| Menu | Controls |
|---|---|
| General | Site title, logo/width/link, contact numbers, optional Google Fonts, notices, default SEO description, plugin shortcut |
| Header | Announcement, social/login links, sticky header, date badge, scrolling notice |
| Results | Headings, descriptions, container width and mobile table-scroll hints |
| Contact | Contact-detail repeater, form shortcode, optional built-in form, email recipient and messages |
| Payment | Original fields retained for compatibility, **disabled and never rendered** in this informational edition |
| FAQ | Editable questions/answers, order, visibility and accordion behavior |
| Footer | Branding/background, nested link columns, social visibility, disclaimer and copyright |
| Social | Editable links, icons/custom images and header visibility |
| Shortcodes | Plugin shortcode assignments and fallback behavior; no result data stored in the theme |

Two additional pages:

- **Homepage**: edits the existing front-page post's ACF fields—hero, images, schedule, daily/monthly blocks, editorial cards, contacts, blog, FAQ and drag-to-reorder section list. **Page Sections** flexible content supports full content, image-left and image-right layouts. Add these to the homepage order list. The same fields remain available in the page editor. If no static front page is configured, Homepage uses options storage; configure Reading before editing for predictable storage.
- **Theme Styles**: palette/light surfaces, body and heading font/size/line height, content width, gutters, section spacing, grid gap, header/footer/nav colors and spacing, card color/padding/radius/shadow, button colors/padding/radius, tables and mobile typography/gutters. Values are allowlisted/clamped on save and output. Changes apply after saving; no arbitrary CSS or JavaScript injection field is provided.

**Style precedence:** Theme Styles → Table Source selects theme table styling or plugin table styling. Plugin card colors remain under Scheduled Results → Settings. The default fonts include a locally hosted Devanagari font; Google Fonts are off unless enabled in General. System/serif choices work without external requests. Recheck contrast after customization.

No third-party phones, guarantees or prediction promotions are seeded. Editorial cards and direct-contact blocks start disabled; enable them only with your own appropriate information. Existing saved ACF content is preserved—review it before launch.

## Results administration — restored plugin screens

- **Dashboard**: counts, current/next scheduled entries, countdown and quick links.
- **Games**: bilingual names, local time, display order, active status, duplication and drag ordering. Slugs remain stable on rename. New duplicates start inactive. Deactivate rather than delete to retain history.
- **Daily Results**: date selector, one row per active/assigned game, result, original source name, URL, private notes, verification, publication status and explicit review checkbox. Save individually or Save All. **Publish Reviewed Rows never checks review boxes automatically**; unreviewed rows fail validation.
- **Monthly Results**: month/year navigation, administrative matrix with draft/verification labels, daily review links, print and CSV export. Draft values appear only in authorized admin views.
- **Settings**: original option names retained—plugin label, date/time/result formats, default status, editing lock, current/next visibility, countdown, AJAX interval, nine colors and five numeric styles. The interval refreshes current/next schedule panels; historical tables refresh on navigation. “Published” as the default does **not** bypass source review. Deletion/uninstall-erasure controls are visibly protected.
- **Import / Export**: secure CSV upload/template/export and persistent logs in the original two-column organization.
- **Records & source review**: additional paginated historical desk with game/date/month/status filtering. Preserves the full 2.0 provenance workflow.

Full results managers (`manage_scheduled_results`) and administrators can publish/manage/import/export. **Users → Edit User → Allowed Games** restores scoped assignments: assigned operators may enter/edit drafts for their games only, cannot change published records or approve publication, and cannot manage schedules/settings/imports/exports. Assignments can be changed only by an administrator who can edit that user.

## Publication and time rules

Publication requires **Published + Verified + source URL + original source name + explicit reviewer confirmation**, and records must belong to an active game and a non-future source date. Public tables and REST never reveal draft values or private notes/reviewer identity. Revisions require fresh review; stale forms are rejected. Concurrent writes are not a fully transactional locking system.

Numeric mode accepts digits and grouped numeric separators (`-`, space, `/`), preserving zeroes. Alphanumeric mode allows a limited letter/digit-led string; formulas and markup are rejected. Values are limited to 50 bytes. Date labels belong to the source's actual calendar date; overnight schedules do not silently move them to yesterday.

The plugin seeds the **14 client schedules only into an empty games table**. No historical values are seeded. Disawar remains at 01:00; timezone-aware next/previous occurrences are independent of display order. Schedules are not promises that results will appear at a given time.

## CSV workflow

Required UTF-8 header:

```csv
date,game,result,status,original_game_name,source_url,verification_status,verification_notes
```

- Real ISO dates (`YYYY-MM-DD`), no future dates; exact stored slug, primary name or Hindi name. Unknown or ambiguous games are rejected. Legacy `event` is accepted as the `game` header.
- Maximum **2 MB / 5,000 rows**. Imports are **always drafts**, even when the file requests publication or claims verification.
- Existing game/date records are never overwritten. A repeated import counts duplicates instead of adding copies.
- Logs report successful/imported, skipped blank, duplicate and invalid rows, with UTC time, user ID, file hash and bounded errors. Mixed-validity files are not all-or-nothing transactions.
- Exports include drafts and provenance; spreadsheet formula prefixes are escaped. Import the result column as **Text** so `00` and `07` are not converted.
- Uploads are processed from PHP temporary storage, not published to the Media Library. Keep your source evidence privately.

### Historical coverage is partial

The unchanged delivery CSV contains **523 unique source-checked rows**, including 154 corresponding values corroborated against A1's displayed chart. This verifies transcription of displayed sources, **not official certification of events** or permission to reuse content.

No Model Town, Punjab Matka or HUDA City records are supplied. Four games have October-only coverage. Missing cells and the unresolved 1 October Disawar record remain omitted. Read `docs/HISTORICAL-SOURCES.md` before importing; recheck sources and permitted reuse. Importing does not publish.

## Shortcodes and SEO

`[srm_active_games]`, `[srm_current_game]`, `[srm_next_game]`, `[srm_live_status]`, `[srm_today_results]`, `[srm_yesterday_results]`, `[srm_today_yesterday_results]`, `[srm_full_month_results]`, `[srm_monthly_results month="10" year="2026"]`, `[srm_previous_month_results]`, `[gbk_latest_records]`, `[gbk_archive]`, `[srm_game_history game_id="1"]`.

Existing aliases and the read-only `srm/v1` REST namespace are retained. A missing/deactivated plugin produces an informational fallback, not fabricated results. Theme SEO supplies title/meta description, canonical behavior, WebSite JSON-LD and noindex for filtered/search views; recognized SEO plugins take over head metadata. Normal WordPress posts/sitemaps work. No betting, predictions, payment processing or payouts are included.

## Upgrades, caching and rollback

See `docs/MIGRATION.md`. Existing game IDs, result values/statuses, ACF field identities, options, pages and posts are retained. Old published rows without provenance remain stored but hidden until review. Some 2.0 native settings map to fallback ACF values; the changed homepage structure requires visual review rather than silently copying every native-only section.

Exclude REST and admin from public caches; purge page/CDN caches after source changes or unpublishing. The application does not reuse stale monthly caches. Use low/appropriate page-cache TTLs for schedule pages. Back up before every upgrade; uninstall retains data. Restore **database and files together** for rollback. Do not restore an old public API without considering draft exposure.

## Development and honest test scope

```sh
npm ci
node tools/prepare-runtime.mjs
node tools/lint.mjs
node tools/runtime.mjs
PHP_VERSION=8.1 node tools/runtime.mjs
python tools/package.py
node tools/runtime.mjs --from-zips --serve
# In another process, against the fresh preview:
node tools/browser-test.mjs
(cd releases && sha256sum -c SHA256SUMS.txt)
```

Tests use disposable WordPress/PHP-WASM databases; synthetic test records are not bundled or seeded. The browser run creates fixtures: restart the preview afterward to clear them.

**ACF limitation:** no licensed PRO package is available in this sandbox. Tests capture PHP field/page registration and inspect saved-option rendering; that is **not** real PRO editor/repeater/media/save testing. Validate these on the owner's licensed staging site, plus MySQL/MariaDB, backups, mail delivery, caches, permissions and legal/editorial requirements. See `TESTING-REPORT.md`.

## Licensing

GPL-2.0-or-later for the theme/plugin; see `LICENSE`. The bundled Devanagari font includes its OFL license. ACF PRO has its own license and is not redistributed. Third-party historical source data is not claimed as original or automatically licensed for reuse.
