$(document).ready(function () {
	$('#resource_types').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "/resource_types/ajax_list",
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
				"data": "resource_type_name",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html('<a class="text_warning_color" href="/resource_types/info?id=' + id_encode(rowData.resource_type_id) + '" title="View resource type">' + cellData + '</a>' + (rowData.supervising == "1" ? ' <i class="fa fa-street-view tip" title="Supervising user"></i>' : ''));
					}
				}
			},
			{
				"data": "resource_type_short_code",
				createdCell: function (td, cellData, rowData) {
					$(td).html('<span class="badge badge-info" style="background: ' + rowData.resource_type_colour + '">' + cellData + '</span>');
				}
			},
			{
				"data": "description",
			},
			{
				"data": "active",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).addClass("text-center").html('<input type="checkbox" ' + (rowData.active != 0 ? 'checked' : '') + ' data-toggle="toggle" data-id="' + rowData.resource_type_id + '" />');
					}
				}
			}
		]

	});
	if ($("#color-block").length) {
		var color = randomColor();
		$("#color-block").wheelColorPicker('setValue', $("#color-block").val() ? $("#color-block").val() : color);

		$('input.time_picker, div.time_picker input').datepicker({
			onlyTimepicker: true,
			timepicker: true,
			timeFormat: 'hh:ii',
			minutesStep: 30,
			multipleDatesSeparator: ' - ',
			autoClose: true,
			});
   
	$("#color-block").on('colorchange', function (e) {
		var c = hexToRgb($(this).val());
		colour = "rgba(" + c.r + "," + c.g + "," + c.b + ",0.9)";
		$("input#form_short_code").css('background', colour);
		
	});
	}
	$.fn.dataTable.ext.errMode = 'none';
});