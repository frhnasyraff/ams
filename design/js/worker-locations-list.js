$(document).ready(function() {
    $('#worker_locations').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "/worker_locations/ajax_list",
			"type": "POST",
			"error": function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert("We are having trouble connecting to the API.");
				}
			}
		},
		drawCallback: initToggle,
		"order": [[1, "asc"]],
		"columns": [
			{
				"data": "worker_location_name",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html('<a class="access-record-link" href="/worker_locations/info?id=' + id_encode(rowData.worker_location_id) + '" title="View location info">' + cellData + '</a>');
					}
					}
			},
			{
				"data": "description",
				    },
			{
				"data": null,
                "searchable": false,
				"orderable": false,
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).addClass("access-actions-cell").html('<div class="access-row-actions"><a class="access-action access-edit" href="' + appUrl("/worker_locations/info?id=" + id_encode(rowData.worker_location_id)) + '" title="Edit worker location"><i class="fas fa-pen" aria-hidden="true"></i> Edit</a><input type="checkbox" ' + (rowData.active != 0 ? 'checked' : '') + ' data-toggle="toggle" data-id="' + rowData.worker_location_id + '" /></div>');
					}
				}
			}
			]

	});

	$.fn.dataTable.ext.errMode = 'none';
});
