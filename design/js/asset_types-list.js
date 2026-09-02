$(document).ready(function() {
    $('#equipment_types').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/equipment_types/ajax_list",
            "type": "POST",
            "error": function(xhr, error, thrown) {
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
                "data": "equipment_type_name",
                createdCell: function(td, cellData, rowData, row, col) {
                    if (!$("table.read-only").length) {
                        $(td).html('<a href="/equipment_types/info?id=' + id_encode(rowData.equipment_type_id) + '" title="View equipment type">' + cellData + '</a>');
                    }
                }
            },
            {
                "data": "equipment_type_short_code",
                createdCell: function(td, cellData, rowData) {
                    $(td).html('<span class="badge badge-info" style="background: ' + rowData.equipment_type_colour + '">' + cellData + '</span>');
                }
            },
            {
                "data": "description",
            },
            {
                "data": "active",
                createdCell: function(td, cellData, rowData, row, col) {
                    if (!$("table.read-only").length) {
                        $(td).addClass("text-center").html('<input type="checkbox" ' + (rowData.active != 0 ? 'checked' : '') + ' data-toggle="toggle" data-id="' + rowData.equipment_type_id + '" />');
                    }
                }
            }
        ]

    });
    if ($("#color-block").length) {
        var color = randomColor();
        $("#color-block").wheelColorPicker('setValue', $("#color-block").val() ? $("#color-block").val() : color);
    }

    $.fn.dataTable.ext.errMode = 'none';
});