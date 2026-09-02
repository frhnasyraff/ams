(function ($) {
    'use strict';

    $.fn.dataTable.ext.errMode = 'none';

    var statusFilter = '';
    var $tableElement = $('#users');
    var canEdit = !$tableElement.hasClass('read-only');

    function escapeHtml(value) {
        return $('<div>').text(value == null || value === '' ? '—' : value).html();
    }

    function initials(name) {
        var parts = String(name || 'User').trim().split(/\s+/);
        return ((parts[0] || 'U').charAt(0) + (parts.length > 1 ? parts[parts.length - 1].charAt(0) : '')).toUpperCase();
    }

    function statusBadge(active) {
        var enabled = parseInt(active, 10) === 1;
        return '<span class="identity-status identity-status--' + (enabled ? 'active' : 'inactive') + '"><i></i>' + (enabled ? 'Active' : 'Inactive') + '</span>';
    }

    function updateSummary(summary) {
        if (!summary) return;
        $('#identity-users-total').text(parseInt(summary.total, 10) || 0);
        $('#identity-users-active').text(parseInt(summary.active, 10) || 0);
        $('#identity-users-inactive').text(parseInt(summary.inactive, 10) || 0);
    }

    function actionButtons(row) {
        if (!canEdit) {
            return '<span class="identity-readonly"><i class="fas fa-lock"></i> Read only</span>';
        }

        var enabled = parseInt(row.active, 10) === 1;
        var manageUrl = appUrl('/users/info?id=' + encodeURIComponent(id_encode(row.user_id)));
        var stateClass = enabled ? 'identity-state-action--deactivate' : 'identity-state-action--activate';
        var stateIcon = enabled ? 'fa-user-slash' : 'fa-user-check';
        var stateLabel = enabled ? 'Deactivate' : 'Activate';
        var displayName = row.full_name || row.username || 'this user';

        return '<div class="identity-row-actions">' +
            '<a class="identity-manage-action" href="' + manageUrl + '" title="Manage user"><i class="fas fa-pen"></i><span>Manage</span></a>' +
            '<button type="button" class="identity-state-action ' + stateClass + '" data-id="' + row.user_id + '" data-active="' + (enabled ? '1' : '0') + '" data-name="' + escapeHtml(displayName) + '"><i class="fas ' + stateIcon + '"></i><span>' + stateLabel + '</span></button>' +
            '</div>';
    }

    var table = $tableElement.DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        stateSave: false,
        order: [[0, 'asc']],
        dom: '<"identity-table-controls"<"identity-length"l><"identity-search"f>>t<"identity-table-footer"<"identity-info"i><"identity-pages"p>>',
        ajax: {
            url: appUrl('/users/ajax_list'),
            type: 'POST',
            data: function (request) {
                request.status_filter = statusFilter;
            },
            dataSrc: function (response) {
                updateSummary(response.summary);
                return response.data || [];
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                    return;
                }
                growl('Unable to load user accounts', 'danger');
            }
        },
        columnDefs: [
            { targets: 0, width: '28%' },
            { targets: 1, width: '14%' },
            { targets: 2, width: '24%' },
            { targets: 3, width: '13%' },
            { targets: 4, width: '21%', orderable: false, searchable: false }
        ],
        columns: [
            {
                data: 'full_name',
                render: function (value, type, row) {
                    if (type !== 'display') return value || row.username || '';
                    var name = value || row.username || 'Unnamed user';
                    return '<div class="identity-user-cell"><span class="identity-avatar">' + initials(name) + '</span><span><strong>' + escapeHtml(name) + '</strong><small>@' + escapeHtml(row.username || 'unknown') + '</small></span></div>';
                }
            },
            {
                data: 'user_code',
                defaultContent: '—',
                render: function (value, type) {
                    if (type !== 'display') return value || '';
                    return '<span class="identity-code">' + escapeHtml(value) + '</span>';
                }
            },
            {
                data: 'email',
                defaultContent: '—',
                render: function (value, type) {
                    if (type !== 'display') return value || '';
                    return '<span class="identity-email"><i class="far fa-envelope"></i>' + escapeHtml(value) + '</span>';
                }
            },
            { data: 'active', render: function (value, type) { return type === 'display' ? statusBadge(value) : value; } },
            { data: null, defaultContent: '', render: function (value, type, row) { return type === 'display' ? actionButtons(row) : ''; } }
        ],
        language: {
            lengthMenu: 'Show _MENU_ entries',
            search: '',
            searchPlaceholder: 'Search users...',
            processing: 'Loading user accounts...',
            info: 'Showing _START_ to _END_ of _TOTAL_ users',
            infoEmpty: 'No users available',
            zeroRecords: 'No matching users found',
            emptyTable: 'No user accounts have been created',
            paginate: { previous: 'Previous', next: 'Next' }
        }
    });

    $('.identity-admin-page--users .identity-filter-btn').on('click', function () {
        $('.identity-admin-page--users .identity-filter-btn').removeClass('is-active');
        $(this).addClass('is-active');
        statusFilter = String($(this).data('status'));
        table.ajax.reload();
    });

    $tableElement.on('click', '.identity-state-action', function () {
        var $button = $(this);
        var active = parseInt($button.attr('data-active'), 10) === 1;
        var nextState = active ? 0 : 1;
        var verb = active ? 'deactivate' : 'activate';
        var name = $button.attr('data-name') || 'this user';

        if (!window.confirm('Are you sure you want to ' + verb + ' ' + name + '?')) return;

        $button.prop('disabled', true).addClass('is-loading');
        $.ajax({
            url: appUrl('/users/state_ajax'),
            type: 'POST',
            dataType: 'json',
            data: { id: $button.data('id'), active: nextState }
        }).done(function (response) {
            if (response && response.state) {
                growl('User account ' + (nextState ? 'activated' : 'deactivated') + ' successfully', 'success');
                table.ajax.reload(null, false);
            } else {
                growl('Could not update the user status', 'danger');
            }
        }).fail(function () {
            growl('Could not update the user status', 'danger');
        }).always(function () {
            $button.prop('disabled', false).removeClass('is-loading');
        });
    });
})(jQuery);
