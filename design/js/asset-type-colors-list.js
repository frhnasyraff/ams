$(document).ready(function () {
    $('#asset-type-colors').DataTable({
        "processing": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/AssetTypesColors/ajax_list",
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
                    $(td).html(`<a href="javascript:void(0);" data-toggle="modal" data-target="#editModal" class='editAssetTypesColors' data-id="${rowData.id}" data-name="${rowData.color}" >${rowData.name}</a>`);
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
                    $(td).html('<a href="/AssetTypesColors/delete?id=' + rowData.id + '" onclick="return confirm(\'Are you sure you want to delete this Color?\');" title="Delete Asset Type Color"><i class="fa fa-trash"></i></a>');
                }
            }
        ]
    });
    

    $(document).on('click', '.editAssetTypesColors', function () {
        var asset_type_id = $(this).data('id');
        var color = $(this).data('name');
        $("#asset_type_color_id").val(asset_type_id);
        $("#asset_type_color_edit").val(color);
    });
});