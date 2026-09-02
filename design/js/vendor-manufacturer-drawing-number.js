$(document).ready(function () {
    $('#vendor_manufacturing_drawing_number').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/VendorManufacturingDrawingNumber/ajax_list",
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
                "data": "id",
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html(`<a href="javascript:void(0);" data-toggle="modal" data-target="#editModal" class='editVendorManufacturingDrawingNumber' data-id="${rowData.id}" data-name="${rowData.drawing_number}" >${rowData.id}</a>`);
                }
            },
            {
                "data": "drawing_number",        
            },
           
            {
                "data": 'action',
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html('<a href="/VendorManufacturingDrawingNumber/delete?id=' + rowData.id + '" onclick="return confirm(\'Are you sure you want to delete this Manufacturer drawing Number?\');" title="Delete Manufacturer drawing Number"><i class="fa fa-trash"></i></a>');
                }
            }
        ]
    });
    
    $.fn.dataTable.ext.errMode = 'none';

    $(document).on('click', '.editVendorManufacturingDrawingNumber', function () {
        var id = $(this).data('id');
        var  drawing_number= $(this).data('name');
        $("#id").val(id);
        $("#drawing_number_edit").val(drawing_number);
    });
   
});
