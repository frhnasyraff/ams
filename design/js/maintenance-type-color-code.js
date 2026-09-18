$(document).ready(function () {
    $('#maintenance-type-color-code').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": appUrl("/MaintenanceTypeColorCode/ajax_list"),
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
                "data": "maintenance_type",
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html(`<a href="javascript:void(0);" data-toggle="modal" data-target="#editModal" class='editMaintenanceTypeColorCode' data-id="${rowData.id}" data-maintenance_type="${rowData.maintenance_type}" data-color="${rowData.color}" >${rowData.maintenance_type}</a>`);
                }
            },
            {
                "data": "color",
                createdCell: function (td, cellData, rowData, row, col) {
                    // Render the color as a small block with the hex code displayed
                    $(td).html(`
                        <div style="width: 20px; height: 20px; background-color: ${rowData.color}; display: inline-block;"></div>
                        ${rowData.color}
                    `);
                }
            },
           
            {
                "data": null,
                "orderable": false,
                "searchable": false,
                createdCell: function (td, cellData, rowData, row, col) {
                    // A direct text label is also recognised by older cached UI scripts.
                    $(td).html(`<a class="maintenance-color-delete" href="${appUrl("/MaintenanceTypeColorCode/delete?maintenance_type=")}${encodeURIComponent(rowData.maintenance_type)}" onclick="return confirm('Are you sure you want to delete this Color?');" title="Delete Maintenance Color" aria-label="Delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>`);
                }
            }
        ]

    });
    $.fn.dataTable.ext.errMode = 'none';

    $(document).on('click', '.editMaintenanceTypeColorCode', function () {
        var maintenance_type = $(this).data('maintenance_type');
        var color = $(this).data('color');
        var idd = $(this).data('id');
        $("#maintenance_type_edit").val(maintenance_type);
        $("#color_edit").val(color);
        $("#id_edit").val(idd);
    });
});


