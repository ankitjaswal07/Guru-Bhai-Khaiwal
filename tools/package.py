#!/usr/bin/env python3
"""Deterministic, single-root WordPress packages plus companion deliverables."""
from pathlib import Path
import zipfile, hashlib, shutil
root=Path(__file__).resolve().parents[1]
release=root/'releases';release.mkdir(exist_ok=True)
companions=['README.md','CHANGELOG.md','TESTING-REPORT.md','historical-data-import.csv']
for folder,name in [('guru-bhai-khaiwal','guru-bhai-khaiwal-theme.zip'),('scheduled-results-manager','guru-bhai-khaiwal-results-manager.zip')]:
    source=root/'project'/folder
    for document in ['README.md','CHANGELOG.md','TESTING-REPORT.md','LICENSE']:
        shutil.copyfile(root/document,source/document)
    (source/'docs').mkdir(exist_ok=True)
    for document in ['MIGRATION.md','HISTORICAL-SOURCES.md','ACF-ADMIN-GUIDE.md','HINDI-SITE-SETUP.md']:
        shutil.copyfile(root/'docs'/document,source/'docs'/document)
    with zipfile.ZipFile(release/name,'w',zipfile.ZIP_DEFLATED,compresslevel=9) as archive:
        for file in sorted(source.rglob('*')):
            if not file.is_file():continue
            arc=folder+'/'+file.relative_to(source).as_posix()
            info=zipfile.ZipInfo(arc,(2026,10,27,0,0,0));info.compress_type=zipfile.ZIP_DEFLATED;info.external_attr=0o100644<<16
            archive.writestr(info,file.read_bytes())
    with zipfile.ZipFile(release/name) as archive:
        assert archive.testzip() is None
        assert all(n.startswith(folder+'/') and '..' not in Path(n).parts for n in archive.namelist())
        assert not any(x in n for n in archive.namelist() for x in ['node_modules','.runtime','.git/','tests/','originals/'])
        required='style.css' if folder=='guru-bhai-khaiwal' else 'scheduled-results-manager.php'
        assert folder+'/'+required in archive.namelist()
        print(f'PASS {name}: {len(archive.namelist())} files, {(release/name).stat().st_size:,} bytes, valid single-root archive')
for document in companions:shutil.copyfile(root/document,release/document)
shutil.copytree(root/'docs',release/'docs',dirs_exist_ok=True)
files=sorted(p for p in release.rglob('*') if p.is_file() and p.name!='SHA256SUMS.txt')
(release/'SHA256SUMS.txt').write_text(''.join(hashlib.sha256(p.read_bytes()).hexdigest()+'  '+p.relative_to(release).as_posix()+'\n' for p in files))
# Input originals must remain byte-identical to the initial inspection.
for line in (root/'docs/INPUT-SHA256.txt').read_text().splitlines():
    digest,name=line.split(maxsplit=1)
    assert hashlib.sha256((root/name).read_bytes()).hexdigest()==digest
print('PASS original ZIP hashes unchanged; release SHA256 manifest written')
