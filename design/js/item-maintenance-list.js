$("#item_maintenance").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    autoWidth: true,
    pageLength: 10,
    stateSave: true,
    ajax: {
        url: "/items/item_maintenance_ajax_list",
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
                id: $('#formA input[name="item_id"]').val(),
            });
        },
    },
    drawCallback: initToggle,
    order: [[0, "desc"]],
    columns: [
        {
            data: "update_date",
            title: "Update Date"
        },
        {
            data: "final_status",
            title: "Final Status"
        },
        {
            data: null,
            title: "Action",
            orderable: false,
            searchable: false,
            createdCell: function (td, cellData, rowData, row, col) {
                if (rowData.id && rowData.item_maintenance_id && rowData.item_id) {
                    $(td).html('<a href="/items/delete?id=' + id_encode(rowData.id) + '&item_maintenance_id=' + id_encode(rowData.item_maintenance_id) + '&item_id=' + id_encode(rowData.item_id) + '" onclick="return confirm(\'Are you sure you want to delete this log?\');" title="Delete Log"><i class="fa fa-trash"></i></a>');
                } else {
                    $(td).html('');
                }
            }
        },
        {
            data: null,
            orderable: false,
            title: "View",
            createdCell: function (td, cellData, rowData, row, col) {
                if (rowData.id) {
                    $(td)
                        .addClass("p-0 m-0 text-center")
                        .html(`
        <button class="btn view-details" 
            data-updated_at="${rowData.updated_at}" 
            data-item_id="${rowData.item_ticket_id}">
            <i class="fas fa-eye"></i>
        </button>
    `);

                } else {
                    $(td).html('');
                }
            }
        }
    ],
});


// Handle the click event for the eye icon for maintenance logs table 
$(document).ready(function () {
    // Prevent duplicate event bindings by using .off("click") before .on("click")
    $(document).off("click", ".view-details").on("click", ".view-details", function () {
        var id = $(this).data("item_id");
        var updated_at = $(this).data("updated_at");

        // Clear the modal content before making a new request
        $("#modal-body-content").html("");

        // Send AJAX request to fetch data based on equipment ID
        $.ajax({
            url: "/items/logDetails", // Update with the correct route
            method: "GET",
            data: { id: id, updated_at: updated_at },
            cache: false, // Prevent AJAX caching
            success: function (response) {
                console.log("AJAX Response:", response); // Debugging

                $("#equipmentModal").modal("show"); // Show the modal
                var modalContent = "";

                // Check if response contains equipment_maintenance data
                if (response.equipment_maintenance) {
                    modalContent += '<div class="row form-entry">';

                    // Update Date
                    modalContent += '<div class="form-group col-sm-6">';
                    modalContent += '<label><strong>Update Date:</strong></label>';
                    modalContent += '<input type="text" class="form-control" value="' + response.equipment_maintenance.update_date + '" readonly>';
                    modalContent += '</div>';

                    // Ticket Number
                    modalContent += '<div class="form-group col-sm-6">';
                    modalContent += '<label><strong>Ticket Number:</strong></label>';
                    modalContent += '<input type="text" class="form-control" value="' + (response.equipment_maintenance.number || 'N/A') + '" readonly>';
                    modalContent += '</div>';

                    // Faulty Type
                    modalContent += '<div class="form-group col-sm-6">';
                    modalContent += '<label><strong>Faulty Type:</strong></label>';
                    modalContent += '<input type="text" class="form-control" value="' + (response.equipment_maintenance.fault_type || 'N/A') + '" readonly>';
                    modalContent += '</div>';

                    // Final Status
                    modalContent += '<div class="form-group col-sm-6">';
                    modalContent += '<label><strong>Final Status:</strong></label>';
                    modalContent += '<input type="text" class="form-control" value="' + response.equipment_maintenance.final_status + '" readonly>';
                    modalContent += '</div>';

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

                        modalContent += "</div>"; // Close form-entry div for task
                    });

                    modalContent += "</div>"; // Close tasks div
                } else {
                    modalContent += "<p>No maintenance tasks found.</p>";
                }

                // Inject the modal content into the modal body
                $("#modal-body-content").html(modalContent);
            },
            error: function () {
                alert("Error fetching equipment details.");
            }
        });
    });

    // Close modal event
    $(document).on("click", ".hideEyeModal", function () {
        $("#equipmentModal").modal("hide");
        setTimeout(function () {
            $("#modal-body-content").html(""); // Ensure modal content is cleared after closing
        }, 500); // Delay clearing for smooth transition
    });
});


