// Development-only WordPress/PHP-WASM harness. Never included in installation ZIPs.
import fs from 'node:fs';
import path from 'node:path';
import http from 'node:http';
import crypto from 'node:crypto';
import {loadNodeRuntime, createNodeFsMountHandler} from '@php-wasm/node';
import {bootWordPressAndRequestHandler} from '@wp-playground/wordpress';
const root=process.cwd();
const version=process.env.PHP_VERSION || '8.3';
const port=Number(process.env.PORT || 8080);
const serve=process.argv.includes('--serve');
const fromZips=process.argv.includes('--from-zips');
const siteUrl=serve && process.env.E2B_SANDBOX_ID ? `https://${port}-${process.env.E2B_SANDBOX_ID}.e2b.app` : `http://127.0.0.1:${port}`;
let processId=0;
const handler=await bootWordPressAndRequestHandler({
  createPhpRuntime:()=>loadNodeRuntime(version,{emscriptenOptions:{processId:++processId}}),
  phpVersion:version, siteUrl, maxPhpInstances:1, cookieStore:false,
  wordPressZip:new File([fs.readFileSync('.runtime/wordpress.zip')],'wordpress.zip'),
  sqliteIntegrationPluginZip:new File([fs.readFileSync('node_modules/@wp-playground/cli/sqlite-database-integration.zip')],'sqlite.zip'),
  constants:{WP_DEBUG:true, WP_DEBUG_LOG:true, WP_DEBUG_DISPLAY:false, DISABLE_WP_CRON:true, AUTOMATIC_UPDATER_DISABLED:true, COMPRESS_SCRIPTS:false, COMPRESS_CSS:false, CONCATENATE_SCRIPTS:false},
  onProgress:console.log,
});
const php=await handler.getPrimaryPhp();
if(fromZips){
 for(const [zip,dest] of [['guru-bhai-khaiwal-theme.zip','themes'],['guru-bhai-khaiwal-results-manager.zip','plugins']]){
   php.writeFile('/tmp/'+zip,fs.readFileSync('releases/'+zip));
   const extracted=await php.run({code:`<?php $z=new ZipArchive();if(true!==$z->open('/tmp/${zip}'))throw new Exception('Invalid package');if(!$z->extractTo('/wordpress/wp-content/${dest}'))throw new Exception('Extraction failed');$z->close();`});
   if(extracted.exitCode||extracted.errors)throw Error(extracted.errors);console.log('PASS install archive '+zip);
 }
}
for(const [host,vfs] of [['project/guru-bhai-khaiwal','/wordpress/wp-content/themes/guru-bhai-khaiwal'],['project/scheduled-results-manager','/wordpress/wp-content/plugins/scheduled-results-manager'],['tests','/gbk-tests']].slice(fromZips?2:0)) {
  php.mkdir(vfs); await php.mount(vfs,createNodeFsMountHandler(path.join(root,host)));
}
async function run(code){const r=await php.run({code});if(r.errors)console.error(r.errors);if(r.exitCode)throw new Error(r.text+'\n'+r.errors);return r;}
const password=crypto.randomBytes(24).toString('hex');
fs.mkdirSync('.runtime',{recursive:true});
if(serve)fs.writeFileSync('.runtime/preview-access.json',JSON.stringify({user:'admin',password,siteUrl}),{mode:0o600});
if(fs.existsSync('historical-data-import.csv'))php.writeFile('/historical-data-import.csv',fs.readFileSync('historical-data-import.csv'));
const setup=await run(`<?php require '/wordpress/wp-load.php'; require_once ABSPATH.'wp-admin/includes/plugin.php';
wp_set_current_user(1); wp_set_password('${password}',1);
update_option('blogname','Guru Bhai Khaiwal'); update_option('blogdescription','साफ़ जानकारी · सही संदर्भ'); update_option('timezone_string','Asia/Kolkata');
update_site_option('can_compress_scripts',0);update_option('date_format','j M Y'); update_option('time_format','g:i A');
$r=activate_plugin('scheduled-results-manager/scheduled-results-manager.php'); if(is_wp_error($r)){throw new Exception($r->get_error_message());}
switch_theme('guru-bhai-khaiwal');
$home=wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'Home','post_name'=>'home']);
$archive=wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'Historical archives','post_name'=>'archives','post_content'=>'[gbk_archive]']);
update_option('show_on_front','page');update_option('page_on_front',$home);set_theme_mod('gbk_archive_page',$archive);
update_option('permalink_structure','/%postname%/');flush_rewrite_rules();
wp_delete_post(1,true); echo 'Activated clean WordPress '.get_bloginfo('version').' / PHP '.PHP_VERSION;`);
console.log(setup.text);
// Load theme's initialization and default FAQs before integration tests.
await handler.request({url:siteUrl+'/'});
if(fs.existsSync('tests/integration.php')){
 const result=await run("<?php require '/wordpress/wp-load.php'; require '/gbk-tests/integration.php';");
 console.log(result.text); fs.writeFileSync(`.runtime/tests-php-${version}.txt`,result.text);
}
// Exercise the real WordPress template loader, not standalone PHP includes.
const fixture=await run(`<?php require '/wordpress/wp-load.php'; wp_set_current_user(1);
$ids=[];$urls=[];
foreach(wp_get_theme()->get_page_templates() as $file=>$label){$id=wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'QA '.$label,'post_name'=>'qa-'.sanitize_title($label),'post_content'=>'Template QA only.']);update_post_meta($id,'_wp_page_template',$file);if(str_contains($file,'game-results'))update_post_meta($id,'_gbk_game_id',1);$ids[]=$id;$urls[]=get_permalink($id);}
$id=wp_insert_post(['post_type'=>'post','post_status'=>'publish','post_title'=>'QA informational article','post_content'=>'Template QA only.']);$ids[]=$id;$urls[]=get_permalink($id);$urls[]=get_category_link(1);$urls[]=home_url('/?s=QA');$urls[]=home_url('/wp-sitemap.xml');echo wp_json_encode(['ids'=>$ids,'urls'=>$urls]);`);
const fixtures=JSON.parse(fixture.text);
for(const url of fixtures.urls){const r=await handler.request({url});if(r.httpStatusCode!==200||r.errors||r.text.includes('There has been a critical error'))throw Error('Template failed '+url+' '+r.errors);console.log('PASS template request '+new URL(url).pathname);}
await run(`<?php require '/wordpress/wp-load.php'; foreach(${JSON.stringify(fixtures.ids).replace('[','array(').replace(']',')')} as $id){wp_delete_post($id,true);} set_theme_mod('gbk_schedule_order',0);set_theme_mod('gbk_latest_enabled',false);`);
let home=await handler.request({url:siteUrl+'/'});
if(home.text.indexOf('id="schedule"')>home.text.indexOf('id="hero"')||home.text.includes('id="latest"'))throw Error('Homepage section controls failed');
console.log('PASS native homepage order and visibility controls');
await run(`<?php require '/wordpress/wp-load.php'; remove_theme_mod('gbk_schedule_order');remove_theme_mod('gbk_latest_enabled');require_once ABSPATH.'wp-admin/includes/plugin.php';deactivate_plugins('scheduled-results-manager/scheduled-results-manager.php');`);
home=await handler.request({url:siteUrl+'/'});if(home.errors||!home.text.includes('informational archive is not available'))throw Error('Plugin-disabled fallback failed');console.log('PASS theme works when results plugin disabled');
await run(`<?php require '/wordpress/wp-load.php';require_once ABSPATH.'wp-admin/includes/plugin.php';activate_plugin('scheduled-results-manager/scheduled-results-manager.php');`);
if(php.fileExists('/wordpress/wp-content/debug.log')){const log=php.readFileAsText('/wordpress/wp-content/debug.log');fs.writeFileSync('.runtime/php-debug-'+version+'.log',log);if(log.trim())throw Error('Nonempty WordPress debug log: '+log);}
console.log('PASS no PHP errors in template requests or WordPress debug log');
if(!serve){process.exit(0);}
// Serialize requests to the single PHP runtime; use relative browser URLs via WP_HOME.
let queue=Promise.resolve();
http.createServer((req,res)=>{
  const chunks=[];req.on('data',c=>chunks.push(c));req.on('end',()=>{
    queue=queue.then(async()=>{
      const headers=Object.fromEntries(Object.entries(req.headers).filter(([k])=>!['host','content-length'].includes(k)).map(([k,v])=>[k,Array.isArray(v)?v.join(', '):v]));
      const response=await handler.request({url:siteUrl+req.url,method:req.method,headers,body:chunks.length?new Uint8Array(Buffer.concat(chunks)):undefined});
      for(const [key,values] of Object.entries(response.headers)){if(!['x-frame-options','content-length','transfer-encoding'].includes(key.toLowerCase()))res.setHeader(key,values);}
      res.statusCode=response.httpStatusCode;res.end(Buffer.from(response.bytes));if(response.errors)console.error(response.errors);
    }).catch(e=>{console.error(e);if(!res.headersSent)res.statusCode=500;res.end('Preview request failed. See development logs.');});
  });
}).listen(port,'0.0.0.0',()=>console.log('WordPress preview listening at '+siteUrl));
