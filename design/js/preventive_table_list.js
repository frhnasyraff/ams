let preventiveTable;

$(document).ready(function () {
    function getQueryParam(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }

    function appUrl(path) {
        const base = (typeof base_url !== 'undefined' ? base_url : '/').replace(/\/+$/, '/');
        return base + String(path).replace(/^\/+/, '');
    }

    function normalizeStatus(status) {
        const key = String(status || '').trim().toLowerCase().replace(/[\s-]+/g, '_');
        if (key === 'maintenance' || key === 'in_maintenance' || key === 'in_progress') return 'in_progress';
        if (key === 'complete' || key === 'completed') return 'complete';
        if (key === 'pending') return 'pending';
        return key || 'pending';
    }

    function statusLabel(status) {
        const key = normalizeStatus(status);
        if (key === 'complete') return 'Complete';
        if (key === 'in_progress') return 'In Progress';
        return 'Pending';
    }

    function statusBadgeClass(status) {
        const key = normalizeStatus(status);
        if (key === 'complete') return 'complete';
        if (key === 'in_progress') return 'maintenance';
        return 'pending';
    }

    function formatDateOnly(value) {
        if (!value) return '';
        const raw = String(value).split(' ')[0];
        if (window.moment) {
            const parsed = moment(raw, ['YYYY-MM-DD', 'DD/MM/YYYY'], true);
            if (parsed.isValid()) return parsed.format('DD/MM/YYYY');
        }
        return raw;
    }

    function selectedMetric($target) {
        const $metric = $target.closest('.asset-metric-box');
        return $metric.length ? ($metric.data('status-filter') || 'all') : 'all';
    }

    function openPreventiveModal(assetId, statusFilter, assetName) {
        const titleParts = [assetName || 'Preventive Maintenance'];
        if (statusFilter && statusFilter !== 'all') titleParts.push(statusFilter === 'maintenance' ? 'In Progress' : statusLabel(statusFilter));
        $('#preventiveModalLabel').text(titleParts.join(' - '));

        if ($('#preventiveModal').length) {
            $('#preventiveModal').modal('show');
        } else {
            $('#preventiveWrapper').show();
        }

        initPreventiveTable(assetId, statusFilter || 'all');
    }

    function initPreventiveTable(assetId, statusFilter) {
        if (!$('#preventive').length) return;

        if ($.fn.DataTable.isDataTable('#preventive')) {
            $('#preventive').DataTable().clear().destroy();
            $('#preventive tbody').empty();
        }

        preventiveTable = $('#preventive').DataTable({
            processing: true,
            serverSide: false,
            responsive: true,
            autoWidth: false,
            pageLength: 5,
            stateSave: false,
            ajax: {
                url: appUrl('preventive_maintenance/preventive_table_list'),
                type: 'POST',
                data: function (d) {
                    return $.extend({}, d, {
                        asset_id: assetId,
                        status_filter: statusFilter || 'all'
                    });
                },
                error: function (xhr) {
                    const response = xhr.responseJSON;
                    if (response && response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        $('#preventive tbody').html('<tr><td colspan="4" class="text-center text-warning">Unable to load maintenance records.</td></tr>');
                    }
                }
            },
            drawCallback: function () {
                if (typeof initToggle === 'function') initToggle();
            },
            order: [[1, 'desc']],
            columns: [
                { data: 'equipment_name', defaultContent: 'N/A' },
                { data: 'store_location_name', defaultContent: 'N/A' },
                {
                    data: 'current_status',
                    render: function (data, type) {
                        const label = statusLabel(data);
                        if (type && type !== 'display') return label;
                        return '<span class="custom-badge preventive-status-badge preventive-status-badge--' + statusBadgeClass(data) + '">' + label + '</span>';
                    }
                },
                {
                    data: 'interval',
                    render: function (data) {
                        return formatDateOnly(data);
                    }
                }
            ],
            language: {
                emptyTable: 'No assets found for this selection',
                zeroRecords: 'No matching assets found'
            }
        });
    }

    const typeFilter = getQueryParam('type_filter');
    if (typeFilter) {
        $('.asset_id_filter .btn-primary').data('filter', typeFilter);
        initPreventiveTable(typeFilter, 'all');
    }

    $(document).on('click', '.apply-type-filter', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $link = $(this);
        const assetId = $link.data('type-filter');
        const statusFilter = selectedMetric($(e.target));
        const assetName = $.trim($link.find('.main-title').text());

        $('.asset_id_filter .btn-primary').data('filter', assetId);
        openPreventiveModal(assetId, statusFilter, assetName);
    });
});