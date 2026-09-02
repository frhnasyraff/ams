$(function () {
    var dashboards = [
        { selector: '#order_list2', key: 'orders', order: [[0, 'desc']] },
        { selector: '#trucks_deployed2', key: 'trucks', order: [[1, 'desc']] },
        { selector: '#worker_deployed2', key: 'drivers', order: [[0, 'asc']] },
        { selector: '#asset_in_use_m', key: 'assets', order: [[0, 'asc']] }
    ];

    function updateCount(table, key) {
        var count = table.rows({ search: 'applied' }).count();
        $('#ops-count-' + key).text(count.toLocaleString());
        $('#ops-kpi-' + key).text(count.toLocaleString());
    }

    dashboards.forEach(function (config) {
        var element = $(config.selector);
        if (!element.length) return;

        if ($.fn.DataTable.isDataTable(config.selector)) {
            element.DataTable().destroy();
        }

        var table = element.DataTable({
            processing: true,
            responsive: true,
            autoWidth: false,
            pageLength: 5,
            pagingType: 'simple_numbers',
            order: config.order,
            dom: '<"operations-dt-top"<"operations-dt-left"l><"operations-dt-right"f>>t<"operations-dt-bottom"ip>',
            language: {
                search: '',
                searchPlaceholder: 'Search records...',
                emptyTable: 'No operational records available',
                zeroRecords: 'No matching records found'
            },
            drawCallback: function () {
                updateCount(this.api(), config.key);
            }
        });

        table.on('search.dt', function () {
            updateCount(table, config.key);
        });
    });
});
