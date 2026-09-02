$(document).ready(function () {
    // Function to initialize DataTable
    function initializeDataTable(url) {
        $('#Assets_item_maintenance_summary').DataTable({
            "processing": true,
            "responsive": true,
            "autoWidth": true,
            "stateSave": true,
            "ajax": {
                "url": url,
                "type": "GET",
                "error": function (xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.redirect) {
                        window.location.href = xhr.responseJSON.redirect;
                    } else {
                        alert("We are having trouble connecting to the API.");
                    }
                }
            },
            "order": [[1, "asc"]],
            "columns": [
                {
                    data: "equipment_name",
                    createdCell: function (td, cellData, rowData, row, col) {
                        if (!$("table.read-only").length) {
                            $(td).html(
                                '<a style="color: #78261f;" href="/assets/info?id=' +
                                id_encode(rowData.equipment_id) +
                                '#nav-new-maintenance" title="View equipment">' +
                                cellData +
                                "</a>"
                            );
                        }
                    },
                },


                { "data": "maintenance_date" },
                { "data": "frequency_year" },
                { "data": "maintenance_reminder_day" },
                
            ]
        });
    }

    // Initialize the DataTable
    initializeDataTable("/Assets_item_maintenance/ajax_list");


 



    $('#item-list').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/Assets_item_maintenance/item_ajax_list",
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
                "data": "number",
                createdCell: function (td, cellData, rowData, row, col) {
                    if (!$("table.read-only").length) {
                        $(td).html('<p style="color: #523500;">' + cellData + '</p>');
                    }
                }
            },

            {
                "data": "item_name",
                createdCell: function (td, cellData, rowData, row, col) {
                    if (!$("table.read-only").length) {
                        $(td).html('<a href="/items/info?id=' + rowData.item_id + '#nav-new-maintenance" title="View item" style="color: #80A874;">' + cellData + '</a>');
                    }
                }
            },

            {
                "data": "equipment_name"
            },


            {
                "data": "issue_date"
            },

            {
                "data": "date_of_completion",
                createdCell: function (td, cellData, rowData, row, col) {
                    if (new Date(cellData)) { // If the date is in the past
                        $(td).css("color", "red");
                    }
                }
            },

            {
                "data": "fault_type"
            },

        ]

    });

     
});

