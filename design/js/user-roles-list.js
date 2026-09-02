(function ($) {
    'use strict';

    $.fn.dataTable.ext.errMode = 'none';

    var statusFilter = '';
    var $tableElement = $('#user_roles');
    var canEdit = !$tableElement.hasClass('read-only');

    function escapeHtml(value) {
        return $('<div>').text(value == null || value === '' ? '—' : value).html();
    }

    function statusBadge(active) {
        var enabled = parseInt(active, 10) === 1;
        return '<span class="identity-status identity-status--' + (enabled ? 'active' : 'inactive') + '"><i></i>' + (enabled ? 'Active' : 'Inactive') + '</span>';
    }

    function updateSummary(summary) {
        if (!summary) return;
        $('#identity-roles-total').text(parseInt(summary.total, 10) || 0);
        $('#identity-roles-active').text(parseInt(summary.active, 10) || 0);
        $('#identity-roles-inactive').text(parseInt(summary.inactive, 10) || 0);
    }

    function actionButtons(row) {
        if (!canEdit) {
            return '<span class="identity-readonly"><i class="fas fa-lock"></i> Read only</span>';
        }

        var enabled = parseInt(row.active, 10) === 1;
        var manageUrl = appUrl('/user_roles/info?id=' + encodeURIComponent(id_encode(row.role_id)));
        var stateClass = enabled ? 'identity-state-action--deactivate' : 'identity-state-action--activate';
        var stateIcon = enabled ? 'fa-ban' : 'fa-check-circle';
        var stateLabel = enabled ? 'Deactivate' : 'Activate';
        var displayName = row.role_name || 'this role';

        return '<div class="identity-row-actions">' +
            '<a class="identity-manage-action" href="' + manageUrl + '" title="Manage role"><i class="fas fa-sliders-h"></i><span>Manage Role</span></a>' +
            '<button type="button" class="identity-state-action ' + stateClass + '" data-id="' + row.role_id + '" data-active="' + (enabled ? '1' : '0') + '" data-name="' + escapeHtml(displayName) + '"><i class="fas ' + stateIcon + '"></i><span>' + stateLabel + '</span></button>' +
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
            url: appUrl('/user_roles/ajax_list'),
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
                growl('Unable to load user roles', 'danger');
            }
        },
        columnDefs: [
            { targets: 0, width: '27%' },
            { targets: 1, width: '35%' },
            { targets: 2, width: '14%' },
            { targets: 3, width: '24%', orderable: false, searchable: false }
        ],
        columns: [
            {
                data: 'role_name',
                render: function (value, type, row) {
                    if (type !== 'display') return value || '';
                    return '<div class="identity-role-cell"><span class="identity-role-icon"><i class="fas fa-shield-alt"></i></span><span><strong>' + escapeHtml(value || 'Unnamed role') + '</strong><small>Role ID ' + String(row.role_id).padStart(2, '0') + '</small></span></div>';
                }
            },
            {
                data: 'description',
                defaultContent: '—',
                render: function (value, type) {
                    if (type !== 'display') return value || '';
                    return value ? '<span class="identity-description">' + escapeHtml(value) + '</span>' : '<span class="identity-description identity-description--empty">No description provided</span>';
                }
            },
            { data: 'active', render: function (value, type) { return type === 'display' ? statusBadge(value) : value; } },
            { data: null, defaultContent: '', render: function (value, type, row) { return type === 'display' ? actionButtons(row) : ''; } }
        ],
        language: {
            lengthMenu: 'Show _MENU_ entries',
            search: '',
            searchPlaceholder: 'Search roles...',
            processing: 'Loading user roles...',
            info: 'Showing _START_ to _END_ of _TOTAL_ roles',
            infoEmpty: 'No roles available',
            zeroRecords: 'No matching roles found',
            emptyTable: 'No user roles have been created',
            paginate: { previous: 'Previous', next: 'Next' }
        }
    });

    $('.identity-admin-page--roles .identity-filter-btn').on('click', function () {
        $('.identity-admin-page--roles .identity-filter-btn').removeClass('is-active');
        $(this).addClass('is-active');
        statusFilter = String($(this).data('status'));
        table.ajax.reload();
    });

    $tableElement.on('click', '.identity-state-action', function () {
        var $button = $(this);
        var active = parseInt($button.attr('data-active'), 10) === 1;
        var nextState = active ? 0 : 1;
        var verb = active ? 'deactivate' : 'activate';
        var name = $button.attr('data-name') || 'this role';

        if (!window.confirm('Are you sure you want to ' + verb + ' ' + name + '?')) return;

        $button.prop('disabled', true).addClass('is-loading');
        $.ajax({
            url: appUrl('/user_roles/state_ajax'),
            type: 'POST',
            dataType: 'json',
            data: { id: $button.data('id'), active: nextState }
        }).done(function (response) {
            if (response && response.state) {
                growl('User role ' + (nextState ? 'activated' : 'deactivated') + ' successfully', 'success');
                table.ajax.reload(null, false);
            } else {
                growl('Could not update the role status', 'danger');
            }
        }).fail(function () {
            growl('Could not update the role status', 'danger');
        }).always(function () {
            $button.prop('disabled', false).removeClass('is-loading');
        });
    });
})(jQuery);
