$(document).ready(function () {
    $('#preventive_table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/maintenance_dashboard/preventive_table_list",
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
                "data": "equipment_id",
                "title": "Equipment ID"
            },
            {
                "data": "maintenance_records",
                "title": "Update Date",
                "render": function (data) {
                    return new Date(data).toLocaleDateString(); // Format date
                }
            },
            {
                "data": "maintenance_type_id",
                "title": "Maintenance Type"
            },
            
            {
                "data": "final_status",
                "title": "Final Status"
            },
            {
                "data": "remarks",
                "title": "Remarks"
            }
        ]
    });

    $.fn.dataTable.ext.errMode = 'none';
});
