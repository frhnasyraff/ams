$(document).ready(function () {
	$('#rebundling_colours').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "/rebundling_colours/ajax_list",
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
				"data": "rebundling_colour_name",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html('<a href="/rebundling_colours/info?id=' + id_encode(rowData.rebundling_colour_id) + '" title="View colour">' + cellData + '</a>');
					}
				}
			},
			{
				"data": "description",
			},
			{
				"data": "active",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).addClass("text-center").html('<input type="checkbox" ' + (rowData.active != 0 ? 'checked' : '') + ' data-toggle="toggle" data-id="' + rowData.rebundling_colour_id + '" />');
					}
				}
			}
		]

	});

	$.fn.dataTable.ext.errMode = 'none';
});