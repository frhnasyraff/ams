function amsUrl(path) {
	// Legacy implementation:
	// var basePath = window.location.pathname.indexOf("/assets_IT-usman/") === 0 ? "/assets_IT-usman" : "";
	// return basePath + path;
	var pathname = window.location.pathname;
	var controllerMatch = pathname.match(/\/assets(?:\/|$)/i);
	var basePath = controllerMatch ? pathname.substring(0, controllerMatch.index) : "";
	return basePath + "/" + path.replace(/^\/+/, "");
}

$(document).ready(function () {
	if ($(".dropzone").length) {
		$(".dropzone").dropzone({
			acceptedFiles: "image/*",
			uploadMultiple: false,
			maxFiles: 1,
			queuecomplete: function (e) {
				window.location.reload();
			},
		});
	}

	assets = $("#assets").DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: false,
		pageLength: 10,
		stateSave: true,
		dom: '<"assets-dt-top"<"assets-dt-left"l><"assets-dt-right"f>>t<"assets-dt-bottom"ip>',
		ajax: {
			// url: "/assets/ajax_list",
			url: amsUrl("/assets/ajax_list"),
			type: "POST",
			data: function (d) {
				return $.extend({}, d, {
					equipment_type: $(".equipment_type_filter .btn-primary").data("filter"),
					equipment_group: $(".equipment_group_filter .btn-primary").data("filter"),
				});
			},
			"error": function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert("We are having trouble connecting to the API.");
				}
			},
		},
		drawCallback: initToggle,
		order: [[1, "desc"]],
		columns: [

			{
				data: "equipment_id",
				orderable: false,
				createdCell: function (td, cellData, rowData, row, col) {
					if (cellData) {
						$(td).html(
							`<input type="checkbox" name="equipment_ids[]" class="checkbox-equipment-id" value="${rowData.equipment_id}" />`
						);
					}
				},
			},
			{
				data: null,
				orderable: false,
				createdCell: function (td, cellData, rowData, row, col) {
					if (cellData) {
						$(td)
							.addClass("p-0 m-0 text-center")
							.html(
								'<div class="btn view-list" data-id="' +
								rowData.equipment_id +
								'">' +
								'<i class="fas fa-eye"></i>' +
								"</div>"
							);
					}
				},
			},

			{
				data: "name",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html(
							'<a class="text_light_blue_color" href="/assets/info?id=' +
							id_encode(rowData.equipment_id) +
							'" title="View equipment">' +
							cellData +
							"</a>"
						);
					}
				},
			},
			{
				data: "equipment_name",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html(
							'<a class="text_light_blue_color" href="/assets/info?id=' +
							id_encode(rowData.equipment_id) +
							'" title="View equipment">' +
							cellData +
							"</a>"
						);
					}
				},
			},
			{
				data: "equipment_registration",
			},

			{
				data: "state_name",
			},

			{
				data: "equipment_status",
				createdCell: function (td, cellData, rowData, row, col) {
					if (cellData) {
						var customClass = "purple";
						var status = rowData.equipment_status.replace(/\s+/g, "");
						if (status == "MAINTENANCE") {
							customClass = "warn";
						} else if (status == "AVAILABLE") {
							customClass = "light-blue";
						} else if (status == "Available") {
							customClass = "light-blue";
						} else if (status == "SERVICEABLE") {
							customClass = "green";
						} else if (status == "UNSERVICEABLE") {
							customClass = "red";
						}else if (status == "STORE") {
							customClass = "dark-blue";
						}
						$(td).html(
							`<span class='custom-badge ${customClass}'>${rowData.equipment_status}<span>`
						);
					}
				},
			},

	{
    data: "active",
    createdCell: function (td, cellData, rowData, row, col) {
        if (!$("table.read-only").length) {
            const toggleCheckbox = `
                <input type="checkbox" 
                       ${rowData.active != 0 ? "checked" : ""} 
                       data-toggle="toggle"
                       data-on="Active"
                       data-off="Inactive"
                       data-onstyle="success"
                       data-offstyle="danger"
                       data-style="asset-row-active-toggle status-text-toggle"
                       data-id="${rowData.equipment_id}" />`;

            const html = `
                <div class="asset-status-action">
                    ${toggleCheckbox}
                </div>`;

            $(td)
                .addClass("text-center status-only-action-cell")
                .html(html);
        }
    }
}



		],
	});

	$(document).on("change", ".checkbox-equipment-id", function () {
		let row = $(this).closest("tr");
		if ($(this).is(":checked")) {
			row.addClass("highlight-row");
		} else {
			row.removeClass("highlight-row");
		}
	});


	$("#assets").on("click", ".add_mileage", function (e) {
		e.preventDefault();
		var that = $(this);
		$("#addMileageModal .equipment_registration").html(
			that.data("registration")
		);
		$("#addMileageModal #form_mileage").val(that.data("mileage"));
		$("#addMileageModal .equipment_id").val(that.data("id"));
		$("#addMileageModal").modal("show");
	});

	$("#assets").on("click", ".add_maintenance", function (e) {
		e.preventDefault();
		var that = $(this);
		$("#addScheduledMaintenanceModal .equipment_registration").html(
			that.data("registration")
		);
		$("#addScheduledMaintenanceModal #form_mileage").val(that.data("mileage"));
		$("#addScheduledMaintenanceModal .equipment_id").val(that.data("id"));
		$("#addScheduledMaintenanceModal").modal("show");
	});

	$("input.date_picker, div.date_picker input").datepicker({
		dateFormat: "dd/mm/yyyy",
		timepicker: false,
	});

	$("input.date_picker_now, div.date_picker_now input").datepicker({
		dateFormat: "dd/mm/yyyy",
		timepicker: false,
		maxDate: new Date(),
	});

	$("#form_equipment_type").select2();

	if ($("#groups").length) {
		$("#groups").multiSelect({
			selectableHeader: "Group(s) available",
			selectionHeader: "Assigned to group(s)",
		});
	}

	$("#equipment_mileage").DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: true,
		pageLength: 10,
		stateSave: true,
		dom: '<"assets-dt-top"<"assets-dt-left"l><"assets-dt-right"f>>t<"assets-dt-bottom"ip>',
		ajax: {
			// url: "/assets/mileage_ajax_list",
			url: amsUrl("/assets/mileage_ajax_list"),
			type: "POST",
			error: function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert("We are having trouble connecting to the API.");
				}
			},
			data: function (d) {
				return $.extend({}, d, {
					id: $('input[name="id"]').val(),
				});
			},
		},
		drawCallback: initToggle,
		order: [[0, "desc"]],
		columns: [
			{
				data: "date_recorded",
			},
			{
				data: "mileage",
			},
		],
	});

	$("#equipment_consumable").DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: true,
		pageLength: 10,
		stateSave: true,
		dom: '<"assets-dt-top"<"assets-dt-left"l><"assets-dt-right"f>>t<"assets-dt-bottom"ip>',
		ajax: {
			// url: "/assets/consumable_ajax_list",
			url: amsUrl("/assets/consumable_ajax_list"),
			type: "POST",
			error: function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert("We are having trouble connecting to the API.");
				}
			},
			data: function (d) {
				return $.extend({}, d, {
					id: $('input[name="id"]').val(),
				});
			},
		},
		drawCallback: initToggle,
		order: [[0, "desc"]],
		columns: [
			{
				data: "date_recorded",
			},
			{
				data: "consumable_name",
				createdCell: function (td, cellData, rowData, row, col) {
					$(td).html(
						'<a href="/consumables/info?id=' +
						id_encode(rowData.consumable_id) +
						'" title="View consumable info">' +
						cellData +
						"</a>"
					);
				},
			},
			{
				data: "quantity",
			},
		],
	});

	$("#equipment_usage").DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: true,
		pageLength: 10,
		stateSave: true,
		dom: '<"assets-dt-top"<"assets-dt-left"l><"assets-dt-right"f>>t<"assets-dt-bottom"ip>',
		ajax: {
			// url: "/assets/usage_ajax_list",
			url: amsUrl("/assets/usage_ajax_list"),
			type: "POST",
			error: function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert("We are having trouble connecting to the API.");
				}
			},
			data: function (d) {
				console.log(d);
				return $.extend({}, d, {
					id: $('input[name="id"]').val(),
				});
			},
		},
		drawCallback: initToggle,
		order: [[0, "desc"]],
		columns: [
			{
				data: "vh_date",
			},
			{
				data: "vh_date_end",
			},
			{
				data: "vh_time_start",
			},
			{
				data: "vh_time_end",
			},
			{
				data: "vh_location_start",
			},
			{
				data: "vh_location_end",
			},
			// {
			// 	data: "worker_name",
			// },
			// {
			// 	data: "vh_driver_name_ic_number",
			// },
		],
	});

	// new maintenance recordd

	$("#equipment_new_maintenance").DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: true,
		pageLength: 10,
		stateSave: true,
		dom: '<"assets-dt-top"<"assets-dt-left"l><"assets-dt-right"f>>t<"assets-dt-bottom"ip>',
		ajax: {
			// url: "/assets/new_maintenance_ajax_list",
			url: amsUrl("/assets/new_maintenance_ajax_list"),
			type: "POST",
			error: function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert("We are having trouble connecting to the API.");
				}
			},
			data: function (d) {
				return $.extend({}, d, {
					id: $('input[name="id"]').val(),
				});
			},
		},
		drawCallback: initToggle,
		order: [[0, "desc"]],
		columns: [

			{
				data: "created_at",
				"title": "Created At"
			},
			{
				data: "update_date",
				"title": "Update Date"
			},
			{
				data: "maintenance_type_id",
				"title": "Maintenance Type"
			},


			{
				data: "final_status",
				"title": "Final Status"
			},
			{
				data: null,
				title: "Action",
				orderable: false,
				searchable: false,
				createdCell: function (td, cellData, rowData, row, col) {
					$(td).html('<a href="/assets/delete?id=' + id_encode(rowData.equipment_maintenance_id) + '" onclick="return confirm(\'Are you sure you want to delete this log?\');" title="Delete Log"><i class="fa fa-trash"></i></a>');
				}
			},
			{
				data: null,
				orderable: false,
				title: "View",
				createdCell: function (td, cellData, rowData, row, col) {
					if (cellData) {
						$(td)
							.addClass("p-0 m-0 text-center")
							.html(
								'<button class="btn view-details" data-equipment_maintenance_id="' +
								rowData.equipment_maintenance_id +
								'">' +
								'<i class="fas fa-eye"></i>' +
								"</button>"
							);
					}
				}
			}


		],
	});

	$("#equipment_maintenance").DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: true,
		pageLength: 10,
		stateSave: true,
		dom: '<"assets-dt-top"<"assets-dt-left"l><"assets-dt-right"f>>t<"assets-dt-bottom"ip>',
		ajax: {
			// url: "/assets/maintenance_ajax_list",
			url: amsUrl("/assets/maintenance_ajax_list"),
			type: "POST",
			error: function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert("We are having trouble connecting to the API.");
				}
			},
			data: function (d) {
				return $.extend({}, d, {
					id: $('input[name="id"]').val(),
				});
			},
		},
		drawCallback: initToggle,
		order: [[0, "desc"]],
		columns: [
			{
				data: "maintenance_date",
				createdCell: function (td, cellData, rowData, row, col) {
					$(td).html(
						'<a href="/assets/maintenance?id=' +
						id_encode(rowData.equipment_maintenance_id) +
						'" title="View maintenance info">' +
						cellData +
						"</a>"
					);
				},
			},
			{
				data: "in_out",
			},
			{
				data: "maintenance_mileage",
			},
		],
	});

	$("#csv-file-upload").on("change", function () {
		var filePath = $(this).val();
		var filename = filePath.split(/[\\\/]/).pop();
		var allowedExtensions = /(\.csv)$/i; //  /(\.csv|\.jpeg|\.png|\.gif)$/i
		if (!allowedExtensions.exec(filePath)) {
			alert("Invalid file type");
			$(this).val("");
			return false;
		}
		$("#csv-file-upload + label").text(filename);
		$("#csv-upload-form").submit();
	});

	$('input[name="service_interval_weeks"]').change(function () {
		if ($('input[name="last_service_date"]').val()) {
			$('input[name="next_service_date"]').val(
				moment($('input[name="last_service_date"]').val(), "DD/MM/YYYY")
					.add($('input[name="service_interval_weeks"]').val(), "weeks")
					.format("DD/MM/YYYY")
			);
		}
	});

	$(".equipment_type_filter .btn").click(function () {
		$(".equipment_type_filter .btn").removeAttr("disabled").removeClass("btn-primary active");
		$(this).addClass("btn-primary active").attr("disabled", "disabled");
		assets.ajax.reload();
	});

	$(".equipment_group_filter .btn").click(function () {
		$(".equipment_group_filter .btn").removeAttr("disabled").removeClass("btn-primary active");
		$(this).addClass("btn-primary active").attr("disabled", "disabled");
		assets.ajax.reload();
	});
	$(document).on("change", "#status", function (e) {
		//on add input button click
		e.preventDefault();
		if ($("#status").val() === "sold") {
			$("#purchased_by_container").show();
			$("#purchase_price_container").show();
		} else {
			$("#purchased_by_container").hide();
			$("#purchase_price_container").hide();
		}
	});

	// for Edit location state

	// for Add
	$(document).on("change", "#stateSelect", function () {
		var stateId = $(this).val();

		// Clear the location select options
		$("#locationSelect").html('<option value="">--Select--</option>');

		if (stateId) {
			// Fetch locations based on selected state
			$.ajax({
				// url: "/assets/locationDropdown", // Replace with your correct endpoint URL
				url: amsUrl("/assets/locationDropdown"),
				method: "POST",
				data: { state_id: stateId },
				dataType: "json",
				success: function (response) {
					if (response.locations.length > 0) {
						// Populate the location dropdown
						$.each(response.locations, function (index, location) {
							$("#locationSelect").append(
								'<option value="' +
								location.id +
								'">' +
								location.name +
								"</option>"
							);
						});
					} else {
						$("#locationSelect").append(
							'<option value="">No locations available</option>'
						);
					}
				},
				// Legacy handler:
				// error: function () { alert("Error fetching locations"); }
				error: function (xhr) {
					console.error("Error fetching locations", xhr.status, xhr.responseText);
					$("#locationSelect").append(
						'<option value="">Unable to load locations</option>'
					);
				},
			});
		}
	});

	// check box code

	$("#faulty_type_field").hide();

	// Toggle visibility based on checkbox
	$("#faulty_type_toggle").change(function () {
		if ($(this).is(":checked")) {
			$("#faulty_type_field").show();
		} else {
			$("#faulty_type_field").hide();
		}
	});

	// check box for item

	$("#faulty_type_field_item").hide();

	// Toggle visibility based on checkbox
	$("#faulty_type_toggle_item").change(function () {
		if ($(this).is(":checked")) {
			$("#faulty_type_field_item").show();
		} else {
			$("#faulty_type_field_item").hide();
		}
	});

	$(
		"#calibration_date_item, #frequency_day_item, #reminder_day_item, #maintenance_date_item, #frequency_year_item, #maintenance_reminder_day_item"
	).hide();

	// Toggle visibility based on checkbox
	$("#calibration_asset_item").change(function () {
		if ($(this).is(":checked")) {
			$(
				"#calibration_date_item, #frequency_day_item, #reminder_day_item"
			).show();
		} else {
			$(
				"#calibration_date_item, #frequency_day_item, #reminder_day_item"
			).hide();
		}
	});

	// for maintenence
	$("#maintenence_asset_item").change(function () {
		if ($(this).is(":checked")) {
			$(
				"#maintenence_date_item, #frequency_year_item, #maintenence_reminder_day_item"
			).show();
		} else {
			$(
				"#maintenence_date_item, #frequency_year_item, #maintenence_reminder_day_item"
			).hide();
		}
	});

	// check box for New item

	$("#faulty_type_new").hide();

	// Toggle visibility based on checkbox
	$("#faulty_type_toggle_new").change(function () {
		if ($(this).is(":checked")) {
			$("#faulty_type_new").show();
		} else {
			$("#faulty_type_new").hide();
		}
	});

	// edit items

	// Attach event handler to all checkboxes with the class "edit_faulty_type_toggle_item"
	$(".edit_faulty_type_toggle_item").each(function () {
		var key = $(this).data("key"); // Get the unique key for the current checkbox

		// Initially hide the faulty type field
		$(".edit_faulty_type_field_item_" + key).hide();

		// Toggle visibility based on the checkbox change event
		$(this).change(function () {
			if ($(this).is(":checked")) {
				$(".edit_faulty_type_field_item_" + key).show();
			} else {
				$(".edit_faulty_type_field_item_" + key).hide();
			}
		});
	});

	// calibration toggle

	// Initially hide fields
	$(
		"#calibration_date, #frequency_day, #reminder_day, #maintenance_date, #frequency_year, #maintenance_reminder_day"
	).hide();

	// Toggle fields based on checkbox state
	$("#calibration_asset").change(function () {
		if ($(this).is(":checked")) {
			$("#calibration_date, #frequency_day, #reminder_day").show();
		} else {
			$("#calibration_date, #frequency_day, #reminder_day").hide();
		}
	});

	// for maintenence
	$("#maintenance_asset").change(function () {
		if ($(this).is(":checked")) {
			$("#maintenance_date, #frequency_year, #maintenance_reminder_day").show();
		} else {
			$("#maintenance_date, #frequency_year, #maintenance_reminder_day").hide();
		}
	});

	$(
		"#calibration_date_item, #frequency_day_item, #reminder_day_item, #maintenance_date_item, #frequency_year_item, #maintenance_reminder_day_item"
	).hide();

	// Toggle visibility based on checkbox
	$("#calibration_asset_item").change(function () {
		if ($(this).is(":checked")) {
			$(
				"#calibration_date_item, #frequency_day_item, #reminder_day_item"
			).show();
		} else {
			$(
				"#calibration_date_item, #frequency_day_item, #reminder_day_item"
			).hide();
		}
	});

	// for maintenence
	$("#maintenence_asset_item").change(function () {
		if ($(this).is(":checked")) {
			$(
				"#maintenence_date_item, #frequency_year_item, #maintenence_reminder_day_item"
			).show();
		} else {
			$(
				"#maintenence_date_item, #frequency_year_item, #maintenence_reminder_day_item"
			).hide();
		}
	});

	// edit

	$(".edit_calibration_item").each(function () {
		var key = $(this).data("key"); // Get the unique key for the current checkbox

		// Initially hide the faulty type field
		$(".edit_calibration_field_item_" + key).hide();

		// Toggle visibility based on the checkbox change event
		$(this).change(function () {
			if ($(this).is(":checked")) {
				$(".edit_calibration_field_item_" + key).show();
			} else {
				$(".edit_calibration_field_item_" + key).hide();
			}
		});
	});

	// asset_calibration value get

	var asset_calibration_edit = $("#equipment_type_calibration_edit").val();
	if (asset_calibration_edit) {
		// Make an AJAX call to check if calibration is required
		$.ajax({
			// url: "/assettypes/asset_calibration", // PHP file to handle the check
			url: amsUrl("/assettypes/asset_calibration"),
			method: "POST",
			data: { asset_id: asset_calibration_edit },
			success: function (response) {
				console.log(response);
				// Parse response (assuming response is JSON with 'calibration' field)

				if (response.calibration == 1) {
					// Show calibration fields if calibration is required
					$("#calibration_date").show();
					$("#frequency_day").show();
					$("#reminder_day").show();
				} else {
					// Hide calibration fields if not required
					$("#calibration_date").hide();
					$("#frequency_day").hide();
					$("#reminder_day").hide();
				}

				if (response.maintenance == 1) {
					// Show maintenance fields if maintenance is required
					$("#maintenance_date").show();
					$("#frequency_year").show();
					$("#maintenance_reminder_day").show();
				} else {
					// Hide maintenance fields if not required
					$("#maintenance_date").hide();
					$("#frequency_year").hide();
					$("#maintenance_reminder_day").hide();
				}
			},
		});
	} else {
		// Hide calibration fields if no type is selected
		$("#calibration_date").hide();
		$("#frequency_day").hide();
		$("#reminder_day").hide();
		$("#maintenance_date").hide();
		$("#frequency_year").hide();
		$("#maintenance_reminder_day").hide();
	}
});

