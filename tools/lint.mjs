import fs from 'node:fs';
import path from 'node:path';
import {PHP} from '@php-wasm/universal';
import {loadNodeRuntime,createNodeFsMountHandler} from '@php-wasm/node';
const php=new PHP(await loadNodeRuntime(process.env.PHP_VERSION||'8.1',{emscriptenOptions:{processId:1}}));
php.mkdir('/project');await php.mount('/project',createNodeFsMountHandler(path.resolve('project')));
const r=await php.run({code:`<?php
$n=0; foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator('/project')) as $f) {
 if($f->isFile() && $f->getExtension()==='php') {token_get_all(file_get_contents($f->getPathname()),TOKEN_PARSE);++$n;}
} echo "PASS: $n PHP files parse on PHP ".PHP_VERSION."\\n";
`});console.log(r.text);if(r.errors)console.error(r.errors);process.exit(r.exitCode?1:0);
