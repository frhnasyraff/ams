$(document).ready(function () {
	$('#fault_lists').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "/fault_lists/ajax_list",
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
		"order": [
			[1, "asc"]
		],
		"columns": [{
				"data": "fault_name",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html('<a href="/fault_lists/info?id=' + id_encode(rowData.fault_id) + '" title="View Fault List">' + cellData + '</a>');
					}
				}
			},
			{
				"data": "active",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).addClass("text-center").html('<input type="checkbox" ' + (rowData.active != 0 ? 'checked' : '') + ' data-toggle="toggle" data-id="' + rowData.fault_id + '" />');
					}
				}
			}
		]

	});

	$.fn.dataTable.ext.errMode = 'none';
});