var assets;
if (typeof Dropzone != "undefined" && Dropzone) {
	Dropzone.autoDiscover = false;
}

$("#select_all_checkboxes").on("click", function () {
	let allSelect = $(this).text();
	$(".checkbox-equipment-id").each(function () {
		if (allSelect == "Select All") {
			$(this).prop("checked", true);
		} else {
			$(this).prop("checked", false);
		}
	});
	$(this).text(function (i, text) {
		return text === "Select All" ? "Un Select All" : "Select All";
	});
});

$(document).ready(function () {
  // Register modal close handler once
  $(document).on("click", ".hideEyeModal", function () {
    $("#equipmentModal").modal("hide");
    $("#modal-body-content").html(""); // clear content
  });

  // Register item view handler
  $(document).on("click", ".view-list", function () {
    const equipmentId = $(this).data("id");
    
    if (!equipmentId) {
      alert("No equipment ID found.");
      return;
    }

    // Show loading message
    $("#modal-body-content").html("<p>Loading...</p>");
    $("#equipmentModal").modal("show");

    // Send AJAX request
    $.ajax({
      // url: "/assets/itemList",
      url: amsUrl("/assets/itemList"),
      method: "GET",
      data: { id: equipmentId },
      dataType: "json",
      success: function (response) {
        console.log("AJAX Response:", response);

        let modalContent = "";

        if (Array.isArray(response) && response.length > 0) {
          response.forEach(function (item) {
            modalContent += `
              <div class="item">
                <p><input type="text" class="form-control" value="${item.item_name}" readonly></p>
                <p>Component Type: ${item.item_type_name}</p>
                <p>Manufacturer: ${item.manufacturer_name}</p>
                <p>Component Status: ${item.status}</p>
                <hr>
              </div>`;
          });
        } else {
          modalContent = "<p>No Component found for this asset.</p>";
        }

        $("#modal-body-content").html(modalContent);
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        $("#modal-body-content").html("<p>Error fetching data. Please try again.</p>");
      }
    });
  });
});



