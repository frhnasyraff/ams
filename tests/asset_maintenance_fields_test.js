'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const maintenance = require('../design/js/asset-maintenance-fields.js');
const blank = {date: '', frequency: '', reminder: ''};
const saved = {date: '2026-09-10', frequency: '3', reminder: '0'};
let count = 0;
function test(name, run) { run(); count++; console.log('PASS: ' + name); }
test('Enabled type shows fields and inherits defaults for a blank asset', () => {
    const result = maintenance.stateFor({maintenance: 1, maintenance_frequency_year: 4, maintenance_reminder_days: 7}, blank, true);
    assert.equal(result.show, true); assert.equal(result.frequency, 4); assert.equal(result.reminder, 7);
});
test('A saved date, custom frequency and zero reminder are never overwritten', () => {
    const result = maintenance.stateFor({maintenance: 1, maintenance_frequency_year: 4, maintenance_reminder_days: 30}, saved, true);
    assert.equal(result.show, true); assert.equal(result.frequency, '3'); assert.equal(result.reminder, '0');
    assert.equal(saved.date, '2026-09-10');
});
test('Disabled type hides only an empty schedule, not a saved date', () => {
    assert.equal(maintenance.stateFor({maintenance: 0}, blank, true).show, false);
    const result = maintenance.stateFor({maintenance: 0}, saved, true);
    assert.equal(result.show, true); assert.match(result.message, /disabled/);
});
test('Missing schema/type configuration does not silently hide fields', () => {
    const result = maintenance.stateFor({}, blank, true);
    assert.equal(result.show, true); assert.match(result.message, /not configured/);
});
test('Deselecting a type retains an existing maintenance date', () => {
    assert.equal(maintenance.stateFor({}, saved, false).show, true);
    assert.equal(maintenance.stateFor({}, blank, false).show, false);
});
test('Late responses, error envelopes and HTML are not applied', () => {
    assert.equal(maintenance.matches({asset_id: 2, maintenance: 1}, 1), false);
    assert.equal(maintenance.matches({asset_id: 1, status: false}, 1), false);
    assert.equal(maintenance.matches('<html>Login</html>', 1), false);
    assert.equal(maintenance.matches({asset_id: 1, status: true}, '1'), true);
});
test('Every controller-loaded legacy form script is preceded by maintenance module', () => {
    const source = fs.readFileSync(path.join(__dirname, '../application/controllers/Assets.php'), 'utf8');
    const scripts = [...source.matchAll(/'design\/js\/(asset-maintenance-fields|assets-list)\.js[^']*'/g)].map(match => match[1]);
    assert.deepEqual(scripts, ['asset-maintenance-fields', 'assets-list', 'asset-maintenance-fields', 'assets-list', 'asset-maintenance-fields', 'assets-list']);
});
test('Legacy callbacks no longer erase or hide parent maintenance values', () => {
    const source = fs.readFileSync(path.join(__dirname, '../design/js/assets-list.js'), 'utf8');
    assert.doesNotMatch(source, /\$\("#(?:maintenance_date|frequency_year|maintenance_reminder_day)"\)\.(hide|val)/);
    assert.equal((source.match(/amsMaintenance\.apply\(/g) || []).length, 3);
    assert.match(source, /#maintenence_asset_item/); // Component checkbox remains independent.
});
test('Standalone repair is included verbatim in the main deployment patch', () => {
    const root = path.join(__dirname, '../rams_DB');
    const repair = fs.readFileSync(path.join(root, 'patch_asset_type_maintenance.sql'), 'utf8').replace(/\r/g, '').trim();
    const deploy = fs.readFileSync(path.join(root, 'ams_deploy_schema_patch.sql'), 'utf8').replace(/\r/g, '');
    assert.ok(deploy.includes(repair));
    assert.doesNotMatch(repair, /\b(?:UPDATE|DELETE|TRUNCATE)\b/i);
});
console.log(`${count} maintenance field tests passed.`);
