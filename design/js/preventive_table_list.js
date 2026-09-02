let preventiveTable; // global reference to allow re-initialization

$(document).ready(function () {
    function getQueryParam(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }

    const typeFilter = getQueryParam('type_filter');
    if (typeFilter) {
        $(".asset_id_filter .btn-primary").data("filter", typeFilter);
    }

    // Initialize default DataTable (if needed on load)
    initPreventiveTable($(".asset_id_filter .btn-primary").data("filter"));

    // Handle click on dynamic filter link
    $(document).on("click", ".apply-type-filter", function (e) {
        e.preventDefault();
        const assetId = $(this).data("type-filter");

        // Show the modal or section (Bootstrap or fallback)
        if ($('#preventiveModal').length) {
            $('#preventiveModal').modal('show');
        } else {
            $('#preventiveWrapper').show(); // use this if modal not available
        }

        // Destroy old DataTable instance if already exists
        if ($.fn.DataTable.isDataTable('#preventive')) {
            preventiveTable.clear().destroy();
        }

        // Initialize new table with fresh filter
        initPreventiveTable(assetId);
    });

    // Table init function with dynamic asset filter
    function initPreventiveTable(assetId) {
        preventiveTable = $('#preventive').DataTable({
            "processing": true,
            "serverSide": false,
            "responsive": true,
            "autoWidth": false,
            "pageLength": 5,
            "stateSave": false,
            "ajax": {
                "url": "/preventive_maintenance/preventive_table_list",
                "type": "POST",
                "data": function (d) {
                    return $.extend({}, d, {
                        asset_id: assetId
                    });
                },
                "error": function (xhr, error, thrown) {
                    const response = xhr.responseJSON;
                    if (response && response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        alert("We are having trouble connecting to the API.");
                    }
                }
            },
            "drawCallback": initToggle,
            "order": [[1, "desc"]],
            "columns": [
                { "data": "equipment_name" },
                { "data": "store_location_name" },
                {
    "data": "current_status",
    "createdCell": function (td, cellData, rowData) {
        if (cellData) {
            let customStyle = "background-color: #e6c98b;"; // default
            const status = (cellData || '').trim().toLowerCase();

            switch (status) {
                case 'pending':
                    customStyle = "background-color:rgba(255, 0, 0, 0.7);"; // red
                    break;
                case 'complete':
                    customStyle = "background-color:rgba(0, 128, 0, 0.7);"; // green
                    break;
                case 'maintenance':
                    customStyle = "background-color:rgba(255, 255, 0, 0.7);"; // yellow
                    break;
            }

            $(td).css({
                "padding": "0",
                "text-transform": "uppercase",
                "color": status === 'maintenance' ? 'black' : 'white', // yellow needs black text for contrast
                "text-align": "center"
            });

            $(td).html(
                `<span class='custom-badge' style="display: block; width: 100%; padding: 5px 0; border-radius: 5px; ${customStyle}">${cellData}</span>`
            );
        }
    }
},
                {
                    "data": "interval",
                    "render": function (data, type, row) {
                        const status = (row.current_status || '').toUpperCase();
                        if (status === "Pending" || status === "Maintenance") {
                            return '';
                        }
                        return data;
                    }
                }
            ]
        });
    }
});


$(document).on('click', '.apply-type-filter', function (e) {
    e.preventDefault();

    const typeFilter = $(this).data('type-filter');

    // Set the filter in the button or wherever it is used
    $(".asset_id_filter .btn-primary").data("filter", typeFilter);

    // Reload the table with the new filter
    $('#preventive').DataTable().ajax.reload();
});

