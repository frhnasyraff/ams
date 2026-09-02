$(document).ready(function () {
	function calibrationUrl(path) {
		const parts = window.location.pathname.split('/').filter(Boolean);
		const base = parts.length > 1 ? '/' + parts[0] : '';
		return base + '/' + String(path).replace(/^\//, '');
	}

	function calibrationEscape(value) {
		return String(value ?? '-')
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	}

	function dayChip(value, tone) {
		const days = Number(value || 0);
		return '<span class="calibration-day-chip calibration-day-' + tone + '">' + days.toLocaleString() + ' days</span>';
	}

	function initializeDataTable(url) {
        $('#faulty_summary').DataTable({
            "processing": true,
            "responsive": true,
            "autoWidth": false,
            "pageLength": 10,
            "pagingType": "simple_numbers",
            "dom": '<"calibration-dt-top"lf>t<"calibration-dt-bottom"ip>',
            stateSave: true,
            "ajax": {
                "url": calibrationUrl(url),
                "type": "GET",
				"dataSrc": function (response) {
					const rows = response && Array.isArray(response.data) ? response.data : [];
					$('#calibration-due-count').text(rows.length.toLocaleString());
					return rows;
				},
                "error": function (xhr) {
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
                    "data": "equipment_name",
					"render": function (data, type) {
						if (type && type !== 'display') return data || '';
						return '<span class="calibration-name-cell"><i class="fas fa-microchip"></i><span>' + calibrationEscape(data) + '</span></span>';
					}
                },
                {
                    "data": "type_name",
					"render": function (data, type) {
						if (type && type !== 'display') return data || '';
						return '<span class="calibration-type-badge">' + calibrationEscape(data) + '</span>';
					}
                },
                {
                    "data": "calibration_date",
					"render": function (data, type) {
						if (type && type !== 'display') return data || '';
						return '<span class="calibration-date-cell"><i class="far fa-calendar-alt"></i>' + calibrationEscape(data) + '</span>';
					}
                },
                {
                    "data": "frequency_day",
					"render": function (data, type) { return type && type !== 'display' ? Number(data || 0) : dayChip(data, 'frequency'); }
                },
                {
                    "data": "reminder_day",
					"render": function (data, type) { return type && type !== 'display' ? Number(data || 0) : dayChip(data, 'reminder'); }
                },
                {
					"data": null,
					orderable: false,
					createdCell: function (td, cellData, rowData) {
						if (cellData) {
							// Check if it's an equipment or an item and set the data-type accordingly
							const type = rowData.item_name ? 'item' : 'equipment'; // Adjust this condition based on your data structure
							$(td).html(`<button data-id="${rowData.equipment_id}" data-type="${type}" class="editCalibration btn btn-sm"><i class="fas fa-check"></i> Complete</button>`);
						}
					}
				}
            ]
        });
    }

	initializeDataTable("Assets_Item_calibration/ajax_list");

    // Click event for #asset_faulty
    $(document).on("click", "#asset_faulty", function () {
        $('#faulty_summary').DataTable().destroy();
		$('#calibration-current-scope').text('Assets');
		$('#calibration-queue-title').text('Assets Requiring Attention');
        initializeDataTable("Assets_Item_calibration/ajax_list");
    });

    // Click event for #item_faulty
    $(document).on("click", "#item_faulty", function () {
        $('#faulty_summary').DataTable().destroy();
		$('#calibration-current-scope').text('Components');
		$('#calibration-queue-title').text('Components Requiring Attention');
        initializeDataTable("Assets_Item_calibration/item_ajax_list");
    });

	// calibration edit button 

	$(document).on("click", ".editCalibration ", function (e) {
		e.preventDefault();
		
		var type = $(this).data("type");
	
		if (type === "equipment") {
			var dataId = $(this).data("id");
			$.ajax({
				url: calibrationUrl(`Assets_Item_calibration/get_calibration_asset/${dataId}`),
				type: 'GET',
				success: function (response) {
					const data = typeof response === 'string' ? JSON.parse(response) : response;
        
					if (data.calibration_date) {
						$('#calibration_date_edit').val(data.calibration_date);
						$('#edit_id').val(dataId);
						$('#type').val("equipment");
						$('#exampleModal').modal('show');
					} else {
						alert("Calibration data not found.");
					}
					

				},
				error: function (xhr) {
					alert("Failed to retrieve calibration data.");
				}
			}); 
		} else if(type === "item"){
			var dataId = $(this).data("id");

			$.ajax({
				url: calibrationUrl(`Assets_Item_calibration/get_calibration_item/${dataId}`),
				type: 'GET',
				success: function (response) {
					console.log(response);
					const data = typeof response === 'string' ? JSON.parse(response) : response;
        
					if (data.calibration_date) {
						$('#calibration_date_edit').val(data.calibration_date);
						$('#edit_id').val(dataId);
						$('#type').val("item");
						$('#exampleModal').modal('show');
					} else {
						alert("Calibration data not found.");
					}
				},
				error: function (xhr) {
					alert("Failed to retrieve calibration data.");
				}
			});    
		}
	});


	$(document).on("click" , "#complete-calibration" , function(){

		const calibrationDate = $("#calibration_date_edit").val();
		const editId = $("#edit_id").val();
		const type = $("#type").val();

		if (!calibrationDate || !editId || !type) {
			alert("Please fill out all fields.");
			return;
		}

		$.ajax({
			url: calibrationUrl(`Assets_Item_calibration/update_calibration`),
			type: 'POST',
			data: {
				calibration_date: calibrationDate,
				id: editId,
				type: type
			},
			success: function(response) {
				
				// Optionally reload the DataTable or update the UI here
				$('#exampleModal').modal('hide');
				// $('#faulty_summary').DataTable().destroy();
				// initializeDataTable("/Assets_Item_calibration/ajax_list");
				window.location.reload();
				
			},
			error: function(xhr) {
				alert("An error occurred while updating calibration data.");
				console.error(xhr.responseText);
			}
		});
	
	})
	

	

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
