$(document).ready(function () {
    tinymce_settings.height = 110;
    tinymce_settings.menubar = false;
    tinymce.init(tinymce_settings);

    $("#effective_date").datepicker({
        dateFormat: "dd/mm/yyyy",
        autoClose: true,
        multipleDatesSeparator: ' to ',
        dateFormat: "dd/mm/yyyy",
        inline: false,
        onSelect: function (d, dd) {
            $('#expiration_date').datepicker().data('datepicker').update('minDate', moment(dd).toDate());
            $('#expiration_date').datepicker().data('datepicker').selectDate(moment(dd).add(1, "months").toDate());
        }
    });

    $(".add_country").click(function () {
        if ($("#typesize_selection").val().length) {
            generate_quotation_country_html($("select#country_1").val(), $("select#country_2").val(), $("#typesize_selection").val());
        } else {
            growl("Please specify a valid type size and then click the plus button", "warning");
        }
    });

    $(".country_sets").on("click", ".delete_port", function () {
        $(this).parent().parent().remove();
    })

    $(".country_sets").on("change", "input.freedays", function () {
        this.value = $.trim(this.value).replace(/([^0-9\+]+)/g, '');
        var numbers = this.value.split("+");
        if (numbers.length > 2) {
            growl("Invalid D&D term. Please try again.", "warning");
            this.value = numbers[0];
        } else if (numbers.length == 2) {
            this.value = parseFloat(numbers[0]) + "+" + parseFloat(numbers[1]);
        }
    });

    $("#customer_search").attr("autocomplete", "off");

    $(".form-group.container_load select").change(function () {
        if ($(this).val() == "Empty") {
            $(this).parent().parent().find(".form-group.dg select").attr("disabled", "disabled");
        } else {
            $(this).parent().parent().find(".form-group.dg select").removeAttr("disabled");
        }
    })
    $(".form-group.dg select").change(function () {
        if ($(this).val() == "") {
            $(this).parent().parent().find(".form-group.container_load select").attr("disabled", "disabled");
        } else {
            $(this).parent().parent().find(".form-group.dg select").removeAttr("disabled");
        }
    })
    $("#customer_search").autocomplete({
        source: "/quotations/customer_search",
        minLength: 2,
        change: function (event, ui) {
            if (!ui.item) {
                $(".form-control#customer_search").addClass("is-invalid").removeClass("is-valid");
                $("#customer_id").val("");
            }
        },
        select: function (event, ui) {
            $(".form-control#customer_search").addClass("is-valid").removeClass("is-invalid");
            $("#customer_id").val(ui.item.id);
        }
    });
    $('#typesize_selection').multiselect({
        nonSelectedText: 'Type sizes',
        enableFiltering: true,
        includeSelectAllOption: true,
        dropUp: true,
        maxHeight: 200
    });

    $(".country_sets").on("click", ".add_country_port", function () {
        var row = $(this).parent().parent().parent().parent().find("tbody tr:last");
        var new_row = row.clone();
        var rand = new Date().getTime();
        new_row.find("input[type='number']").each(function () {
            $(this).val("").attr("name", $(this).attr("name").replace(/(costs\[([^\[\]]+)\])\[([0-9]+)\]/, "$1[" + rand + "]"));
        });
        row.after(new_row);
    });

    $("form").submit(function (e) {
        if ($("form .text-danger, form .text-warning, form .bg-danger, form .bg-warning").length) {
            e.preventDefault();
        } else if (!$("form #customer_id").val()) {
            growl("Please select a valid customer from the drop down.", "danger");
            e.preventDefault();
        } else if (!$(".country_set").length) {
            growl("Please add at least one country.", "danger");
            e.preventDefault();
        }
    });

    $(".trash_can").removeClass("d-none");

    $(".trash_can").droppable({
        accept: ".country_set",
        greedy: true,
        tolerance: "pointer",
        drop: function (event, ui) {
            ui.draggable.remove();
            console.log("Deleted");
        }
    });

    $(".country_sets").sortable();

    var country_set_surcharge;
    $(".country_sets").on("dblclick", ".surcharge.badge", function () {
        $(this).remove();
    })
    $(".country_sets").on("click", "a.add_surcharge", function () {
        // Show the surcharge modal
        $("#surchargeModal input#instance_id").val($(this).data("id"));
        country_set_surcharge = $(this).parent().find(".surcharges");
    });

    $("#surchargeModal .add_surcharge").click(function () {
        country_set_surcharge.append('<span class="badge badge-primary surcharge" title="Double click to delete">' + $("#surchargeModal select#form_charge_id option:selected").html() + ' - ' + $("#surchargeModal #form_surcharge_price").val() + '<input type="hidden" name="' + $("#surchargeModal #instance_id").val() + '[' + $("#surchargeModal select#form_charge_id").val() + ']" value="' + $("#surchargeModal #form_surcharge_price").val() + '" /></span>');

        $("#surchargeModal").modal("hide");
    })
})

function generate_quotation_country_html(origin, destination, typesizes) {
    $(".loading").removeClass("d-none");
    $.ajax({
        url: "/quotations/country_html_ajax",
        dataType: "json",
        context: document.body,
        type: "POST",
        data: {
            origin: origin,
            destination: destination,
            typesizes: typesizes
        },
        success: function (s) {
            $(".loading").addClass("d-none");
            if (s.state) {
                $(".country_sets").append(s.content);
                tinymce.remove();
                tinymce.init(tinymce_settings);
            } else {
                growl("Could not fetch the country's details. Please try again.", "danger");
            }
        },
        error: function () {
            $(".loading").addClass("d-none");
            growl("Could not fetch the country's details. Please try again.", "danger");
        }
    });
}