// Handle the click event for the eye icon for maintenance logs table 
$(document).on("click", ".view-details", function () {
	var equipmentmaintenanceid = $(this).data("equipment_maintenance_id");

	// Send AJAX request to fetch data based on equipment ID
	$.ajax({
		// url: "/assets/logDetails", // Replace with your route
		url: amsUrl("/assets/logDetails"),
		method: "GET",
		data: { id: equipmentmaintenanceid },
		success: function (response) {
			console.log("AJAX Response:", response); // Log the response
			$("#equipmentModal").modal("show");
			var modalContent = "";

			// Check if response is valid and contains equipment_maintenance data
			if (response.equipment_maintenance) {
				modalContent += '<div class="row form-entry">';

				// Equipment Maintenance ID
				// modalContent += '<div class="form-group col-sm-6">';
				// modalContent += '<label><strong>Equipment Maintenance ID:</strong></label>';
				// modalContent += '<input type="text" class="form-control" value="' + response.equipment_maintenance.equipment_maintenance_id + '" readonly>';
				// modalContent += '</div>';

				// Equipment ID
				// modalContent += '<div class="form-group col-sm-6">';
				// modalContent += '<label><strong>Equipment ID:</strong></label>';
				// modalContent += '<input type="text" class="form-control" value="' + response.equipment_maintenance.equipment_id + '" readonly>';
				// modalContent += '</div>';

				// Update Date
				modalContent += '<div class="form-group col-sm-6">';
				modalContent += '<label><strong>Update Date:</strong></label>';
				modalContent += '<input type="text" class="form-control" value="' + response.equipment_maintenance.update_date + '" readonly>';
				modalContent += '</div>';

				// Maintenance Type
				modalContent += '<div class="form-group col-sm-6">';
				modalContent += '<label><strong>Maintenance Type:</strong></label>';
				modalContent += '<input type="text" class="form-control" value="' + response.equipment_maintenance.maintenance_type_id + '" readonly>';
				modalContent += '</div>';

				// Ticket Number
				modalContent += '<div class="form-group col-sm-6">';
				modalContent += '<label><strong>Ticket Number:</strong></label>';
				modalContent += '<input type="text" class="form-control" value="' + (response.equipment_maintenance.ticket_number || 'N/A') + '" readonly>';
				modalContent += '</div>';

				// Faulty Type
				modalContent += '<div class="form-group col-sm-6">';
				modalContent += '<label><strong>Faulty Type:</strong></label>';
				modalContent += '<input type="text" class="form-control" value="' + (response.equipment_maintenance.faulty_type || 'N/A') + '" readonly>';
				modalContent += '</div>';

				// Final Status
				modalContent += '<div class="form-group col-sm-6">';
				modalContent += '<label><strong>Final Status:</strong></label>';
				modalContent += '<input type="text" class="form-control" value="' + response.equipment_maintenance.final_status + '" readonly>';
				modalContent += '</div>';

				// Created At
				// modalContent += '<div class="form-group col-sm-6">';
				// modalContent += '<label><strong>Created At:</strong></label>';
				// modalContent += '<input type="text" class="form-control" value="' + response.equipment_maintenance.created_at + '" readonly>';
				// modalContent += '</div>';

				// Updated At
				// modalContent += '<div class="form-group col-sm-6">';
				// modalContent += '<label><strong>Updated At:</strong></label>';
				// modalContent += '<input type="text" class="form-control" value="' + response.equipment_maintenance.updated_at + '" readonly>';
				// modalContent += '</div>';

				modalContent += "</div>"; // Close form-entry div
			}

			// Check if there are maintenance tasks
			if (response.maintenance_tasks && response.maintenance_tasks.length > 0) {
				modalContent += '<div class="tasks">';
				modalContent += '<h5>Maintenance Tasks:</h5>';

				response.maintenance_tasks.forEach(function (task) {
					modalContent += '<div class="row form-entry">';

					// Task Done
					modalContent += '<div class="form-group col-sm-6">';
					modalContent += '<label><strong>Task Done:</strong></label>';
					modalContent += '<input type="text" class="form-control" value="' + task.task_done + '" readonly>';
					modalContent += '</div>';

					// Remarks
					modalContent += '<div class="form-group col-sm-6">';
					modalContent += '<label><strong>Remarks:</strong></label>';
					modalContent += '<textarea class="form-control" readonly>' + task.remarks + '</textarea>';
					modalContent += '</div>';

					// Task Created At
					// modalContent += '<div class="form-group col-sm-6">';
					// modalContent += '<label><strong>Created At:</strong></label>';
					// modalContent += '<input type="text" class="form-control" value="' + task.created_at + '" readonly>';
					// modalContent += '</div>';

					// Task Updated At
					// modalContent += '<div class="form-group col-sm-6">';
					// modalContent += '<label><strong>Updated At:</strong></label>';
					// modalContent += '<input type="text" class="form-control" value="' + task.updated_at + '" readonly>';
					// modalContent += '</div>';

					modalContent += "</div>"; // Close form-entry div for task
					modalContent += "<hr>"; // Add a separator
				});

				modalContent += "</div>"; // Close tasks div
			} else {
				modalContent += "<p>No maintenance tasks found.</p>";
			}

			// Log the modal content
			console.log("Modal Content:", modalContent); // Log the generated content

			// Inject the modal content into the modal body
			$("#modal-body-content").html(modalContent);

		},
		error: function () {
			alert("Error fetching equipment details.");
		},
	});

	// Close the modal when the hide button is clicked
	$(document).on("click", ".hideEyeModal", function () {
		$("#equipmentModal").modal("hide");
		$("#modal-body-content").html("");
	});
});





