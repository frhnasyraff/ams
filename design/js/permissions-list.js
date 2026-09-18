$(document).ready(function() {
    $('#permissions').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "/permissions/ajax_list",
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
		"order": [[0, "desc"]],
		"columns": [
			{
				"data": "perm_id",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html('<a class="access-record-link" href="/permissions/info?id=' + id_encode(cellData) + '" title="View permission">' + pad(cellData, 3) + '</a>');
					}
				}
			},
			{
				"data": "perm_cat_name"
            },
            {
				"data": "perm_name"
			},
			{
				"data": null,
                "searchable": false,
				"orderable": false,
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						if (rowData.system == "1") {
                        $(td).addClass("text-center").html('<span class="access-protected" title="This system rule cannot be deleted"><i class="fas fa-lock" aria-hidden="true"></i> System Rule</span>');
                    } else {
                    $(td).addClass("text-center").html('<button type="button" class="access-action access-delete delete" data-id="' + rowData.perm_id + '" data-toggle="modal" data-target="#deleteModal"><i class="fas fa-trash-alt" aria-hidden="true"></i> Delete</button>');
                    }
				}
			}
			}
			]

	});

    $.fn.dataTable.ext.errMode = 'none';
    
    $(".card-body").on("click", "button.delete", function() {
$("#deleteModal .record_id").val($(this).attr("data-id"));
    });
});
