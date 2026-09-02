(function ($) {
	'use strict';

	$.fn.dataTable.ext.errMode = 'none';

	var $tableElement = $('#asset_groups');
	var canEdit = !$tableElement.hasClass('read-only');

	function escapeHtml(value) {
		return $('<div>').text(value == null || value === '' ? '—' : value).html();
	}

	function actionButtons(row) {
		if (!canEdit) {
			return '<span class="identity-readonly"><i class="fas fa-lock"></i><span>Read only</span></span>';
		}

		var enabled = parseInt(row.active, 10) === 1;
		var manageUrl = appUrl('/asset_groups/info?id=' + encodeURIComponent(id_encode(row.equipment_group_id)));
		var stateClass = enabled ? 'identity-state-action--deactivate' : 'identity-state-action--activate';
		var stateIcon = enabled ? 'fa-ban' : 'fa-check-circle';
		var stateLabel = enabled ? 'Deactivate' : 'Activate';
		var displayName = row.equipment_group_name || 'this asset group';

		return '<div class="identity-row-actions master-record-actions">' +
			'<a class="identity-manage-action" href="' + manageUrl + '" title="Manage asset group"><i class="fas fa-pen"></i><span>Manage</span></a>' +
			'<button type="button" class="identity-state-action ' + stateClass + '" data-id="' + row.equipment_group_id + '" data-active="' + (enabled ? '1' : '0') + '" data-name="' + escapeHtml(displayName) + '"><i class="fas ' + stateIcon + '"></i><span>' + stateLabel + '</span></button>' +
		'</div>';
	}

	var table = $tableElement.DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: false,
		pageLength: 10,
		stateSave: true,
		ajax: {
			url: appUrl('/asset_groups/ajax_list'),
			type: 'POST',
			error: function (xhr) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
					return;
				}
				growl('Unable to load asset groups', 'danger');
			}
		},
		order: [[1, 'asc']],
		columnDefs: [
			{ targets: 0, width: '18%' },
			{ targets: 1, width: '44%' },
			{ targets: 2, width: '38%', orderable: false, searchable: false }
		],
		columns: [
			{
				data: 'equipment_group_code',
				defaultContent: '—',
				render: function (value, type) {
					if (type !== 'display') return value || '';
					return '<span class="identity-code">' + escapeHtml(value) + '</span>';
				}
			},
			{
				data: 'equipment_group_name',
				defaultContent: '—',
				render: function (value, type) {
					return type === 'display' ? '<strong class="master-record-name">' + escapeHtml(value) + '</strong>' : (value || '');
				}
			},
			{
				data: null,
				defaultContent: '',
				render: function (value, type, row) {
					return type === 'display' ? actionButtons(row) : '';
				}
			}
		],
		language: {
			lengthMenu: 'Show _MENU_ entries',
			search: '',
			searchPlaceholder: 'Search asset groups...',
			processing: 'Loading asset groups...',
			info: 'Showing _START_ to _END_ of _TOTAL_ asset groups',
			infoEmpty: 'No asset groups available',
			zeroRecords: 'No matching asset groups found',
			emptyTable: 'No asset groups have been created',
			paginate: { previous: 'Previous', next: 'Next' }
		}
	});

	$tableElement.on('click', '.identity-state-action', function () {
		var $button = $(this);
		var active = parseInt($button.attr('data-active'), 10) === 1;
		var nextState = active ? 0 : 1;
		var verb = active ? 'deactivate' : 'activate';
		var name = $button.attr('data-name') || 'this asset group';

		if (!window.confirm('Are you sure you want to ' + verb + ' ' + name + '?')) return;

		$button.prop('disabled', true).addClass('is-loading');
		$.ajax({
			url: appUrl('/asset_groups/state_ajax'),
			type: 'POST',
			dataType: 'json',
			data: { id: $button.data('id'), active: nextState }
		}).done(function (response) {
			if (response && response.state) {
				growl('Asset group ' + (nextState ? 'activated' : 'deactivated') + ' successfully', 'success');
				table.ajax.reload(null, false);
			} else {
				growl('Could not update the asset group status', 'danger');
			}
		}).fail(function () {
			growl('Could not update the asset group status', 'danger');
		}).always(function () {
			$button.prop('disabled', false).removeClass('is-loading');
		});
	});
})(jQuery);
