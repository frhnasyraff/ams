$(document).ready(function () {
	$('#insurance_companies').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "/insurance_companies/ajax_list",
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
						$(td).html('<a href="/insurance_companies/info?id=' + id_encode(rowData.insurance_company_id) + '" title="View Insurance Companies">' + cellData + '</a>');
					}
				}
			},
			{
				"data": "address",
			},
			{
				"data": "active",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).addClass("text-center").html('<input type="checkbox" ' + (rowData.active != 0 ? 'checked' : '') + ' data-toggle="toggle" data-id="' + rowData.insurance_company_id + '" />');
					}
				}
			}
		]

	});

	$.fn.dataTable.ext.errMode = 'none';
});