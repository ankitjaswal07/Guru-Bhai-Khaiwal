# Hindi site setup — 2.2.0

## Install and create content

1. Back up files/database. Replace the existing plugin and theme with the 2.2 ZIPs on staging; keep licensed ACF PRO active.
2. Set WordPress timezone to Asia/Kolkata (or the intended local zone).
3. Open **Appearance → Hindi content setup**. Read/check the confirmation, then **Create missing Hindi pages**. This explicitly publishes starter prose/pages/articles—not results.
4. Created routes: Home, Games, About, Blog, FAQ, Results, Chart, Contact, Today, Yesterday and one `game-{slug}` page per configured game. With the original 14 schedules: **24 pages + 3 articles**. All page and article copy is editable Hindi starter material, not a statement that an editorial team reviewed all history.
5. Existing pages with matching slugs are preserved, including titles, templates and content. Existing menus and front page are preserved. Check **Settings → Reading** and **Appearance → Menus** if you already had pages. Running setup again creates only missing items, including new game pages. A conflicting existing game slug is not silently repurposed; assign the correct result shortcode manually.

## Edit everything without PHP

- **Guru Bhai Khaiwal → Homepage**: hero copy/images/buttons, section headings, visibility and section order; original supplied field keys are retained. Public language is editable in **General → Public content language** (default `hi-IN`; WordPress admin language is not changed).
- **Pages → Edit → Page Display Settings → Page Sections**: starter copy is stored here, not duplicated into the main editor. Four layouts: full content, image-left, image-right, **shortcode/result section**. Add/reorder/disable sections. Additional sections now render on Blog, FAQ and Contact as well as normal/results pages.
- Homepage custom sections can be placed using **Homepage Section Order**. The normal homepage core order remains hero → game table → combined daily table → optional cards → this month → previous month → optional contact blocks → blog → FAQs.
- **Results Page Settings**: set a page-specific shortcode. Example: `[srm_monthly_results game_id="1"]` using the actual game ID. The game is a hard constraint; changing the URL cannot expose other games in that chart.
- **Posts**: three editable Hindi starter articles. They contain no fabricated result numbers. Blog listing supports extra ACF page sections; article bodies use the normal WordPress editor.
- **Theme Styles**: maroon/ivory surrounding design, typography, spacing, cards/buttons, responsive controls. **Table Source defaults to Plugin**, preserving the supplied gold-bordered, dark-header tables; switch to Theme to customize table variables there. Existing saved style choices are not overwritten.
- **FAQ, Footer, Header, Contact, Social**: original global option pages. Set genuine contact details and test mail delivery; no fake phone/address is seeded. Payment rendering remains disabled.

When ACF PRO is temporarily absent, saved/default content still renders. Content created without PRO is marked for one-time conversion into native ACF storage when PRO returns; independently edited fields are not overwritten. The actual licensed PRO editor/media/repeater UI must be accepted on your staging site.

## Import and publish actual history

1. **Scheduled Results → Historical setup → Import bundled historical CSV as drafts**. The bundle decodes to exactly the delivered CSV bytes. It is stored in a PHP-guarded payload, not a publicly downloadable draft CSV inside the plugin. Temporary import files are removed.
2. There are **523 entries, 26 August–26 October 2026, 11 of 14 games**. Repeating the import reports duplicates and preserves edited records. Full logs remain in Import / Export.
3. Review the original game names, dates, exact values and URLs displayed privately in Historical setup. Nothing is selected automatically. Select only rows you personally checked, tick the explicit approval confirmation, then publish the selected versions. Maximum **40 per request**. Stale/tampered snapshots, missing confirmation, unauthorized users and editing locks fail safely. A batch is not a fully transactional all-or-nothing database operation; the UI reports published/failed counts.
4. Missing dates stay missing. Model Town, Punjab and HUDA have no supplied history; four games have October-only coverage. Today may be empty because the delivered historical window ends on 26 October. **Importing or page setup never publishes results.**
5. Public tables contain no sources or source links. Public REST rows also omit provenance. Admin records/exports retain it. Revisions require fresh approval. Public grids show only approved, published records for active games and non-future dates.

## Table reference and filters

- Game table: Game + Game Time; bilingual names, original 600px maximum width.
- Daily table: Game | Game Time | Yesterday | Today; one row per active game, blank when unpublished.
- Month tables: sticky Date × games, schedule time under each name, green values, current month through today, full prior month. Missing is `—`, never an estimated number.
- Original game/month/year filtering is available. Current chart uses `srm_game_id`, `srm_month`, `srm_year`; previous chart has independent `srm_previous_*` parameters. Repeated shortcode instances have unique form IDs. Use `show_filter="no"` to hide filters (homepage default).
- Structure/classes and base CSS come from the supplied ZIP. Text is Hindi-first, table scrolling is keyboard accessible, headers have semantic scope, and original filter bugs/global input selectors were corrected. This is not a claim of pixel-identical behavior with an uninspected live Satta20 deployment.

## Before launch

Review all starter prose, menus and contacts; test real PRO saves (including cleared fields), desktop/mobile tables, historical review/unpublish, SMTP delivery, production MySQL, CDN purging, SEO-plugin coexistence and backups. The local preview uses WordPress/PHP-WASM/SQLite, not your live hosting.
