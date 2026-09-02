$(document).ready(function () {
	$('#states_tabel').DataTable({
		"processing": true,	
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			"url": "/states/ajax_list",
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
				"data": "state_name",
			
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
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: '/states/delete',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response); // Parse it if it's a string
                    }
                
                    console.log(response);
                    console.log(response.status);
                    if (response.status === 'success') {
                        $('#states_tabel').DataTable().ajax.reload(); // Reload the table data
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
            url: '/states/get_data',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Populate the modal fields
                    $('#stateName').val(response.data.state_name);
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
            url: '/states/update', // Your update endpoint
            type: 'POST',
            data: $(this).serialize(), // Serialize the form data
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#editModal').modal('hide'); // Hide the modal
                    $('#states_tabel').DataTable().ajax.reload(); // Reload the table data
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
    
	
});