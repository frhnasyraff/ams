$(document).ready(function () {
    window.inventoryMetricCell = function (data, type, tone) {
        const value = Number(data || 0);
        if (type && type !== 'display') return value;
        return '<span class="inventory-metric inventory-metric-' + tone + '">' + value.toLocaleString() + '</span>';
    };

    $('#assets').DataTable({
        "processing": true,
        "responsive": true,
        "autoWidth": false,
        "pageLength": 10,
        "pagingType": "simple_numbers",
        "dom": '<"inventory-dt-top"<"inventory-dt-left"l><"inventory-dt-right"f>>t<"inventory-dt-bottom"ip>',
        "language": {
            "paginate": {
                "previous": "",
                "next": ""
            }
        },
        "stateSave": true,
        "ajax": {
            "url": "/InventorySummary/getAssetSummary_table",
            "dataSrc": function (json) {
                // Filter rows where all relevant columns are zero
                return json.filter(function (item) {
                    const loc = item.total_locations || 0;
                    const qty = item.total_quantity?.total_assets || 0;
                    const serviceable = item.total_serviceable?.assets_serviceable || 0;
                    const store = item.total_store || 0;
                    const corrective = item.corrective_maintenance?.total_assets || 0;

                    return (loc + qty + serviceable + store + corrective) > 0;
                });
            },
            "error": function (xhr, error, thrown) {
                console.log(xhr);
                const response = xhr.responseJSON;
                if (response && response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    alert("We are having trouble connecting to the API.");
                }
            }
        },

        // ðŸŸ¡ Sort: Locations, Quantity, Serviceable, In Store, Corrective
        "order": [
            [1, "desc"], // Locations
            [2, "desc"], // Asset Quantity
            [3, "desc"], // Serviceable
            [4, "desc"], // In Store
            [5, "desc"]  // Corrective
        ],

        "columns": [
            { "data": "equipment_type", "title": "Equipment Type", "render": function(data, type){
                const name = (data || '-');
                if (type && type !== 'display') return name;
                const lower = String(name).toLowerCase();
                const icon = lower.includes('bin') ? 'fa-trash-alt' : (lower.includes('tipper') || lower.includes('truck') ? 'fa-truck' : 'fa-box');
                return '<span class="inventory-type-cell"><i class="fas ' + icon + '"></i><span>' + name + '</span></span>';
            } },
            { "data": "total_locations", "title": "Locations", "render": function(data, type){ return window.inventoryMetricCell(data, type, 'location'); } },
            {
                "data": "total_quantity.total_assets",
                "title": "Asset Quantity",
                "render": function (data, type) {
                    return window.inventoryMetricCell(data, type, 'quantity');
                }
            },
            {
                "data": "total_serviceable.assets_serviceable",
                "title": "Serviceable",
                "render": function (data, type) {
                    return window.inventoryMetricCell(data, type, 'serviceable');
                }
            },
            { "data": "total_store", "title": "In Store", "render": function(data, type){ return window.inventoryMetricCell(data, type, 'store'); } },
            {
                "data": "corrective_maintenance.total_assets",
                "title": "Corrective",
                "render": function (data, type) {
                    return window.inventoryMetricCell(data, type, 'corrective');
                }
            },
            {
                "data": "preventive_maintenance.total_assets",
                "title": "Preventive",
                "render": function (data, type) {
                    return window.inventoryMetricCell(data, type, 'preventive');
                }
            }
        ]
    });
});


$(document).ready(function () {
    $('#items').DataTable({
        "processing": true,
        "responsive": true,
        "autoWidth": false,
        "pageLength": 10,
        "pagingType": "simple_numbers",
        "dom": '<"inventory-dt-top"<"inventory-dt-left"l><"inventory-dt-right"f>>t<"inventory-dt-bottom"ip>',
        "language": {
            "paginate": {
                "previous": "",
                "next": ""
            }
        },
        "stateSave": true,
        "ajax": {
            "url": "/InventorySummary/getItemSummary",
            "dataSrc": function (response) {
                console.log("API Response:", response);

                if (response && Array.isArray(response)) {
                    // Filter out records where all percentages are zero
                    // const filteredResponse = response.filter(item =>
                    //     parseFloat(item.in_use_percentage) > 0 ||
                    //     parseFloat(item.store_percentage) > 0 ||
                    //     parseFloat(item.corrective_maintenance_percentage) > 0
                    // );
                    return response;
                }

                return []; // In case of invalid response
            },
            "error": function (xhr, error, thrown) {
                console.log("AJAX Error:", xhr, error, thrown);
                const response = xhr.responseJSON;
                if (response && response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    alert("We are having trouble connecting to the API.");
                }
            }
        },
        "columns": [
            { "data": "item_type", "title": "Component Type", "render": function(data, type){
                const name = data || '-';
                if (type && type !== 'display') return name;
                return '<span class="inventory-type-cell"><i class="fas fa-cube"></i><span>' + name + '</span></span>';
            } },
            { "data": "total_locations", "title": "Locations", "render": function(data, type){ return window.inventoryMetricCell(data, type, 'location'); } },
            { "data": "total_quantity", "title": "Quantity", "render": function(data, type){ return window.inventoryMetricCell(data, type, 'quantity'); } },
            // { "data": "items_in_use_count", "title": "In Use" },
            { "data": "items_serviceable_count", "title": "Serviceable", "render": function(data, type){ return window.inventoryMetricCell(data, type, 'serviceable'); } },

            { "data": "items_in_store_count", "title": "In Store", "render": function(data, type){ return window.inventoryMetricCell(data, type, 'store'); } },
            { "data": "total_corrective_maintenance", "title": "Corrective Maintenance", "render": function(data, type){ return window.inventoryMetricCell(data, type, 'corrective'); } },
            
        ]
    });
});







