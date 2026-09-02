$("#bin-table").on("click", ".OrderAssetsModalTrigger", function () {
    $('#OrderBinQRModal').modal('show');
    var orderId = $(this).data('orderid');
    $.ajax({
        "url": "/Bin_Performance/get_bin_data_html",
        "method": "GET",
        "data": {
            "orderId": orderId
        },
        success: function (response) {
            $("#bin-ajax-response").html('');
            $("#bin-ajax-response").html(response);
        }
    });
});

$('#datefilter').on('click', function() {

    var month = $("#month").val();
    var year = $("#year").val();

   
    // Call your function here

    if(year && month == ""){
        $('#bin-table').DataTable().ajax.url("/Bin_Performance/get_bin_data?year="+ year).load();
        
    }

    if(month && year == ""){
        $('#bin-table').DataTable().ajax.url("/Bin_Performance/get_bin_data?month="+ month).load();

    }

    if(month && year){
        $('#bin-table').DataTable().ajax.url("/Bin_Performance/get_bin_data?month="+ month +"&year=" +year).load();

    }

   
});

$("#select_all_checkboxes:not([disabled])").on("click", function () {
	$('.checkbox-select').each(function () {
		$(this).prop('checked', !$(this)[0].checked);
	});
	$(this).text(function (i, text) {
		return text === "Select All" ? "Un-Select All" : "Select All";
	})
});



$(document).ready(function() {
    
$(document).on("change", "#download_type_select", function () {
    var value = $(this).val();
    $("#downlaod-type").val(value);
});


var table = $('#bin-table').DataTable({
    "ajax": {
        "url": "/Bin_Performance/get_bin_data",
        "type": "POST",
        "data": function (d) {
            return $.extend({}, d);
        },
        "error": function(xhr, error, thrown, message) {
            console.log(error);
            console.log(message);
            if (xhr.responseJSON && xhr.responseJSON.redirect) {
                window.location.href = xhr.responseJSON.redirect;
            } else {
                alert("We are having trouble connecting to the API.");
            }
        }
    },
    drawCallback: initToggle,
    // "order": [
    //     [1, "asc"]
    // ],
    "columns": [
        {
            data: "order_id",
            orderable: false,
            createdCell: function(td, cellData, rowData, row, col) {
                if (cellData) {
                    $(td).html(`<input type="checkbox" name="record[]" class="checkbox-select" value="${rowData.order_id}" />`);
                }
            }
        },
      
        {
            "data": "company_name",
        },
        {
            "data": "asset_type_name",
        },
        {
            "data": "start_date",
        },
        {
            "data": "total_qr_codes",
        },
        {
            "data": "company_name",
            createdCell: function (td, cellData, rowData, row, col) {
                if (!$("table.read-only").length) {
                    $(td).addClass("text-center").html('<a href="javascript:void(0);" class="OrderAssetsModalTrigger" data-orderid=' + rowData.order_id + '  ><i class="fa fa-eye"></i></a>');
                }
            }
        }
    ]

});

});

