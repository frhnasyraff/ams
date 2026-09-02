$(document).ready(function () {
    $('#asset-type-colors').DataTable({
        "processing": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/DashboardStatusColor/ajax_list",
            "type": "POST",
            "dataSrc": "data", // Ensure 'data' key is used for table rows
            "error": function (xhr, error, thrown) {
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                } else {
                    alert("We are having trouble connecting to the API.");
                }
            }
        },
        drawCallback: initToggle,
        "order": [[1, "asc"]],
        "columns": [
            {
                "data": "name",
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html(`<a href="javascript:void(0);" data-toggle="modal" data-target="#editModal" class='editAssetTypesColors' data-id="${rowData.id}" data-color="${rowData.color}"  data-name="${rowData.name}">${rowData.name}</a>`);
                }
            },
            {
                "data": "color",
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html(`
                        <div style="width: 20px; height: 20px; background-color: ${rowData.color}; display: inline-block;"></div>
                        ${rowData.color}
                    `);
                }
            },
            {
                "data": 'action',
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html('<a href="/DashboardStatusColor/delete?id=' + rowData.id + '" onclick="return confirm(\'Are you sure you want to delete this Color?\');" title="Delete Asset Type Color"><i class="fa fa-trash"></i></a>');
                }
            }
        ]
    });
    

    $(document).on('click', '.editAssetTypesColors', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var color = $(this).data('color');
        $("#edit_id").val(id);
        $("#edit_status").val(name);
        $("#edit_color").val(color);
    });
});