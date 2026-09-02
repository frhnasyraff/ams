$(document).ready(function () {
	$('#item_tabel').DataTable({
		"processing": true,	
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "/item_type/ajax_list",
			"type": "GET",
			"error": function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert("We are having trouble connecting to the API.");
				}
			}
		},
	
		
		"columns": [{
				"data": "id",
				
			},
			{
				"data": "name",
			
			},
            {
				"data": "manufacturer_name",
			
			},
            {
				"data": "part_number",
			
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
				"data": null,
				createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html(
                        '<button class="btn  btn-edit btn-sm " data-id="' + rowData.id + '" style="font-size:13px;"><i class="fa fa-edit text-warning"></i></button>' +
                        '<button class="btn  btn-delete btn-sm" data-id="' + rowData.id + '" style="font-size:13px;"><i class="fa fa-trash text-danger" aria-hidden="true"></i></button>'
                    );
				}
			}
		]

	});



    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this component?')) {
            $.ajax({
                url: '/item_type/delete',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response); // Parse it if it's a string
                    }
                
                    console.log(response);
                    console.log(response.status);
                    if (response.status === 'success') {
                        $('#item_tabel').DataTable().ajax.reload(); // Reload the table data
                        $('.flash-message-container').html('<div class="alert alert-success">' + response.message + '</div>');
                    } else {
                        $('.flash-message-container').html('<div class="alert alert-error">' + response.message + '</div>');
                    }
                 },
                error: function() {
                    $('.flash-message-container').html('<div class="alert alert-error">' + response.message + '</div>');
                }
            });
        }
    });
    

    // edit button 
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
    
        // Fetch existing data (you might have an AJAX endpoint for this)
        $.ajax({
            url: '/item_type/get_data',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Populate the modal fields
                    $('#stateName').val(response.data.name);
                    $('#manufacturer').val(response.data.manufacturer);
                    $('#vendor_part_number').val(response.data.vendor_part_number);
                    var calibration_data = response.data.calibration;
                    var maintenance_data = response.data.maintenance;
    
                    if (calibration_data == 1) {
                        $("#calibration-check-edit").prop('checked', true); // Correct way to check the checkbox
                    } else {
                        $("#calibration-check-edit").prop('checked', false); // Uncheck if needed
                    }

                    if (maintenance_data == 1) {
                        $("#maintenance-check-edit").prop('checked', true); // Correct way to check the checkbox
                    } else {
                        $("#maintenance-check-edit").prop('checked', false); // Uncheck if needed
                    }
                    $('#editId').val(id); // Store the ID in a hidden input
                    
                    // Show the modal
                    $('#editModal').modal('show');
                } else {
                    alert('Error fetching data.');
                }
            },
            error: function() {
                alert('Error fetching data.');
            }
        });
    });

    
    // update submit form 
    $('#editForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the form from submitting normally
    
        $.ajax({
            url: '/item_type/update', // Your update endpoint
            type: 'POST',
            data: $(this).serialize(), // Serialize the form data
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#editModal').modal('hide'); // Hide the modal
                    $('#item_tabel').DataTable().ajax.reload(); // Reload the table data
                    $('.flash-message-container').html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error updating item.');
            }
        });
    });
    
	// calibration-check

    $('#calibration-check').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    $('#maintenance-check').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });


    
});