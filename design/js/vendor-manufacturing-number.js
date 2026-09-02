$(document).ready(function () {
    $('#vendor_manufacturing_number').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/VendorManufacturingNumber/ajax_list",
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
                   
                    $(td).html(`<a href="javascript:void(0);" data-toggle="modal" data-target="#editModal" class='editVendorManufacturingNumber' 
                        data-id="${rowData.id}"                       
                        data-manufacturer_name="${rowData.manufacturer_name}" 
                        data-manufacturer_number="${rowData.manufacturer_number}">
                        ${rowData.id}</a>`);
                }
            },
            {
                "data": "manufacturer_name",        
            },
            {
                "data": "manufacturer_number",        
            },
            {
                "data": 'action',
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html('<a href="/VendorManufacturingNumber/delete?id=' + rowData.id + '" onclick="return confirm(\'Are you sure you want to delete this Manufacturer Number?\');" title="Delete Manufacturer Number"><i class="fa fa-trash"></i></a>');
                }
            }
        ]
    });
    
    $.fn.dataTable.ext.errMode = 'none';

   
    $(document).on('click', '.editVendorManufacturingNumber', function () {
        var id = $(this).data('id');
        var manufacturer_name = $(this).data('manufacturer_name');
        var manufacturer_number = $(this).data('manufacturer_number');

        
        $("#id").val(id);
        $("#manufacturer_name_edit").val(manufacturer_name);
        $("#manufacturer_number_edit").val(manufacturer_number);
    });
});
