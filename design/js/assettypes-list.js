$(document).ready(function () {
    var toggleInitialized = 0;

    $('#assettypes').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        stateSave: true,
        "ajax": {
            "url": "/Assettypes/ajax_list",
            "type": "POST",
            "error": function (xhr, error, thrown) {
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                } else {
                    alert("We are having trouble connecting to the API.");
                }
            }
        },
        drawCallback: function (settings) {

            if (toggleInitialized < 2 || $('[data-toggle="toggle"]').attr('init') != "true") {

                $(".tooltip.fade.show").remove();

                $('[data-toggle="tooltip"],.tip').tooltip();


                $('[data-toggle="toggle"]').bootstrapToggle(bootstrapToggle).change(function () {
                    var that = $(this);
                    $.ajax({
                        url: "/" + settings.sInstance + "/state_ajax",
                        dataType: "json",
                        context: document.body,
                        type: "POST",
                        data: {
                            id: that.data("id"),
                            active: (that.prop("checked") ? 1 : 0)
                        },
                        success: function (s) {
                            if (s.state) {
                                growl((that.prop("checked") ? "Active" : "Inactive") + " successfully", "success");
                            } else {
                                that.prop('checked', (that.prop("checked") ? 0 : 1)).bootstrapToggle('destroy').bootstrapToggle(bootstrapToggle);
                                growl("Could not save changes", "danger");
                            }
                        },
                        error: function () {
                            that.bootstrapToggle('toggle');
                            growl("Could not save changes", "danger");
                        }
                    });
                });
                toggleInitialized++;
                $('[data-toggle="toggle"]').attr('init', "true");
            }
        },
        "order": [
            [1, "asc"]
        ],
        "columns": [{
            "data": "name",
            createdCell: function (td, cellData, rowData, row, col) {
                if (!$("table.read-only").length) {
                    $(td).html('<a href="/assettypes/info?id=' + id_encode(rowData.asset_id) + '" title="View Asset Types">' + cellData + '</a>');
                }
            }
        },

        {
            "data": "manufacturer",
            createdCell: function (td, cellData, rowData, row, col) {
                if (!$("table.read-only").length) {
                    rowData.manufacturer == "" ? "" : rowData.manufacturer_name;
                }
            }
        },

        {
            "data": "part_number",
            createdCell: function (td, cellData, rowData, row, col) {
                if (!$("table.read-only").length) {
                }
            }
        },

        {
            "data": "calibration",
            createdCell: function (td, cellData, rowData, row, col) {
                let html;
                if (cellData == 1) {
                    html = "<h6>Yes</h6>";
                } else {
                    html = "<h6>No</h6>";
                }
                $(td).html(html);
            }
        },

        {
            "data": "maintenance",
            createdCell: function (td, cellData, rowData, row, col) {
                let html;
                if (cellData == 1) {
                    html = "<h6>Yes</h6>";
                } else {
                    html = "<h6>No</h6>";
                }
                $(td).html(html);
            }
        },

        {
    "data": "active",
    createdCell: function (td, cellData, rowData, row, col) {
        if (!$("table.read-only").length) {
            const checkboxHtml = '<input type="checkbox" ' +
                (rowData.active != 0 ? 'checked' : '') +
                ' data-toggle="toggle" data-on="Active" data-off="Inactive" data-style="status-text-toggle" data-id="' + rowData.asset_id + '" />';

            $(td).addClass("text-center status-only-action-cell").html(checkboxHtml);
        }
    }
}


        ]
    });

    $.fn.dataTable.ext.errMode = 'none';

    // calibration-check

    $('#calibration-check').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });


    // maintenance-check

    $('#maintenance-check').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    // ========= Append fields =============

    $('#itm_qty').click(function (e) {
        e.preventDefault();
        // Fetch item types from the server
        $.ajax({
            url: 'Assettypes/getItemTypes', // Adjust with your controller and method name
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                // Construct the options for the select dropdown
                let options = '<option value="">Select Item Type</option>';
                data.forEach(function (item) {
                    options += `<option value="${item.id}">${item.name}</option>`; // Adjust 'id' and 'name' to your table fields
                });

                // Append a new row with the dynamically populated select field, quantity input, and remove button
                $('#item-container').append(`
                <div class="item-row mb-2 d-flex">
                    <div class="col-md-4 pe-1">
                        <select class="form-control mb-2" name="item_type[]">
                            ${options}
                        </select>
                    </div>
                    <div class="col-md-4 pe-1">
                        <input type="number" class="form-control mb-2" name="quantity[]" placeholder="Enter Quantity">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-danger remove-item">Remove</button>
                    </div>
                </div>
            `);
            },
            error: function () {
                alert('Error fetching item types');
            }
        });
    });

    // Event delegation for removing an appended row
    $('#item-container').on('click', '.remove-item', function () {
        $(this).closest('.item-row').remove();
    });



    // ============= Edit Append ===========

    // Add new item row
    $(document).on('click', "#add-item-btn", function (e) {
        e.preventDefault();

        $.ajax({
            url: '/Assettypes/getItemTypes', // Adjust with your controller and method name
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                let options = '<option value="">Select Item Type</option>';
                data.forEach(function (item) {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });

                $('#dynamic-item-container').append(`
                        <div class="dynamic-item-row mb-2 d-flex">
                            <div class="col-md-5 pe-1">
                                <select class="form-control mb-2 dynamic-item-type" name="item_type[]">
                                    ${options} <!-- Injected options dynamically -->
                                </select>
                            </div>
                            <div class="col-md-5 pe-1">
                                <input type="number" class="form-control mb-2 dynamic-quantity" name="quantity[]" placeholder="Enter Quantity">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
                            </div>
                        </div>
                    `);
            },
            error: function () {
                alert('Error fetching item types');
            }
        });
    });

    // Remove item row
    $('#dynamic-item-container').on('click', '.remove-item-btn', function () {
        $(this).closest('.dynamic-item-row').remove();
    });

});
