$(document).ready(function () {
    var filterValue = '';
    var locationCharts = {};
    var stateChartSources = {};
    var stateColorLookup = {};
    var statePalette = [
        '#2f80ff', '#2dd4bf', '#9b6cf6', '#f59e0b', '#f05d68',
        '#22c55e', '#38bdf8', '#ec4899', '#a3e635', '#fb7185',
        '#14b8a6', '#8b5cf6', '#f97316', '#06b6d4', '#84cc16',
        '#eab308', '#6366f1', '#10b981', '#d946ef', '#ef4444'
    ];

    function lsUrl(path) {
        var pathname = window.location.pathname;
        var marker = pathname.toLowerCase().indexOf('/location_summary');
        var basePath = marker >= 0 ? pathname.substring(0, marker) : '';
        return basePath + '/' + path.replace(/^\/+/, '');
    }

    function escapeLocationValue(value) {
        return String(value == null || value === '' ? '-' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function displayOnly(value, type, html) {
        return type && type !== 'display' ? (value || '') : html;
    }

    function nameCell(value, type, item) {
        var icon = item ? 'fa-cubes' : 'fa-box';
        var className = item ? ' is-item' : '';
        return displayOnly(value, type,
            '<span class="location-name-cell' + className + '"><i class="fas ' + icon + '"></i><span>' + escapeLocationValue(value) + '</span></span>'
        );
    }

    function typeCell(value, type, item) {
        var className = item ? ' is-item' : '';
        return displayOnly(value, type,
            '<span class="location-type-chip' + className + '">' + escapeLocationValue(value) + '</span>'
        );
    }

    function locationCell(value, type) {
        return displayOnly(value, type,
            '<span class="location-place-cell"><i class="fas fa-map-marker-alt"></i>' + escapeLocationValue(value) + '</span>'
        );
    }

    function dateCell(value, type) {
        return displayOnly(value, type,
            '<span class="location-date-cell"><i class="far fa-calendar-alt"></i>' + escapeLocationValue(value) + '</span>'
        );
    }

    function codeCell(value, type, tone) {
        return displayOnly(value, type,
            '<span class="location-code-chip ' + tone + '">' + escapeLocationValue(value) + '</span>'
        );
    }

    function statusCell(value, type) {
        var label = String(value == null || value === '' ? 'Unknown' : value);
        var normalized = label.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        return displayOnly(value, type,
            '<span class="location-status-chip status-' + normalized + '"><i></i>' + escapeLocationValue(label) + '</span>'
        );
    }

    function initializeDataTable(tableId, type, columns) {
        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().destroy();
        }
        $(tableId).DataTable({
            autoWidth: false,
            pageLength: 10,
            pagingType: 'simple_numbers',
            stateSave: true,
            responsive: true,
            order: [[0, 'asc']],
            dom: '<"location-dt-top"<"location-dt-left"l><"location-dt-right"f>>t<"location-dt-bottom"ip>',
            ajax: {
                url: lsUrl('/location_summary/ajax_list'),
                type: 'GET',
                data: function (d) {
                    d.filter = filterValue === 'N/A' ? '' : filterValue;
                    d.type = type;
                },
                error: function (xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.redirect) {
                        window.location.href = xhr.responseJSON.redirect;
                    } else {
                        console.error('Location summary API error', xhr.status, xhr.responseText);
                    }
                }
            },
            columns: columns,
            language: {
                search: '',
                searchPlaceholder: 'Search...'
            }
        });
    }

    function stateColor(label, index) {
        var key = String(label || index);
        if (!stateColorLookup[key]) {
            stateColorLookup[key] = statePalette[Object.keys(stateColorLookup).length % statePalette.length];
        }
        return stateColorLookup[key];
    }

    function setBarChartHeight(canvas, itemCount) {
        var chartWrap = canvas ? canvas.closest('.location-chart-wrap') : null;
        if (!chartWrap) return;
        var rowHeight = window.innerWidth <= 620 ? 34 : 30;
        var chartHeight = Math.max(250, (itemCount * rowHeight) + 94);
        chartWrap.style.setProperty('height', chartHeight + 'px', 'important');
        chartWrap.style.setProperty('min-height', chartHeight + 'px', 'important');
    }

    function createBarChart(id, labels, values, datasetLabel) {
        var canvas = document.getElementById(id);
        if (!canvas || typeof Chart === 'undefined') return;
        var ctx = canvas.getContext('2d');
        var colors = labels.map(stateColor);
        setBarChartHeight(canvas, labels.length);
        if (locationCharts[id]) locationCharts[id].destroy();
        locationCharts[id] = new Chart(ctx, {
            type: 'horizontalBar',
            data: {
                labels: labels,
                datasets: [{
                    label: datasetLabel,
                    data: values,
                    backgroundColor: colors,
                    hoverBackgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 1,
                    barThickness: 14,
                    maxBarThickness: 18,
                    barPercentage: .58,
                    categoryPercentage: .72
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                layout: { padding: { top: 12, right: 20, bottom: 8, left: 8 } },
                scales: {
                    xAxes: [{
                        ticks: { beginAtZero: true, precision: 0, fontColor: '#b9c9de', fontStyle: '700' },
                        gridLines: { color: 'rgba(107,169,255,.12)', zeroLineColor: 'rgba(107,169,255,.22)' },
                        scaleLabel: { display: true, labelString: 'Number of ' + datasetLabel, fontColor: '#9fb7d2' }
                    }],
                    yAxes: [{
                        ticks: { fontColor: '#eaf4ff', fontStyle: '700', padding: 8 },
                        gridLines: { display: false },
                        scaleLabel: { display: true, labelString: 'State', fontColor: '#9fb7d2' }
                    }]
                },
                tooltips: {
                    backgroundColor: '#07162b',
                    titleFontColor: '#fff',
                    bodyFontColor: '#dff6ff',
                    borderColor: '#27b8ff',
                    borderWidth: 1
                },
                plugins: { datalabels: { color: '#fff', anchor: 'end', align: 'right', font: { weight: 'bold', size: 12 } } }
            }
        });
    }

    function renderStateChart(type, selectedState) {
        var source = stateChartSources[type];
        if (!source) return;
        var labels = source.labels.slice();
        var values = source.values.slice();

        if (selectedState) {
            var selectedIndex = labels.indexOf(selectedState);
            labels = selectedIndex >= 0 ? [labels[selectedIndex]] : [];
            values = selectedIndex >= 0 ? [values[selectedIndex]] : [];
        }

        createBarChart(source.id, labels, values, source.datasetLabel);
    }

    function createDonut() {
        var payload = window.locationSummaryPayload || {};
        var canvas = document.getElementById('assetCoverageDonut');
        if (!canvas || typeof Chart === 'undefined') return;
        var ctx = canvas.getContext('2d');
        if (locationCharts.assetCoverageDonut) locationCharts.assetCoverageDonut.destroy();
        locationCharts.assetCoverageDonut = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Unassigned', 'Assigned'],
                datasets: [{
                    data: [payload.unassignedAssets || 0, payload.assignedAssets || 0],
                    backgroundColor: ['#8c55ff', '#1598ff'],
                    borderColor: '#07162b',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutoutPercentage: 68,
                legend: { display: false },
                tooltips: { backgroundColor: '#07162b', borderColor: '#27b8ff', borderWidth: 1 }
            }
        });
    }

    var payload = window.locationSummaryPayload || { assetLabels: [], assetValues: [], itemLabels: [], itemValues: [] };
    stateChartSources.asset = { id: 'assetLocationBarChart', labels: payload.assetLabels || [], values: payload.assetValues || [], datasetLabel: 'Assets' };
    stateChartSources.item = { id: 'itemLocationBarChart', labels: payload.itemLabels || [], values: payload.itemValues || [], datasetLabel: 'Items' };
    renderStateChart('asset', '');
    renderStateChart('item', '');
    createDonut();

    initializeDataTable('#locations_tabel', 'asset', [
        { data: 'equipment_name', render: function (data, type) { return nameCell(data, type, false); } },
        { data: 'asset_type', render: function (data, type) { return typeCell(data, type, false); } },
        { data: 'location_name', render: locationCell },
        { data: 't_updated', render: dateCell },
        { data: 'vendor', render: function (data, type) { return codeCell(data, type, 'vendor'); } },
        { data: 'manufacturer_drwing_number', render: function (data, type) { return codeCell(data, type, 'manufacturer'); } },
        { data: 'equipment_status', render: statusCell }
    ]);

    initializeDataTable('#item_locations_tabel', 'item', [
        { data: 'item_name', render: function (data, type) { return nameCell(data, type, true); } },
        { data: 'item_type_name', render: function (data, type) { return typeCell(data, type, true); } },
        { data: 'location_name', render: locationCell },
        { data: 'equipment_registration', render: function (data, type) { return codeCell(data, type, 'asset-number'); } },
        { data: 'name', render: statusCell }
    ]);

    function refreshLocationLayout() {
        Object.keys(locationCharts).forEach(function (key) {
            if (locationCharts[key] && typeof locationCharts[key].resize === 'function') {
                locationCharts[key].resize();
                locationCharts[key].update(0);
            }
        });

        ['#locations_tabel', '#item_locations_tabel'].forEach(function (tableId) {
            if (!$.fn.DataTable.isDataTable(tableId)) return;
            var table = $(tableId).DataTable();
            table.columns.adjust();
            if (table.responsive && typeof table.responsive.recalc === 'function') {
                table.responsive.recalc();
            }
        });
    }

    var locationResizeTimer;
    $(window).on('resize.locationSummary', function () {
        clearTimeout(locationResizeTimer);
        locationResizeTimer = setTimeout(refreshLocationLayout, 120);
    });

    var locationSummaryRoot = document.querySelector('.location-summary-redesign');
    if (locationSummaryRoot && typeof ResizeObserver !== 'undefined') {
        var locationResizeObserver = new ResizeObserver(function () {
            clearTimeout(locationResizeTimer);
            locationResizeTimer = setTimeout(refreshLocationLayout, 80);
        });
        locationResizeObserver.observe(locationSummaryRoot);
    }

    setTimeout(refreshLocationLayout, 120);

    $(document).on('click', '.location-tabs a', function (e) {
        e.preventDefault();
        var target = $(this).attr('href');
        if (!target || !$(target).length) return;

        $('.location-tabs a').removeClass('active').attr('aria-selected', 'false');
        $(this).addClass('active').attr('aria-selected', 'true');

        $('.location-tab-content .tab-pane').removeClass('show active');
        $(target).addClass('show active');

        setTimeout(refreshLocationLayout, 80);
    });

    $(document).on('click', '#filterTab', function () {
        filterValue = $(this).val() || '';
        $('.location-kpi-card, .location-tabs a').removeClass('is-filtered');
        $(this).addClass('is-filtered');
        $('#locations_tabel').DataTable().ajax.reload();
        $('#item_locations_tabel').DataTable().ajax.reload();
    });

    $(document).on('click', '#total-btn, #item-total-btn, #active-location-card, #states-with-assets-card', function () {
        filterValue = '';
        $('.location-kpi-card').removeClass('is-filtered');
        $(this).addClass('is-filtered');
        $('#locations_tabel').DataTable().ajax.reload();
        $('#item_locations_tabel').DataTable().ajax.reload();
    });

    $(document).on('change', '.location-state-filter select', function () {
        var type = $(this).data('chart-type');
        var selectedState = $(this).val() || '';
        filterValue = selectedState;
        renderStateChart(type, selectedState);
        $('.location-kpi-card').removeClass('is-filtered');

        var tableId = type === 'item' ? '#item_locations_tabel' : '#locations_tabel';
        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().ajax.reload();
        }
    });
});

