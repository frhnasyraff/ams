$(document).ready(function () {
	$('#designations').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"autoWidth": true,
		"pageLength": 10,
		stateSave: false,
		"columnDefs": [
			{ "targets": 0, "width": "54%" },
			{ "targets": 1, "width": "24%" },
			{ "targets": 2, "width": "22%", "className": "access-actions-cell" }
		],
		"ajax": {
			"url": appUrl("/designations/ajax_list"),
			"type": "POST",
			"error": function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert("We are having trouble connecting to the API.");
				}
			}
		},
		drawCallback: function () {
			initToggle();
		},
		"order": [
			[0, "asc"]
		],
		"columns": [{
				"data": "designation_name",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html('<a class="access-record-link" href="/designations/info?id=' + id_encode(rowData.designation_id) + '" title="View designation">' + cellData + '</a>');
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
						$(td).addClass("access-actions-cell").html('<div class="access-row-actions"><a class="access-action access-edit" href="' + appUrl("/designations/info?id=" + id_encode(rowData.designation_id)) + '" title="Edit designation"><i class="fas fa-pen" aria-hidden="true"></i> Edit</a><input type="checkbox" ' + (rowData.active != 0 ? 'checked' : '') + ' data-toggle="toggle" data-id="' + rowData.designation_id + '" /></div>');
					}
				}
			}
		]

	});

	$.fn.dataTable.ext.errMode = 'none';
});
