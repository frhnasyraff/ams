$(document).ready(function () {
    $('#fault-type-color-code').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/FaultTypeColorCode/ajax_list",
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
                "data": "Fault Type",
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html(`<a href="javascript:void(0);" data-toggle="modal" data-target="#editModal" class='editFaultTypeColorCode' data-id="${rowData.id}" data-fault_type="${rowData.fault_type}" data-color="${rowData.color}" >${rowData.fault_type}</a>`);
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
                "data": 'action',
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html('<a href="/FaultTypeColorCode/delete?fault_type=' + rowData.fault_type + '" onclick="return confirm(\'Are you sure you want to delete this Color?\');" title="Delete Asset Type Color"><i class="fa fa-trash"></i></a>');
                }
            }
        ]

    });
    $.fn.dataTable.ext.errMode = 'none';

    $(document).on('click', '.editFaultTypeColorCode', function () {
        var fault_type = $(this).data('fault_type');
        var color = $(this).data('color');
        var id = $(this).data('id');
        $("#fault_type_edit").val(fault_type);
        $("#color_edit").val(color);
        $("#id_edit").val(id);
    });
});