// Asset Calibration

// $(document).on('change', '#equipment_type_calibration', function() {
//     const assetId = $(this).val();
//     const selectedManufacturer = $(this).find(':selected').data('manufacturer');
//     const selectedPartNumber = $(this).find(':selected').data('part-number');

//     if (assetId) {
//         // Make an AJAX call to check if calibration is required
//         $.ajax({
//             url: '/assettypes/asset_calibration',
//             method: 'POST',
//             data: { asset_id: assetId },
//             success: function(response) {
//                 console.log(response);

//                 // Show calibration fields based on the response
//                 if (response.calibration == 1) {
//                     $('#calibration_date').show();
//                     $('#frequency_day').show();
//                     $('#reminder_day').show();
//                 } else {
//                     $('#calibration_date').hide().val('');
//                     $('#frequency_day').hide().val('');
//                     $('#reminder_day').hide().val('');
//                 }

//                 // Show and auto-select manufacturer and part number fields
//                 if (response.manufacturer || response.vpn) {
//                     $('#manufacturerField').show();
//                     $('#manufacturerSelect').val(response.manufacturer);
//                     $('#partNumberField').show();
//                     $('#partNumberSelect').val(response.vpn);
//                 } else {
//                     $('#manufacturerField').hide().val('');
//                     $('#partNumberField').hide().val('');
//                     $('#manufacturerSelect').val('');
//                     $('#partNumberSelect').val('');
//                 }
//             }
//         });
//     } else {
//         // Hide calibration fields and clear input values if no type is selected
//         $('#calibration_date').hide().val('');
//         $('#frequency_day').hide().val('');
//         $('#reminder_day').hide().val('');

