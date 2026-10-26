# Migration and rollback — 2.1.0

## Before replacing files

Back up the complete database, uploads, themes and plugins; record PHP/WP/database versions and test the backup restoration on staging. Never run both copies of Scheduled Results Manager. **Do not uninstall the original plugin first:** its original destructive option may erase data.

Use the ZIP replacement workflow for the same plugin folder/main file. The new theme uses `guru-bhai-khaiwal`, leaving the original Satta20 theme installed for rollback. ACF PRO remains separately licensed and installed by the owner.

## Results data

- Retained identities: WordPress-prefixed `srm_events` and `srm_results`, existing game/result IDs, slugs, names, scheduled times, date labels, values, publication statuses and original records.
- The 2.0 additive schema introduced provenance (`original_game_name`, `source_url`, `verification_status`, `verification_notes`, `reviewed_by`, `verified_at`), archive indexes and `srm_import_logs`. The 2.1 admin restoration does not rebuild those tables or re-seed populated schedules.
- A 1.1.0 upgrade preserves existing rows but initializes verification to unverified. Consequently, old `published` values are private until reviewed. Check the **Records & source review** desk; do not assume the data disappeared.
- Fresh installations seed exactly the 14 client schedules, only into an empty events table; zero historical records are seeded. Source schedules are not substituted for client times.
- The unique game/date constraint and import-only inserts prevent duplicate overwrites. Import errors/counts are logged. Bounded CSV validation is not a whole-file database transaction.
- New record/log timestamps use UTC; source date labels remain calendar dates. Existing legacy timestamp strings are not silently reinterpreted. Check their provenance before treating them as UTC evidence.
- Slugs remain stable when names change. Duplicates receive a new ID/slug and are inactive initially. Deactivate games or unpublish records instead of deletion.

## ACF compatibility: original Satta20 → 2.1

The PHP-registered groups, original field/layout keys and names are retained, including Payment fields. The nine original option-page slugs and `s20-settings` parent remain. ACF values keep their `options_*` storage and existing post metadata. No manual field-group import is required; resolve any duplicate custom ACF JSON/UI groups on staging first.

Homepage options use the existing static front-page ID. A first administrator visit with PRO copies legacy global homepage values only where that front page has no saved metadata. An explicitly cleared field or zero-row repeater counts as saved and is preserved. The old options are not deleted. The `s20_home_bound_{post_id}` marker makes this binding one-time per front page. Set Reading correctly before configuring Homepage; if no front page exists, options storage is used.

Existing source-site content is **not scrubbed or overwritten automatically**. Review owned logos/contact details, old claims, links, footer disclaimers and images before going live. New defaults are neutral, but existing saved promotional content could still render if enabled. Payment rendering is hard-disabled regardless of its old toggle.

Additional styles use new `style_*` options/`field_s20_styles_*` keys. Existing plugin style options stay `srm_*`; Theme Styles → Table Source controls precedence for tables.

## Native 2.0 theme → 2.1

The 2.0 custom theme panel is replaced by ACF PRO, as requested. Existing theme mods are not deleted. Fallback mappings cover explicitly saved hero title/text/image, announcement, footer disclaimer/tagline, email/phone, SEO description, accent and page background. Custom logo and site icon remain WordPress settings. Saved ACF values take precedence over native fallbacks.

Published `gbk_faq` posts provide initial FAQ repeater rows only if ACF FAQ data is unset; old posts remain in the database. If ACF FAQ data already exists, migrate selected FAQ entries manually rather than overwriting it.

The eight existing `page-templates/...` paths are preserved. Single-game pages retain the `_gbk_game_id` fallback when a page-specific shortcode is absent. The original ACF page editor now exposes that shortcode explicitly.

The reference homepage has a different section structure. Native-only About/Archive/Latest sections and their numeric order settings are retained in theme mods but **not all automatically converted**. Recreate desired extra content with Page Sections, use plugin shortcodes for archives/latest records, and configure Homepage Section Order. Review this intentional layout change on staging.

## Permissions and UI changes

Original plugin menus return: Dashboard, Games, Daily Results, Monthly Results, Settings, Import/Export. The reviewed historical desk is an additional page. Old links to `srm-results` now open Daily Results; historical review links use `srm-review`.

Admin/user `srm_allowed_games` assignments are retained. Assigned operators may read/edit assigned drafts only. They cannot publish, alter published records, manage games/settings, or import/export. Administrator-only profile saves require capability and nonce checks. Full managers retain `manage_scheduled_results`; do not grant it merely to provide draft entry access.

Default Published is a selection preference, not a publication bypass. Every published save requires a valid source, verified state and explicit review. Protected deletion/uninstall controls remain visible but inactive. Old bare CSV files require the new provenance columns and always import as pending drafts.

## Public components and external services

Shortcode aliases, REST namespace/routes and original classic templates remain. Draft/private-note filtering applies consistently to public reads. The previous unreviewed result cache is not used. Current/next panels refresh from the read-only public endpoint at the saved interval; history tables refresh on navigation. Source data is never fetched/imported automatically from third-party sites.

Google Fonts are opt-in. Check privacy/consent requirements if enabled. Built-in contact mail is nonce/honeypot/rate-limited but requires actual mail-delivery and abuse testing on hosting. Prefer a maintained form plugin where appropriate.

## Required upgrade verification

Compare backups against post-upgrade IDs/counts/values/schedules, ACF option names/values, zero-row repeaters and page metadata. Test front-page binding, licensed PRO media/repeaters/flexible layouts, styles, forms, assigned users, daily publication, duplicate imports and public privacy. Test native MySQL/MariaDB and a full backup restore; Playground SQLite is not certification. Purge/exclude CDN caches where required.

## Rollback

1. Restrict writes and take a fresh recovery snapshot of any new data.
2. Restore **database and files together** from the chosen pre-upgrade backup; retain separately any new provenance/import records needed for reconciliation.
3. Restore the previous theme/plugin versions and ACF installation; verify permalinks/cache behavior.
4. Re-audit public API/shortcode draft exposure before restoring the original unsafe publication behavior. A rollback is not automatically safer.

2.1 uninstall intentionally retains plugin data, settings and user assignments. Any final erasure must be a separately authorized, backed-up database operation.
