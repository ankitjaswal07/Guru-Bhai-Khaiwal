#!/usr/bin/env python3
"""Build a draft-only, source-checked CSV from the retained factual excerpts.
No network scraping, inferred values, date shifting, or automatic publication.
"""
from pathlib import Path
import csv, datetime, collections, json, re
ROOT = Path(__file__).resolve().parents[1]
def read_chart(name, columns):
    rows = {}
    for line in (ROOT / 'docs' / name).read_text().splitlines():
        if not re.match(r'^2026-\d\d-\d\d ', line):
            continue
        parts = line.split()
        assert len(parts) == columns + 1, line
        assert all(v == '-' or re.fullmatch(r'\d{2}', v) for v in parts[1:]), line
        assert parts[0] not in rows
        datetime.date.fromisoformat(parts[0])
        rows[parts[0]] = parts[1:]
    return rows
primary = read_chart('source-chart-excerpts.txt', 10)
secondary = read_chart('corroborating-chart-excerpt.txt', 12)
assert len(primary) == 62 and len(secondary) == 26
# Index, stored slug, original name, corresponding A1 column (if actually equivalent).
mapping = [
    (2, 'delhi-bazar', 'Delhi Bazar / दिल्ली बाज़ार', 2),
    (3, 'shri-ganesh', 'Shri Ganesh / श्री गणेश', 4),
    (4, 'faridabad', 'Faridabad / फरीदाबाद', 6),
    (6, 'hr-matka', 'HR MATKA / HR मटका', None),
    (7, 'gaziabad', 'Ghaziabad / गाजियाबाद', 8),
    (8, 'gali', 'Gali / गली', 10),
    (9, 'dishavar', 'Disawar / दिशावर', 11),
]
rows, gaps, matched = [], [], 0
for date, values in sorted(primary.items()):
    for index, slug, original, cross in mapping:
        value = values[index]
        if (date, slug) == ('2026-10-01', 'dishavar'):
            gaps.append([date, slug, 'withheld: primary=25; A1/A7 missing; attribution unresolved'])
            continue
        if value == '-':
            gaps.append([date, slug, 'missing in primary source'])
            continue
        notes = 'Source-checked on 2026-10-26 against the retained chart excerpt; not official certification. Administrator must recheck before publishing.'
        if date in secondary and cross is not None:
            assert secondary[date][cross] == value, (date, slug, value, secondary[date][cross])
            matched += 1
            notes += ' Matching value also displayed at https://a1-satta.com/ (October 2026 chart).'
        source = 'https://satta20.com/?srm_month=8&srm_year=2026' if date.startswith('2026-08') else 'https://satta20.com/'
        rows.append([date, slug, value, 'draft', original, source, 'verified', notes])
for date, values in sorted(secondary.items()):
    for index, slug, original in [(0,'sadar-bazar','sadar bazar'),(1,'gwalior','gwalior'),(5,'agra','agra'),(7,'alwar','alwar')]:
        value=values[index]
        if value=='-':
            gaps.append([date,slug,'missing in A1 source'])
            continue
        rows.append([date,slug,value,'draft',original,'https://a1-satta.com/','verified','Source-checked on 2026-10-26 against the retained A1 October chart excerpt; not official certification. Administrator must recheck before publishing.'])
rows.sort(key=lambda row:(row[0],row[1]))
assert len({(r[0],r[1]) for r in rows}) == len(rows)
assert all('2026-08-26' <= r[0] <= '2026-10-26' for r in rows)
assert len(rows) == 523 and matched == 154
with (ROOT/'historical-data-import.csv').open('w',newline='',encoding='utf-8') as f:
    writer=csv.writer(f)
    writer.writerow(['date','game','result','status','original_game_name','source_url','verification_status','verification_notes'])
    writer.writerows(rows)
with (ROOT/'docs/withheld-and-missing.csv').open('w',newline='',encoding='utf-8') as f:
    writer=csv.writer(f);writer.writerow(['date','game','reason']);writer.writerows(gaps)
summary={'requested_start':'2026-08-26','requested_end':'2026-10-26','checked_on':'2026-10-26','rows':len(rows),'cross_checked_rows':matched,'by_game':dict(collections.Counter(r[1] for r in rows)),'by_month':dict(collections.Counter(r[0][:7] for r in rows)),'complete':False,'verification_meaning':'Exact value/date/name checked against the publicly displayed source chart; NOT independent official certification. All rows imported as drafts.'}
(ROOT/'docs/HISTORICAL-COVERAGE.json').write_text(json.dumps(summary,indent=2)+'\n')
print(json.dumps(summary,indent=2))
