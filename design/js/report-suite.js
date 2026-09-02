(function (window, $) {
    'use strict';

    function appBasePath() {
        var segments = window.location.pathname.split('/').filter(Boolean);

        if (segments.length > 1) {
            return '/' + segments[0];
        }

        return '';
    }

    window.reportSuiteUrl = function (path) {
        return appBasePath() + '/' + String(path || '').replace(/^\/+/, '');
    };

    function activeTable() {
        var selector = '#asset_summary, #asset_types, #component_table';
        var tableElement = $(selector).filter(':visible').first();

        if (!tableElement.length) {
            tableElement = $(selector).first();
        }

        if (tableElement.length && $.fn.DataTable && $.fn.DataTable.isDataTable(tableElement[0])) {
            return tableElement.DataTable();
        }

        return null;
    }

    function updateReportOverview() {
        var table = activeTable();
        var total = table ? table.rows({ search: 'applied' }).count() : $('.checkbox-select').length;
        var selected = $('.checkbox-select:checked').length;
        var selectAll = $('#select_all_checkboxes');
        var allSelected = total > 0 && selected === $('.checkbox-select').length && $('.checkbox-select').length > 0;

        $('#report-total-count').text(total);
        $('#report-selected-count').text(selected);
        $('.report-suite-page:not(.mtbf-report-page) .report-download-button')
            .prop('disabled', selected === 0)
            .attr('title', selected === 0 ? 'Select at least one record' : 'Download selected records');

        if (selectAll.length) {
            selectAll.toggleClass('is-active', allSelected);
            selectAll.find('i').attr('class', allSelected ? 'fas fa-check-square' : 'far fa-square');
            selectAll.find('.report-select-all-label').text(allSelected ? 'Clear All' : 'Select All');
        }
    }

    function updateExportFormat() {
        var select = $('#download_type_select');

        if (!select.length) return;

        var label = select.find('option:selected').text() || select.val() || 'Excel';
        $('#download-type').val(select.val());
        $('#report-export-format').text(label);
    }

    function updateMtbfPeriod(scope) {
        var suffix = scope === 'components' ? 'components' : 'assets';
        var year = $('#summary_year_' + suffix).val();
        var monthSelect = $('#summary_month_' + suffix);
        var month = monthSelect.val();
        var monthLabel = month ? monthSelect.find('option:selected').text() : '';
        var label = 'All Time';

        if (monthLabel && year) label = monthLabel + ' ' + year;
        else if (monthLabel) label = monthLabel;
        else if (year) label = year;

        $('#mtbf-current-period').text(label);
    }

    $(document)
        .off('click.reportSuite', '#select_all_checkboxes')
        .on('click.reportSuite', '#select_all_checkboxes', function () {
            var checkboxes = $('.checkbox-select');
            var shouldSelect = checkboxes.length > 0 && checkboxes.filter(':checked').length !== checkboxes.length;

            checkboxes.prop('checked', shouldSelect);
            updateReportOverview();
        })
        .off('change.reportSuite', '.checkbox-select')
        .on('change.reportSuite', '.checkbox-select', updateReportOverview)
        .off('change.reportSuite', '#download_type_select')
        .on('change.reportSuite', '#download_type_select', updateExportFormat)
        .off('click.reportSuite', '#assets_filter_btn')
        .on('click.reportSuite', '#assets_filter_btn', function () {
            updateMtbfPeriod('assets');
        })
        .off('click.reportSuite', '#components_filter_btn')
        .on('click.reportSuite', '#components_filter_btn', function () {
            updateMtbfPeriod('components');
        })
        .off('draw.dt.reportSuite', '#asset_summary, #asset_types, #component_table')
        .on('draw.dt.reportSuite', '#asset_summary, #asset_types, #component_table', updateReportOverview);

    $(function () {
        updateExportFormat();
        updateReportOverview();
    });

    window.updateReportOverview = updateReportOverview;
})(window, jQuery);
