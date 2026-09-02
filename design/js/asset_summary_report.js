$(document).ready(function () {
	
	$('#asset_summary_report').DataTable({
		"processing": true,    
		"responsive": true,
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "Asset_summary_report/ajax_list",
			"type": "GET",
			"dataSrc": function (json) {
				// Verify if the structure of json is correct
				return [json]; // Since it's a single-row summary report
			},
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
				data: null,
				orderable: false,
				createdCell: function(td, cellData, rowData, row, col) {
					if (cellData) {
						$(td).html(`<input type="checkbox" name="record" class="checkbox-select" value="1" />`);
					}
				}
			},
			{ "data": "total" },
			{ "data": "location" },
			{ "data": "in_use" },
			{ "data": "faulty" },
			// {
			// 	data: null,  // Fixed: set `data: null` if no actual data is available for the column
			// 	orderable: false,
			// 	createdCell: function(td, cellData, rowData, row, col) {
			// 		$(td).html('----------------------------------------');
			// 	}
			// },
			{ "data": "maintenance" }
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


