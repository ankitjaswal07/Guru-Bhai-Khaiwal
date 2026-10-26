# Data migration and compatibility

## Baseline and upgrade path

Inputs are the supplied Satta20 1.0.0 theme and Scheduled Results Manager 1.1.0, not an assumed copy of the live site. Release source lives in `project/`. Input ZIPs remain untouched; SHA256 and extracted file inventory are included separately.

Plugin identity stays `scheduled-results-manager/scheduled-results-manager.php`. The downloadable ZIP and visible name are branded Guru Bhai Khaiwal. Replace the existing plugin in place; never run both copies, which would redeclare `SRM_*` classes.

`SRM_Database`, event table structure, stable IDs/slugs, `SRM_Games` CRUD foundations and the existing full-access capability are reused. The new repository, review desk and CSV/public rendering paths replace unsafe behavior rather than layer a second database on top of it. There are no theme-owned game/result tables.

## Schema 1.1.0 → 2.0.0

`dbDelta()` applies additive changes using the actual WordPress prefix and charset/collation.

### Existing `*_srm_events`

Retained: `id`, `event_name`, `event_name_secondary`, `slug`, `scheduled_time`, `display_order`, `status`, `created_at`, `updated_at`. Original indexes on slug/time/status/order remain. No existing times, names, IDs or visibility flags are reset. Default client schedules are inserted only into an empty events table.

### Existing `*_srm_results`

Retained without rewriting: `id`, `event_id`, `result_date`, `result_value`, `result_status`, `created_at`, `updated_at`; unique `(event_id,result_date)` and original indexes.

Added:

| Column | Initial value / purpose |
|---|---|
| `original_game_name` VARCHAR(191) | Empty; exact original source game label |
| `source_url` TEXT | NULL; public source URL |
| `verification_status` VARCHAR(30) | `unverified`; later verified/disputed |
| `verification_notes` TEXT | NULL; private editorial evidence/context |
| `reviewed_by` BIGINT | 0 until explicit in-dashboard review |
| `verified_at` DATETIME | NULL; UTC review timestamp |
| `public_archive` index | `(result_status,verification_status,result_date)` |

New record saves use UTC timestamps; the public last-updated display converts them to the current WordPress timezone. Original timestamps are not rewritten by migration. A legacy row must be reviewed/saved before it can appear publicly, giving it a fresh UTC update timestamp. Old created-at timestamps remain untouched on edits. REST source-aware update timestamps are labelled UTC.

Legacy `published` statuses are preserved, **but are insufficient for public display**. Publication gates require verified status, source URL, nonempty value, reviewer ID and review timestamp, with an active game and a non-future date. This prevents the supplied unsourced seed values from being silently treated as verified. Drafts remain `pending` at the database layer for compatibility.

### New `*_srm_import_logs`

`id`, `user_id`, `created_at` (UTC), `file_hash` (SHA256), `summary` (JSON with total/imported/skipped/duplicate/invalid and bounded error details). Indexed by creation time. This is import auditing, not a duplicate copy of result data. There is no retention purge or destructive admin control; establish a backup/retention policy for large installations.

The schema version option remains `srm_db_version`. Version 2.0.0 is recorded only after the required result/log columns exist. Failure leaves an administrator notice and a retryable version. No old table/column is dropped. Multisite migration is not implemented.

## Preserved and intentionally changed behavior

- **Preserved:** original table names, result IDs/values/dates/statuses, game IDs/slugs/names/times, all stored old options/user metadata, shortcode names, original theme custom page-template file paths, REST namespace/route names.
- **Changed for safety:** no July/August historical seeder, no auto-publication from CSV, no upsert overwrite during import, strict ISO/calendar/future-date validation, source/review required, public repository reads always gated, no old shared monthly transient data, no destructive uninstall.
- **Capabilities:** administrators receive the full `manage_scheduled_results` capability. The old `srm_manage_game_results` capability and `srm_allowed_games` user metadata are retained but do not authorize the new desk. Full management must be granted deliberately; game-scoped editorial delegation is a documented outstanding compatibility boundary, not an implicit privilege escalation.
- **Old AJAX writers:** no longer registered. Old admin JS/forms must not be used to bypass the new nonce-protected review workflow. The new admin forms use `admin-post.php` with action `gbk_manage`, POST, capability checks and nonce verification.
- **CSV:** old files lacking source/verification columns are rejected with a current-template instruction. `event` remains accepted as a header alias for `game`. `published` is accepted as a file value but stored as pending. Exact source labels are retained separately from normalized game resolution.
- **Shortcodes:** all original tags remain registered. Active-game aliases show cards; today/yesterday show verified record groups; monthly aliases show source-linked grids. `srm_current_game_name` and `_time` show the most recent scheduled entry. `srm_live_status` shows the next informational schedule entry without live polling; `srm_current_result` / `srm_latest_game_result` show latest published records. Legacy CSS class names, custom wrapper/display attributes and countdown animation are not preserved. Use `[gbk_archive]` for current filter controls rather than old `show_filter` UI attributes. Game history supports game/event IDs and optional month/year attributes; legacy `srm_*` filter query names are recognized by archive filtering.
- **REST:** `/srm/v1/games` and `/events` keep the named game fields. `/monthly` keeps month/year/games/rows, with nulls for unavailable values, and adds source-aware results. `/today` returns published rows only (no speculative pending values). `/current` keeps current/latest/next schedule concepts with source-gated current values and calendar-correct dates. External integrations must re-test their assumptions about missing rows, status and markup.
- **Formatting:** public date/time display now follows WordPress General settings. Old `srm_*` style/countdown/date-format options remain stored but are not the active source of presentation settings.
- **Caching:** legacy transient keys are neither read nor reused. Old transient rows may expire normally. Host/plugin/CDN full-page caches remain the site owner's responsibility.
- **Deactivation:** visibility changes do not delete records. Public views omit inactive games; historical data remains accessible to administrators.

## Theme and ACF content

The classic section dispatcher, separate presentation/data responsibilities, WordPress template hierarchy and eight page-template paths follow the supplied architecture. The presentation and settings implementation were substantially revised because the source depended on unconfirmed ACF PRO licensing and embedded promotional/third-party defaults.

No ACF option or post metadata is erased. The new theme does **not** automatically migrate proprietary repeater values or promotional/payment copy. Before switching:

1. Export/screenshot old theme settings and record page-template assignments.
2. Review which content is appropriate for an independent informational site.
3. Copy approved title/hero/about/contact/footer/SEO copy into native Customizer settings.
4. Reassign menus, logo and favicon; check new page-template behavior.
5. Recreate appropriate FAQs as native FAQ entries and choose games in the native page dropdown.
6. Use Posts/Pages for real editorial content. Source-site phone numbers, guarantees, payment offers and prediction copy must not be blindly imported.

FAQ content is stored as normal WordPress posts of type `gbk_faq`. Switching themes does not erase it, though its editing menu is supplied by this theme and will disappear until it is registered again.

## Retention and rollback

New uninstall is intentionally a no-op for data. It does not honor the old destructive `srm_delete_on_uninstall` option. Keep original ZIPs plus a **matching complete database/files backup**, not only CSV exports. Restore both on staging/maintenance when rolling back. A downgrade does not automatically remove additive columns; do not manually drop them unless deliberately restoring the whole old database backup. Original-plugin security problems would return with a downgrade.

Tested: clean install, repeat activation and an isolated original-schema fixture upgraded through `dbDelta()`, preserving a legacy result's ID, date, leading-zero value and published status while adding unverified provenance. Tests used WordPress's SQLite adapter, so real MySQL/MariaDB and hosting acceptance remain mandatory.
