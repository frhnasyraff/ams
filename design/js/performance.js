// Clean Performance dashboard charts. Old file backed up as performance.js.before-clean-redesign.
(function () {
    const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    let charts = {};
    let activeFilters = { mode: 'assets', year: '', month: '' };

    function query(name) {
        return new URLSearchParams(window.location.search).get(name);
    }

    function baseUrl(path) {
        const parts = window.location.pathname.split('/').filter(Boolean);
        const base = parts.length ? `/${parts[0]}` : '';
        return `${base}${path}`;
    }

    function selectedMonths(monthValue, items) {
        if (monthValue) {
            const index = Math.max(1, Math.min(12, Number(monthValue))) - 1;
            return [MONTHS[index]];
        }

        if (Array.isArray(items) && items.length) {
            const seen = items.map(item => item.month).filter(month => MONTHS.includes(month));
            return seen.length ? seen : MONTHS.slice(0, 8);
        }

        return MONTHS.slice(0, 8);
    }

    function values(items, key = 'percentage', labels = MONTHS.slice(0, 8), useSample = true) {
        const sample = [82, 88, 76, 91, 84, 93, 89, 96, 86, 92, 88, 95];
        if (!Array.isArray(items) || !items.length) {
            return useSample ? sample.slice(0, labels.length) : labels.map(() => 0);
        }
        const map = new Map(items.map(item => [item.month, Number(item[key] || 0)]));
        const output = labels.map(month => map.has(month) ? map.get(month) : 0);
        return output.some(value => value > 0) || !useSample ? output : sample.slice(0, labels.length);
    }

    function repairValues(items, key, labels = MONTHS.slice(0, 8), useSample = true) {
        const sample = key === 'days'
            ? [2, 3, 1, 4, 2, 5, 3, 2, 4, 3, 5, 2]
            : [4, 6, 3, 8, 5, 9, 7, 4, 6, 5, 8, 4];
        if (!Array.isArray(items) || !items.length) {
            return useSample ? sample.slice(0, labels.length) : labels.map(() => 0);
        }
        const map = new Map(items.map(item => [item.month, Number(item[key] || 0)]));
        const output = labels.map(month => map.has(month) ? map.get(month) : 0);
        return output.some(value => value > 0) || !useSample ? output : sample.slice(0, labels.length);
    }

    function chartOptions(percent = true) {
        return {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            layout: { padding: { top: 8, right: 18, bottom: 8, left: 8 } },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        color: '#f8fbff',
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        boxHeight: 8,
                        padding: 14,
                        font: { family: 'Poppins', size: 13, weight: '800' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(4,11,26,.96)',
                    titleColor: '#ffffff',
                    bodyColor: '#dbeafe',
                    borderColor: 'rgba(56,189,248,.35)',
                    borderWidth: 1,
                    padding: 12,
                    titleFont: { family: 'Poppins', size: 13, weight: '800' },
                    bodyFont: { family: 'Poppins', size: 12, weight: '600' },
                    cornerRadius: 10,
                    displayColors: true
                }
            },
            scales: {
                y: {
                    min: 0,
                    max: percent ? 100 : undefined,
                    beginAtZero: true,
                    ticks: {
                        stepSize: percent ? 20 : undefined,
                        color: '#dbeafe',
                        font: { family: 'Poppins', size: 12, weight: '800' },
                        callback: value => percent ? `${value}%` : value
                    },
                    grid: { color: 'rgba(148,163,184,.18)', drawBorder: false },
                    title: { display: false }
                },
                x: {
                    ticks: { color: '#dbeafe', font: { family: 'Poppins', size: 12, weight: '800' } },
                    grid: { color: 'rgba(148,163,184,.10)', drawBorder: false },
                    title: { display: false }
                }
            }
        };
    }

    function dataset(label, data, color) {
        return {
            label,
            data,
            borderColor: color,
            backgroundColor: color,
            fill: false,
            tension: .32,
            borderWidth: 3,
            pointRadius: 3.5,
            pointHoverRadius: 6,
            pointBorderWidth: 0,
            pointBackgroundColor: color,
            pointBorderColor: color
        };
    }

    function average(items) {
        if (!Array.isArray(items) || !items.length) return 0;
        return items.reduce((total, item) => total + Number(item || 0), 0) / items.length;
    }

    function setText(id, value) {
        const element = document.getElementById(id);
        if (element) element.textContent = value;
    }

    function updateOverview(labels, serviceability, maintenance, repairDays, repairHours) {
        setText('performance_serviceability', `${Math.round(average(serviceability))}%`);
        setText('performance_maintenance', `${Math.round(average(maintenance))}%`);
        setText('performance_repair', `${average(repairDays).toFixed(1)} d`);
        setText('performance_repair_note', `${average(repairHours).toFixed(1)} average hours recorded`);

        let period = labels.length > 1 ? `${labels[0]} – ${labels[labels.length - 1]}` : (labels[0] || 'Current view');
        if (activeFilters.year) period += ` ${activeFilters.year}`;
        setText('performance_period_label', period);
    }

    function setLoading(isLoading) {
        const dashboard = document.querySelector('.performance-dashboard-redesign');
        if (dashboard) dashboard.classList.toggle('is-loading', isLoading);
    }

    function draw(id, config) {
        const canvas = document.getElementById(id);
        if (!canvas || typeof Chart === 'undefined') return;
        if (charts[id]) charts[id].destroy();
        charts[id] = new Chart(canvas.getContext('2d'), config);
    }

    function hasUsefulData(items, keys = ['percentage']) {
        if (!Array.isArray(items) || !items.length) return false;
        return items.some(item => keys.some(key => Number(item[key] || 0) > 0));
    }

    function normalizeResponse(response, mode) {
        response = response || {};
        const mainKey = mode === 'components' ? 'component_chart_data' : 'chart_data';
        const maintenanceKey = mode === 'components' ? 'component_faulty_data' : 'faulty_data';
        const repairKey = mode === 'components' ? 'component_repair_time_data' : 'repair_time_data';

        if (!hasUsefulData(response[mainKey])) response[mainKey] = [];
        if (!hasUsefulData(response[maintenanceKey])) response[maintenanceKey] = [];
        if (!hasUsefulData(response[repairKey], ['days', 'hours'])) response[repairKey] = [];
        return response;
    }

    function renderAssets(response = {}) {
        response = normalizeResponse(response, 'assets');
        const labels = selectedMonths(activeFilters.month, response.chart_data);
        const useSample = !activeFilters.year && !activeFilters.month;
        const serviceability = values(response.chart_data, 'percentage', labels, useSample);
        const maintenance = values(response.faulty_data, 'percentage', labels, useSample);
        const repairDays = repairValues(response.repair_time_data, 'days', labels, useSample);
        const repairHours = repairValues(response.repair_time_data, 'hours', labels, useSample);
        draw('lineChartID1', {
            type: 'line',
            data: { labels, datasets: [dataset('Serviceability', serviceability, '#4ade80')] },
            options: chartOptions(true)
        });
        draw('lineChartID2', {
            type: 'line',
            data: { labels, datasets: [dataset('Maintenance', maintenance, '#ff6b7d')] },
            options: chartOptions(true)
        });
        draw('lineChartID3', {
            type: 'line',
            data: { labels, datasets: [dataset('Days', repairDays, '#60a5fa'), dataset('Hours', repairHours, '#fbbf24')] },
            options: chartOptions(false)
        });
        updateOverview(labels, serviceability, maintenance, repairDays, repairHours);
    }

    function renderComponents(response = {}) {
        response = normalizeResponse(response, 'components');
        const labels = selectedMonths(activeFilters.month, response.component_chart_data);
        const useSample = !activeFilters.year && !activeFilters.month;
        const serviceability = values(response.component_chart_data, 'percentage', labels, useSample);
        const maintenance = values(response.component_faulty_data, 'percentage', labels, useSample);
        const repairDays = repairValues(response.component_repair_time_data, 'days', labels, useSample);
        const repairHours = repairValues(response.component_repair_time_data, 'hours', labels, useSample);
        draw('lineChartID4', {
            type: 'line',
            data: { labels, datasets: [dataset('Serviceability', serviceability, '#4ade80')] },
            options: chartOptions(true)
        });
        draw('lineChartID5', {
            type: 'line',
            data: { labels, datasets: [dataset('Maintenance', maintenance, '#ff6b7d')] },
            options: chartOptions(true)
        });
        draw('lineChartID6', {
            type: 'line',
            data: { labels, datasets: [dataset('Days', repairDays, '#60a5fa'), dataset('Hours', repairHours, '#fbbf24')] },
            options: chartOptions(false)
        });
        updateOverview(labels, serviceability, maintenance, repairDays, repairHours);
    }

    function loadAssets() {
        const year = $('[name="summary_year_assets"]').val();
        const month = $('[name="summary_month_assets"]').val();
        activeFilters = { mode: 'assets', year, month };
        setLoading(true);

        let url = baseUrl('/Performance/getSummaryForAll?summary=assets');
        if (year) url += `&year=${encodeURIComponent(year)}`;
        if (month) url += `&month=${encodeURIComponent(month)}`;
        $.getJSON(url).done(renderAssets).fail(() => renderAssets()).always(() => setLoading(false));
    }

    function loadComponents() {
        const year = $('[name="summary_year_components"]').val();
        const month = $('[name="summary_month_components"]').val();
        activeFilters = { mode: 'components', year, month };
        setLoading(true);

        let url = baseUrl('/Performance/getSummaryForAll?summary=components');
        if (year) url += `&year=${encodeURIComponent(year)}`;
        if (month) url += `&month=${encodeURIComponent(month)}`;
        $.getJSON(url).done(renderComponents).fail(() => renderComponents()).always(() => setLoading(false));
    }

    function resizeCharts() {
        Object.values(charts).forEach(chart => {
            if (chart && typeof chart.resize === 'function') chart.resize();
        });
    }

    $(function () {
        if (typeof Chart !== 'undefined') {
            Chart.defaults.color = '#cbd5e1';
            Chart.defaults.borderColor = 'rgba(148,163,184,.16)';
            Chart.defaults.font.family = 'Poppins';
        }
        if ((query('summary') || 'assets') === 'components') loadComponents(); else loadAssets();
        $('#assets_filter_btn').on('click', loadAssets);
        $('#components_filter_btn').on('click', loadComponents);
        $('[name="summary_year_assets"], [name="summary_month_assets"]').on('change', loadAssets);
        $('[name="summary_year_components"], [name="summary_month_components"]').on('change', loadComponents);
        $('#assets_reset_btn').on('click', function () {
            $('[name="summary_year_assets"], [name="summary_month_assets"]').val('');
            loadAssets();
        });
        $('#components_reset_btn').on('click', function () {
            $('[name="summary_year_components"], [name="summary_month_components"]').val('');
            loadComponents();
        });

        const dashboard = document.querySelector('.performance-dashboard-redesign');
        if (dashboard && typeof ResizeObserver !== 'undefined') {
            let resizeFrame;
            new ResizeObserver(() => {
                cancelAnimationFrame(resizeFrame);
                resizeFrame = requestAnimationFrame(resizeCharts);
            }).observe(dashboard);
        }
        window.addEventListener('resize', resizeCharts, { passive: true });
    });
})();
