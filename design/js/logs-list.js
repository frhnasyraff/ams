(function ($) {
    'use strict';

    $.fn.dataTable.ext.errMode = 'none';

    var $tableElement = $('#logs');
    var $error = $('#audit-log-error');

    function escapeHtml(value) {
        return $('<div>').text(value == null || value === '' ? '—' : value).html();
    }

    function label(value) {
        return String(value || 'Unknown').replace(/[_-]+/g, ' ').replace(/\b\w/g, function (letter) {
            return letter.toUpperCase();
        });
    }

    function initials(name) {
        var parts = String(name || 'System').trim().split(/\s+/);
        return ((parts[0] || 'S').charAt(0) + (parts.length > 1 ? parts[parts.length - 1].charAt(0) : '')).toUpperCase();
    }

    function timestampCell(value, type) {
        if (type !== 'display') return value || '';
        var raw = String(value || '');
        var parsed = new Date(raw.replace(' ', 'T'));

        if (isNaN(parsed.getTime())) {
            return '<span class="audit-time"><strong>' + escapeHtml(raw) + '</strong></span>';
        }

        var date = parsed.toLocaleDateString('en-MY', { day: '2-digit', month: 'short', year: 'numeric' });
        var time = parsed.toLocaleTimeString('en-MY', { hour: '2-digit', minute: '2-digit' });
        return '<span class="audit-time"><strong>' + escapeHtml(date) + '</strong><small><i class="far fa-clock"></i>' + escapeHtml(time) + '</small></span>';
    }

    function userCell(value, type, row) {
        if (type !== 'display') return value || row.username || '';
        var name = value || row.username || 'System';
        var handle = row.username ? '@' + row.username : 'Automated event';
        return '<div class="audit-user-cell"><span class="audit-avatar">' + escapeHtml(initials(name)) + '</span><span><strong>' + escapeHtml(name) + '</strong><small>' + escapeHtml(handle) + '</small></span></div>';
    }

    function recordCell(value, type, row) {
        if (type !== 'display') return value || '';
        var recordId = row.log_item_id == null || row.log_item_id === '' ? '—' : row.log_item_id;
        return '<div class="audit-record-cell"><span><i class="fas fa-cube"></i></span><div><strong>' + escapeHtml(label(value)) + '</strong><small>Record #' + escapeHtml(recordId) + '</small></div></div>';
    }

    function activityTone(code) {
        var value = String(code || '').toUpperCase();
        if (/(DELETE|DISABLE|FAILED|ERROR|REJECT)/.test(value)) return 'danger';
        if (/(ADD|CREATE|SUCCESS|ACTIVE|APPROVE|COMPLETE)/.test(value)) return 'success';
        if (/(UPDATE|EDIT|ASSIGN|PERMISSION|CHANGE|UPLOAD)/.test(value)) return 'violet';
        if (/(ATTEMPT|WARNING|PENDING|MAINTENANCE)/.test(value)) return 'warning';
        return 'blue';
    }

    function activityCell(value, type) {
        if (type !== 'display') return value || '';
        var tone = activityTone(value);
        return '<span class="audit-activity audit-activity--' + tone + '"><i></i>' + escapeHtml(label(value)) + '</span>';
    }

    function detailsPanel(row) {
        var description = String(row.log_description || '').trim();
        var descriptionHtml = '';

        if (description) {
            try {
                var parsed = JSON.parse(description);
                descriptionHtml = '<pre>' + escapeHtml(JSON.stringify(parsed, null, 2)) + '</pre>';
            } catch (error) {
                descriptionHtml = '<p>' + escapeHtml(description) + '</p>';
            }
        } else {
            descriptionHtml = '<p class="is-empty">No additional information was recorded for this event.</p>';
        }

        return '<div class="audit-event-detail">' +
            '<div class="audit-event-detail__head"><span><i class="fas fa-info-circle"></i> Event Details</span><small>' + escapeHtml(row.log_code || '—') + '</small></div>' +
            '<div class="audit-event-detail__body">' + descriptionHtml + '</div>' +
            '<div class="audit-event-detail__meta"><span><i class="fas fa-network-wired"></i> IP Address: <strong>' + escapeHtml(row.log_ip || 'Not recorded') + '</strong></span><span><i class="fas fa-hashtag"></i> Log ID: <strong>' + escapeHtml(row.log_id || '—') + '</strong></span></div>' +
            '</div>';
    }

    function updateSummary(summary) {
        if (!summary) return;
        $('#audit-total-events').text(parseInt(summary.total, 10) || 0);
        $('#audit-today-events').text(parseInt(summary.today, 10) || 0);
        $('#audit-users-count').text(parseInt(summary.users, 10) || 0);
        $('#audit-modules-count').text(parseInt(summary.modules, 10) || 0);
    }

    var table = $tableElement.DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        stateSave: false,
        order: [[0, 'desc']],
        dom: '<"audit-table-controls"<"audit-length"l><"audit-search"f>>t<"audit-table-footer"<"audit-info"i><"audit-pages"p>>',
        ajax: {
            url: $tableElement.data('source'),
            type: 'POST',
            data: function (request) {
                request.module_filter = $('#audit-module-filter').val();
                request.activity_filter = $('#audit-activity-filter').val();
                request.period_filter = $('#audit-period-filter').val();
            },
            dataSrc: function (response) {
                $error.prop('hidden', true);
                updateSummary(response.summary);
                return response.data || [];
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                    return;
                }
                $error.prop('hidden', false);
            }
        },
        columnDefs: [
            { targets: 0, width: '18%' },
            { targets: 1, width: '22%' },
            { targets: 2, width: '20%' },
            { targets: 3, width: '26%' },
            { targets: 4, width: '14%', orderable: false, searchable: false }
        ],
        columns: [
            { data: 'timestamp', render: timestampCell },
            { data: 'full_name', defaultContent: '', render: userCell },
            { data: 'log_item_table', render: recordCell },
            { data: 'log_code', render: activityCell },
            {
                data: null,
                defaultContent: '',
                render: function (value, type) {
                    return type === 'display' ? '<button type="button" class="audit-view-event"><i class="far fa-eye"></i><span>View</span></button>' : '';
                }
            }
        ],
        language: {
            lengthMenu: 'Show _MENU_ entries',
            search: '',
            searchPlaceholder: 'Search audit activity...',
            processing: 'Loading audit activity...',
            info: 'Showing _START_ to _END_ of _TOTAL_ events',
            infoEmpty: 'No audit events available',
            zeroRecords: 'No matching activity found',
            emptyTable: 'No system activity has been recorded',
            paginate: { previous: 'Previous', next: 'Next' }
        }
    });

    $('#audit-module-filter, #audit-activity-filter, #audit-period-filter').on('change', function () {
        table.ajax.reload();
    });

    $('#audit-log-reset').on('click', function () {
        $('#audit-module-filter, #audit-activity-filter, #audit-period-filter').val('');
        table.search('').ajax.reload();
    });

    $('#audit-log-retry').on('click', function () {
        $error.prop('hidden', true);
        table.ajax.reload();
    });

    $tableElement.on('click', '.audit-view-event', function () {
        var $button = $(this);
        var tr = $button.closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('is-expanded');
            $button.removeClass('is-active').html('<i class="far fa-eye"></i><span>View</span>');
        } else {
            row.child(detailsPanel(row.data()), 'audit-detail-row').show();
            tr.addClass('is-expanded');
            $button.addClass('is-active').html('<i class="fas fa-chevron-up"></i><span>Close</span>');
        }
    });
})(jQuery);
