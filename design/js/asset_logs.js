$(document).ready(function () {
    const loader = $('#datatable-loader');

    const table = $('#logs').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": false,
        "pageLength": 10,
        "deferRender": true,
        "stateSave": false,
        "ajax": {
            "url": "/Assets/asset_logs_ajax_list",
            "type": "POST",
            "beforeSend": function () {
                loader.show();
            },
            "complete": function () {
                loader.hide();
            },
            "error": function (xhr, error, thrown) {
                loader.hide();
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                } else {
                    alert("We are having trouble connecting to the API.");
                }
            }
        },
        "drawCallback": function () {
            loader.hide(); // double-check
            initToggle();
        },
        "order": [[0, "desc"]],
        "columns": [
            {
                "data": "timestamp",
                "orderable": true,
                "searchable": false
            },
            {
                "data": "full_name",
                "orderable": true,
                "searchable": true
            },
            {
                "data": "log_code",
                "orderable": true,
                "searchable": true
            },
             {
                "data": "log_description",
                "orderable": true,
                "searchable": true
            }
        ]
    });

    $.fn.dataTable.ext.errMode = 'none';
});