//         // Hide manufacturer and part number fields and reset values
//         $('#manufacturerField').hide().val('');
//         $('#partNumberField').hide().val('');
//         $('#manufacturerSelect').val('');
//         $('#partNumberSelect').val('');
//     }
// });

$(document).on("change", "#equipment_type_calibration", function () {
	const assetId = $(this).val();
	const selectedManufacturer = $(this).find(":selected").data("manufacturer");
	const selectedPartNumber = $(this).find(":selected").data("part-number");

	// Clear existing rows in item container when asset type changes
	$("#itemContainer").empty();

	if (assetId) {
		// Make an AJAX call to check if calibration is required and fetch related asset items
		$.ajax({
			// url: "/assettypes/asset_calibration", // Your existing endpoint to check calibration and get related asset items
			url: amsUrl("/assettypes/asset_calibration"),
			method: "POST",
			data: { asset_id: assetId },
			success: function (response) {
				console.log(response);

				// Handle Calibration fields based on response
				if (response.calibration == 1) {
					$("#calibration_date").show();
					$("#frequency_day").show();
					$("#reminder_day").show();
				} else {
					$("#calibration_date").hide().val("");
					$("#frequency_day").hide().val("");
					$("#reminder_day").hide().val("");
				}

				if (response.maintenance == 1) {
					$("#maintenance_date").show();
					$("#frequency_year").show();
					$("#maintenance_reminder_day").show();
				} else {
					$("#maintenance_date").hide().val("");
					$("#frequency_year").hide().val("");
					$("#maintenance_reminder_day").hide().val("");
				}

				// Show and auto-select manufacturer and part number fields
				if (response.manufacturer || response.vpn) {
					$("#manufacturerField").show();
					$("#manufacturerSelect").val(response.manufacturer);
					$("#partNumberField").show();
					$("#partNumberSelect").val(response.vpn);
				} else {
					$("#manufacturerField").show();
					$("#partNumberField").show();
					$("#manufacturerSelect").val("");
					$("#partNumberSelect").val("");
				}

				// Check if there are related items for this asset type
				if (response.items && response.items.length > 0) {
					response.items.forEach(function (item) {
						// Append rows based on qty field of the related item
						for (let i = 0; i < item.qty; i++) {
							appendItemRow(item); // Call the function to append a row
						}
					});
				}
			},
		});
	} else {
		// If no asset type is selected, hide calibration fields and clear values
		$("#calibration_date").hide().val("");
		$("#frequency_day").hide().val("");
		$("#reminder_day").hide().val("");

		$("#maintenance_date").hide().val("");
		$("#frequency_year").hide().val("");
		$("#maintenance_reminder_day").hide().val("");

		// Hide manufacturer and part number fields and reset values
		$("#manufacturerField").hide().val("");
		$("#partNumberField").hide().val("");
		$("#manufacturerSelect").val("");
		$("#partNumberSelect").val("");
	}
});

