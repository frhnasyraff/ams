$(document).ready(function() {
    // DataTable initialization
    if ($('.asset-datatable').length > 0) {
        $('.asset-datatable').DataTable({
            "scrollX": true,
            "scrollY": "400px",
            "scrollCollapse": true,
            "paging": false,
            "searching": false,
            "ordering": true,
            "info": false,
            "responsive": true,
            "autoWidth": false,
            "dom": '<"top">rt<"bottom"flp><"clear">',
            "language": {
                "emptyTable": "No data available"
            },
            "columnDefs": [
                { "orderable": false, "targets": 0 }, // Checkbox column
                { "width": "30px", "targets": 0 },
                { "width": "150px", "targets": 1 }, // Asset Name
                { "width": "100px", "targets": 2 }, // Registration
                { "width": "120px", "targets": 3 }, // Serial No.
                { "width": "80px", "targets": 4 }, // Status
                { "width": "100px", "targets": 5 }, // Purchase Date
                { "width": "80px", "targets": 6 }, // Price
                { "width": "100px", "targets": 7 }, // Type
                { "width": "120px", "targets": 8 }  // Location
            ]
        });
    }
    
    // Filter form validation
    $('#filterForm').submit(function(e) {
        const dateFrom = $('input[name="date_from"]').val();
        const dateTo = $('input[name="date_to"]').val();
        
        if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
            alert('End date must be after start date');
            e.preventDefault();
            return false;
        }
        
        return true;
    });
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Auto-select first group's checkboxes
    if ($('.asset-datatable').length > 0) {
        setTimeout(function() {
            $('.group-checkbox:first').trigger('change');
        }, 500);
    }
});
