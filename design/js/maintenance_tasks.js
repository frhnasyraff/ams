$(document).ready(function() {
    let currentMaintenanceId = $('input[name="maintenance_id"]').val();
    let equipmentId = $('input[name="equipment_id"]').val();

    console.log('🔧 Initial Data:');
    console.log('   - Equipment ID:', equipmentId);
    console.log('   - Maintenance ID:', currentMaintenanceId);

    // ✅ SIMPLIFIED DATATABLE CONFIGURATION
    const tasksTable = $('#tasks-table').DataTable({
        "processing": true,
        "serverSide": false,
        "ordering": true,
        "searching": true,
        "paging": true,
        "pageLength": 5,
        "lengthMenu": [5, 10, 25, 50, 100],
        "ajax": {
            "url": "/Assets_Item_maintenance/get_tasks_ajax",
            "type": "POST",
            "data": function(d) {
                const postData = {
                    equipment_id: equipmentId,
                    maintenance_id: currentMaintenanceId,
                    draw: d.draw,
                    start: d.start,
                    length: d.length,
                    search: d.search
                };
                console.log('📤 Sending AJAX Data:', postData);
                return postData;
            },
            "dataSrc": function(response) {
                console.log('📥 Received AJAX Response:', response);
                
                if (response.debug) {
                    console.log('🐛 Debug Info:', response.debug);
                }
                
                if (response.error) {
                    console.error('❌ Server Error:', response.error);
                    alert('Error loading tasks: ' + response.error);
                }
                
                return response.data;
            },
            "error": function(xhr, error, thrown) {
                console.error('❌ DataTable AJAX Error:', error);
                console.error('Status:', thrown);
                console.error('Full Response:', xhr.responseText);
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    console.error('Parsed Error:', errorResponse);
                } catch (e) {
                    console.error('Raw Error Response:', xhr.responseText);
                }
            }
        },
        "columns": [
            { 
                "data": "task_name",
                "className": "text-left"
            },
            { 
                "data": "assigned_user",
                "className": "text-center"
            },
            { 
                "data": "cost",
                "className": "text-center"
            },
            { 
                "data": "file",
                "className": "text-center"
            },
            { 
                "data": "status",
                "className": "text-center"
            },
            { 
                "data": "actions",
                "className": "text-center",
                "orderable": false,
                "searchable": false
            }
        ],
        "responsive": true,
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "language": {
            "emptyTable": "No tasks found",
            "info": "Showing _START_ to _END_ of _TOTAL_ tasks",
            "infoEmpty": "Showing 0 to 0 of 0 tasks",
            "infoFiltered": "(filtered from _MAX_ total tasks)",
            "lengthMenu": "Show _MENU_ tasks",
            "loadingRecords": "Loading...",
            "processing": "Processing...",
            "search": "Search:",
            "zeroRecords": "No matching tasks found",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "initComplete": function(settings, json) {
            console.log('✅ DataTable Initialization Complete:', json);
        }
    });

    // ✅ FIXED: EDIT TASK FUNCTIONALITY WITH PROPER ERROR HANDLING
$(document).on('click', '.edit-task', function() {
    try {
        const taskDataString = $(this).data('task');
        console.log("🎯 Raw task data string:", taskDataString);
        
        let taskData;
        
        if (typeof taskDataString === 'object') {
            taskData = taskDataString;
        } else if (typeof taskDataString === 'string') {
            taskData = JSON.parse(taskDataString);
        } else {
            console.error('❌ Invalid task data type:', typeof taskDataString);
            alert('Error: Invalid task data. Please try again.');
            return;
        }
        
        console.log("🎯 Parsed task data:", taskData);

        // ✅ SET DYNAMIC VALUES
        $('#edit-task-id').val(taskData.id || 'new');
        $('#edit-task-list-id').val(taskData.task_list_id || '');
        $('#edit-cost').val(taskData.cost || '0');
        $('#edit-user').val(taskData.user_id || '');
        $('#edit-status').val(taskData.status || 'pending');

        // ✅ DYNAMIC TASK NAME (current row se)
        const taskName = $(this).closest('tr').find('td:first').text();
        $('#edit-task-name').val(taskName || 'Task Name');

        // ✅ DYNAMIC FILE INFO
        if (taskData.file_path) {
            $('#current-file').html(`
                <strong>Current File:</strong> 
                <a href="${ taskData.file_path}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i> View File
                </a>
                <br><small class="text-muted">Upload new file to replace existing one</small>
            `);
        } else {
            $('#current-file').html('<span class="text-muted">No file uploaded</span>');
        }

        // ✅ AGAR NEW TASK HAI TO MODAL TITLE CHANGE KAREN
        if (taskData.id === 'new') {
            $('.modal-title').text('Add Task Details');
        } else {
            $('.modal-title').text('Edit Task');
        }

        $('#editTaskModal').modal('show');
        
    } catch (error) {
        console.error('💥 Error in edit task:', error);
        alert('Error loading task details: ' + error.message);
    }
});

    // ✅ FIXED: EDIT FORM SUBMISSION
    $(document).on('submit', '#edit-task-form', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('📤 Edit form submitted via AJAX');
        
        const formData = new FormData(this);
        
        // ✅ Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

        $.ajax({
            url: '/Assets_Item_maintenance/update_task',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function(response) {
                submitBtn.prop('disabled', false).html(originalText);
                
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                console.log('📥 Edit Response:', result);
                
                if (result.success) {
                    alert('✅ ' + result.message);
                    $('#editTaskModal').modal('hide');
                    // ✅ Refresh DataTable
                    tasksTable.ajax.reload(null, false);
                } else {
                    alert('❌ Error: ' + result.message);
                }
            },
            error: function(xhr, status, error) {
                submitBtn.prop('disabled', false).html(originalText);
                alert('❌ AJAX Error: ' + error);
                console.error('Edit Error:', xhr.responseText);
            }
        });
        
        return false;
    });

    // ✅ MANUAL AJAX TEST BUTTON (Temporary debugging)
    $('body').append(`
        <div style="position: fixed; bottom: 10px; right: 10px; z-index: 9999;">
            <button id="debug-ajax" class="btn btn-warning btn-sm">Test AJAX</button>
        </div>
    `);

    $('#debug-ajax').on('click', function() {
        console.log('🧪 Manual AJAX Test');
        
        $.ajax({
            url:  "/Assets_Item_maintenance/get_tasks_ajax",
            type: "POST",
            data: {
                equipment_id: equipmentId,
                maintenance_id: currentMaintenanceId,
                draw: 1,
                start: 0,
                length: 10
            },
            success: function(response) {
                console.log('✅ Manual AJAX Success:', response);
                if (response.debug) {
                    alert('Debug: ' + JSON.stringify(response.debug));
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Manual AJAX Error:', error);
                alert('AJAX Error: ' + error);
            }
        });
    });
});