// Function to append a new item row dynamically
function appendItemRowOld(item) {
	const newRow = `
        <div class="itemSection">
            <div class="modal-body row">
                <div class="col-sm-4 form-group">
                    <label for="item[]">Component</label>
                    <input type="text" name="item[]" class="form-control" value="${item.item_name}" readonly>
                </div>

                <div class="col-sm-4 form-group">
                    <label for="vendor_part_number">Vendor Part Number</label>
                    <select name="vendor_part_number[]" class="form-control">
                        <option value="${item.vendor_part_number}" selected>${item.vendor_part_number}</option>
                    </select>
                </div>

                <div class="col-sm-4 form-group">
                    <label for="manufacturer_name">Manufacturer Name</label>
                    <select name="manufacturer_name[]" class="form-control">
                        <option value="${item.manufacturer_name}" selected>${item.manufacturer_name}</option>
                    </select>
                </div>

                <div class="col-sm-4 form-group">
                    <label for="manufacturer_drawing_number">Manufacturer Drawing Number</label>
                    <select name="manufacturer_drawing_number[]" class="form-control">
                        <option value="${item.manufacturer_drawing_number}" selected>${item.manufacturer_drawing_number}</option>
                    </select>
                </div>

                <div class="col-sm-4 form-group">
                    <label for="item_type">Component Type</label>
                    <select name="item_type[]" class="form-control item-type-calibration">
                        <option value="${item.item_type_id}" selected>${item.item_type_name}</option>
                    </select>
                </div>

                <div class="col-sm-4 form-group">
                    <label for="item_picture">Component Picture</label>
                    <input type="file" name="item_picture[]" class="form-control" />
                </div>

                <div class="col-sm-4 form-group">
                    <label for="faulty_type_item">Faulty Type</label>
                    <select name="faulty_type_item[]" class="form-control">
                        <option value="${item.faulty_type_id}" selected>${item.faulty_type}</option>
                    </select>
                </div>

                <div class="col-sm-4 form-group">
                    <label for="calibration_date_item">1st Calibration Date</label>
                    <input type="date" name="calibration_date_item[]" class="form-control" value="${item.calibration_date}" />
                </div>

                <div class="col-sm-4 form-group">
                    <label for="frequency_day_item">Frequency In Days</label>
                    <input type="text" name="frequency_day_item[]" class="form-control" value="${item.frequency_day}" />
                </div>

                <div class="col-sm-4 form-group">
                    <label for="reminder_day_item">Reminder In Days</label>
                    <input type="text" name="reminder_day_item[]" class="form-control" value="${item.reminder_day}" />
                </div>

                 <div class="col-sm-4 form-group">
                    <label for="maintenance_date_item">Maintenance Date</label>
                    <input type="date" name="maintenance_date_item[]" class="form-control" value="${item.maintenance_date}" />
                </div>

                <div class="col-sm-4 form-group">
                    <label for="frequency_year_item">Frequency In Years</label>
                    <input type="text" name="frequency_year_item[]" class="form-control" value="${item.frequency_year}" />
                </div>

                <div class="col-sm-4 form-group">
                    <label for="maintenance_reminder_day_item">Reminder In Days</label>
                    <input type="text" name="maintenance_reminder_day_item[]" class="form-control" value="${item.maintenance_reminder_day}" />
                </div>
            </div>
        </div>
    `;

	// Append the new row to the item container
	$("#itemContainer").append(newRow);
}

var originalItemSection = document.querySelector(".itemSection");

function appendItemRow(item) {
	var newItemSection = originalItemSection.cloneNode(true);

	// Clear input fields in the cloned section
	newItemSection
		.querySelectorAll("input")
		.forEach((input) => (input.value = ""));

	// Reset select dropdowns
	newItemSection
		.querySelectorAll("select")
		.forEach((select) => (select.selectedIndex = 0));

	// Ensure #calibration_asset_item is unchecked by default

	// Remove any existing remove buttons from cloned section
	newItemSection
		.querySelectorAll(".removeItemButton")
		.forEach((button) => button.remove());
	const calibrationDate = newItemSection.querySelector(
		"#calibration_date_item"
	);
	const frequencyDay = newItemSection.querySelector("#frequency_day_item");
	const reminderDay = newItemSection.querySelector("#reminder_day_item");
	const maintenanceDate = newItemSection.querySelector(
		"#maintenance_date_item"
	);
	const frequencyYear = newItemSection.querySelector("#frequency_year_item");
	const maintenanceReminderDay = newItemSection.querySelector(
		"#maintenance_reminder_day_item"
	);
	const itemType = newItemSection.querySelector("#item_type");
	const menufacturer = newItemSection.querySelector("#menufacturer_item");
	const vendorPartNumber = newItemSection.querySelector("#part_number_item");
	if (item.calibration != 1) {
		calibrationDate.style.display = "none";
		frequencyDay.style.display = "none";
		reminderDay.style.display = "none";
	} else {
		calibrationDate.style.display = "block";
		frequencyDay.style.display = "block";
		reminderDay.style.display = "block";
	}

	if (item.maintenance != 1) {
		maintenanceDate.style.display = "none";
		frequencyYear.style.display = "none";
		maintenanceReminderDay.style.display = "none";
	} else {
		maintenanceDate.style.display = "block";
		frequencyYear.style.display = "block";
		maintenanceReminderDay.style.display = "block";
	}
	itemType.value = item.item_type_id;
	menufacturer.value = item.manufacturer;
	vendorPartNumber.value = item.vendor_part_number;

	// Append the cloned item section to the container

	console.log(newItemSection);
	itemContainer.appendChild(newItemSection);

	// Add remove button for cloned item section
	var removeButton = document.createElement("button");
	removeButton.type = "button";
	removeButton.classList.add(
		"btn",
		"btn-danger",
		"removeItemButton",
		"form-group"
	);
	removeButton.textContent = "X";
	newItemSection.querySelector(".modal-body").appendChild(removeButton);

	// Remove the section when the "X" button is clicked
	removeButton.addEventListener("click", function () {
		newItemSection.remove();
	});

	// Toggle faulty type field visibility based on checkbox
	var faultyCheckbox = newItemSection.querySelector("#faulty_type_toggle_item");
	faultyCheckbox.addEventListener("change", function () {
		var faultyTypeField = newItemSection.querySelector(
			"#faulty_type_field_item"
		);
		faultyTypeField.style.display = faultyCheckbox.checked ? "block" : "none";
	});
}



