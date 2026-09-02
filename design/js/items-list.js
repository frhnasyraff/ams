$(document).ready(function () {

    if ($(".dropzone").length) {
        $(".dropzone").dropzone({
            acceptedFiles: "image/*",
            uploadMultiple: false,
            maxFiles: 1,
            queuecomplete: function (e) {
                window.location.reload();
            }
        });
    }

    assets = $('#assets').DataTable({
        "processing": true,
        "responsive": false,
        "autoWidth": false,
        "pageLength": 10,
        "stateSave": true,
        "ajax": {
            "url": "/items/ajax_list",
            "type": "POST",
            data: function (d) {
                return $.extend({}, d, {
                    item_type_filter: $(".item_type_filter .btn-primary").data("filter"),
                    item_group_filter: $(".item_group_filter .btn-primary").data("filter")
                });
            },
            "error": function (xhr, error, thrown) {
                const response = xhr.responseJSON;
                if (response && response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    alert("We are having trouble connecting to the API.");
                }
            }
        },
        "drawCallback": initToggle,
        "order": [[1, "desc"]],
        "columns": [
            {
                "data": "item_name",
                createdCell: function (td, cellData, rowData) {
                    if (!$("table.read-only").length) {
                        $(td).html('<a class="text_light_blue_color" href="/items/info?id=' + rowData.id + '" title="View equipment">' + ((cellData == null) ? "" : cellData) + '</a>');
                    }
                }
            },
            { "data": "vendor_part_number" },
            {
                "data": "manufacturer_name",
                createdCell: function (td, cellData, rowData) {
                    if (!$("table.read-only").length) {
                        $(td).html('<a class="text_light_blue_color" href="/items/info?id=' + rowData.id + '" title="View equipment">' + ((cellData == null) ? "" : cellData) + '</a>');
                    }
                }
            },
            // { "data": "manufacturer_part_number" },
            // { "data": "manufacturer_drawing_number" },
            {
                data: null,
                orderable: false,
                createdCell: function (td, cellData, rowData, row, col) {
                    if (cellData) {
                        $(td)
                            .addClass("p-0 m-0 text-center")
                            .html(
                                '<div class="btn view-asset-type-list" data-id="' +
                                rowData.asset_id +
                                '">' +
                                '<i class="fas fa-eye"></i><span>View Asset</span>' +
                                "</div>"
                            );
                    }
                },
            },
            { "data": "location_name" },

            {
                data: "item_status_name",
                createdCell: function (td, cellData, rowData) {
                    if (cellData) {
                        var customClass = "light-blue";
                        var status = rowData.item_status_name.replace(/\s+/g, "");
                        if (status == "INUSE") customClass = "light-blue";
                        else if (status == "MAINTENANCE") customClass = "warn";
                        else if (status == "Available") customClass = "success";
                        else if (status == "Repair") customClass = "dark-blue";
                        else if (status == "Dispose") customClass = "green";
                        else if (status == "Scrap") customClass = "purple";
                        else if (status == "SERVICEABLE") customClass = "green";
                        else if (status == "UNSERVICEABLE") customClass = "red";

                        $(td).html(`<span class='custom-badge ${customClass}'>${rowData.item_status_name}</span>`);
                    }
                }
            },
            {
                data: null,
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html(
                        '<a href="/items/deleteItem?id=' + rowData.id +
                        '" onclick="return confirm(\'Are you sure you want to delete this item?\');" class="btn btn-danger" title="Delete item">' +
                        '<i class="fa fa-trash trash-icon"></i><span>Delete</span></a>'
                    );
                }
            }
        ]
    });



    $(document).ready(function () {
        // AJAX request to fetch counts
        $.ajax({
            url: "/items/ajax_list", // Ensure this matches the correct URL route
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data && data.counts) {
                    // Update counts in the HTML
                    $("#totalAssets").text(data.counts.total_items || 0);
                    $("#totalItemsInUse").text(data.counts.ServiceableItemCount || 0);
                    $("#faultyItemCount").text(data.counts.UnServiceableItemCount || 0);
                    $("#totalAssetsInMaintenance").text(data.counts.MaintinenceItemCount || 0);
                    $("#storelocationItemCount").text(data.counts.storelocationItemCount || 0);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching counts:", status, error);
            }
        });
    });

    $('#assets').on('change', 'input[type="checkbox"][data-toggle="toggle"]', function () {
        console.log('Checkbox changed');
        // Retrieve row data
        var rowData = assets.row($(this).closest('tr')).data();
        // Determine new status based on checkbox state
        var newStatus = ($(this).is(':checked')) ? 'Available' : 'Dispose';
        // Update the status value in the rowData
        rowData.equipment_status = newStatus;
        // Reference to the checkbox element
        var checkbox = $(this);

        $.ajax({
            url: "/items/update_asset",
            type: "POST",
            data: {
                id: rowData.equipment_id,
                name: rowData.equipment_name,
                code: rowData.equipment_registration,
                equipment_type: rowData.equipment_type,
                equipment_manufacturer: rowData.equipment_manufacturer,
                purchase_date: rowData.purchase_date,
                equipment_status: newStatus,
                branch_office_id: rowData.branch_office_id,
                ownership: rowData.ownership,
                notes: rowData.equipment_notes,
                safe_load: rowData.equipment_safe_load
            },
            success: function (response) {
                console.log('Row Data:', rowData);
                // Update the status displayed in the table
                var statusCell = checkbox.closest('tr').find('td:eq(5)'); // equipment_status is in the 6th column
                statusCell.html(`<span class='custom-badge ${getCustomClass(newStatus)}'>${newStatus}<span>`);
            },
            error: function (xhr, status, error) {
                console.error("Status:", status);
                console.error("Error:", error);
                console.error("Response:", xhr.responseText);
            }
        });
    });

    // Function to get custom class based on status
    function getCustomClass(status) {
        switch (status) {
            case 'Available':
                return 'success';
            case 'Dispose':
                return 'green';
            // Add cases for other statuses as needed
            default:
                return '';
        }
    }


    $("#assets").on("click", ".add_mileage", function (e) {
        e.preventDefault();
        var that = $(this);
        $("#addMileageModal .equipment_registration").html(that.data("registration"));
        $("#addMileageModal #form_mileage").val(that.data("mileage"));
        $("#addMileageModal .equipment_id").val(that.data("id"));
        $("#addMileageModal").modal("show");
    });

    $("#assets").on("click", ".add_maintenance", function (e) {
        e.preventDefault();
        var that = $(this);
        $("#addScheduledMaintenanceModal .equipment_registration").html(that.data("registration"));
        $("#addScheduledMaintenanceModal #form_mileage").val(that.data("mileage"));
        $("#addScheduledMaintenanceModal .equipment_id").val(that.data("id"));
        $("#addScheduledMaintenanceModal").modal("show");
    });

    $('input.date_picker, div.date_picker input').datepicker({
        dateFormat: "dd/mm/yyyy",
        timepicker: false
    });

    $('input.date_picker_now, div.date_picker_now input').datepicker({
        dateFormat: "dd/mm/yyyy",
        timepicker: false,
        maxDate: new Date(),
    });


    $("#form_equipment_type").select2();

    if ($("#groups").length) {
        $("#groups").multiSelect({
            selectableHeader: "Group(s) available",
            selectionHeader: "Assigned to group(s)"
        });
    }

    $('#equipment_mileage').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/assets/mileage_ajax_list",
            "type": "POST",
            "error": function (xhr, error, thrown) {
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                } else {
                    alert("We are having trouble connecting to the API.");
                }
            },
            data: function (d) {
                return $.extend({}, d, {
                    id: $('input[name="id"]').val()
                });
            },
        },
        drawCallback: initToggle,
        "order": [
            [0, "desc"]
        ],
        "columns": [{
            "data": "date_recorded"
        },
        {
            "data": "mileage"
        }
        ]
    });

    $('#equipment_consumable').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/assets/consumable_ajax_list",
            "type": "POST",
            "error": function (xhr, error, thrown) {
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                } else {
                    alert("We are having trouble connecting to the API.");
                }
            },
            data: function (d) {
                return $.extend({}, d, {
                    id: $('input[name="id"]').val()
                });
            },
        },
        drawCallback: initToggle,
        "order": [
            [0, "desc"]
        ],
        "columns": [{
            "data": "date_recorded"
        },
        {
            "data": "consumable_name",
            createdCell: function (td, cellData, rowData, row, col) {
                $(td).html('<a href="/consumables/info?id=' + id_encode(rowData.consumable_id) + '" title="View consumable info">' + cellData + '</a>');
            }
        },
        {
            "data": "quantity"
        }
        ]
    });


    $('#equipment_usage').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/assets/usage_ajax_list",
            "type": "POST",
            "error": function (xhr, error, thrown) {
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                } else {
                    alert("We are having trouble connecting to the API.");
                }
            },
            data: function (d) {
                console.log(d)
                return $.extend({}, d, {
                    id: $('input[name="id"]').val()
                });
            },
        },
        drawCallback: initToggle,
        "order": [
            [0, "desc"]
        ],
        "columns": [

            {
                "data": "vh_date"
            },
            {
                "data": "vh_date_end"
            },
            {
                "data": "vh_time_start"
            },
            {
                "data": "vh_time_end"
            },
            {
                "data": "vh_location_start"
            },
            {
                "data": "vh_location_end"
            },
            {
                "data": "worker_name"
            },
            {
                "data": "vh_driver_name_ic_number"
            }
        ]
    });

    // new maintenance recordd

    $('#equipment_new_maintenance').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/assets/new_maintenance_ajax_list",
            "type": "POST",
            "error": function (xhr, error, thrown) {
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                } else {
                    alert("We are having trouble connecting to the API.");
                }
            },
            data: function (d) {

                return $.extend({}, d, {
                    id: $('input[name="id"]').val()
                });
            },
        },
        drawCallback: initToggle,
        "order": [
            [0, "desc"]
        ],
        "columns": [{
            "data": "maintenance_date"
        },
        {
            "data": "in_out"
        },
        {
            "data": "maintenance_notes"
        },
        {
            "data": "maintenance_cost"
        }
        ]
    });



    $('#equipment_maintenance').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/assets/maintenance_ajax_list",
            "type": "POST",
            "error": function (xhr, error, thrown) {
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                } else {
                    alert("We are having trouble connecting to the API.");
                }
            },
            data: function (d) {
                return $.extend({}, d, {
                    id: $('input[name="id"]').val()
                });
            },
        },
        drawCallback: initToggle,
        "order": [
            [0, "desc"]
        ],
        "columns": [{
            data: "maintenance_date",
            createdCell: function (td, cellData, rowData, row, col) {
                $(td).html('<a href="/assets/maintenance?id=' + id_encode(rowData.equipment_maintenance_id) + '" title="View maintenance info">' + cellData + '</a>');
            }
        },
        {
            "data": "in_out"
        },
        {
            "data": "maintenance_mileage"
        }
        ]
    });


    $("#csv-file-upload").on("change", function () {
        var filePath = $(this).val();
        var filename = filePath.split(/[\\\/]/).pop();
        var allowedExtensions = /(\.csv)$/i; //  /(\.csv|\.jpeg|\.png|\.gif)$/i
        if (!allowedExtensions.exec(filePath)) {
            alert('Invalid file type');
            $(this).val('');
            return false;
        }
        $("#csv-file-upload + label").text(filename);
        $("#csv-upload-form").submit();
    });



    $('input[name="service_interval_weeks"]').change(function () {
        if ($('input[name="last_service_date"]').val()) {
            $('input[name="next_service_date"]').val(moment($('input[name="last_service_date"]').val(), "DD/MM/YYYY").add($('input[name="service_interval_weeks"]').val(), "weeks").format("DD/MM/YYYY"));
        }
    });

    $(".item_type_filter .btn").click(function () {
        $(".item_type_filter .btn").removeAttr("disabled").removeClass("btn-primary active");
        $(this).addClass("btn-primary").attr("disabled", "disabled");
        assets.ajax.reload();
    });

    $(".item_group_filter .btn").click(function () {
        $(".item_group_filter .btn").removeAttr("disabled").removeClass("btn-primary active");
        $(this).addClass("btn-primary").attr("disabled", "disabled");
        assets.ajax.reload();
    });
    $(document).on('change', '#status', function (e) { //on add input button click
        e.preventDefault();
        if ($('#status').val() === 'sold') {
            $('#purchased_by_container').show();
            $('#purchase_price_container').show();
        } else {
            $('#purchased_by_container').hide();
            $('#purchase_price_container').hide();
        }
    });


    // for Edit location state


    // for Add 
    $('#stateSelect').on('change', function () {
        var stateId = $(this).val();

        // Clear the location select options
        $('#locationSelect').html('<option value="">--Select--</option>');

        if (stateId) {

            // Fetch locations based on selected state
            $.ajax({
                url: '/assets/locationDropdown', // Replace with your correct endpoint URL
                method: 'POST',
                data: { state_id: stateId },
                dataType: 'json',
                success: function (response) {
                    if (response.locations.length > 0) {
                        // Populate the location dropdown
                        $.each(response.locations, function (index, location) {
                            $('#locationSelect').append('<option value="' + location.id + '">' + location.name + '</option>');
                        });
                    } else {
                        $('#locationSelect').append('<option value="">No locations available</option>');
                    }
                },
                error: function () {
                    alert('Error fetching locations');
                }
            });
        }
    });

    // check box code 

    $("#faulty_type_field").hide();

    // Toggle visibility based on checkbox
    $("#faulty_type_toggle").change(function () {
        if ($(this).is(':checked')) {
            $("#faulty_type_field").show();
        } else {
            $("#faulty_type_field").hide();
        }
    });

    // check box for item

    $("#faulty_type_field_item").hide();

    // Toggle visibility based on checkbox
    $("#faulty_type_toggle_item").change(function () {
        if ($(this).is(':checked')) {
            $("#faulty_type_field_item").show();
        } else {
            $("#faulty_type_field_item").hide();
        }
    });

    // check box for New item

    $("#faulty_type_new").hide();

    // Toggle visibility based on checkbox
    $("#faulty_type_toggle_new").change(function () {
        if ($(this).is(':checked')) {
            $("#faulty_type_new").show();
        } else {
            $("#faulty_type_new").hide();
        }
    });

    // edit items

    // Attach event handler to all checkboxes with the class "edit_faulty_type_toggle_item"
    $(".edit_faulty_type_toggle_item").each(function () {
        var key = $(this).data("key"); // Get the unique key for the current checkbox
        alert();
        // Initially hide the faulty type field
        $(".edit_faulty_type_field_item_" + key).hide();

        // Toggle visibility based on the checkbox change event
        $(this).change(function () {
            if ($(this).is(':checked')) {
                $(".edit_faulty_type_field_item_" + key).show();
            } else {
                $(".edit_faulty_type_field_item_" + key).hide();
            }
        });
    });


});
var assets;
if (typeof Dropzone != 'undefined' && Dropzone) {
    Dropzone.autoDiscover = false;
}



