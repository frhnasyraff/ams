function getQueryParam(name) {
    return new URLSearchParams(window.location.search).get(name);
}

function mtbfTableOptions(data, columns) {
    return {
        data: data || [],
        processing: true,
        responsive: false,
        autoWidth: false,
        pageLength: 10,
        pagingType: 'simple_numbers',
        dom: '<"report-dt-top"l f>t<"report-dt-bottom"i p>',
        language: {
            search: 'Search:',
            searchPlaceholder: 'Search reliability data...',
            lengthMenu: 'Show _MENU_ entries',
            emptyTable: 'No reliability data for this period'
        },
        columns: columns,
        drawCallback: function () {
            if (typeof updateReportOverview === 'function') updateReportOverview();
        }
    };
}

function fetchSummary(scope) {
    var year = $('#summary_year_' + scope).val();
    var month = $('#summary_month_' + scope).val();
    var url = reportSuiteUrl('Mean_time_between_failure_report/getSummaryForAll') + '?summary=' + encodeURIComponent(scope);

    if (year) url += '&year=' + encodeURIComponent(year);
    if (month) url += '&month=' + encodeURIComponent(month);

    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json'
    }).done(function (response) {
        var rows = response && Array.isArray(response.data) ? response.data : [];
        if (scope === 'components') renderComponentTable(rows);
        else renderAssetTable(rows);
    }).fail(function () {
        if (scope === 'components') renderComponentTable([]);
        else renderAssetTable([]);
    });
}

function renderAssetTable(data) {
    if ($.fn.DataTable.isDataTable('#asset_types')) {
        $('#asset_types').DataTable().clear().rows.add(data).draw();
        return;
    }

    $('#asset_types').DataTable(mtbfTableOptions(data, [
        {
            data: 'Type',
            defaultContent: '-',
            render: function (value, type, row) {
                if (type !== 'display') return value;
                return '<a href="#" class="show-breakdown" data-type="' + encodeURIComponent(row.Type || '') + '"><i class="fas fa-chart-line"></i> ' + $('<div>').text(value || '-').html() + '</a>';
            }
        },
        { data: 'Average MTBF (Days)', defaultContent: '-' }
    ]));
}

function renderComponentTable(data) {
    if ($.fn.DataTable.isDataTable('#component_table')) {
        $('#component_table').DataTable().clear().rows.add(data).draw();
        return;
    }

    $('#component_table').DataTable(mtbfTableOptions(data, [
        {
            data: 'Type',
            defaultContent: '-',
            render: function (value, type, row) {
                if (type !== 'display') return value;
                return '<a href="#" class="show-breakdown-component" data-id="' + encodeURIComponent(row.id || '') + '"><i class="fas fa-chart-line"></i> ' + $('<div>').text(value || '-').html() + '</a>';
            }
        },
        { data: 'Average MTBF', defaultContent: '-' }
    ]));
}

function openBreakdownTable(config) {
    $(config.modal).modal('show');

    if ($.fn.DataTable.isDataTable(config.table)) {
        $(config.table).DataTable().clear().destroy();
    }

    var options = mtbfTableOptions([], config.columns);
    options.ajax = {
        url: reportSuiteUrl(config.endpoint),
        type: 'GET',
        data: config.data,
        dataSrc: 'data'
    };

    $(config.table).DataTable(options);
}

$(function () {
    var scope = getQueryParam('summary') === 'components' ? 'components' : 'assets';

    fetchSummary(scope);

    $('#assets_filter_btn').on('click', function () {
        fetchSummary('assets');
    });

    $('#components_filter_btn').on('click', function () {
        fetchSummary('components');
    });

    $('#downloadForm').on('submit', function () {
        $('#download_type').val($('#asset_download_type_select').val());
        $('#hidden_year').val($('#summary_year_assets').val());
        $('#hidden_month').val($('#summary_month_assets').val());
    });

    $('#downloadFormComponents').on('submit', function () {
        $('#component_download_type').val($('#components_download_type_select').val());
        $('#component_hidden_year').val($('#summary_year_components').val());
        $('#component_hidden_month').val($('#summary_month_components').val());
    });

    $(document).on('click', 'a.show-breakdown', function (event) {
        event.preventDefault();
        openBreakdownTable({
            modal: '#breakdownModal',
            table: '#breakdownTable',
            endpoint: 'Mean_time_between_failure_report/getBreakdownByType',
            data: { type: decodeURIComponent($(this).attr('data-type') || '') },
            columns: [
                { data: 'Asset Code', defaultContent: '-' },
                { data: 'Asset Name', defaultContent: '-' },
                { data: 'Type', defaultContent: '-' },
                { data: 'Serviceable Date', defaultContent: '-' },
                { data: 'Unserviceable Date', defaultContent: '-' },
                { data: 'MTBF (Days)', defaultContent: '-' }
            ]
        });
    });

    $(document).on('click', 'a.show-breakdown-component', function (event) {
        event.preventDefault();
        openBreakdownTable({
            modal: '#componentbreakdownModal',
            table: '#componentbreakdownTable',
            endpoint: 'Mean_time_between_failure_report/getBreakdownByTypeComponents',
            data: { type: decodeURIComponent($(this).attr('data-id') || '') },
            columns: [
                { data: 'component_code', defaultContent: '-' },
                { data: 'component_name', defaultContent: '-' },
                { data: 'Serviceable_Date', defaultContent: '-' },
                { data: 'Unserviceable_Date', defaultContent: '-' },
                { data: 'MTBF (Days)', defaultContent: '-' }
            ]
        });
    });
});