// Item calibration
$(document).on("change", ".item-type-calibration", function () {
	const assetId = $(this).val();

	const calibrationDate = $(this)
		.closest(".itemSection")
		.find("#calibration_date_item");
	const frequencyDay = $(this)
		.closest(".itemSection")
		.find("#frequency_day_item");
	const reminderDay = $(this)
		.closest(".itemSection")
		.find("#reminder_day_item");

	const maintenanceDate = $(this)
		.closest(".itemSection")
		.find("#maintenance_date_item");
	const frequencyYear = $(this)
		.closest(".itemSection")
		.find("#frequency_year_item");
	const maintenanceReminderDay = $(this)
		.closest(".itemSection")
		.find("#maintenance_reminder_day_item");

	if (assetId) {
		// Make an AJAX call to check if calibration is required
		$.ajax({
			// url: "/item_type/item_calibration",
			url: amsUrl("/item_type/item_calibration"),
			method: "POST",
			data: { asset_id: assetId },
			success: function (response) {
				if (response.calibration == 1) {
					calibrationDate.show();
					frequencyDay.show();
					reminderDay.show();
				} else {
					calibrationDate.hide().val("");
					frequencyDay.hide().val("");
					reminderDay.hide().val("");
				}

				if (response.maintenance == 1) {
					maintenanceDate.show();
					frequencyYear.show();
					maintenanceReminderDay.show();
				} else {
					maintenanceDate.hide().val("");
					frequencyYear.hide().val("");
					maintenanceReminderDay.hide().val("");
				}
			},
		});
	} else {
		calibrationDate.hide().val("");
		frequencyDay.hide().val("");
		reminderDay.hide().val("");
		maintenanceDate.hide().val("");
		frequencyYear.hide().val("");
		maintenanceReminderDay.hide().val("");
	}
});

// Asset Calibration edit
$(document).on("change", "#equipment_type_calibration_edit", function () {
	const assetId = $(this).val();
	// This will now display the selected asset ID

	if (assetId) {
		// Make an AJAX call to check if calibration is required
		$.ajax({
			// url: "/assettypes/asset_calibration", // PHP file to handle the check
			url: amsUrl("/assettypes/asset_calibration"),
			method: "POST",
			data: { asset_id: assetId },
			success: function (response) {
				console.log(response);
				// Parse response (assuming response is JSON with 'calibration' field)

				if (response.calibration == 1) {
					// Show calibration fields if calibration is required
					$("#calibration_date").show();
					$("#frequency_day").show();
					$("#reminder_day").show();
				} else {
					// Hide calibration fields if not required
					$("#calibration_date").hide().val("");
					$("#frequency_day").hide().val("");
					$("#reminder_day").hide().val("");
				}

				if (response.maintenance == 1) {
					// Show calibration fields if calibration is required

					$("#maintenance_date").show();
					$("#frequency_year").show();
					$("#maintenance_reminder_day").show();
				} else {
					// Hide calibration fields if not required

					$("#maintenance_date").hide().val("");
					$("#frequency_year").hide().val("");
					$("#maintenance_reminder_day").hide().val("");
				}
			},
		});
	} else {
		// Hide calibration fields if no type is selected
		$("#calibration_date").hide().val("");
		$("#frequency_day").hide().val("");
		$("#reminder_day").hide().val("");
		$("#maintenance_date").hide().val("");
		$("#frequency_year").hide().val("");
		$("#maintenance_reminder_day").hide().val("");
	}
});

$(document).on("change", ".item_type_calibration_edit", function () {
	const assetId = $(this).val();

	const calibrationDate = $(this)
		.closest(".item-section")
		.find("#calibration_date_item_edit");
	const frequencyDay = $(this)
		.closest(".item-section")
		.find("#frequency_day_item_edit");
	const reminderDay = $(this)
		.closest(".item-section")
		.find("#reminder_day_item_edit");

	const maintenanceDate = $(this)
		.closest(".item-section")
		.find("#maintenance_date_item_edit");
	const frequencyYear = $(this)
		.closest(".item-section")
		.find("#frequency_year_item_edit");
	const maintenanceReminderDay = $(this)
		.closest(".item-section")
		.find("#maintenance_reminder_day_item_edit");

	if (assetId) {
		// Make an AJAX call to check if calibration is required
		$.ajax({
			// url: "/item_type/item_calibration",
			url: amsUrl("/item_type/item_calibration"),
			method: "POST",
			data: { asset_id: assetId },
			success: function (response) {
				if (response.calibration == 1) {
					calibrationDate.show();
					frequencyDay.show();
					reminderDay.show();
				} else {
					calibrationDate.hide().val("");
					frequencyDay.hide().val("");
					reminderDay.hide().val("");
				}

				if (response.maintenance == 1) {
					maintenanceDate.show();
					frequencyYear.show();
					maintenanceReminderDay.show();
				} else {
					maintenanceDate.hide().val("");
					frequencyYear.hide().val("");
					maintenanceReminderDay.hide().val("");
				}
			},
		});
	} else {
		calibrationDate.hide().val("");
		frequencyDay.hide().val("");
		reminderDay.hide().val("");
		maintenanceDate.hide().val("");
		frequencyYear.hide().val("");
		maintenanceReminderDay.hide().val("");
	}
});