$("#select_all_checkboxes").on("click", function () {
    let allSelect = $(this).text();
    $('.checkbox-equipment-id').each(function () {
        if (allSelect == 'Select All') {
            $(this).prop('checked', true);
        } else {
            $(this).prop('checked', false);
        }
    });
    $(this).text(function (i, text) {
        return text === "Select All" ? "Un Select All" : "Select All";
    })
});


// Handle the click event for the eye icon
$(document).on('click', '.view-list', function () {
    // Prevent the default action of the event (e.g., form submission)

    var equipmentId = $(this).data('id');

    // Send AJAX request to fetch data based on equipment ID
    $.ajax({
        url: '/assets/itemList', // Replace with your route
        method: 'GET',
        data: { id: equipmentId },
        success: function (response) {
            console.log('AJAX Response:', response); // Log the response
            $('#equipmentModal').modal('show');
            var modalContent = '';

            // Check if response is an array
            if (Array.isArray(response) && response.length > 0) {
                response.forEach(function (item) {
                    modalContent += '<div class="item">';
                    modalContent += '<p><input type="text" class="form-control" value="' + item.item_name + '" readonly></p>';
                    modalContent += '<p>Vendor Part Number: ' + item.vendor_part_number + '</p>';
                    modalContent += '<p>Manufacturer: ' + item.manufacturer_name + '</p>';
                    modalContent += '<p>Manufacturer Part Number: ' + item.manufacturer_part_number + '</p>';
                    modalContent += '<p>Manufacturer Drawing Number: ' + item.manufacturer_drawing_number + '</p>';
                    modalContent += '<hr>'; // Add a separator
                    modalContent += '</div>';
                });
            } else if (response) {
                modalContent += '<div class="item">';
                modalContent += '<p><input type="text" class="form-control" value="' + item.item_name + '" readonly></p>';
                modalContent += '<p>Vendor Part Number: ' + response.vendor_part_number + '</p>';
                modalContent += '<p>Manufacturer: ' + response.manufacturer_name + '</p>';
                modalContent += '<p>Manufacturer Part Number: ' + response.manufacturer_part_number + '</p>';
                modalContent += '<p>Manufacturer Drawing Number: ' + response.manufacturer_drawing_number + '</p>';
                // modalContent += '<p>QR Code: ' + response.qr_code + '</p>';
                modalContent += '<hr>'; // Add a separator
                modalContent += '</div>';
            } else {
                modalContent = '<p>No items found.</p>';
            }

            // Log the modal content
            console.log('Modal Content:', modalContent); // Log the generated content

            // Inject the modal content into the modal body
            $('#modal-body-content').html(modalContent);

            // Show the modal

        },

        error: function () {
            alert('Error fetching equipment details.');
        }
    });


    $(document).on('click', '.hideEyeModal', function () {
        $('#equipmentModal').modal('hide');
        $('#modal-body-content').html('');
    })

});


