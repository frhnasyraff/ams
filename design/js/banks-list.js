$(document).ready(function () {
	$('#banks').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "/banks/ajax_list",
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
				"data": "name",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html('<a href="/banks/info?id=' + id_encode(rowData.bank_id) + '" title="View Bank">' + cellData + '</a>');
					}
				}
			},
			{
				"data": "active",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).addClass("text-center").html('<input type="checkbox" ' + (rowData.active != 0 ? 'checked' : '') + ' data-toggle="toggle" data-id="' + rowData.bank_id + '" />');
					}
				}
			}
		]

	});

	$.fn.dataTable.ext.errMode = 'none';
});