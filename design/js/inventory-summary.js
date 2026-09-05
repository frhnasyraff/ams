$(document).ready(function () {
    const theme = {
        text: '#f8fbff',
        muted: '#9fb6d4',
        grid: 'rgba(148, 163, 184, .16)',
        panel: 'rgba(7, 18, 39, .92)',
        cyan: '#2dd4bf',
        blue: '#3b82f6',
        green: '#31c875',
        amber: '#f5a524',
        red: '#f05d68',
        purple: '#9b6cf6'
    };

    if (window.Chart && Chart.defaults && Chart.defaults.global) {
        Chart.defaults.global.defaultFontFamily = 'Poppins, Montserrat, sans-serif';
        Chart.defaults.global.defaultFontColor = theme.text;
    }

    const summaryCache = { asset: null, component: null };
    const inventoryCharts = { asset: null, component: null };

    function number(value) {
        return Number(value || 0);
    }

    function baseUrl(path) {
        const cleanPath = String(path || '').replace(/^\//, '');
        const appBase = (typeof base_url !== 'undefined' ? base_url : (window.AMS_BASE_URL || window.APP_BASE_URL || '/')).replace(/\/+$/, '/');
        return appBase + cleanPath;
    }

    function totalBy(rows, key) {
        return (rows || []).reduce((sum, row) => sum + number(row[key]), 0);
    }

    function setMetric(name, value) {
        $('[data-inventory-metric="' + name + '"]').text(number(value).toLocaleString());
    }

    function setLabel(name, value) {
        $('[data-inventory-label="' + name + '"]').text(value);
    }

    function setNote(name, value) {
        $('[data-inventory-note="' + name + '"]').text(value);
    }

    function wrapAxisLabel(value, lineLength = 15) {
        const words = String(value || '').trim().split(/\s+/).filter(Boolean);
        const lines = [];
        let line = '';

        words.forEach(word => {
            const candidate = line ? line + ' ' + word : word;
            if (line && candidate.length > lineLength) {
                lines.push(line);
                line = word;
            } else {
                line = candidate;
            }
        });

        if (line) lines.push(line);
        return lines.length ? lines : [''];
    }

    function sizeChartStage(canvas, labels) {
        if (!canvas) return;

        const frame = canvas.closest('.inventory-chart-frame');
        const stage = canvas.closest('.inventory-chart-stage');
        if (!frame || !stage) return;

        const availableWidth = Math.max(frame.clientWidth - 16, 320);
        const categoryWidth = window.innerWidth <= 600 ? 118 : 132;
        stage.style.width = Math.max(availableWidth, labels.length * categoryWidth) + 'px';
    }

    function resizeInventoryChart(type, canvas, labels) {
        sizeChartStage(canvas, labels);
        if (inventoryCharts[type]) inventoryCharts[type].resize();
    }

    function updateSnapshot(type, rows) {
        if (!Array.isArray(rows)) return;

        const isAsset = type === 'asset';
        const total = totalBy(rows, isAsset ? 'total_assets' : 'total_quantity');
        const serviceable = totalBy(rows, isAsset ? 'assets_serviceable' : 'items_serviceable_count');
        const store = totalBy(rows, isAsset ? 'total_store' : 'items_in_store_count');
        const attention = isAsset
            ? totalBy(rows, 'corrective') + totalBy(rows, 'preventive')
            : totalBy(rows, 'total_corrective_maintenance');

        setLabel('total', isAsset ? 'Total Assets' : 'Total Components');
        setLabel('attention', isAsset ? 'Maintenance' : 'Corrective');
        setMetric('total', total);
        setMetric('serviceable', serviceable);
        setMetric('store', store);
        setMetric('attention', attention);
        setNote('total', 'Across ' + rows.length + ' categor' + (rows.length === 1 ? 'y' : 'ies'));
        setNote('serviceable', total ? Math.round((serviceable / total) * 100) + '% of total inventory' : 'Ready for operation');
        setNote('store', 'Available in storage');
        setNote('attention', isAsset ? 'Corrective + preventive' : 'Open maintenance items');
    }

    function chartOptions(titleText, yTitle, stacked = false) {
        return {
            responsive: true,
            maintainAspectRatio: false,
            tooltips: {
                mode: 'index',
                intersect: false,
                backgroundColor: 'rgba(3, 10, 24, .96)',
                titleFontColor: '#ffffff',
                bodyFontColor: '#dbeafe',
                borderColor: 'rgba(56, 189, 248, .35)',
                borderWidth: 1,
                cornerRadius: 12,
                xPadding: 12,
                yPadding: 12
            },
            legend: {
                position: 'bottom',
                labels: {
                    fontColor: theme.muted,
                    usePointStyle: true,
                    padding: 16,
                    boxWidth: 10,
                    fontSize: 12,
                    fontStyle: 'bold'
                }
            },
            title: {
                display: false,
                text: titleText,
                fontColor: theme.text,
                fontSize: 16,
                fontStyle: 'bold'
            },
            scales: {
                xAxes: [{
                    stacked,
                    ticks: {
                        fontColor: theme.muted,
                        fontSize: 11,
                        fontStyle: 'bold',
                        maxRotation: 0,
                        minRotation: 0,
                        autoSkip: false,
                        padding: 8,
                        callback: function (value) { return wrapAxisLabel(value); }
                    },
                    gridLines: { color: 'rgba(148, 163, 184, .07)', drawBorder: false }
                }],
                yAxes: [{
                    stacked,
                    ticks: { fontColor: theme.muted, beginAtZero: true, precision: 0, fontSize: 12, fontStyle: 'bold' },
                    gridLines: { color: theme.grid, drawBorder: false },
                    scaleLabel: { display: true, labelString: yTitle, fontColor: theme.muted, fontSize: 12, fontStyle: 'bold' }
                }]
            }
        };
    }
    function barDataset(label, data, color, order = 1) {
        return {
            label,
            data,
            backgroundColor: color + 'cc',
            borderColor: color,
            borderWidth: 1.5,
                        barPercentage: .62,
            categoryPercentage: .78,
            order
        };
    }

    function drawAssetChart(response) {
        if (!Array.isArray(response)) return;
        summaryCache.asset = response;
        if ($('#asset_tab').hasClass('active')) updateSnapshot('asset', response);
        const labels = response.map(item => item.equipment_type);
        const totalData = response.map(item => number(item.total_assets));
        const serviceableData = response.map(item => number(item.assets_serviceable));
        const storeData = response.map(item => number(item.total_store));
        const correctiveData = response.map(item => number(item.corrective));
        const preventiveData = response.map(item => number(item.preventive));
        const ctx = $('#stackedChartID')[0];
        if (!ctx) return;

        sizeChartStage(ctx, labels);

        if (inventoryCharts.asset) inventoryCharts.asset.destroy();
        inventoryCharts.asset = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    barDataset('Total Quantity', totalData, theme.blue, 0),
                    barDataset('Serviceable', serviceableData, theme.green, 1),
                    barDataset('In Store', storeData, theme.purple, 2),
                    barDataset('Corrective', correctiveData, theme.red, 3),
                    barDataset('Preventive', preventiveData, theme.amber, 4)
                ]
            },
            options: chartOptions('Asset Status Overview', 'Number of Assets'),

        });
    }

    function drawItemChart(response) {
        if (!Array.isArray(response)) return;
        summaryCache.component = response;
        if ($('#item_tab').hasClass('active')) updateSnapshot('component', response);
        const labels = response.map(item => item.item_type);
        const storeData = response.map(item => number(item.items_in_store_count));
        const correctiveMaintenanceData = response.map(item => number(item.total_corrective_maintenance));
        const itemsServiceableData = response.map(item => number(item.items_serviceable_count));
        const ctx = $('#stackedChartItem')[0];
        if (!ctx) return;

        sizeChartStage(ctx, labels);

        if (inventoryCharts.component) inventoryCharts.component.destroy();
        inventoryCharts.component = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    barDataset('Serviceable', itemsServiceableData, theme.green, 1),
                    barDataset('Store', storeData, theme.purple, 2),
                    barDataset('Corrective Maintenance', correctiveMaintenanceData, theme.red, 3)
                ]
            },
            options: chartOptions('Component Status Overview', 'Number of Components', false),

        });
    }

    $.ajax({
        url: baseUrl('InventorySummary/getAssetSummary'),
        type: 'GET',
        dataType: 'json',
        success: drawAssetChart,
        error: function (xhr, status, error) { console.error('Error fetching asset inventory summary:', error); }
    });

    $.ajax({
        url: baseUrl('InventorySummary/getItemSummary'),
        type: 'GET',
        dataType: 'json',
        success: drawItemChart,
        error: function (xhr, status, error) { console.error('Error fetching component inventory summary:', error); }
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (event) {
        if (event.target.id === 'item_tab' && summaryCache.component) {
            updateSnapshot('component', summaryCache.component);
            resizeInventoryChart('component', $('#stackedChartItem')[0], summaryCache.component.map(item => item.item_type));
        } else if (event.target.id === 'asset_tab' && summaryCache.asset) {
            updateSnapshot('asset', summaryCache.asset);
            resizeInventoryChart('asset', $('#stackedChartID')[0], summaryCache.asset.map(item => item.equipment_type));
        }
    });

    let chartResizeTimer;
    $(window).on('resize.inventorySummary', function () {
        window.clearTimeout(chartResizeTimer);
        chartResizeTimer = window.setTimeout(function () {
            if (summaryCache.asset) {
                resizeInventoryChart('asset', $('#stackedChartID')[0], summaryCache.asset.map(item => item.equipment_type));
            }
            if (summaryCache.component) {
                resizeInventoryChart('component', $('#stackedChartItem')[0], summaryCache.component.map(item => item.item_type));
            }
        }, 120);
    });
});



