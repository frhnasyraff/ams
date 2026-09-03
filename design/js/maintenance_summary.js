$(function () {
    $('#asset_summary').DataTable({
        processing: true,
        responsive: false,
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
        stateSave: true,
        pageLength: 10,
        pagingType: 'simple_numbers',
        dom: '<"report-dt-top"l f>t<"report-dt-bottom"i p>',
        language: {
            search: 'Search:',
            searchPlaceholder: 'Search maintenance records...',
            lengthMenu: 'Show _MENU_ entries',
            emptyTable: 'No preventive maintenance records available'
        },
        ajax: {
            url: reportSuiteUrl('Maintenance_summary_report/ajax_list'),
            type: 'GET',
            error: reportAjaxError
        },
        order: [[1, 'asc']],
        columns: [
            {
                data: 'equipment_id',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<input type="checkbox" name="record[]" class="checkbox-select" value="' + row.equipment_id + '">';
                }
            },
            { data: 'type_name', defaultContent: '-' },
            { data: 'equipment_registration', defaultContent: '-' },
            { data: 'location', defaultContent: '-' },
            { data: 'equipment_name', defaultContent: '-' },
            { data: 'item_type_name', defaultContent: '-' },
            { data: 'manufacturer_name', defaultContent: '-' },
            { data: 'part_number', defaultContent: '-' },
            { data: 'maintenance_type', defaultContent: '-' },
            {
                data: null,
                defaultContent: '-',
                render: function (data, type, row) {
                    return getCurrentIntervalMaintenanceDate(row.maintenance_date, row.frequency_year, row.last_maintenance);
                }
            },
            { data: 'actual_date', defaultContent: '-' },
            { data: 'task_done', defaultContent: '-' },
            {
                data: 'equipment_status',
                defaultContent: '-',
                render: function (data, type) {
                    return type === 'display' ? reportStatus(data) : data;
                }
            },
            {
                data: null,
                defaultContent: '-',
                render: function (data, type, row) {
                    return maintenanceDelay(row);
                }
            }
        ],
        drawCallback: reportDrawCallback,
        initComplete: function () {
            var table = this.api();
            var wrapper = $(table.table().container());

            window.requestAnimationFrame(function () {
                wrapper.find('.dataTables_scrollBody').scrollLeft(0);
                table.columns.adjust();
            });
        }
    });
});

function reportAjaxError(xhr) {
    if (xhr.responseJSON && xhr.responseJSON.redirect) {
        window.location.href = xhr.responseJSON.redirect;
        return;
    }

    $('#asset_summary_processing').html('<span class="report-load-error">Unable to load report data. Please refresh.</span>');
}

function reportDrawCallback() {
    if (typeof initToggle === 'function') initToggle();
    if (typeof updateReportOverview === 'function') updateReportOverview();
}

function reportStatus(value) {
    var label = value || 'Not set';
    var className = String(label).toLowerCase().replace(/[^a-z0-9]+/g, '-');
    return '<span class="report-status-pill report-status-' + className + '">' + $('<div>').text(label).html() + '</span>';
}

function getCurrentIntervalMaintenanceDate(startDate, frequency, latestMaintenance) {
    var count = parseInt(frequency, 10);

    if (!startDate || !latestMaintenance || !count || count < 1) return '-';

    var interval = 12 / count;
    var start = new Date(startDate);
    var latest = new Date(latestMaintenance);
    var current = null;

    if (isNaN(start.getTime()) || isNaN(latest.getTime())) return '-';

    for (var index = 0; index < count; index += 1) {
        var candidate = new Date(start);
        candidate.setUTCMonth(start.getUTCMonth() + (index * interval));
        if (candidate > latest) break;
        current = candidate;
    }

    return current ? current.toISOString().split('T')[0] : start.toISOString().split('T')[0];
}

function maintenanceDelay(row) {
    var planned = getCurrentIntervalMaintenanceDate(row.maintenance_date, row.frequency_year, row.last_maintenance);
    var actualValue = row.actual_date && row.actual_date.date ? row.actual_date.date : row.actual_date;
    var plannedDate = new Date(planned);
    var actualDate = new Date(actualValue);

    if (planned === '-' || isNaN(plannedDate.getTime()) || isNaN(actualDate.getTime())) return '-';

    return Math.max(0, Math.ceil((actualDate - plannedDate) / 86400000));
}
