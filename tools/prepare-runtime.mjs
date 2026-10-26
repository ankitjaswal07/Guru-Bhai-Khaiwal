// Development-only assets; never package these with the WordPress theme/plugin.
import fs from 'node:fs';
import crypto from 'node:crypto';
import zlib from 'node:zlib';
import {execFileSync} from 'node:child_process';
fs.mkdirSync('.runtime',{recursive:true});
const destination='.runtime/wordpress.zip';
if(!fs.existsSync(destination)){
 const response=await fetch('https://codeload.github.com/WordPress/WordPress/zip/refs/tags/6.7.2');
 if(!response.ok)throw Error('WordPress fixture download failed: '+response.status);
 fs.writeFileSync(destination,Buffer.from(await response.arrayBuffer()));
}
const hash=crypto.createHash('sha256').update(fs.readFileSync(destination)).digest('hex');
if(hash!=='9a460011e7d534a45f3b844485ba3b5dfa7b90ab1908b10ccf33721590157b7e')throw Error('WordPress test archive hash mismatch. Investigate before running.');
// Minimal Linux shared libraries bundled by the Chromium test package, for
// stripped-down sandboxes without libnss/libnspr. Does not alter system packages.
const archive='node_modules/@sparticuz/chromium/bin/al2023.tar.br';
if(process.platform==='linux'&&fs.existsSync(archive)){
 fs.writeFileSync('.runtime/al2023.tar',zlib.brotliDecompressSync(fs.readFileSync(archive)));
 execFileSync('tar',['-xf','.runtime/al2023.tar','-C','.runtime']);
}
console.log('Development WordPress fixture verified; Chromium support libraries prepared.');
