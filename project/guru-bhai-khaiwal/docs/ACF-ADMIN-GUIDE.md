# ACF PRO administration and staging checklist — 2.1

The owner confirmed licensed ACF PRO is already installed on their website. The sandbox does not contain that paid plugin; the following **real editor acceptance checks remain required**.

## Configure in this order

1. Back up database/files. Replace the results plugin in place and activate the new theme on staging. Keep ACF PRO active.
2. Settings → General: timezone. Settings → Reading: published static Home page.
3. Guru Bhai Khaiwal → General: owned site title, logo, contact fields, SEO description. Leave Google Fonts off for local/system fonts.
4. Header: announcement, navigation via Appearance → Menus, date format, sticky mode and owned links. Scrolling notice is off by default; avoid continuous motion unless necessary.
5. Homepage: edit hero; enable/disable sections; drag rows in Homepage Section Order. Page Sections flexible layouts provide additional editorial content/images. Save the custom sections first, then reopen the order field so its choices include their headings. Add each desired custom section to the order list.
6. Shortcodes: inspect the default mappings; single-game pages need a game-specific shortcode. No game names/times are stored in ACF.
7. FAQ/Footer/Social/Contact: edit repeaters and nested links; remove unused rows, save and reload. Only link to existing pages. Contact cards start off. The optional built-in contact form requires working host mail; a configured form-plugin shortcode takes precedence.
8. Theme Styles: save palette, fonts, spacing, header/footer, cards/buttons/table/mobile values. Table Source determines whether Theme Styles or the plugin controls tables. Plugin schedule-card colors remain in Scheduled Results → Settings.
9. Scheduled Results → Games: check all names, times, order and visibility. Update entries manually if the pre-existing site has a different schedule; upgrades never overwrite it.
10. Review source records, import as drafts, publish only after explicit source checks. Validate both Daily Results and Records & source review.

## Storage and compatibility

- Parent `s20-settings`; original subpages `s20-general`, `s20-header`, `s20-results`, `s20-contact`, `s20-payment`, `s20-faq`, `s20-footer`, `s20-social`, `s20-shortcodes`.
- Added `s20-homepage` and `s20-styles`. Original `field_s20_*`, group names and flexible layout keys remain.
- Global ACF values remain `options_*`. Homepage uses the configured front-page post ID; the page editor and Homepage options edit the same metadata. If no front page exists, it uses options storage instead.
- On the first administrator visit with PRO and a configured front page, legacy global homepage values are copied only where post metadata is absent. Existing metadata—including empty strings and zero-row repeaters—is never replaced. The old options are retained. A per-front-page binding marker prevents repeated copying.
- Default repeater seeding never replaces a saved zero-row value. Previously published native `gbk_faq` posts can supply initial FAQ rows if ACF FAQ options are unset; original posts are not deleted.
- Original Payment fields remain visible/read-only; payment display is hard-disabled. Result/game deletion and uninstall erasure are protected. These are intentional informational/data-retention exceptions, not exact behavioral parity with the original.
- Flexible content and image return formatting are handled by ACF PRO when active. Without PRO, saved options/repeaters and the three supplied flexible layouts have a bounded fallback reader; the editor itself is unavailable.

## Required staging evidence

- [ ] All eleven subpages load with actual licensed PRO; no duplicate field keys or license/plugin errors.
- [ ] Original site's existing options and front-page fields are visible before saving; compare backup values.
- [ ] Homepage changes save/reload in both the options page and page editor; no duplicate storage or lost content.
- [ ] Repeater add/edit/reorder/delete works for FAQ, nested footer links, contacts, social and editorial cards. Clearing all rows stays cleared after reload/reactivation.
- [ ] Flexible content: all three layouts, Media Library selection/alt text, multiple custom sections and homepage ordering work.
- [ ] ACF style color pickers/numeric/select fields save; computed CSS changes on desktop/mobile; invalid values cannot inject CSS. Check accessible contrast with the selected palette.
- [ ] Logo, favicon, header/footer imagery, owned links and empty contact states work; no old third-party phone or promotional claim remains in saved content.
- [ ] Full manager and game-assigned operator tested with separate accounts; operator cannot view/edit unassigned drafts or publish.
- [ ] Daily AJAX row save, bulk error reporting, explicit review, monthly draft labels, import/reimport/export and publication privacy verified on hosting.
- [ ] Native MySQL/MariaDB upgrade, backup/restore, PHP extensions, HTTPS, permalinks, mail delivery and cache purge verified.
- [ ] Plugin-deactivated and ACF-deactivated frontends remain graceful; reinstall/reactivation retains data.

The source-capture tests in `tests/restoration.php` are deliberately labelled contract checks. They do not provide evidence for any unchecked real-PRO editor item above.