// // Handle the click event for the eye icon
$(document).on("click", ".view-asset-type-list", function () {
    // Prevent the default action of the event (e.g., form submission)

    var equipmentId = $(this).data("id");

    // Send AJAX request to fetch data based on equipment ID
    $.ajax({
        url: "/items/assetList", // Replace with your route
        method: "GET",
        data: { id: equipmentId },
        success: function (response) {
            console.log("AJAX Response:", response); // Log the response
            $("#equipmentModal").modal("show");
            var modalContent = "";

            // Check if response is an array
            if (Array.isArray(response) && response.length > 0) {
                response.forEach(function (item) {
                    modalContent += '<div class="item">';
                    modalContent +=
                        '<p><input type="text" class="form-control" value="' +
                        item.asset_name +
                        '" readonly></p>';


                    modalContent += "</div>";
                });
            } else if (response) {
                modalContent += '<div class="item">';
                modalContent +=
                    '<p><input type="text" class="form-control" value="' +
                    item.asset_name +
                    '" readonly></p>';


                modalContent += "</div>";
            } else {
                modalContent = "<p>No items found.</p>";
            }

            // Log the modal content
            console.log("Modal Content:", modalContent); // Log the generated content

            // Inject the modal content into the modal body
            $("#modal-body-content").html(modalContent);

            // Show the modal
        },

        error: function () {
            alert("Error fetching equipment details.");
        },
    });

    $(document).on("click", ".hideEyeModal", function () {
        $("#equipmentModal").modal("hide");
        $("#modal-body-content").html("");
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
