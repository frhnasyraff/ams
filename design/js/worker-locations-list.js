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
						$(td).html('<a class="text_warning_color" href="/worker_locations/info?id=' + id_encode(rowData.worker_location_id) + '" title="View location info">' + cellData + '</a>');
					}
					}
			},
			{
				"data": "description",
				    },
			{
				"data": "",
				"orderable": false,
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).addClass("text-center").html('<input type="checkbox" ' + (rowData.active != 0 ? 'checked' : '') + ' data-toggle="toggle" data-id="' + rowData.worker_location_id + '" />');
					}
				}
			}
			]

	});

	$.fn.dataTable.ext.errMode = 'none';
});