$(document).ready(function () {
	// Copy Equipment Status to Item Status
	$(document).on("change", "select[name='equipment_status']", function () {
		let selectedValue = $(this).val(); // Get selected value from equipment_status

		$("select[name='item_status[]']").each(function () {
			let found = false;
			$(this).find("option").each(function () {
				if ($(this).text().trim() === selectedValue.trim()) { // Match by text
					$(this).prop("selected", true);
					found = true;
				}
			});

			if (!found) {
				$(this).val("0"); // Default value
			}
		});
	});

	// Copy Store Location to Store Location Item (FIXED)
	$(document).on("change", "select[name='store_location']", function () {
		let selectedValue = $(this).val(); // Get the selected value (ID) from store_location

		$("select[name='store_location_item[]']").each(function () {
			let found = false;

			$(this).find("option").each(function () {
				if ($(this).val() === selectedValue) { // Match by value (ID)
					$(this).prop("selected", true);
					found = true;
				}
			});

			if (!found) {
				$(this).val("0"); // Default value if no match found
			}
		});
	});

	// Copy maintenance date to maintenance date item
	$(document).on("change", "input[name='maintenance_date']", function () {
		let selectedValue = $(this).val(); // Get the selected date value

		$("input[name='maintenance_date_item[]']").each(function () {
			$(this).val(selectedValue); // Directly set the value since it's an input field
		});
	});


	// Copy Frequency Year to Frequency Year item
	$(document).on("change", "input[name='frequency_year']", function () {
		let selectedValue = $(this).val(); // Get the selected date value

		$("input[name='frequency_year_item[]']").each(function () {
			$(this).val(selectedValue); // Directly set the value since it's an input field
		});
	});


	// Copy Frequency Year to Frequency Year item
	$(document).on("change", "input[name='maintenance_reminder_day']", function () {
		let selectedValue = $(this).val(); // Get the selected date value

		$("input[name='maintenance_reminder_day_item[]']").each(function () {
			$(this).val(selectedValue); // Directly set the value since it's an input field
		});
	});

	// Function to copy values to dynamically added rows
	function copyValuesToNewRow() {
		let equipmentStatus = $("select[name='equipment_status']").val();
		let storeLocation = $("select[name='store_location']").val();
		let maintenanceDate = $("input[name='maintenance_date']").val();
		let frequencyYear = $("input[name='frequency_year']").val();
		let maintenanceReminderDay = $("input[name='maintenance_reminder_day']").val();

		$(".item-status").last().val(equipmentStatus);
		$(".store-location-item").last().val(storeLocation);
		$(".maintenance-date-item").last().val(maintenanceDate);
		$(".frequency-year").last().val(frequencyYear);
		$(".maintenance-reminder-day").last().val(maintenanceReminderDay);
	}

	// Ensure new rows get updated values when items are added dynamically
	$(document).on("change", "select[name='equipment_status'], select[name='store_location'], input[name='maintenance_date'], input[name='frequency_year'], input[name='maintenance_reminder_day']", function () {
		copyValuesToNewRow();
	});

	$(document).ready(function () {
		$('.maintenance-type').on('change', function () {
			var selectedType = $(this).val();
			var $finalStatus = $('select[name="final_status"]');

			// Show the "in_progress" option by default
			$finalStatus.find('option[value="in_progress"]').show();

			// Hide "in_progress" only if "preventive" is selected
			if (selectedType === 'preventive') {
				$finalStatus.find('option[value="in_progress"]').hide();

				// If "in_progress" is already selected, reset selection
				if ($finalStatus.val() === 'in_progress') {
					$finalStatus.val('');
				}
			}
		});
	});

	$('#ticket_logs').DataTable({
            "pageLength": 5,  // Number of rows per page
            "lengthMenu": [5, 10, 25, 50],
            "order": [[1, "desc"]], // Order by issue date descending
            "language": {
                "search": "Search:",
                "emptyTable": "No ticket logs available"
            }
        });
});


document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const purchaseDateInput = document.getElementById('purchase_date');
    if (purchaseDateInput) {
        purchaseDateInput.max = today;
    }
    
    // For edit modal
    const purchaseDateEdit = document.querySelector('#addModal #purchase_date');
    if (purchaseDateEdit) {
        purchaseDateEdit.max = today;
    }
});

function deleteInvoice(equipmentId, invoiceFileName) {
    console.log("Delete clicked - ID:", equipmentId, "File:", invoiceFileName);
    
    if (confirm("Are you sure you want to delete this invoice?")) {
        const invoiceCard = document.querySelector('.invoice-card .card-body');
        if (invoiceCard) {
            const originalContent = invoiceCard.innerHTML;
            invoiceCard.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Deleting...</p></div>';
            
            // Hardcoded URL (adjust according to your setup)
            const url = window.location.origin + '/assets/delete_invoice_simple';
            console.log("Hardcoded URL:", url);
            
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    id: equipmentId,
                    invoice_file: invoiceFileName,
                    csrf_test_name: $('input[name="csrf_test_name"]').val()
                },
                dataType: 'json',
                success: function(response) {
                    console.log("Response:", response);
                    
                    if (response.success) {
                        // Simple reload
                        window.location.reload();
                    } else {
                        invoiceCard.innerHTML = originalContent;
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    
                    // Try alternative URL
                    const altUrl = window.location.origin + '/index.php/assets/delete_invoice_simple';
                    console.log("Trying alternative URL:", altUrl);
                    
                    // Retry with alternative URL
                    $.ajax({
                        url: altUrl,
                        type: 'POST',
                        data: {
                            id: equipmentId,
                            invoice_file: invoiceFileName
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log("Alt Response:", response);
                            window.location.reload();
                        },
                        error: function() {
                            invoiceCard.innerHTML = originalContent;
                            alert('Failed to delete. Please refresh and try again.');
                        }
                    });
                }
            });
        }
    }


	// Excel file upload handling
$('#excel-file-upload').on('change', function() {
    var filePath = $(this).val();
    var filename = filePath.split(/[\\\/]/).pop();
    var allowedExtensions = /(\.xlsx|\.xls|\.csv)$/i;
    
    if (!allowedExtensions.exec(filePath)) {
        alert("Invalid file type. Please select .xlsx, .xls, or .csv file");
        $(this).val("");
        return false;
    }
    
    // Update label
    $(this).next('.custom-file-label').text(filename);
});


// Excel file upload handling
$(document).ready(function() {
    $('#excel-file-upload').on('change', function() {
        var filename = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').text(filename);
    });

    $('#excel-upload-form').on('submit', function(e) {
        var fileInput = $('#excel-file-upload')[0];
        
        if (fileInput.files.length === 0) {
            alert('Please select an Excel file first');
            e.preventDefault();
            return false;
        }
        
        // Show loading
        $('#upload-excel-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
    });
});
	
}


