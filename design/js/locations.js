function locationsUrl(path) {
	var pathname = window.location.pathname;
	var controllerMatch = pathname.match(/\/locations(?:\/|$)/i);
	var basePath = controllerMatch ? pathname.substring(0, controllerMatch.index) : "";
	return basePath + "/" + path.replace(/^\/+/, "");
}

$(document).ready(function () {
	$('#locations_tabel').DataTable({
		"processing": true,	
		"autoWidth": true,
		"pageLength": 10,
		stateSave: true,
		"ajax": {
			// "url": "/locations/ajax_list", // Legacy URL
			"url": locationsUrl("/locations/ajax_list"),
			"type": "GET",
            "data": function(d) {
                // Add the filter value to the request
                d.filter = $('#filterTab').val(); // Assuming you have a hidden input to store filter value
            },
			"error": function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert("We are having trouble connecting to the API.");
				}
			}
		},
	
		
		"columns": [{
				"data": "name",
				
			},
			{
				"data": "state_name",
			
			},
			// {
			// 	"data": "state_code",
			// },
            {
				"data": "lat",
			},
            {
				"data": "long",
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
                // url: '/locations/delete', // Legacy URL
                url: locationsUrl('/locations/delete'),
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response); // Parse it if it's a string
                    }
                
                    console.log(response);
                    console.log(response.status);
                    if (response.status === 'success') {
                        $('#locations_tabel').DataTable().ajax.reload(); // Reload the table data
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
            // url: '/locations/get_data', // Legacy URL
            url: locationsUrl('/locations/get_data'),
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Populate the modal fields
                    // $('#countryName').val(response.data.country_name);
                    // $('#stateName').val(response.data.state_name.trim()).change(); // Legacy state_name
                    $('#stateName').val(response.data.state_id).change();
                    // Set selected value in dropdown
                    $('#name').val(response.data.name);
                    $('#lat').val(response.data.lat);
                    $('#long').val(response.data.long);
                    $('#address').val(response.data.address);
                    $('#colorCode').val(response.data.colour);
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
            // url: '/locations/update', // Legacy URL
            url: locationsUrl('/locations/update'), // Your update endpoint
            type: 'POST',
            data: $(this).serialize(), // Serialize the form data
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#editModal').modal('hide'); // Hide the modal
                    $('#locations_tabel').DataTable().ajax.reload(); // Reload the table data
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
    
    
    $(document).on("click", ".filterTab", function () {
        var value = $(this).text().trim();
    
        // Set the filter value in the hidden input
        $('#filterTab').val(value);
        
        // Retrieve and alert the value after setting it

        $('#locations_tabel').DataTable().ajax.reload();
        // Set the filter value from the button's data attribute
       
        
    });

    $(document).on("click", "#allStates", function () {        
        // Retrieve and alert the value after setting it
        window.location.reload();
        // Set the filter value from the button's data attribute
       
        
    });
    
	
});
