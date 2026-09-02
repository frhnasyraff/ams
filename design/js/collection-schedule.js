
var total_dates = 1;

$(document).ready(function () {

    $("#add-more-dates").on("click", function () {
        var input_frequency = $('#r_frequency_schedule').val();
        input_frequency = input_frequency ? input_frequency : $('#r_frequency_schedule').data('maxmonthlyfrequency');
        if (total_dates >= parseInt(input_frequency)) {
            alert('monthly frequency exceed ' + input_frequency);
            return;
        }
        var html = `
            <div class="d-flex align-items-center">
                <input type="date" class="field mb-2 mr-1 w-100" name="schedule_dates[]">
                <i class="fa fa-times remove-more"></i>
            </div>
        `;
        $("#more-month-dates").append(html);
        total_dates++;
    });

    $("#r_frequency_schedule").on("input", function () {
        total_dates = 1;
        $("#more-month-dates").html('');
    });

    $(document).on("click", ".remove-more", function (evt) {
        $(this).parent().remove();
        total_dates--;
    });

});




