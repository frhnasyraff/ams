<?php
// Render the real view with fixtures only: no database, login, or network requests.
define('BASEPATH', __DIR__);
class CI_Model {}
require __DIR__ . '/../application/models/Steve.php';
function site_url($path = '') { return '/' . $path; }
function base_url($path = '') { return site_url($path); }
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
$renderer = new class {
    public $steve;
    public $user_model;
    public function render($withPicture = false, $withQr = false) {
        $this->steve = (new ReflectionClass(Steve::class))->newInstanceWithoutConstructor();
        $this->user_model = new class { public function has_perm($permission) { return false; } };
        $items = (object) array_fill_keys([
            'asset_id', 'calibration', 'calibration_date', 'drawing_number', 'faulty_type_id',
            'frequency_day', 'frequency_year', 'maintenance_reminder_day', 'id', 'item_name',
            'item_status_id', 'item_type_id', 'items_qr_code', 'maintenance', 'maintenance_date',
            'manufacturer_drawing_number', 'manufacturer_name', 'part_number', 'reminder_day',
            'serial_number', 'store_location_id', 'vendor_part_number'
        ], '');
        $items->id = 34;
        $items->asset_id = 10;
        $items->item_name = 'GPS Tracker 34';
        $items->item_status_id = 1;
        $items->item_type_id = 1;
        $items->store_location_id = 1;
        $items->manufacturer_name = 'Mercedes';
        $items->maintenance = 1;
        $items->items_qr_code = $withQr ? 1 : 0;
        $equipments = [(object) ['equipment_id' => 10, 'equipment_name' => 'Asset Unit 010']];
        $manufacturer_number = [(object) ['manufacturer_name' => 'Mercedes']];
        $storeLocation = [(object) ['id' => 1, 'name' => 'STOR UTAMA']];
        $itemStatus = [(object) ['id' => 1, 'name' => 'AVAILABLE']];
        $itemTypes = [(object) ['id' => 1, 'name' => 'GPS Tracker']];
        $part_numbers = $drawing_numbers = $faulty = $task = $ticket = [];
        $pictures = $withPicture ? [(object) ['add_asset_items_id' => 34, 'id' => 1, 'item_picture' => 'example.png']] : [];
        ob_start();
        require __DIR__ . '/../application/views/item-info.php';
        return ob_get_clean();
    }
};
function check($condition, $description) {
    if (!$condition) { throw new RuntimeException($description); }
    echo "PASS: $description\n";
}
$html = $renderer->render();
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
$xpath = new DOMXPath($dom);
check($xpath->query('//*[@id="formA"]//input[@name="item_id" and @value="34"]')->length === 1, 'Component update retains item ID');
check($xpath->query('//*[@id="formA"]//input[@name="id" and @value="10"]')->length === 1, 'Component update retains linked asset ID');
check($xpath->query('//*[@id="formA"]//select[@name="asset_id"]/option[@selected and @value="10"]')->length === 1, 'Linked asset selection is preserved');
check($xpath->query('//*[@id="formA"]//div[contains(@class,"modal-body")]')->length === 0, 'Edit form no longer inherits modal offsets');
check($xpath->query('//*[contains(@class,"component-workspace")]/div[contains(@class,"tab-pane")]')->length === 2, 'Details and maintenance are sibling tabs');
check($xpath->query('//*[contains(@class,"component-workspace")]/div[contains(@class,"tab-pane") and contains(@class,"active")]')->length === 1, 'Only Details is active initially');
check($xpath->query('//*[contains(@class,"component-sidebar")]/div[contains(@class,"card")]')->length === 2, 'Pictures and QR have separate cards');
check($xpath->query('//*[@id="fileElem"]')->length === 1 && $xpath->query('//*[@id="saveBtn"]')->length === 1, 'Upload hooks remain unique');
check(strpos($renderer->render(true), 'data-picture-id="1"') !== false, 'Existing component pictures still render');
check(strpos($renderer->render(false, true), 'title="Scan QR Code"') !== false, 'Existing QR code still renders');
if (isset($argv[1])) {
    $root = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $preview = '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    foreach (['sb-admin-2.min.css', 'custom-select.css', 'styles.css', 'steve-dark-theme.css', 'asset-component-forms.css'] as $css) {
        $preview .= '<link rel="stylesheet" href="file:///' . $root . '/design/css/' . $css . '">';
    }
    $preview .= '<style>body{padding:24px;background:#050b18}#content{max-width:1440px;margin:auto}div{text-transform:capitalize}</style></head>';
    $preview .= '<body id="page-top" data-controller="items" data-method="info"><div id="content">';
    // No live calls or external CDNs in the preview. Exercise real Bootstrap tabs locally.
    $preview .= preg_replace(['~<script\b[^>]*>.*?</script>~is', '~<link\b[^>]*>~is'], '', $html);
    $preview .= '</div><script src="file:///' . $root . '/design/vendor/jquery/jquery.min.js"></script>';
    $preview .= '<script src="file:///' . $root . '/design/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>';
    $preview .= <<<'HTML'
<script>
window.addEventListener('load', async function () {
    const wait = () => new Promise(resolve => setTimeout(resolve, 350));
    const d = document.querySelector('#nav-details');
    const m = document.querySelector('#nav-new-maintenance');
    const label = document.querySelector('label[for=asset_id]');
    const fields = [...document.querySelectorAll('.component-form-grid>.form-group')];
    const checks = {
        hiddenMaintenance: getComputedStyle(m).display === 'none',
        fontSize: getComputedStyle(label).fontSize,
        fontFamily: getComputedStyle(label).fontFamily,
        gridGap: getComputedStyle(d).gap,
        sidebarGap: getComputedStyle(document.querySelector('.component-sidebar')).gap,
        formOverflow: fields.some(x => x.getBoundingClientRect().right > d.getBoundingClientRect().right + 1),
        pageOverflow: document.documentElement.scrollWidth > innerWidth,
        cardOverflow: [...document.querySelectorAll('#nav-details .card')].some(x => x.getBoundingClientRect().right > innerWidth || x.getBoundingClientRect().left < 0),
        layoutDisplay: getComputedStyle(d).display
    };
    document.querySelector('#nav-new-maintenance-tab').click();
    await wait();
    checks.tabsWork = getComputedStyle(d).display === 'none' && getComputedStyle(m).display !== 'none';
    document.querySelector('#nav-details-tab').click();
    await wait();
    checks.detailsRestored = getComputedStyle(m).display === 'none' && getComputedStyle(d).display !== 'none';
    const p = document.createElement('pre');
    p.id = 'layout-checks';
    p.textContent = JSON.stringify(checks);
    document.body.append(p);
    window.scrollTo(0, 0);
});
</script></body></html>
HTML;
    file_put_contents($argv[1], $preview);
}
