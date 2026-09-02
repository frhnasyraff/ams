$(document).ready(function () {
    var color = randomColor();
    $("#color-block").wheelColorPicker('setValue', $("#color-block").val() ? $("#color-block").val() : color);

    $("#color-block").on('colorchange', function (e) {
        var c = hexToRgb($(this).val());
        colour = "rgba(" + c.r + "," + c.g + "," + c.b + ",0.9)";
        $("input#form_short_code").css('background', colour);

    });

    $('input.time_picker, div.time_picker input').datepicker({
        onlyTimepicker: true,
        timepicker: true,
        timeFormat: 'hh:ii',
        minutesStep: 30,
        multipleDatesSeparator: ' - ',
        autoClose: true,
    });

    $(".work_rate, .standby_rate").change(function () {
        var rate_calculation = $(this).parent().parent().find(".rate_calculation");
        var work_rate = $(this).parent().parent().find(".work_rate").val();
        var standby_rate = $(this).parent().parent().find(".standby_rate").val();
        rate_calculation.html("");
        if (work_rate) {
            if (rate_calculation.data("type") == "casual-daily") {
                rate_calculation.append('Worker rate - RM' + (work_rate * 26) + '/mo. & RM' + (work_rate /11 ) + '/hr.<br />');
                rate_calculation.append('Overtime - <span class="overtime_value" data-start="0" data-end="0">Not eligible</span><br />');
                rate_calculation.append('Annual & medical leave - Not eligible<br />');
            } else if (rate_calculation.data("type") == "contract-daily") {
                rate_calculation.append('Worker rate - RM' + (work_rate * 26) + '/mo. & RM' + (work_rate / 8) + '/hr.<br />');
                rate_calculation.append('Overtime before - <span class="overtime_value" data-start="0">Not eligible</span><br />');
                rate_calculation.append('Overtime after - <span class="overtime_value" data-end="3">RM' + (work_rate / 8 * 1.5) + '/hr. up to 3hrs.<br />');
                rate_calculation.append('Pay loss - RM' + (work_rate /8) + '/hr.<br />');
                rate_calculation.append('Annual & medical leave - Not eligible<br />');
            } else if (rate_calculation.data("type") == "contract-monthly" || rate_calculation.data("type") == "permanent-office" || rate_calculation.data("type") == "permanent-ops") {
                rate_calculation.append('Worker rate - RM' + (work_rate * 26) + '/mo.<br />');
                rate_calculation.append('Rest day rate - RM' + (work_rate / 8 * 1) + '/hr.<br />');
                rate_calculation.append('Public holiday rate - RM' + (work_rate / 8 * 3) + '/hr.<br />');
                rate_calculation.append('Overtime before - Not eligible<br />');
                rate_calculation.append('Overtime after - RM' + (work_rate / 8 * 1.5) + '/hr. on normal days.<br />');
                rate_calculation.append('Overtime after - RM' + (work_rate / 8 * 2) + '/hr. on rest day.<br />');
                rate_calculation.append('Overtime after - RM' + (work_rate / 8 * 3) + '/hr. on Public holidays.<br />');
                rate_calculation.append('Pay loss - RM' + (work_rate/8) + '/hr.<br />');
                rate_calculation.append('12 days annual leave & 14 days medical leave<br />');
            } else if (rate_calculation.data("type") == "van-driver") {
                rate_calculation.append('Worker rate - RM' + (work_rate * 26) + '/mo.<br />');
                rate_calculation.append('Rest day rate - RM' + (work_rate / 8 * 2) + '/hr.<br />');
                rate_calculation.append('Public holiday rate - RM' + (work_rate / 8 * 2) + '/hr.<br />');
                rate_calculation.append('Overtime before - RM' + (work_rate / 8 * 1.5) + '/hr. up to 2 hrs. on normal days.<br />');
                rate_calculation.append('Overtime before - RM' + (work_rate / 8 * 2 ) + '/hr. up to 2 hrs. on rest day.<br />');
                rate_calculation.append('Overtime before - RM' + (work_rate / 8 * 3 ) + '/hr. up to 2 hrs. on Public holidays.<br />');
                rate_calculation.append('Overtime after - RM' + (work_rate / 8 * 1.5) + '/hr. up to 5 hrs. on normal days.<br />');
                rate_calculation.append('Overtime after - RM' + (work_rate / 8 * 2) + '/hr. up to 5 hrs. on rest day.<br />');
                rate_calculation.append('Overtime after - RM' + (work_rate / 8 * 3) + '/hr. up to 5 hrs. on Public holidays.<br />');
                rate_calculation.append('Pay loss - RM' + (work_rate/8) + '/hr.<br />');
                rate_calculation.append('12 days annual leave & 14 days medical leave<br />');
            }
        }
        if (standby_rate) {
            if (rate_calculation.data("type") == "casual-daily") {
                rate_calculation.append('Standby rate - RM' + (standby_rate * 26) + '/mo. & RM' + (standby_rate / 8) + '/hr.<br />');
            } else if (rate_calculation.data("type") == "contract-daily") {
                rate_calculation.append('Standby rate - RM' + (standby_rate * 26) + '/mo.<br />');
            }
        }
    });

    setTimeout(function () {
        $(".work_rate, .standby_rate").trigger("change");
    }, 500);
});