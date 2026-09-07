import { mkdir, writeFile } from 'node:fs/promises';
const tabs = await (await fetch('http://127.0.0.1:9223/json')).json();
const socket = new WebSocket(tabs.find(t => t.type === 'page').webSocketDebuggerUrl);
await new Promise(resolve => socket.addEventListener('open', resolve, { once: true }));
let id = 0;
const pending = new Map();
socket.addEventListener('message', event => { const msg = JSON.parse(event.data); if (pending.has(msg.id)) { const { resolve, reject } = pending.get(msg.id); pending.delete(msg.id); msg.error ? reject(new Error(JSON.stringify(msg.error))) : resolve(msg.result); } });
function send(method, params = {}) { return new Promise((resolve, reject) => { const requestId = ++id; pending.set(requestId, { resolve, reject }); socket.send(JSON.stringify({ id: requestId, method, params })); }); }
async function evaluate(expression) { const result = await send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true }); if (result.exceptionDetails) throw new Error(JSON.stringify(result.exceptionDetails)); return result.result.value; }
async function navigate(path) { await send('Page.navigate', { url: 'http://127.0.0.1:8000' + path }); await new Promise(resolve => setTimeout(resolve, 800)); await evaluate('document.fonts.ready'); await evaluate(`Promise.all([...document.images].map(image => { image.loading = 'eager'; return image.decode().catch(() => null); }))`); }
await send('Page.enable');
await send('Network.clearBrowserCookies');
await mkdir('storage/app/screenshots', { recursive: true });
const findings = [];
for (const width of [1366, 768, 360]) {
    await send('Emulation.setDeviceMetricsOverride', { width, height: 950, deviceScaleFactor: 1, mobile: width === 360 });
    await navigate('/');
    findings.push(await evaluate(`({page: 'home', width: innerWidth, overflow: document.documentElement.scrollWidth > innerWidth, title: document.title, brokenImages: [...document.images].filter(i => !i.complete || i.naturalWidth === 0).map(i => i.src)})`));
    const shot = await send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true });
    await writeFile(`storage/app/screenshots/home-${width}.png`, Buffer.from(shot.data, 'base64'));
}
await send('Emulation.setDeviceMetricsOverride', { width: 1366, height: 950, deviceScaleFactor: 1, mobile: false });
await navigate('/login');
await evaluate(`document.querySelector('[name=email]').value='mawar@kwt.test'; document.querySelector('[name=password]').value='KwtDemo2026!'; document.querySelector('.login-card').requestSubmit();`);
await new Promise(resolve => setTimeout(resolve, 1000));
findings.push(await evaluate(`({page:'login',path:location.pathname,heading:document.querySelector('h1')?.textContent})`));
for (const path of ['/admin','/admin/cycles/1','/admin/reports','/admin/manage/commodities/create']) {
    await navigate(path);
    findings.push(await evaluate(`({page:location.pathname,width:innerWidth,overflow:document.documentElement.scrollWidth>innerWidth,heading:document.querySelector('h1')?.textContent,brokenImages:[...document.images].filter(i=>!i.complete||i.naturalWidth===0).map(i=>i.src)})`));
    const shot = await send('Page.captureScreenshot', { format: 'png' });
    await writeFile(`storage/app/screenshots/${path.split('/').filter(Boolean).join('-')}.png`, Buffer.from(shot.data, 'base64'));
}
await send('Emulation.setDeviceMetricsOverride', { width: 360, height: 800, deviceScaleFactor: 1, mobile: true });
await navigate('/admin');
findings.push(await evaluate(`({page:'admin-mobile',width:innerWidth,overflow:document.documentElement.scrollWidth>innerWidth,drawer:!!document.querySelector('[data-menu=sidebar]')})`));
const shot = await send('Page.captureScreenshot', { format: 'png' });
await writeFile('storage/app/screenshots/admin-mobile.png', Buffer.from(shot.data, 'base64'));
console.log(JSON.stringify(findings, null, 2));
socket.close();
if (findings.some(f => f.overflow || f.brokenImages?.length) || findings.find(f => f.page === 'login').path !== '/admin') process.exitCode = 1;
