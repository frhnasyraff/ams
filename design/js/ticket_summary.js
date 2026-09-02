$(function () {
    $('#asset_summary').DataTable({
        processing: true,
        responsive: false,
        autoWidth: false,
        stateSave: true,
        pageLength: 10,
        pagingType: 'simple_numbers',
        dom: '<"report-dt-top"l f>t<"report-dt-bottom"i p>',
        language: {
            search: 'Search:',
            searchPlaceholder: 'Search ticket records...',
            lengthMenu: 'Show _MENU_ entries',
            emptyTable: 'No ticket records available'
        },
        ajax: {
            url: reportSuiteUrl('ticket_summary_report/ajax_list'),
            type: 'GET',
            error: reportAjaxError
        },
        order: [[1, 'desc']],
        columns: [
            {
                data: 'equipment_id',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<input type="checkbox" name="record[]" class="checkbox-select" value="' + row.id + '">';
                }
            },
            { data: 'issue_date', defaultContent: '-' },
            { data: 'ticket_number', defaultContent: '-' },
            {
                data: 'severity',
                defaultContent: '-',
                render: function (data, type) {
                    return type === 'display' ? reportStatus(data) : data;
                }
            },
            { data: 'asset_type', defaultContent: '-' },
            { data: 'registration_number', defaultContent: '-' },
            { data: 'location', defaultContent: '-' },
            { data: 'managed_by', defaultContent: '-' },
            { data: 'manufacturer_name', defaultContent: '-' },
            { data: 'part_number', defaultContent: '-' },
            { data: 'maintenance_type', defaultContent: '-' },
            { data: 'task_done', defaultContent: '-' },
            { data: 'date_of_completion', defaultContent: '-' },
            {
                data: 'status',
                defaultContent: '-',
                render: function (data, type) {
                    return type === 'display' ? reportStatus(data) : data;
                }
            }
        ],
        drawCallback: reportDrawCallback
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
