$(document).ready(function () {
    $('#managed-by-add-data').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/ManagedByAddData/ajax_list",
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
                    $(td).html(`<a href="javascript:void(0);" data-toggle="modal" data-target="#editModal" class='editManagedByAddData' data-id="${rowData.id}" data-name="${rowData.name}" >${rowData.id}</a>`);
                }
            },
            {
                "data": "name",
                
                
            },
           
            {
                "data": 'action',
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html('<a href="/ManagedByAddData/delete?id=' + rowData.id + '" onclick="return confirm(\'Are you sure you want to delete this Name?\');" title="Delete editManaged By"><i class="fa fa-trash"></i></a>');
                }
            }
        ]

    });
    $.fn.dataTable.ext.errMode = 'none';

    $(document).on('click', '.editManagedByAddData', function () {
        var id = $(this).data('id');
        var  vendor_name= $(this).data('name');
        $("#id").val(id);
        $("#name_edit").val(vendor_name);
    });
});