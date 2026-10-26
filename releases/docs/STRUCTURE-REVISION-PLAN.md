# 2.2 structure revision — 27 October 2026

User clarified: same supplied Satta20 site/table structure, different surrounding homepage design, Hindi-first copy, editable ACF PRO pages/extra sections and complete page setup. User selected **existing historical data only**, not synthetic results; publication requires editorial approval. Do not invent missing values or show source columns in public tables.

1. Restore source table contracts/classes/styles: Game + Time; Game + Time + Yesterday + Today; monthly Date × Game matrices including schedule times. Keep source/verification in administration, never expose drafts. Copy the supplied table CSS, correcting only accessibility/responsive issues and scoping global selectors.
2. Create a distinct maroon/ivory Hindi-first presentation around these tables, preserving the original homepage section order and ACF field keys. Retain all original settings plus Theme Styles. Add a shortcode flexible-content section and render additional page sections consistently on all templates.
3. Add a nonce/capability-protected, idempotent setup screen: create Home, Games, About, Blog, FAQ, Results, Chart, Contact, Today/Yesterday, and one page per existing game; Hindi editable starter articles/content, menus, page templates and game associations. Never overwrite existing content, schedules or options. Store page content in ACF-compatible fields; do not package fake addresses/phones.
4. Bundle the unchanged 523-row CSV with an explicit draft-import action; provide bounded snapshot-based bulk editorial approval, not silent verification or auto-publication. Missing cells stay blank. No arbitrary synthetic history.
5. Test table shapes, sources absent in table HTML, privacy gates, duplicate-safe page/history setup, fixed game filters, custom section rendering, settings and approval actions. Preserve old QA as historical; rerun PHP/runtime/browser checks and package 2.2 ZIPs/docs on the same PR.

ACF PRO is confirmed installed remotely, not available in the sandbox. Registration/storage tests do not certify its real editor UI. Original uploaded ZIPs remain unchanged.
