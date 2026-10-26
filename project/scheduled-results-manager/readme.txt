=== Guru Bhai Khaiwal Results Manager ===
Requires at least: 6.7
Tested up to: 6.7.2
Requires PHP: 8.1
Stable tag: 2.0.0
License: GPL-2.0-or-later

Informational schedules and source-reviewed historical archives. Compatible data-preserving upgrade of the supplied Scheduled Results Manager 1.1.0.

== Installation ==
Back up database and files first. Upload this ZIP under Plugins and activate. For an existing installation, replace scheduled-results-manager in place, never activate two copies. PHP mbstring is required. Network activation is unsupported.

Open Guru Bhai Records to manage schedules, records, CSV imports/exports and source review. Set site timezone/date/time in WordPress Settings / General. The theme is optional for data management.

== Important ==
No historical results are seeded. Imported rows are always drafts. Source URL, verification and explicit administrator approval are required before publication. Legacy values remain in the database but are private until verified. Deactivation and uninstall retain all data.

See the bundled README.md, docs/MIGRATION.md and TESTING-REPORT.md for setup, compatibility boundaries and hosting acceptance requirements. The companion historical CSV is a partial source-checked dataset, not complete coverage or official certification.

== Shortcodes ==
[srm_active_games]
[gbk_latest_records]
[srm_today_yesterday_results]
[srm_monthly_results]
[srm_previous_month_results]
[gbk_archive]
[srm_game_history game_id="1"]

Original shortcode names and REST namespace are retained; legacy live/countdown displays now use informational rendering without polling. No betting, payments, recommendations or payouts are provided.

== Changelog ==
= 2.0.0 =
Add provenance and review workflow, secure duplicate-safe CSV, calendar-aware schedules, source-gated public data, native administration, additive migrations and non-destructive retention policy.
