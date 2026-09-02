$(document).ready(function () {
    $('#maintenence_type').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/MaintenenceType/ajax_list",
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
                    $(td).html(`<a href="javascript:void(0);" data-toggle="modal" data-target="#editModal" class='editMaintenenceType' data-id="${rowData.id}" data-name="${rowData.name}" >${rowData.id}</a>`);
                }
            },
            {
                "data": "name",        
            },
           
            {
                "data": 'action',
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html('<a href="/Fault Type/delete?id=' + rowData.id + '" onclick="return confirm(\'Are you sure you want to delete this FAult Type?\');" title="Delete FAult Type"><i class="fa fa-trash"></i></a>');
                }
            }
        ]
    });
    
    $.fn.dataTable.ext.errMode = 'none';

    $(document).on('click', '.editMaintenenceType', function () {
        var id = $(this).data('id');
        var  name= $(this).data('name');
        $("#id").val(id);
        $("#name_edit").val(name);
    });
   
});
