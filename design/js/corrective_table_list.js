(function ($) {
    'use strict';

    function escapeHtml(value) {
        return $('<div>').text(value == null || value === '' ? '—' : value).html();
    }

    function formatStatusLabel(value) {
        var raw = value == null || value === '' ? 'pending' : String(value).trim();
        var normalised = raw.toLowerCase().replace(/[\s_]+/g, '-');

        if (normalised === 'in-maintenance') return 'In Maintenance';
        if (normalised === 'in-progress') return 'In Progress';
        if (normalised === 'complete' || normalised === 'completed') return 'Completed';
        if (normalised === 'pending') return 'Pending';

        return raw.replace(/[-_]+/g, ' ').replace(/\b\w/g, function (letter) {
            return letter.toUpperCase();
        });
    }

    function formatDateOnly(value) {
        if (value == null || value === '') return '—';
        var text = String(value).trim();
        var match = text.match(/^(\d{4}-\d{2}-\d{2})/);
        return match ? match[1] : text.replace(/\s+00:00:00$/, '');
    }

    function typeBadge(value) {
        var label = value == null || value === '' ? 'Asset' : String(value).trim();
        var normalised = label.toLowerCase();
        var className = normalised.indexOf('component') !== -1 ? 'component' : 'asset';

        return '<span class="corrective-type-badge corrective-type-badge--' + className + '">' + escapeHtml(label) + '</span>';
    }
    function statusBadge(value) {
        var label = formatStatusLabel(value);
        var normalised = String(value || label).toLowerCase().replace(/[\s_]+/g, '-');
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

    var activeTable = $('#correctiveAllStatus').DataTable($.extend(true, {}, commonOptions, {
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
        order: [[2, 'desc']],
        columnDefs: [
            { targets: 0, width: '12%' },
            { targets: 1, width: '22%' },
            { targets: 2, width: '16%' },
            { targets: 3, width: '18%' },
            { targets: 4, width: '32%' }
        ],
        columns: [
            { data: 'record_type', defaultContent: 'Asset', render: function (data, type) { return type === 'display' ? typeBadge(data) : (data || 'Asset'); } },
            { data: 'equipment_name', defaultContent: '—', render: escapeHtml },
            { data: 'update_date', defaultContent: '—', render: formatDateOnly },
            { data: 'final_status', defaultContent: 'Pending', render: statusBadge },
            { data: 'remarks', defaultContent: '—', render: escapeHtml }
        ]
    }));

    $('.corrective-type-filter [data-corrective-type]').on('click', function () {
        var type = $(this).data('corrective-type') || '';
        $('.corrective-type-filter [data-corrective-type]').removeClass('is-active');
        $(this).addClass('is-active');
        activeTable.column(0).search(type ? '^' + type + '$' : '', true, false).draw();
    });
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





