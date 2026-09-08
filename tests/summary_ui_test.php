<?php
// Render production markup/styles with fixture data; no live database or API calls.
function site_url($path = '') { return '/' . $path; }
function check($condition, $message) { if (!$condition) { throw new RuntimeException($message); } echo "PASS: $message\n"; }
set_error_handler(function ($severity, $message) { throw new ErrorException($message, 0, $severity); });
$totalAssets = 30; $totalLocations = 6; $totalAssetsServiceable = 18; $UnServiceable_assets = 4; $totalAssetsInMaintenance = 8;
$total_items = 36; $storelocationItemCount = 3; $ServiceableCount = 20; $UnserviceableCount = 5; $MaintinenceItemCount = 8;
$asset_maintenanceAlertMessage = 2;
ob_start(); require __DIR__ . '/../application/views/order_summary.php'; $html = ob_get_clean();
$dom = new DOMDocument(); libxml_use_internal_errors(true); $dom->loadHTML($html); libxml_clear_errors();
$xpath = new DOMXPath($dom);
check($xpath->query('//main[@class="ams-order-summary"]')->length === 1, 'Summary uses a single scoped root');
check($xpath->query('//section[@class="ams-summary-charts"]/article')->length === 6, 'All six cards are siblings, not nested');
check($xpath->query('//section[@class="ams-summary-bottom"]/article')->length === 2, 'Table and map are separate sibling panels');
foreach (['pie-chart-quantity','pie-chart-location','pie-chart-asset','pie-chart-faulty','pie-chart-maintenance','pie-chart-store-summary','home','map','maintenance-box'] as $id) {
    check($xpath->query('//*[@id="' . $id . '"]')->length === 1, "$id hook remains unique");
}
check(!str_contains($html, 'All systems operational'), 'No unconditional all-operational claim');
check(!str_contains($html, 'summary-panel-heading'), 'Legacy Summary reshaping selectors do not match');
check(str_contains(file_get_contents(__DIR__ . '/../application/controllers/Order_summary.php'), "'design/js/summary-chart-canvas-fix.js'"), 'Summary loads its chart helper');
if (isset($argv[1])) {
    $root = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $preview = '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    foreach (['sb-admin-2.min.css','order-summary.css','order-summary-cards.css','order-summaryMaintenance.css','styles.css','steve-dark-theme.css','ams-summary-layout.css'] as $css) {
        $preview .= '<link rel="stylesheet" href="file:///' . $root . '/design/css/' . $css . '">';
    }
    $preview .= '<style>body{background:#050b18!important}#content{padding:24px}.container-fluid{padding:0!important}#map{background:#163650}</style></head><body data-controller="order_summary"><div id="content"><div class="container-fluid">' . $html . '</div></div>';
    $preview .= '<script src="file:///' . $root . '/design/vendor/chart.js/Chart.min.js"></script><script src="file:///' . $root . '/design/js/summary-chart-canvas-fix.js"></script>';
    $preview .= <<<'HTML'
<script>
window.addEventListener('load', function () {
    const totals = [30,6,18,4,8];
    document.querySelectorAll('.ams-summary-chart canvas').forEach(node => new Chart(prepareSummaryChartCanvas(node.id), {type: 'doughnut', data: {labels: ['A','B'], datasets: [{data: [4,2], backgroundColor: ['#24c8f7','#2bdfa5'], borderColor: '#e8f5ff', borderWidth: 2}]}, options: {animation: {duration: 0}, responsive: false, maintainAspectRatio: false, cutoutPercentage: 75, legend: {display: false}}}));
    document.querySelectorAll('.ams-donut-total p').forEach((node, index) => node.textContent = totals[index]);
    document.querySelectorAll('.ams-summary-breakdown').forEach(node => node.innerHTML = '<div class="breakdown-item"><span class="type">Water Jetter</span><strong class="total">4</strong></div><div class="breakdown-item"><span class="type">Arm Roll RORO</span><strong class="total">2</strong></div>');
    document.querySelector('#home tbody').innerHTML = '<tr><td>RRB_2</td><td>Johor</td><td>SERVICEABLE</td></tr><tr><td>RRB_4</td><td>Selangor</td><td>MAINTENANCE</td></tr>';
    function overlaps(a,b) { a = a.getBoundingClientRect(); b = b.getBoundingClientRect(); return a.left < b.right - 1 && b.left < a.right - 1 && a.top < b.bottom - 1 && b.top < a.bottom - 1; }
    const cards = [...document.querySelectorAll('.ams-summary-charts > article')];
    const panels = [...document.querySelectorAll('.ams-summary-bottom > article')];
    const result = {
        noCardOverlap: cards.every((card,i) => cards.slice(i+1).every(next => !overlaps(card,next))),
        noPanelOverlap: !overlaps(panels[0],panels[1]),
        panelsAfterCharts: Math.min(...panels.map(node => node.getBoundingClientRect().top)) >= Math.max(...cards.map(node => node.getBoundingClientRect().bottom)),
        noHorizontalOverflow: document.documentElement.scrollWidth <= window.innerWidth + 1,
        chartsVisible: [...document.querySelectorAll('.ams-summary-chart')].every(node => node.getBoundingClientRect().height > 150),
        titlesReadable: getComputedStyle(document.querySelector('.ams-summary-card h2')).color === 'rgb(244, 249, 255)',
        chartsDrawn: [...document.querySelectorAll('.ams-summary-chart canvas')].every(node => node.getContext('2d').getImageData(0,0,node.width,node.height).data.some((value,i) => i % 4 === 3 && value > 0))
    };
    document.body.dataset.testResult = Object.values(result).every(Boolean) ? 'PASS' : 'FAIL';
    const report = document.createElement('pre'); report.id = 'ui-test-results'; report.textContent = JSON.stringify(result); document.body.append(report);
});
</script></body></html>
HTML;
    file_put_contents($argv[1], $preview);
}
