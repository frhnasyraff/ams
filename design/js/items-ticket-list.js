$(document).ready(function () {
	$('#list').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "/items_ticket/ajax_list",
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
		"columns": [
            {
				"data": "number",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html('<a href="/items_ticket/info?id=' + id_encode(rowData.id) + '" title="View equipment group">' + cellData + '</a>');
					}
				}
			},
			{
				"data": "equipment_name"
            },

			{
				"data": "item_name",
				createdCell: function (td, cellData, rowData, row, col) {
                    if (!$("table.read-only").length) {
                        $(td).html('<a href="/items/info?id=' + rowData.item_id + '#nav-new-maintenance" title="View item" style="color: #80A874;">' + cellData + '</a>');
                    }
                }
            },
			{
				"data": "issue_date"
            },
            
            {
				"data": "fault_type"
            },
            {
				"data": "location"
            },
            {
				"data": "state"
            },
            {
				"data": "details_of_issue"
            },
            {
				"data": "severity"
            },
            {
				"data": "date_of_completion"
            },
            {
                "data": 'action',
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html('<a href="/items_ticket/delete?id=' + id_encode(rowData.id) + '" onclick="return confirm(\'Are you sure you want to delete this Ticket?\');" title="Delete Ticket"><i class="fa fa-trash"></i></a>');
                }
            }
		]

	});

	$.fn.dataTable.ext.errMode = 'none';
});