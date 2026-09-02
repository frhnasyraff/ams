(function ($) {
    'use strict';

    function escapeHtml(value) {
        return $('<div>').text(value == null || value === '' ? '—' : value).html();
    }

    function tableDom() {
        return '<"corrective-table-controls"<"corrective-length"l><"corrective-search"f>>' +
            't' +
            '<"corrective-table-footer"<"corrective-info"i><"corrective-pages"p>>';
    }

    $.ajax({
        url: appUrl('/CorrectiveMaintenanceSummary/ajax_list'),
        type: 'POST',
        dataType: 'json'
    }).done(function (response) {
        var faultTypes = response && response.fault_types ? response.fault_types : [];
        var rows = response && response.data ? response.data : [];
        var columns = [{
            title: 'Asset Type',
            data: 'asset_type',
            defaultContent: '—',
            render: escapeHtml,
            width: '28%'
        }];

        faultTypes.forEach(function (faultType) {
            columns.push({
                title: escapeHtml(faultType),
                data: function (row) {
                    return row[faultType] == null ? 0 : row[faultType];
                },
                className: 'text-center',
                render: function (value) {
                    var count = parseInt(value, 10) || 0;
                    var state = count > 0 ? ' corrective-count-pill--active' : '';
                    return '<span class="corrective-count-pill' + state + '">' + count + '</span>';
                }
            });
        });

        $('#corrective-fault-count').text(faultTypes.length);

        if ($.fn.DataTable.isDataTable('#assets')) {
            $('#assets').DataTable().destroy();
            $('#assets').empty();
        }

        $('#assets').DataTable({
            data: rows,
            columns: columns,
            processing: true,
            responsive: false,
            autoWidth: false,
            pageLength: 5,
            lengthMenu: [[5, 10, 25], [5, 10, 25]],
            stateSave: false,
            order: [[0, 'asc']],
            dom: tableDom(),
            language: {
                lengthMenu: 'Show _MENU_ entries',
                search: '',
                searchPlaceholder: 'Search fault matrix...',
                info: 'Showing _START_ to _END_ of _TOTAL_ asset types',
                infoEmpty: 'No asset types available',
                zeroRecords: 'No matching asset types found',
                emptyTable: 'No fault matrix data available',
                paginate: { previous: 'Previous', next: 'Next' }
            }
        });
    }).fail(function () {
        $('#assets tbody').html('<tr><td class="dataTables_empty">Unable to load the fault matrix.</td></tr>');
    });
})(jQuery);
