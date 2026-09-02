$(document).ready(function () {
	$('#assetMaintenance_summary').DataTable({
		"processing": true,
		
		"responsive": true,
		"autoWidth": true,
		// "pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "/AssetMaintenance_summary/ajax_list",
			"type": "GET",
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
		"columns": [ {
				data: "equipment_maintenance_id",
				orderable: false,
				createdCell: function(td, cellData, rowData, row, col) {
					if (cellData) {
						$(td).html(`<input type="checkbox" name="record[]" class="checkbox-select" value="${rowData.equipment_maintenance_id}" />`);
					}
				}
			},
			{
				"data": "equipment_name"
            },
			{
				"data": "type_name"
            },
			{
				"data": "maintenance_cost"
            },
			{
				"data": "maintenance_date"
            },
			
           
		]

	});

});


$(document).on("change", "#download_type_select", function () {
    var value = $(this).val();
    $("#downlaod-type").val(value);
});

$("#select_all_checkboxes:not([disabled])").on("click", function () {
	$('.checkbox-select').each(function () {
		$(this).prop('checked', !$(this)[0].checked);
	});
	$(this).text(function (i, text) {
		return text === "Select All" ? "Un-Select All" : "Select All";
	})
});


