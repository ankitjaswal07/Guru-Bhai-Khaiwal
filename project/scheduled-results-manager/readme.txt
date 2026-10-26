=== Guru Bhai Khaiwal Results Manager ===
Requires at least: 6.7
Requires PHP: 8.1
Stable tag: 2.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Informational schedules and source-reviewed historical records, preserving the original Scheduled Results Manager identities.

== Description ==
Restores Dashboard, Games, Daily Results, Monthly Results, Settings and Import/Export. Adds provenance, explicit publication review, draft-only duplicate-safe CSV, retained history, scoped draft operators and an additional review desk.

The data plugin does not require ACF. The companion theme uses the owner's licensed ACF PRO; it is not bundled.

== Installation ==
Back up and test on staging. Replace the existing scheduled-results-manager folder in place; do not uninstall the original first. PHP mbstring required. Single-site only; no network activation. Existing schedules/data are retained. Legacy published rows require source review before public display. See README.md and docs/MIGRATION.md.

== Changelog ==
= 2.1.0 =
Original administrative structure/settings restored with source-review security, scoped draft access, frontend styles and calendar-aware live panels. No wagering/payment/prediction functions. Protected deletion controls; uninstall retains data.

= 2.0.0 =
Additive provenance migration, reviewed publication gate, bounded CSV and client schedule seed. Historical data never seeded.
