# Changelog

## 2.0.0 — 2026-10-26

### Inspection and provenance
- Retained both supplied ZIPs unchanged and recorded hashes/file inventory.
- Inspected supplied classic theme architecture, ACF fields/defaults, component assets and templates, plugin tables/CRUD/admin/AJAX/REST/shortcodes/import/export/seeding paths.
- Wrote `IMPLEMENTATION-PLAN.md` before implementation; source access and actual-code/live-site differences are documented.

### Theme
- New independent Guru Bhai Khaiwal identity: navy/gold/white editorial layout, typographic logo fallback, bundled SVG favicon, rounded schedule cards, source-aware record tables, archive filter panel and neutral informational copy.
- Preserved classic WordPress template hierarchy, section-dispatch architecture and the eight supplied custom page-template paths; reworked presentation rather than retaining source-site marketing defaults.
- Replaced unconfirmed ACF PRO dependency with native Customizer controls, page metadata/game dropdown, menus, Media Library, FAQ posts and ordinary WordPress Posts/Pages. Existing ACF data is left untouched; migration is manual and documented.
- Editable homepage order/visibility/headings/descriptions/images, branding/colors/hero/buttons/contact/social/about/footer/SEO defaults.
- Responsive layouts, progressive mobile menu with Escape support, skip link, labelled filters, semantic tables with keyboard-scroll regions, native details/summary FAQs and reduced-motion support.
- Self-hosted variable Devanagari font subset with OFL license; no Google Fonts request, remote asset dependency, jQuery carousel or ticking homepage clock.
- Added title-tag, metadata, canonical handling, breadcrumbs, WebSite structured data, core-sitemap-compatible archive pages and noindex controls for internal filter/search URLs. Common SEO plugins take over duplicate head metadata.
- Removed source-site payment methods, guarantees, wagering/prediction promotions, copied logos and third-party contact details. Contact defaults remain unfilled rather than fabricated.

### Results plugin
- Reused the original events/results table names, database setup foundations, `SRM_Games` management and full-management capability; retained stable IDs, slugs and original values during upgrades.
- Added provenance, verification state, reviewer and review timestamp columns plus a public archive index and persistent import-log table via additive `dbDelta()` migration.
- Seed only the 14 client schedules on empty installations; removed automatic unsourced historical seeding. Existing schedules remain unchanged until explicitly edited.
- Public query gate requires published + verified + source + reviewer + review timestamp, an active game and a non-future date. Legacy published values without provenance remain stored but hidden.
- Replaced unsafe legacy admin/AJAX writer paths with POST-only capability/nonce-protected review forms. Added strict calendar/value/source validation, explicit review confirmation, private notes, stale-edit checks and paginated date/month/game/status filtering.
- New UTF-8 CSV workflow: mandatory provenance, ISO dates, bounded file/row count, name/slug normalization, draft-only imports, unique-key duplicate protection, no import overwrites, persistent row counts/errors, source-aware paginated export and formula neutralization.
- Preserved original shortcode registrations and REST namespace/routes; added `[gbk_archive]` and `[gbk_latest_records]`. Legacy countdown/live aliases now provide informational displays without automatic polling. Behavioral boundaries are documented rather than claiming exact UI/API parity.
- Calendar-aware next/previous schedule occurrences use the configured WordPress timezone, including midnight crossings independent of display order.
- Replaced shared public/admin monthly caching with source-gated indexed reads. No old unverified transient payloads are reused.
- Deactivation/uninstall retain all data. Game deactivation replaces destructive deletion. Existing scoped-user metadata is retained but does not grant the new full review capability.

### Historical records
- Added a partial 523-row, draft-only CSV covering checked records within 2026-08-26–2026-10-26, across 11 games.
- Retained factual source excerpts and deterministic conversion/validation tooling; 154 corresponding October values also match A1's displayed chart.
- Preserved leading zeroes, original game labels, source URLs and verification context. Withheld missing/ambiguous data, including Disawar October 1; did not equate unrelated game names or claim complete coverage.

### Delivery and testing
- Added reproducible WordPress/PHP-WASM integration and browser QA tooling, original-schema migration fixtures, accessibility checks, source CSV validation and deterministic ZIP packaging.
- Installation, content setup, import/review, caching, permissions, backups, rollback, licensing and hosting acceptance documentation included.
- Native PHP/MySQL hosting validation, multisite, production SEO-plugin integration matrices and full real-device/assistive-technology certification remain outside the completed test scope; see `TESTING-REPORT.md`.

## Supplied baseline

- `satta20.zip`: Satta20 theme 1.0.0, GPL v2 or later; ACF PRO-based presentation/options.
- `scheduled-results-manager.zip`: Scheduled Results Manager 1.1.0, GPL-2.0-or-later; existing schedules/results tables, seeding, AJAX/REST and CSV functionality.
