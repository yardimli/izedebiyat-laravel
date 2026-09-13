const fs=require('fs'),http=require('http'),path=require('path');
const {chromium}=require(process.env.WRITER_PLAYWRIGHT_MODULE || 'playwright');
const root=path.resolve('storage/app/writer-preview');const state=JSON.parse(fs.readFileSync(path.join(root,'state.json'),'utf8'));const errors=[];const saves=[];
const model={id:'test/writer',name:'Writing Model',context_length:200000,pricing:{prompt:'0.000001',completion:'0.000002'},architecture:{output_modalities:['text']}};
const server=http.createServer(async(req,res)=>{
 const pathname=new URL(req.url,'http://127.0.0.1:8123').pathname;
 const json=value=>{res.setHeader('Content-Type','application/json');res.end(JSON.stringify(value));};
 if(pathname.startsWith('/build/')){const p=path.resolve('public','.'+pathname);if(!p.startsWith(path.resolve('public/build')+path.sep)||!fs.existsSync(p)){res.statusCode=404;return res.end();}res.setHeader('Content-Type',p.endsWith('.css')?'text/css':'application/javascript');return res.end(fs.readFileSync(p));}
 if(['/css/','/js/','/assets/'].some(prefix=>pathname.startsWith(prefix))){const p=path.resolve('public','.'+pathname);if(!p.startsWith(path.resolve('public')+path.sep)||!fs.existsSync(p)){res.statusCode=404;return res.end();}res.setHeader('Content-Type',p.endsWith('.css')?'text/css':p.endsWith('.js')?'application/javascript':p.endsWith('.png')?'image/png':'application/octet-stream');return res.end(fs.readFileSync(p));}
 if(pathname==='/check-llms-json')return json([model]);
 if(pathname==='/sohbet/oturumlar')return json([]);
 if(pathname==='/sohbet-oturum-ac')return json({session_id:'preview-session'});
 if(pathname.endsWith('/api/models'))return json({data:[model],refreshed_at:new Date().toISOString()});
 if(pathname.endsWith('/api/countries'))return json([{code:'TR',name:'Türkiye'}]);
 if(pathname==='/yazi-atolyesi/hesap'&&req.method==='PATCH')return json({saved:true});
 if(pathname.endsWith('/publication-ai/category'))return json({category_id:state.book.category_id});
 if(pathname.endsWith('/publication-ai/keywords'))return json({keywords_string:'deniz, umut'});
 if(req.method==='POST'&&pathname==='/image-gen')return json({success:true,image_medium_filename:'generated_medium.jpg'});
 if(pathname==='/yazi-atolyesi/api/books/1'){
  if(req.method==='PATCH'){let body='';for await(const chunk of req)body+=chunk;const data=JSON.parse(body);saves.push(data);state.book={...state.book,...data,revision:state.book.revision+1};return json({revision:state.book.revision});}
  return json(state);
 }
 const portalFiles={'/yazi-atolyesi/hesap':'account.html','/sohbet':'chat.html','/favorilerim':'favorites.html','/admin/kullanicilar':'users.html','/admin/eserler':'articles.html'};
 const file=portalFiles[pathname] || (pathname==='/eserlerim'?'library.html':pathname.includes('/admin/kotalar')?'budget.html':pathname.includes('/duzenle')?'editor.html':null);
 if(file){res.setHeader('Content-Type','text/html; charset=utf-8');return res.end(fs.readFileSync(path.join(root,file)));}res.statusCode=404;res.end();
});
(async()=>{await new Promise(resolve=>server.listen(8123,'127.0.0.1',resolve));let browser;try{
 browser=await chromium.launch({channel:'chrome',headless:true});const page=await browser.newPage({viewport:{width:1440,height:1000}});page.on('pageerror',e=>errors.push(e.message));
 await page.goto('http://127.0.0.1:8123/eserlerim');await page.locator('.book-card').waitFor();await page.screenshot({path:path.join(root,'library.png'),fullPage:true});
 if(await page.locator('[data-archive-book]').count())throw new Error('Archive control remains');
 await page.locator('[data-publish-book]').selectOption('1');await page.waitForTimeout(350);
 if(!saves.some(s=>s.is_published===true))throw new Error('Dashboard did not save publish status');
 state.book.document.content.push(...Array.from({length:20},(_,i)=>({type:'paragraph',content:[{type:'text',text:'Uzun eser paragrafı '+i+'. '+ 'Deniz kıyısında yürüyordu. '.repeat(12)}]})));
 await page.locator('.book-bottom a').first().click();await page.locator('.ProseMirror').waitFor();await page.waitForFunction(()=>document.querySelector('#usage').textContent.includes('100'));
 const welcome=page.locator('#writing-welcome');if(await welcome.isVisible())await welcome.locator('[data-welcome-close]').last().click();
 await page.screenshot({path:path.join(root,'editor.png'),fullPage:true});
 const countBefore=await page.locator('#word-count').textContent();
 const textBefore=await page.locator('.ProseMirror').innerText();
 if(await page.locator('.page-marker').count())throw Error('Page breaks enabled by default');
 await page.locator('#toggle-page-breaks').click();await page.locator('.page-marker').first().waitFor();
 await page.locator('#toggle-page-breaks').click();
 if(await page.locator('.page-marker').count())throw Error('Page breaks remain after disabling');
 if(await page.locator('#word-count').textContent()!==countBefore||await page.locator('.ProseMirror').innerText()!==textBefore)throw Error('Page break toggle changed manuscript');
 if(!await page.locator('#details-form').isVisible())throw new Error('Details did not open automatically');
 if(await page.locator('#details-form [name="is_published"]').count())throw new Error('Publish status remains in editor');
 await page.locator('#details-form input[name="subtitle"]').fill('Yeni alt başlık');await page.locator('#details-form textarea[name="subheading"]').fill('Yeni giriş');await page.locator('#details-form input[name="keywords_string"]').fill('deniz, öykü');await page.locator('#details-form button.primary').click();await page.waitForFunction(()=>document.querySelector('#save-status').textContent.length>0);await page.waitForTimeout(300);
 if(!saves.some(s=>s.subtitle==='Yeni alt başlık'&&s.subheading==='Yeni giriş'&&!('is_published' in s)&&s.category_id===String(state.book.category_id)))throw new Error('Publication metadata was not sent correctly: '+JSON.stringify(saves));
 await page.locator('[data-publication-ai="category"]').click();await page.waitForTimeout(150);
 await page.locator('[data-publication-ai="keywords"]').click();await page.waitForTimeout(150);
 if(await page.locator('[name="keywords_string"]').inputValue()!=='deniz, umut')throw new Error('AI tags were not applied');
 await page.locator('#generate-featured-image').click();await page.waitForTimeout(150);
 if(await page.locator('[name="featured_image"]').inputValue()!=='/storage/ai-images/medium/generated_medium.jpg')throw new Error('AI image was not applied');
 await page.locator('#details-form button.primary').click();await page.waitForTimeout(200);
 await page.screenshot({path:path.join(root,'details.png'),fullPage:true});
 await page.setViewportSize({width:390,height:844});await page.screenshot({path:path.join(root,'mobile-details.png'),fullPage:true});await page.locator('#close-panel').click();await page.waitForTimeout(300);if(await page.locator('.writing-pane').evaluate(el=>el.getBoundingClientRect().width)<350)throw new Error('Mobile manuscript is squeezed');await page.screenshot({path:path.join(root,'mobile.png'),fullPage:true});
 await page.goto('http://127.0.0.1:8123/yazi-atolyesi/admin/kotalar');await page.locator('.budget-table').waitFor();await page.screenshot({path:path.join(root,'budget.png'),fullPage:true});
 // A paragraph containing text and one BR matched the old CSS :only-child rule.
 state.book.document={type:'doc',content:[{type:'paragraph',content:[{type:'text',text:'Loaded manuscript'},{type:'hard_break'}]}]};
 const placeholderPage=await browser.newPage();placeholderPage.on('pageerror',e=>errors.push(e.message));
 await placeholderPage.goto('http://127.0.0.1:8123/eserlerim/test/duzenle');
 const prose=placeholderPage.locator('.ProseMirror');await prose.waitFor();
 const intro=placeholderPage.locator('#writing-welcome');if(await intro.isVisible())await intro.locator('[data-welcome-close]').last().click();
 if(await prose.getAttribute('data-placeholder')!==null)throw new Error('Placeholder overlaps loaded text');
 if(await prose.evaluate(el=>getComputedStyle(el,'::before').content)!=='none')throw new Error('Placeholder is rendered over loaded text');
 await prose.click();await placeholderPage.keyboard.press('Control+a');await placeholderPage.keyboard.press('Backspace');
 if(!await prose.getAttribute('data-placeholder'))throw new Error('Empty manuscript has no placeholder');
 await placeholderPage.keyboard.type('New writing');
 if(await prose.getAttribute('data-placeholder')!==null)throw new Error('Placeholder persists after typing');
 await placeholderPage.close();
 await page.setViewportSize({width:1440,height:1000});
 for(const [url,name] of [['/yazi-atolyesi/hesap','account'],['/sohbet','chat'],['/favorilerim','favorites'],['/admin/kullanicilar','users'],['/admin/eserler','articles']]){
  await page.goto('http://127.0.0.1:8123'+url);await page.locator('.portal-content').waitFor();await page.waitForTimeout(250);
  if(name==='account'){await page.locator('.CodeMirror').waitFor();if(!await page.locator('[name="name"]').count())throw Error('Profile form missing');}
  for(const theme of ['paper','dark','light']){await page.locator('#theme-current').click();await page.locator('#theme-'+theme).click();await page.waitForTimeout(100);if(await page.locator('html').getAttribute('data-theme')!==theme)throw Error('Theme failed');await page.screenshot({path:path.join(root,name+'-'+theme+'.png'),fullPage:true});}
  await page.locator('.admin-menu summary').click();if(!await page.locator('.admin-menu-items').isVisible())throw Error('Admin menu missing');await page.keyboard.press('Escape');
  await page.setViewportSize({width:390,height:844});await page.screenshot({path:path.join(root,name+'-mobile.png'),fullPage:true});
  if(await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth+1))throw Error(name+' overflows mobile');
  await page.setViewportSize({width:1440,height:1000});
 }
 if(errors.length)throw new Error(errors.join('\n'));console.log('Browser checks passed: Turkish library, editor, metadata save, mobile layout, admin budget; no JavaScript errors.');
 }finally{if(browser)await browser.close();server.close();}})().catch(e=>{console.error(e);process.exitCode=1;});
