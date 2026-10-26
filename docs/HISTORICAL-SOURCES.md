# Historical sources and verification scope

**Research date:** 26 October 2026.  
**Requested inclusive window:** 26 August–26 October 2026 (62 calendar dates).  
**Delivered:** 523 rows, 11 games, **partial coverage**. No automatic publication.

## What “verified” means here

The `verified` field in the delivered CSV means the exact date, source game label and value were checked against the publicly displayed chart returned by the page-retrieval tool. It does **not** certify that the underlying event took place as reported, that a source is official, or that several sites are editorially independent. Source pages are mutable; recheck them before publishing. The plugin stores the imported assertion but **always creates drafts without a reviewer**, so it cannot substitute for the administrator's explicit approval.

No record is estimated, generated from the original plugin's seed array, inferred from neighbouring dates, or filled from another differently named game. Dates retain the source's calendar labels; they are not shifted based on the client's different schedule times.

## Access and research log

| Reference | Access observed | Use / limitation |
|---|---|---|
| https://satta20.com/ | Public homepage retrievable on research date; current October and previous September charts visible | Primary architectural reference and source for seven matching games. Supplied ZIP differs: old seeded values cover July/partial August, not the requested complete period. |
| https://satta20.com/?srm_month=8&srm_year=2026 | Public August filter retrievable | Source for 26–31 August. The repeated previous-month section still showed September; it was not mistaken for August data. |
| https://a1-satta.com/ | Public homepage and October chart retrievable across chunks | October source for Sadar Bazar, Gwalior, Agra and Alwar; cross-check of 154 corresponding primary-source cells. Older individual charts were not exhaustively retrieved. |
| https://www.a7-satta.com/ | Public homepage/current chart retrievable | Confirmed the common daily/table hierarchy and the missing 1 October Disawar cell. Not a separate source of imported rows. |
| https://www.lucky-satta.com/ → https://lucky-satta.com/ | Public redirected homepage retrievable | Reviewed page organization, daily chart presentation and overlapping game labels. Not a separate source of imported rows. |

The reference sites were **not blocked** by the page-reading tool in this session. The sandbox's ordinary outbound host allowlist does not include them, so this work did not implement or run a background scraper. No authentication, bypass, private endpoints or anti-bot evasion was used. Public readability is not a blanket redistribution license: the site owner must confirm applicable source terms, permitted factual reuse and local publication requirements before launch. No claim is made that publisher terms or robots policies have been comprehensively audited.

Only short factual chart excerpts are retained, not advertisements, wagering copy, source logos, images or third-party contacts.

## Evidence and reproducibility

- `source-chart-excerpts.txt`: dated primary-source table excerpts for the requested period, preserving all ten original columns to make column selection auditable.
- `corroborating-chart-excerpt.txt`: the twelve-column A1 October chart used for direct records and cross-checks.
- `tools/build-history.py`: explicit column/name/slug mapping, valid-date checks, exact two-digit checks, de-duplication assertion, period boundary assertions and equality checks for every cross-checked cell. It performs **no scraping, forecasting or synthesis**.
- `HISTORICAL-COVERAGE.json`: machine-readable counts and verification definition.
- `withheld-and-missing.csv`: 14 missing cells in the selected primary games plus one unresolved withheld entry. This file is **not** the entire missing coverage for all 14 client games; whole-game and earlier-period research gaps are described below.

The excerpts are transcriptions of the returned pages, not publisher-signed snapshots. The build script checks their consistency and cross-source equality, but cannot establish independence or prevent later changes on the websites.

## Coverage by client game

| Client game / stored slug | Rows | Coverage |
|---|---:|---|
| Model Town / `model-town` | 0 | No matching dataset verified |
| Sadar Bazar / `sadar-bazar` | 26 | October 1–26 only, A1 |
| Gwalior / `gwalior` | 26 | October 1–26 only, A1 |
| Delhi Bazar / `delhi-bazar` | 60 | Primary source; missing August 31 and September 30 |
| Punjab Matka / `punjab-matka` | 0 | No matching dataset verified |
| Shri Ganesh / `shri-ganesh` | 60 | Primary source; missing August 31 and September 30 |
| Agra / `agra` | 26 | October 1–26 only, A1 |
| Faridabad / `faridabad` | 60 | Primary source; missing August 31 and September 30 |
| Alwar / `alwar` | 26 | October 1–26 only, A1 |
| HR Matka / `hr-matka` | 60 | Primary source only; missing August 31 and September 30 |
| Ghaziabad / `gaziabad` | 60 | Primary source; missing August 31 and September 30 |
| HUDA City / `huda-city` | 0 | No matching dataset verified |
| Gali / `gali` | 59 | Primary source; missing August 31, September 30 and October 26 at retrieval |
| Disawar / `dishavar` | 60 | Primary source; missing September 1; October 1 withheld |

By month: **August 36**, **September 203**, **October 284**. The CSV contains no dates outside the requested window and no duplicate game/date keys.

For the 62-date × 14-game comparison grid, 345 combinations are not represented. This is a coverage indicator, **not** an assertion that every combination ought to have a real result. Three entire games are missing; four more have no checked August/September rows. Do not label this dataset “complete” or interpret missing values as zero.

## Mappings and exclusions

- Original labels such as `Delhi Bazar / दिल्ली बाज़ार`, `HR MATKA / HR मटका`, `Ghaziabad / गाजियाबाद` and `Disawar / दिशावर` remain in `original_game_name`. Stable slugs link them to the local records database.
- `gaziabad` and `dishavar` preserve the existing plugin's stable slugs; visible English/Hindi names are independently editable.
- Spelling variants Ghaziabad/Gaziabad and Disawar/Disawer are mapped explicitly for chart comparison. No general fuzzy matching or “closest game” algorithm is used in imports.
- **Do not map HR Satta to HR Matka.** For example, the sources show different October 26 values for those differently named entries.
- **Do not map Dwarka to HUDA City, Delhi Matka to Punjab Matka, or Sarafa Bazar to Model Town.** Similar timing or location is not evidence of equivalence.
- Satta20's IPL, RDL Bazar and Hukum columns are preserved in the factual excerpt for column alignment but excluded from the delivered CSV because they are not in the requested schedule.
- Source-site game times are not imported. The client-provided 14 times are the site's initial informational schedule and differ from the references.

### Withheld Disawar entry

On **2026-10-01**, Satta20's October chart displayed **25** for Disawar, while A1 and the reviewed A7 chart showed no value. Satta20's September 30 row also displayed 25, so copying the October cell without resolving calendar attribution would be unsafe. The October 1 entry is withheld, not assumed false, copied from September, or replaced with zero. Source owners/editorial review must resolve it before a future import.

## Completing missing data manually

1. Inspect permitted public historical pages or obtain a source-backed file from an authorized source.
2. Record the exact original game label, calendar date, value and stable public source URL. Keep a private evidence snapshot/date if allowed.
3. Resolve ambiguous names/date attribution before choosing the local game slug. Do not invent alias equivalence.
4. Use the current CSV template. Leave unavailable records absent; no row should be created solely to fill a table cell.
5. Import as drafts, inspect the log, recheck each source and explicitly approve publication in the review desk.
6. If a source blocks access, requires login, withdraws data or prohibits reuse, respect that limitation and record it. Do not bypass it or describe the dataset as complete.

A later source correction is handled through explicit record editing, not by silently overwriting data during a repeated import.
