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
            searchPlaceholder: 'Search faulty items...',
            lengthMenu: 'Show _MENU_ entries',
            emptyTable: 'No faulty items recorded'
        },
        ajax: {
            url: reportSuiteUrl('faulty_item_list_report/ajax_list'),
            type: 'GET',
            error: reportAjaxError
        },
        order: [[5, 'desc']],
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
            { data: 'date_installed', defaultContent: '-' },
            { data: 'faulty_date', defaultContent: '-' },
            { data: 'equipment_name', defaultContent: '-' },
            { data: 'item_type_name', defaultContent: '-' },
            { data: 'manufacturer_name', defaultContent: '-' },
            { data: 'part_number', defaultContent: '-' },
            { data: 'store_location', defaultContent: '-' }
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
