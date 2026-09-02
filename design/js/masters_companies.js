$(document).ready(function () {
	$('#masters_companies').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "/masters_companies/ajax_list",
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
				"data": "registration_id",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html('<a class="text_warning_color" href="/masters_companies/info?id=' + id_encode(rowData.company_id ) + '" title="View company">' + cellData + '</a>');
					}
				}
			},
			{
				"data": "company_name",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html('<a href="/masters_companies/info?id=' + id_encode(rowData.company_id ) + '" title="View company">' + cellData + '</a>');
					}
				}
				
			},
			{
				"data": "contact_person",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html('<a href="/masters_companies/info?id=' + id_encode(rowData.company_id ) + '" title="View company">' + cellData + '</a>');
					}
				}
				
			},
			{
				"data": "contact_email",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html('<a href="/masters_companies/info?id=' + id_encode(rowData.company_id ) + '" title="View company">' + cellData + '</a>');
					}
				}
				
			},
			{
				"data": "business_type",
								
			},
			{
				"data": "active",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).addClass("text-center").html('<input type="checkbox" ' + (rowData.active != 0 ? 'checked' : '') + ' data-toggle="toggle" data-id="' + rowData.company_id + '" />');
					}
				}
			}
		]

	});

	$.fn.dataTable.ext.errMode = 'none';
});