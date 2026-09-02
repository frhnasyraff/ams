$(document).ready(function() {
   quotations = $('#quotations').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "/quotations/ajax_list",
			"type": "POST",
			"error": function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert("We are having trouble connecting to the API.");
				}
			},
			data: function (d) {
				return $.extend({}, d, {
					show: $(".filters .btn[disabled]").data("filter")
				});
			},
		},
		drawCallback: initToggle,
		"order": [[0, "desc"]],
		"columns": [
			{
				"data": "quotation_number",
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$("table.read-only").length) {
						$(td).html('<a href="/quotations/info?id=' + id_encode(rowData.quotation_id) + '" title="View quotation">' + cellData + (rowData.revision && rowData.revision > 0 ? "-" + rowData.revision : '') + '</a>');
					}
				}
			},
			{
                "data": "status",
                createdCell: function(td, cellData) {
                    $(td).html(cellData.substr(0,1).toUpperCase()+cellData.substr(1))
                }
            },
            {
                "data": "merchant_name",
                createdCell: function (td, cellData, rowData, row, col) {
						$(td).html('<a href="/merchants/info?id=' + id_encode(rowData.merchant_id) + '" title="View customer">' + cellData + '</a>');
				}
            },
            {
				"data": "t_effective"
            },
            {
				"data": "t_expiry"
            },
            {
				"data": "internal_remark"
            },
            {
				"data": "t_quotation_updated"
            },
            {
				"data": "web_pdf",
				"orderable": false,
				createdCell: function (td, cellData, rowData, row, col) {
					if (cellData) {
                    $(td).addClass("text-center").html('<a class="btn btn-info btn-sm" href="/storage/' + cellData + '?_' + new Date(). valueOf() + '" download><i class="fa fa-download"></i> PDF</a>');
				}
			}
			}
			]

	});

    $.fn.dataTable.ext.errMode = 'none';
    $(".filters .btn").click(function() {
		$(".filters .btn").each(function() {
			if ($(this).attr("disabled")) {
				$(this).removeAttr("disabled").removeClass("btn-success").addClass("btn-primary");
			} else {
				$(this).attr("disabled", "disabled").addClass("btn-success").removeClass("btn-primary");
			}
		});
		quotations.ajax.reload();
		$(".tooltip.show").remove();
	})
    $(".card-body").on("click", "button.delete", function() {
$("#deleteModal .record_id").val($(this).attr("data-id"));
    });
});
