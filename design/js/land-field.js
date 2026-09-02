$(document).ready(function () {
    $('#land_field_location').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/land_field/ajax_list",
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
            "data": "land_field_id",
            createdCell: function (td, cellData, rowData, row, col) {
                if (!$("table.read-only").length) {
                    $(td).html('<a class="text_warning_color" href="/land_field/info?id=' + id_encode(rowData.land_field_id) + '" title="View Land Field">' + cellData + '</a>');
                }
            }
        },
        {
            "data": "location_name",
        },
        {
            "data": "address",
        },
        {
            "data": "latitude",
        },
        {
            "data": "longitude",
        },
        {
            "data": "land_field_id",
            createdCell: function (td, cellData, rowData, row, col) {
                if (!$("table.read-only").length) {
                    $(td).html('<a class="text-danger" href="/land_field/delete?id=' + rowData.land_field_id + '" title="Delete Land Field"  onclick="return confirm(\'Sure to delete this field location ?\');"><i class="fa fa-trash"></i></a>');
                }
            }
        }
        ]
    });

    $.fn.dataTable.ext.errMode = 'none';
});