$(document).ready(function () {
    // DataTable initialization
    var table = $('#task-list').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 10,
        "stateSave": true,
        "ajax": {
            "url": "Task_list/ajax_list",
            "type": "POST",
            "error": function (xhr, error, thrown) {
                console.error("DataTables error:", error, thrown);
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                }
            }
        },
        "order": [[0, "asc"]],
        "columns": [
            {"data": "id"},
            {"data": "name"},
            {"data": "frequency_in_days"},
            {"data": "action", "orderable": false, "searchable": false}
        ],
        "drawCallback": function(settings) {
            // Har draw ke baad event handlers re-attach karo
            initEditButtons();
        }
    });

    // Edit button handler
    function initEditButtons() {
        $(document).off('click', '.editBtn').on('click', '.editBtn', function (e) {
            e.preventDefault();
            
            var id = $(this).data('id');
            var name = $(this).data('name');
            var frequency = $(this).data('frequency');

            console.log("Editing - ID:", id, "Name:", name, "Frequency:", frequency);

            $("#edit_id").val(id);
            $("#edit_name").val(name);
            $("#edit_frequency").val(frequency);

            $("#editModal").modal("show");
        });
    }

    // Add form submission (AJAX)
    $('#addForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'Task_list/add',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#addModal').modal('hide');
                    table.ajax.reload(); // Table refresh
                    $('#addForm')[0].reset(); // Form reset
                }
            }
        });
    });

    // Edit form submission (AJAX)
    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'Task_list/update',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#editModal').modal('hide');
                    table.ajax.reload(); // Table refresh
                }
            }
        });
    });

    // Initial call for edit buttons
    initEditButtons();
});