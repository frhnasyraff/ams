(function ($) {
    'use strict';

    function escapeHtml(value) {
        return $('<div>').text(value == null || value === '' ? '—' : value).html();
    }

    function statusBadge(value) {
        var label = value || 'Pending';
        var normalised = String(label).toLowerCase().replace(/[\s_]+/g, '-');
        var className = 'maintenance';

        if (normalised.indexOf('complete') !== -1) {
            className = 'complete';
        } else if (normalised.indexOf('progress') !== -1) {
            className = 'progress';
        }

        return '<span class="corrective-status corrective-status--' + className + '"><i></i>' + escapeHtml(label) + '</span>';
    }

    function tableDom() {
        return '<"corrective-table-controls"<"corrective-length"l><"corrective-search"f>>' +
            't' +
            '<"corrective-table-footer"<"corrective-info"i><"corrective-pages"p>>';
    }

    function drawComplete() {
        if (typeof initToggle === 'function') {
            initToggle();
        }
    }

    var commonOptions = {
        processing: true,
        serverSide: false,
        responsive: false,
        autoWidth: false,
        // The table wrapper handles horizontal overflow. DataTables scrollX
        // clones the header and the shared theme makes that hidden clone visible.
        scrollX: false,
        scrollCollapse: false,
        pagingType: 'simple_numbers',
        pageLength: 5,
        lengthMenu: [[5, 10, 25], [5, 10, 25]],
        stateSave: false,
        dom: tableDom(),
        language: {
            lengthMenu: 'Show _MENU_ entries',
            search: '',
            searchPlaceholder: 'Search records...',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'No records available',
            zeroRecords: 'No matching corrective records found',
            emptyTable: 'No corrective maintenance records available',
            paginate: { previous: 'Previous', next: 'Next' }
        },
        drawCallback: drawComplete
    };

    $('#correctiveAllStatus').DataTable($.extend(true, {}, commonOptions, {
        ajax: {
            url: appUrl('/corrective_maintenance/corrective_table_list_all_status'),
            type: 'POST',
            dataSrc: function (response) {
                var rows = response && response.data ? response.data : [];
                $('#corrective-active-count').text(rows.length);
                return rows;
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                }
            }
        },
        order: [[1, 'desc']],
        columnDefs: [
            { targets: 0, width: '25%' },
            { targets: 1, width: '18%' },
            { targets: 2, width: '22%' },
            { targets: 3, width: '35%' }
        ],
        columns: [
            { data: 'equipment_name', defaultContent: '—', render: escapeHtml },
            { data: 'update_date', defaultContent: '—', render: escapeHtml },
            { data: 'final_status', defaultContent: 'Pending', render: statusBadge },
            { data: 'remarks', defaultContent: '—', render: escapeHtml }
        ]
    }));

    $('#corrective').DataTable($.extend(true, {}, commonOptions, {
        ajax: {
            url: appUrl('/corrective_maintenance/corrective_table_list'),
            type: 'POST',
            dataSrc: function (response) {
                var rows = response && response.data ? response.data : [];
                $('#corrective-complete-count').text(rows.length);
                return rows;
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                }
            }
        },
        order: [[1, 'asc']],
        columnDefs: [
            { targets: 0, width: '26%' },
            { targets: 1, width: '28%' },
            { targets: 2, width: '25%' },
            { targets: 3, width: '21%' }
        ],
        columns: [
            { data: 'asset_type_name', defaultContent: '—', render: escapeHtml },
            { data: 'equipment_name', defaultContent: '—', render: escapeHtml },
            { data: 'ticket_location', defaultContent: '—', render: escapeHtml },
            { data: 'final_status', defaultContent: 'Complete', render: statusBadge }
        ]
    }));
})(jQuery);
