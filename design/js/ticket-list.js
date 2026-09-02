$(document).ready(function () {
    $(document).ready(function() {
        $('#ticket_list').DataTable({
          "processing": true,
          "serverSide": true,
          "responsive": true,
          "autoWidth": true,
          "pageLength": 10,
          stateSave: true,
          "ajax": {
            "url": "/ticket/ajax_list",
            "type": "POST",
            "error": function(xhr, error, thrown) {
              if (xhr.responseJSON && xhr.responseJSON.redirect) {
                window.location.href = xhr.responseJSON.redirect;
              } else {
                alert("We are having trouble connecting to the API.");
              }
            }
          },
          "drawCallback": initToggle,
          "order": [
            [1, "asc"]
          ],
          "columns": [
            {
              "data": "ticket_number",
              createdCell: function(td, cellData, rowData, row, col) {
                $(td).text(cellData ? cellData.toUpperCase() : ''); // Apply uppercase
                if (!$("table.read-only").length) {
                  $(td).html('<a href="/ticket/info?id=' + id_encode(rowData.id) + '" title="View equipment group">' + (cellData ? cellData.toUpperCase() : '') + '</a>'); // Apply uppercase in link
                }
              }
            },
            {
              "data": "issue_date",
              createdCell: function(td, cellData, rowData, row, col) {
                $(td).text(cellData ? cellData.toUpperCase() : ''); // Apply uppercase
              }
            },
            {
              "data": "equipment_name",
              createdCell: function(td, cellData, rowData, row, col) {
                $(td).text(cellData ? cellData.toUpperCase() : ''); // Apply uppercase
                if (!$("table.read-only").length) {
                  $(td).html(
                    '<a style="color: #78261f;" href="/assets/info?id=' +
                    id_encode(rowData.equipment_id) +
                    '#nav-new-maintenance" title="View equipment Maintenance">' +
                    (cellData ? cellData.toUpperCase() : '') + // Apply uppercase in link
                    "</a>"
                  );
                }
              }
            },
            {
              data: null,
              orderable: false,
              createdCell: function(td, cellData, rowData, row, col) {
                if (cellData) {
                  $(td)
                    .addClass("p-0 m-0 text-center")
                    .html(
                      '<button class="btn view-list" data-id="' +
                      rowData.equipment_id +
                      '">' +
                      '<i class="fas fa-eye"></i>' +
                      "</button>"
                    );
                }
              }
            },
            {
              "data": "fault_type",
              createdCell: function(td, cellData, rowData, row, col) {
                $(td).text(cellData ? cellData.toUpperCase() : ''); // Apply uppercase
              }
            },
            {
              "data": "ticket_location",
              createdCell: function(td, cellData, rowData, row, col) {
                $(td).text(cellData ? cellData.toUpperCase() : ''); // Apply uppercase
              }
            },
            {
              "data": "ticket_state",
              createdCell: function(td, cellData, rowData, row, col) {
                $(td).text(cellData ? cellData.toUpperCase() : ''); // Apply uppercase
              }
            },
            {
              "data": "details_of_issue",
              createdCell: function(td, cellData, rowData, row, col) {
                if (typeof cellData === 'string') {
                  $(td).text(cellData.toUpperCase());
                } else if (cellData !== null && cellData !== undefined) {
                  $(td).text(String(cellData).toUpperCase()); // Try converting to string first
                } else {
                  $(td).text(''); // Handle null or undefined
                }
              }
            },
            {
              "data": "severity",
              createdCell: function(td, cellData, rowData, row, col) {
                $(td).text(cellData ? cellData.toUpperCase() : ''); // Apply uppercase
              }
            },
            {
              "data": "date_of_completion",
              createdCell: function(td, cellData, rowData, row, col) {
                $(td).text(cellData ? cellData.toUpperCase() : ''); // Apply uppercase
              }
            },
            {
              "data": 'action',
              createdCell: function(td, cellData, rowData, row, col) {
                $(td).html('<a href="/ticket/delete?id=' + id_encode(rowData.id) + '" onclick="return confirm(\'Are you sure you want to delete this Ticket?\');" title="Delete Ticket"><i class="fa fa-trash"></i></a>');
              }
            }
          ]
        });
      });
    $.fn.dataTable.ext.errMode = 'none';
});

// Handle the click event for the eye icon
$(document).on("click", ".view-list", function () {
    // Prevent the default action of the event (e.g., form submission)

    var equipmentId = $(this).data("id");

    // Send AJAX request to fetch data based on equipment ID
    $.ajax({
        url: "/ticket/itemList", // Replace with your route
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
                        '<p><a href="/items/info?id=' + item.item_id + '#nav-new-maintenance" title="View Component" style="color: #80A874;">' +
                        item.number +
                        '</a></p>';

                    modalContent +=
                        "<p>Component Name: " + item.item_name + "</p>";
                    modalContent += "<p>Fault Type: " + item.fault_type + "</p>";


                    modalContent += "<hr>"; // Add a separator
                    modalContent += "</div>";
                });
            } else if (response) {
                modalContent += '<div class="item">';
                modalContent +=
                    modalContent +=
                    '<p><a href="/items/info?id=' + item.item_id + '#nav-new-maintenance" title="View Component" style="color: #80A874;">' +
                    item.number +
                    '</a></p>';
                modalContent +=
                    "<p>Component Name: " + item.item_name + "</p>";
                modalContent += "<p>Fault Type: " + item.fault_type + "</p>";

                // modalContent += '<p>QR Code: ' + response.qr_code + '</p>';
                modalContent += "<hr>"; // Add a separator
                modalContent += "</div>";
            } else {
                modalContent = "<p>No component found.</p>";
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
});
