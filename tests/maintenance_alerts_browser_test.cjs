const fs = require('fs');
const path = require('path');
const assert = require('assert/strict');
const { execFileSync } = require('child_process');
const { chromium } = require('playwright');
const root = path.resolve(__dirname, '..');
(async () => {
    const browser = await chromium.launch({headless: true, channel: process.env.PLAYWRIGHT_CHANNEL || 'msedge'});
    try {
        const page = await browser.newPage();
        const header = fs.readFileSync(path.join(root, 'application/views/header.php'), 'utf8');
        const bell = header.match(/<li class="nav-item maintenance-alert-nav">[\s\S]*?<\/li>/)[0];
        const modal = execFileSync('php', ['-r', '$s=file_get_contents("application/views/footer.php"); $a=strpos($s, \'<div class="modal fade maintenance-alert-modal"\'); $b=strpos($s, \'<div class="loading d-none">\', $a); eval("?>".substr($s,$a,$b-$a));'], {cwd:root}).toString();
        const fixtures = [
            {title:'Pump <script>alert(1)</script>', subtitle:'P-001', type:'overdue', status_label:'Overdue', due_date:'2026-09-16', days_text:'1 day(s) overdue', asset_type:'Pump', location:'Workshop', entity_type:'asset', detail_url:'http://localhost/assets_IT-usman/assets/info?id=1'},
            {title:'Valve component', subtitle:'V-002', type:'due_soon', status_label:'Due soon', due_date:'2026-09-18', days_text:'Due in 1 day(s)', asset_type:'Component', location:'Store', entity_type:'component', detail_url:'http://localhost/assets_IT-usman/items/info?id=2'}
        ];
        fixtures[0].maintenance_type = 'Preventive';
        fixtures[0].work_status = 'In Progress';
        fixtures[0].remarks = '<script>unsafe</script>';
        fixtures[0].tasks = [{name:'Check seal', status:'Pending', remarks:'Inspect wear'}];
        let mode = 'success';
        await page.route('**/maintenance_alerts/list', route => route.fulfill({
            status:mode === 'error' ? 500 : 200, contentType:'application/json',
            body:JSON.stringify({success:true, count:mode === 'empty' ? 0 : 2, alerts:mode === 'empty' ? [] : fixtures})
        }));
        await page.route('**/phase-one-test', route => route.fulfill({contentType:'text/html',body:'<html><body></body></html>'}));
        await page.goto('http://localhost/phase-one-test');
        await page.setContent('<html><body><nav style="height:76px;display:flex;justify-content:flex-end"><ul style="display:flex;list-style:none;margin:0">'+bell+'<li style="width:100px">Admin</li></ul></nav>'+modal+'</body></html>');
        await page.addStyleTag({path:path.join(root,'design/css/sb-admin-2.min.css')});
        await page.addStyleTag({path:path.join(root,'design/css/maintenance-alerts.css')});
        await page.addScriptTag({path:path.join(root,'design/vendor/jquery/jquery.min.js')});
        await page.addScriptTag({path:path.join(root,'design/vendor/bootstrap/js/bootstrap.bundle.min.js')});
        await page.addScriptTag({path:path.join(root,'design/js/maintenance-alerts.js')});
        await page.waitForFunction(() => document.querySelector('#maintenanceAlertBadge').textContent === '2');
        for (const width of [1440,390,320]) {
            await page.setViewportSize({width,height:900});
            await page.click('#maintenanceAlertButton');
            await page.waitForSelector('.maintenance-alert-dropdown.is-open');
            const box = await page.locator('#maintenanceAlertDropdown').boundingBox();
            assert(box.x >= 0 && box.x+box.width <= width, 'Dropdown fits viewport');
            await page.screenshot({path:path.join(process.env.TEMP,'maintenance-alerts-'+width+'.png')});
            await page.click('.maintenance-alert-item >> nth=0');
            await page.waitForSelector('#maintenanceAlertModal.show');
            assert.equal(await page.locator('[data-alert-field="title"]').textContent(), fixtures[0].title);
            assert.equal(await page.locator('[data-alert-field="title"] script').count(), 0);
            assert.equal(await page.locator('[data-alert-field="maintenance_type"]').textContent(), 'Preventive');
            assert.equal(await page.locator('[data-alert-field="work_status"]').textContent(), 'In Progress');
            assert.equal(await page.locator('[data-alert-field="remarks"] script').count(), 0);
            assert.equal(await page.locator('[data-alert-field="tasks"] li').count(), 1);
            assert.equal(await page.locator('[data-alert-action="details"]').getAttribute('href'), fixtures[0].detail_url);
            await page.screenshot({path:path.join(process.env.TEMP,'maintenance-alert-modal-'+width+'.png')});
            await page.click('#maintenanceAlertModal .modal-footer button');
            await page.waitForSelector('#maintenanceAlertModal', {state:'hidden'});
        }
        await page.click('#maintenanceAlertButton');
        await page.click('.maintenance-alert-item >> nth=1');
        await page.waitForSelector('#maintenanceAlertModal.show');
        assert.equal(await page.locator('[data-alert-action="details"]').textContent(), 'Go To Component Detail');
        assert.equal(await page.locator('[data-alert-action="details"]').getAttribute('href'), fixtures[1].detail_url);
        await page.click('#maintenanceAlertModal .modal-footer button');
        await page.waitForSelector('#maintenanceAlertModal', {state:'hidden'});
        await page.click('#maintenanceAlertButton');
        await page.keyboard.press('Escape');
        assert.equal(await page.locator('#maintenanceAlertButton').getAttribute('aria-expanded'), 'false');
        mode = 'empty';
        await page.click('#maintenanceAlertButton');
        await page.waitForFunction(() => document.querySelector('#maintenanceAlertEmpty').textContent.includes('No maintenance'));
        await page.keyboard.press('Escape');
        mode = 'error';
        await page.click('#maintenanceAlertButton');
        await page.waitForFunction(() => document.querySelector('#maintenanceAlertEmpty').textContent.includes('Unable to load'));
        console.log('PASS: desktop/mobile dropdown, popup, escaped text, asset/component links, Escape, empty and error states');
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode=1; });
