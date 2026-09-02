(function ($) {
    'use strict';

    function number(value) {
        var parsed = parseInt(value, 10);
        return isNaN(parsed) ? 0 : parsed;
    }

    function setText(selector, value) {
        $(selector).text(number(value));
    }

    function chartData(values, colors) {
        var hasData = values.some(function (value) { return value > 0; });
        return {
            values: hasData ? values : [1],
            colors: hasData ? colors : ['rgba(103, 133, 173, .22)'],
            empty: !hasData
        };
    }

    function createDoughnut(canvasId, labels, values, colors) {
        var canvas = document.getElementById(canvasId);
        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        var prepared = chartData(values, colors);

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: prepared.empty ? ['No records'] : labels,
                datasets: [{
                    data: prepared.values,
                    backgroundColor: prepared.colors,
                    borderColor: '#07162b',
                    borderWidth: 4,
                    hoverBorderColor: '#0b203b',
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutoutPercentage: 72,
                animation: { duration: 650 },
                legend: { display: false },
                tooltips: {
                    enabled: !prepared.empty,
                    backgroundColor: '#061326',
                    titleFontColor: '#ffffff',
                    bodyFontColor: '#dbeafe',
                    borderColor: '#27598a',
                    borderWidth: 1,
                    displayColors: true
                },
                plugins: { datalabels: { display: false } }
            }
        });
    }

    $.when(
        $.ajax({
            url: appUrl('/corrective_maintenance/corrective_table_list_all_status'),
            method: 'GET',
            dataType: 'json'
        }),
        $.ajax({
            url: appUrl('/corrective_maintenance/corrective_table_list'),
            method: 'GET',
            dataType: 'json'
        })
    ).done(function (openResult, completeResult) {
        var openResponse = openResult[0] || {};
        var completeResponse = completeResult[0] || {};
        var openSummary = openResponse.summary || {};
        var completeSummary = completeResponse.summary || {};

        var maintenance = number(openSummary.corrective_in_maintenance);
        var progress = number(openSummary.corrective_in_progress_count);
        var complete = number(completeSummary.corrective_complete_count);
        var open = maintenance + progress;
        var total = open + complete;

        setText('#corrective-kpi-total', total);
        setText('#corrective-kpi-maintenance', maintenance);
        setText('#corrective-kpi-progress', progress);
        setText('#corrective-kpi-complete', complete);
        setText('#pie-chart-asset-total-all-status', open);
        setText('#pie-chart-asset-total', complete);
        setText('#corrective-legend-maintenance', maintenance);
        setText('#corrective-legend-progress', progress);
        setText('#corrective-legend-complete', complete);
        setText('#corrective-legend-open', open);
        setText('#corrective-legend-done', complete);

        createDoughnut('pie-chart-asset-all-status', ['Requires Maintenance', 'In Progress', 'Completed'], [maintenance, progress, complete], ['#f59e0b', '#9b6dff', '#2dd4a7']);
        createDoughnut('pie-chart-asset', ['Open Jobs', 'Completed Jobs'], [open, complete], ['#2f80ff', '#2dd4a7']);
    }).fail(function () {
        $('.corrective-live-card__label').addClass('is-offline').html('<i></i> Data unavailable');
    });
})(jQuery);
