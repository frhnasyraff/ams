$(document).ready(function () {
    $('#vendor_part_numbers').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/VendorPartNumber/ajax_list",
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
                    $(td).html(`<a href="javascript:void(0);" data-toggle="modal" data-target="#editModal" class='editVendorPartNumber' data-id="${rowData.id}" data-name="${rowData.part_number}" >${rowData.id}</a>`);
                }
            },
            {
                "data": "part_number",
                
                
            },
           
            {
                "data": 'action',
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html('<a href="/VendorPartNumber/delete?id=' + rowData.id + '" onclick="return confirm(\'Are you sure you want to delete this Color?\');" title="Delete Asset Type Color"><i class="fa fa-trash"></i></a>');
                }
            }
        ]

    });
    $.fn.dataTable.ext.errMode = 'none';

    $(document).on('click', '.editVendorPartNumber', function () {
        var id = $(this).data('id');
        var  vendor_part_number= $(this).data('name');
        $("#id").val(id);
        $("#part_number_edit").val(vendor_part_number);
    });
});