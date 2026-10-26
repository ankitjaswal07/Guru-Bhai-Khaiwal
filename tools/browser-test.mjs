import {chromium as playwright} from 'playwright';
import chromium from '@sparticuz/chromium';
import AxeBuilder from '@axe-core/playwright';
import fs from 'node:fs';
if(fs.existsSync('.runtime/lib'))process.env.LD_LIBRARY_PATH=process.cwd()+'/.runtime/lib'+(process.env.LD_LIBRARY_PATH?':'+process.env.LD_LIBRARY_PATH:'');
const access=JSON.parse(fs.readFileSync('.runtime/preview-access.json'));
const browser=await playwright.launch({executablePath:await chromium.executablePath(),args:chromium.args,headless:true});
const context=await browser.newContext({viewport:{width:1440,height:1000}});
await context.route('**/*',async route=>{
 const url=new URL(route.request().url());
 if(url.hostname.endsWith('.e2b.app')) {
   try {
   // CDP interception omits file bytes from multipart postData. Restore only our
   // known test fixture when proxying the upload to the sandbox-local server.
   let postData=route.request().postDataBuffer();
   if(postData && route.request().headers()['content-type']?.includes('multipart/form-data') && postData.includes(Buffer.from('filename="historical-data-import.csv"'))){
     postData=Buffer.from(postData.toString().replace(/(name="csv"; filename="historical-data-import.csv"\r\nContent-Type: [^\r]+\r\n\r\n)([\s\S]*?)(\r\n--)/,(_,a,b,c)=>a+fs.readFileSync('historical-data-import.csv','utf8')+c));
   }
   const response=await route.fetch({postData:postData||undefined,url:'http://127.0.0.1:8080'+url.pathname+url.search,maxRedirects:0});if(response.headers()['location']) { const headers={...response.headers(),'content-type':'text/html'};const destination=headers.location;delete headers.location;await route.fulfill({response,status:200,headers,body:'<script>location.replace('+JSON.stringify(destination)+')</script>'}); } else { await route.fulfill({response}); }} catch(e){console.error(e);await route.abort();}
 } else {await route.abort();}
});
const page=await context.newPage();const errors=[];page.on('pageerror',e=>errors.push(e.message));
function ok(c,m){if(!c)throw Error('FAIL '+m);console.log('PASS '+m);}
for(const width of [1440,768,390]){
 await page.setViewportSize({width,height:1000});const res=await page.goto(access.siteUrl+'/',{waitUntil:'networkidle'});
 ok(res.status()===200,'homepage HTTP 200 at '+width);
 ok(await page.locator('h1').count()===1,'one H1 at '+width);
 ok(await page.locator('.gbk-game').count()===14,'14 schedule cards at '+width);
 ok(await page.evaluate(()=>Array.from(document.images).every(img=>img.complete&&img.naturalWidth>0)),'no broken image/emoji assets at '+width);
 ok(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),'no viewport overflow at '+width);
 await page.screenshot({path:'.runtime/home-'+width+'.png',fullPage:true});
 const axe=await new AxeBuilder({page}).withTags(['wcag2a','wcag2aa','wcag21aa']).analyze();
 fs.writeFileSync('.runtime/axe-'+width+'.json',JSON.stringify(axe.violations,null,2));
 ok(axe.violations.length===0,'zero axe violations at '+width);
 console.log('AXE '+width+': '+axe.violations.length+' violations',axe.violations.map(v=>({id:v.id,nodes:v.nodes.length})));
 if(width===390){await page.getByRole('button',{name:'Menu'}).click();ok(await page.locator('#primary-nav').isVisible(),'mobile menu opens');await page.keyboard.press('Escape');ok(!(await page.locator('#primary-nav').isVisible()),'Escape closes menu');}
}
await page.goto(access.siteUrl+'/archives/',{waitUntil:'networkidle'});ok(await page.locator('.gbk-filters').count()===1,'archive page renders');
await page.locator('input[name=gbk_date]').fill('2026-08-26');await Promise.all([page.waitForNavigation({waitUntil:'networkidle'}),page.getByRole('button',{name:'Find records'}).click()]);
ok((await page.locator('meta[name=robots]').getAttribute('content')).includes('noindex'),'filtered archives noindex');
ok(await page.locator('link[rel=canonical]').count()===1,'singular canonical not duplicated');
const missing=await page.goto(access.siteUrl+'/nonexistent-test-path/',{waitUntil:'networkidle'});ok(missing.status()===404,'404 template keeps 404 status');
await page.goto(access.siteUrl+'/wp-login.php',{waitUntil:'networkidle'});await page.locator('#user_login').fill(access.user);await page.locator('#user_pass').fill(access.password);await Promise.all([page.waitForURL('**/wp-admin/**'),page.locator('#wp-submit').click()]);await page.waitForLoadState('networkidle');
console.log('LOGIN URL',page.url(),'ERROR',await page.locator('#login_error').allTextContents());ok(page.url().includes('wp-admin'),'WordPress login works');
for(const tab of ['srm-results','srm-events','srm-import-export','srm-settings']){const r=await page.goto(access.siteUrl+'/wp-admin/admin.php?page='+tab,{waitUntil:'networkidle'});ok(r.status()===200 && await page.locator('h1').textContent()==='Guru Bhai Khaiwal · Records desk','admin screen '+tab);}
await page.goto(access.siteUrl+'/wp-admin/admin.php?page=srm-events',{waitUntil:'networkidle'});
await page.locator('input[name=event_name]').fill('UI test game');await page.locator('input[name=event_name_secondary]').fill('परीक्षण');await page.locator('input[name=scheduled_time]').fill('00:15');await page.locator('input[name=display_order]').fill('99');await Promise.all([page.waitForNavigation({waitUntil:'networkidle'}),page.getByRole('button',{name:'Add game'}).click()]);
ok(await page.locator('td').filter({hasText:/^UI test game$/}).count()===1,'schedule creation from real admin form');
const row=page.locator('tr').filter({has:page.locator('td').filter({hasText:/^UI test game$/})});await row.getByRole('link',{name:'Edit',exact:true}).click();await page.locator('input[name=status]').uncheck();await Promise.all([page.waitForNavigation({waitUntil:'networkidle'}),page.getByRole('button',{name:'Save schedule'}).click()]);
await page.goto(access.siteUrl+'/',{waitUntil:'networkidle'});ok(await page.locator('.gbk-game').count()===14,'deactivated game hidden publicly');
await page.goto(access.siteUrl+'/wp-admin/admin.php?page=srm-results',{waitUntil:'networkidle'});
await page.locator('select[name=event_id]').selectOption({label:'Model Town'});await page.locator('input[name=result_date]').fill('2026-08-26');await page.locator('input[name=result_value]').fill('07');await page.locator('input[name=original_game_name]').fill('UI TEST FIXTURE');await page.locator('input[name=source_url]').fill('https://example.com/test-fixture');await Promise.all([page.waitForNavigation({waitUntil:'networkidle'}),page.getByRole('button',{name:'Save record',exact:true}).click()]);ok(await page.locator('.notice-success').count()>0,'draft save via nonce-protected form');
await page.getByRole('link',{name:'Review / edit',exact:true}).first().click();await page.locator('select[name=verification_status]').selectOption('verified');await page.locator('select[name=result_status]').selectOption('published');await page.locator('input[name=confirm_review]').check();await Promise.all([page.waitForNavigation({waitUntil:'networkidle'}),page.getByRole('button',{name:'Save record',exact:true}).click()]);
await page.goto(access.siteUrl+'/archives/?gbk_date=2026-08-26',{waitUntil:'networkidle'});ok(await page.locator('.gbk-result').textContent()==='07','reviewed record visible on archive');
await page.setViewportSize({width:390,height:844});ok(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),'published table scrolls inside mobile region');
const archiveAxe=await new AxeBuilder({page}).withTags(['wcag2a','wcag2aa','wcag21aa']).analyze();console.log('AXE populated archive:',archiveAxe.violations.map(v=>v.id));ok(archiveAxe.violations.length===0,'zero axe violations on populated archive');
await page.goto(access.siteUrl+'/wp-admin/admin.php?page=srm-results',{waitUntil:'networkidle'});await page.getByRole('link',{name:'Review / edit',exact:true}).first().click();await page.locator('select[name=result_status]').selectOption('pending');await Promise.all([page.waitForNavigation({waitUntil:'networkidle'}),page.getByRole('button',{name:'Save record',exact:true}).click()]);
await page.goto(access.siteUrl+'/archives/?gbk_date=2026-08-26',{waitUntil:'networkidle'});ok(await page.locator('.gbk-result').count()===0,'unpublish removes fixture from public page');
// Actual HTTP permission/nonce and CSV handlers, not just direct model calls.
const badNonce=await page.evaluate(async()=>{const r=await fetch('/wp-admin/admin-post.php',{method:'POST',body:new URLSearchParams({action:'gbk_manage',task:'record',_wpnonce:'invalid'})});return r.status;});
ok(badNonce===403,'invalid admin nonce returns HTTP 403');
const badMethod=await page.evaluate(async()=>{const r=await fetch('/wp-admin/admin-post.php?action=gbk_manage');return r.status;});ok(badMethod===405,'admin mutations require POST');
await page.goto(access.siteUrl+'/wp-admin/admin.php?page=srm-import-export',{waitUntil:'networkidle'});
await page.locator('input[name=csv]').setInputFiles('historical-data-import.csv');
await Promise.all([page.waitForNavigation({waitUntil:'networkidle'}),page.getByRole('button',{name:'Import as drafts',exact:true}).click()]);
ok((await page.locator('summary').allTextContents()).some(t=>t.includes('imported 523')&&t.includes('invalid 0')),'actual upload handler accepts all 523 source-checked rows');
await page.locator('input[name=start]').fill('2026-08-26');await page.locator('input[name=end]').fill('2026-10-26');
const csv=await page.evaluate(async()=>{const form=document.querySelector('input[name=task][value=export]').closest('form');const response=await fetch(form.getAttribute('action'),{method:'POST',body:new FormData(form)});return {status:response.status,type:response.headers.get('content-type'),body:await response.text()};});
ok(csv.status===200&&csv.type.includes('text/csv')&&csv.body.includes('original_game_name,source_url,verification_status'),'actual CSV export includes provenance columns');
ok(csv.body.includes('model-town,07,pending'),'actual export preserves leading-zero draft value');
await page.goto(access.siteUrl+'/archives/',{waitUntil:'networkidle'});ok(await page.locator('.gbk-result').count()===0,'HTTP-imported records remain private');
await page.setViewportSize({width:1440,height:1000});
await page.goto(access.siteUrl+'/wp-admin/customize.php',{waitUntil:'networkidle'});
ok(await page.locator('#accordion-panel-gbk').count()===1,'native branding/homepage Customizer panel available');
console.log('Browser JS errors:',errors);ok(errors.length===0,'no browser JS errors');
await browser.close();
