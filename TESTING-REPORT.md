# Testing report — Guru Bhai Khaiwal 2.1.0

**27 October 2026.** Scope: supplied Satta20 ACF/reference-theme restoration, original plugin admin organization/settings, source-review safeguards, unchanged partial CSV and installation packages.

## Important ACF distinction

The owner confirmed licensed ACF PRO is installed **on their website**. No licensed PRO code or staging access is available in this sandbox. We did **not** install an unofficial copy, distribute PRO, simulate an editor for the preview, or test actual PRO repeater/media/save UI.

`tests/restoration.php` uses explicitly labelled, test-request-only **API capture functions** to compare the supplied PHP definitions with the new definitions. This verifies registration contracts, **not ACF PRO behavior**. The live preview truthfully shows the missing-PRO notice; ordinary public rendering and plugin administration work without it. `docs/ACF-ADMIN-GUIDE.md` lists required licensed-site checks.

## Environment

- Actual WordPress **6.7.2** in Playground, official archive pinned by `tools/prepare-runtime.mjs`.
- PHP-WASM **8.1.34 / 8.3.33**, WordPress SQLite integration. No native PHP-FPM/MySQL/MariaDB hosting certification.
- Chromium 138 / Playwright / axe-core 4.10.2.
- Viewports: **1440, 768, 390px**, plus a populated historical archive at 390px.
- No production secrets or license keys in packages. Synthetic records exist only in disposable QA databases and are not seeded or included in the historical CSV.

## Completed checks

| Area | Evidence / result |
|---|---|
| PHP syntax | **79 release PHP files** parse on PHP 8.1 |
| Core repository | **56 assertions**: 14 exact client schedules, empty initial history, midnight/noon boundaries, valid dates/sources, leading zeroes, review requirement, draft privacy, stale edits, CSV limits/duplicates, migration and pagination |
| Supplied ACF contract | All **278 original field/layout keys** retained with matching name/type, including nested/flexible definitions; original groups and nine option-page slugs retained; Homepage/Styles added |
| Restoration integration | **319 assertions**, including contract comparisons, Homepage post-ID registration, style validation/output, deliberately empty values and cleared repeaters, six plugin views, settings effects, scoped operator restrictions and editing lock |
| Styles | Typed colors/fonts/numbers reject injection or clamp; stored values reach CSS. Original plugin card color changed actual computed browser color. Current/next visibility controls affect actual output |
| Public frontend | Correct reference-theme sections, one default-home H1, 14 bilingual schedule cards, no broken images/emoji assets and no horizontal viewport overflow at tested widths |
| Accessibility automation | Zero detected tested WCAG 2 A/AA and 2.1 AA axe violations on the default homepage at all three widths and populated mobile archive; not a full accessibility certification |
| Navigation/SEO | Mobile menu and Escape work; archive filters, noindex, single singular canonical and 404 status verified; core sitemap loads |
| Plugin administration | Seven screens (six supplied screens plus review desk), real schedule creation/deactivation, review-form draft/publish/unpublish, Daily Results AJAX source save, failed publication without review, verified publication and unpublish |
| HTTP security | Invalid nonces return 403; mutation GET requests return 405 for both historical admin-post and restored AJAX paths |
| CSV browser workflow | Actual upload handler accepts **523** source-checked rows; actual export carries provenance and preserves `07`; all HTTP-imported records remain private |
| Browser run | **55 checks** pass in one uninterrupted workflow; no browser JavaScript errors |
| Templates | All eight original custom template paths, post, category, search and sitemap return through the real template loader; homepage saved order/visibility works; plugin-disabled fallback works |
| Logs | No PHP warning/fatal/deprecation in completed integration/template checks |
| ZIP installation | Both installation ZIPs extracted/activated in fresh WP installations on PHP 8.1 and 8.3; all repository/restoration/template checks passed. The packaged PHP 8.3 preview passed the uninterrupted 55-check browser suite |
| Preservation | Original supplied ZIP hashes retained; no changes to the historical CSV; game/result IDs, values/statuses retained in migration/re-activation tests |

## Release-package verification

The reproducible final acceptance commands are:

```sh
node tools/lint.mjs
python tools/package.py
PHP_VERSION=8.1 node tools/runtime.mjs --from-zips
node tools/runtime.mjs --from-zips --serve
node tools/browser-test.mjs
(cd releases && sha256sum -c SHA256SUMS.txt)
```

Final package-run evidence is retained under `docs/QA-ACF-*`. ZIPs have a single correct WordPress folder and exclude runtime databases, credentials, tests, node modules and ACF PRO. Earlier `docs/QA-PHP81.txt`, `QA-PHP83.txt` and `QA-BROWSER.txt` belong to **2.0's native design**, not this restored editor, and are retained as historical evidence only.

## Defects corrected during this revision

- Original CSS had stale external/local-site background-image URLs, an oversized global H1 rule, invisible header text against its background, light-section contrast failures, and a flex wrapper incompatible with the reviewed schedule grid.
- Restored table/card styles now reach actual plugin markup; plugin time-format/visibility/editing settings are effective rather than merely displayed.
- Historical review links were moved to `srm-review` so they do not accidentally open the restored daily screen.
- Original mutation-by-GET, broad admin access and review-bypass paths were not restored. Added dedicated nonce/permission/method gates and draft-only assigned operators.
- Deliberately cleared repeaters are not reseeded by rendering. Homepage option binding preserves existing post metadata and old options.
- Multi-block result templates use valid nested article/container structure; source/notes remain escaped and public reads stay gated.

## Not certified / launch gates

1. **Real licensed ACF PRO editor**: options-page saves, repeaters, nested repeaters, flexible sections, image return formats/media picker, actual clearing/reordering and the one-time legacy homepage binding. Capture tests are not proof of these editor behaviors.
2. Actual site's saved theme content and styles: preserve and compare backup values; review any existing third-party phone, payment/promotion copy or old page links. The defaults are neutral but saved content is not automatically rewritten.
3. Native MySQL/MariaDB upgrade, hosting mail, HTTPS/rewrites, PHP extensions, backup restoration, production caches/CDN and actual role configuration.
4. Concurrent editing under load, performance/load testing, all assistive technologies/devices, complete Hindi translation, broad SEO/plugin conflict matrix and multisite. Network activation is unsupported.
5. Contact deliverability and abuse controls on the real host; working `wp_mail()` transport is not verified here.
6. Source rechecks, editorial sign-off, permitted reuse and applicable legal requirements. **523 rows are partial displayed-source transcription**, not independent official certification; missing data remains missing.

The theme matches the **supplied** backend definitions/design structure with documented security/informational exceptions. Exact private live-site dashboard parity has not been verified.
