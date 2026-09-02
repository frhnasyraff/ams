$(document).ready(function () {

    $(".add_resource").popover({
        //        trigger: 'focus',
        html: true,
        content: $(".d-none .add_resource_box"),
    });

    $(".add_equipment").popover({
        html: true,
        content: $(".d-none .add_equipment_box"),
    });

    $(".auto_assign").click(function () {
        $(".modal#autoAssignModal input[name='date']").val($(this).data("date"));
    })

    $(".add_gear").popover({
        html: true,
        content: $(".d-none .add_gear_box"),
    });

    $(".card[data-date]").each(function () {
        Sortable.create($(this).find(".equipments.draggable")[0], {
            group: "equipments_" + $(this).attr("data-date"),
            sort: true,
            animation: 200,
            onAdd: function (evt) {
                var parent = $(evt.to).closest(".card");
                var element = $(evt.clone).find("input[type='hidden']");
                if ($(evt.to).find("input[value='" + element.val() + "']").length > 1) {
                    $(evt.item).remove();
                    $(evt.from).append(evt.item);
                    growl("Equipment already exists.", "warning");
                } else {
                    $(evt.to).find("input[name='" + element.attr("name") + "']").attr("name", "equipments[" + parent.attr("data-date") + "][" + parent.attr("data-gang") + "][" + parent.attr("data-shift") + "][]");
                }
            }
        });
        Sortable.create($(this).find(".resources.draggable")[0], {
            group: "resources_" + $(this).attr("data-shift") + "_" + $(this).attr("data-date"),
            sort: true,
            animation: 200,
            onAdd: function (evt) {
                var parent = $(evt.to).closest(".card");
                var element = $(evt.clone).find("input[type='hidden']");
                if ($(evt.to).find("input[value='" + element.val() + "']").length > 1) {
                    $(evt.item).remove();
                    $(evt.from).append(evt.item);
                    growl("Resource already exists in the shift.", "warning");
                } else {
                    $(evt.to).find("input[name='" + element.attr("name") + "']").attr("name", "workers[" + parent.attr("data-date") + "][" + parent.attr("data-gang") + "][" + parent.attr("data-shift") + "][]");
                }
            }
        });
        Sortable.create($(this).find(".gears.draggable")[0], {
            group: "gears_" + $(this).attr("data-date"),
            sort: true,
            animation: 200,
            onAdd: function (evt) {
                var parent = $(evt.to).closest(".card");
                var element = $(evt.clone).find("input[type='hidden']");
                if ($(evt.to).find("input[value='" + element.val() + "']").length > 1) {
                    $(evt.item).remove();
                    $(evt.from).append(evt.item);
                    growl("Gear already exists.", "warning");
                } else {
                    $(evt.to).find("input[name='" + element.attr("name") + "']").attr("name", "gears[" + parent.attr("data-date") + "][" + parent.attr("data-gang") + "][" + parent.attr("data-shift") + "][]");
                }
            }
        });
    });

    $("body").on("change", ".popover-body .form #form_type", function () {
        if ($(this).val()) {
            $(".popover-body .form select[multiple] option").attr("disabled", "disabled");

            $(".popover-body .form select[multiple] option[data-type='" + $(".popover-body .form #form_type").val() + "']").removeAttr("disabled");
            $(".popover-body .form select[multiple]").multiselect('deselectAll', false);
            $(".popover-body .form select[multiple]").multiselect('refresh');
        } else {
            $(".popover-body .form select[multiple] option").removeAttr("disabled");
        }
    })

    $(".add_resource").on("show.bs.popover", function (e) {
        active_date = $(e.target).attr("data-date");
        active_gang = $(e.target).attr("data-gang");
        if ($(".popover-body .multiselect-native-select").length) {
            $(".popover-body .form select[multiple]").multiselect('destroy');
        }
        $(".popover.show").remove();

        $.ajax({
            url: "/service_requests/available_resources_ajax",
            dataType: "json",
            context: document.body,
            type: "POST",
            data: {
                date: active_date
            },
            success: function (s) {
                $(".popover-body .form #form_type").val('');
                if (s.state) {
                    $(".popover-body .add_resource_box .loader").addClass("d-none");
                    $(".popover-body .add_resource_box .form").removeClass("d-none");
                    $(".popover-body .add_resource_box .form select[name='worker_group_id'] option[value!=''], .popover-body .add_resource_box .form select[name='worker_group_id'] option[disabled], .popover-body .add_resource_box .form select[name='worker_id'] option[value!=''], .popover-body .add_resource_box .form select[name='worker_id'] option[disabled]").remove();

                    if (s.groups && s.groups.length) {
                        s.groups.forEach(function (g) {
                            $(".popover-body .add_resource_box .form select[name='worker_group_id']").append('<option value="' + g.worker_group_id + '">' + (g.worker_group_code ? g.worker_group_code : g.worker_group_name) + '</option>');
                        })
                    }
                    if (s.workers && s.workers.length) {
                        s.workers.forEach(function (g) {
                            $(".popover-body .add_resource_box .form select[name='worker_id']").append('<option value="' + g.worker_id + '" data-shift="' + (g.worker_shift ? g.worker_shift : g.worker_group_shift) + '" data-type="' + g.resource_type_id + '" data-colour="' + g.resource_type_colour + '">' + g.worker_name + '</option>');
                        });
                        $(".popover-body .add_resource_box .form select[name='worker_id']").multiselect({
                            maxHeight: 400,
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: true
                        });
                    }

                } else {
                    growl("Could not open link. Please try again.", "danger");
                    $(".loading").addClass("d-none");
                }
            },
            error: function () {
                growl("Could not get data. Please check your network.", "danger");
            }
        });
    });

    $("#worker_groups, #equipment_groups").multiSelect({
        selectableHeader: "Group(s) available",
        selectionHeader: "Assigned to group(s)"
    });

    $(".add_equipment").on("show.bs.popover", function (e) {
        active_date = $(e.target).attr("data-date");
        active_gang = $(e.target).attr("data-gang");
        active_shift = $(e.target).attr("data-shift");
        if ($(".popover-body .multiselect-native-select").length) {
            $(".popover-body .form select[multiple]").multiselect('destroy');
        }
        $(".popover.show").remove();

        $.ajax({
            url: "/service_requests/available_equipments_ajax",
            dataType: "json",
            context: document.body,
            type: "POST",
            data: {
                date: active_date
            },
            success: function (s) {
                if (s.state) {
                    $(".popover-body .add_equipment_box .loader").addClass("d-none");
                    $(".popover-body .add_equipment_box .form").removeClass("d-none");
                    if (s.groups && s.groups.length) {
                        $(".popover-body .add_equipment_box .form select[name='equipment_group_id'] option[value!=''], .popover-body .add_equipment_box .form select[name='equipment_group_id'] option[disabled]").remove();
                        s.groups.forEach(function (g) {
                            $(".popover-body .add_equipment_box .form select[name='equipment_group_id']").append('<option value="' + g.equipment_group_id + '">' + (g.equipment_group_code ? g.equipment_group_code : gequipment_group_name) + '</option>');
                        })
                    }
                    if (s.equipments && s.equipments.length) {
                        $(".popover-body .add_equipment_box .form select[name='equipment_id'] option[value!=''], .popover-body .add_equipment_box .form select[name='equipment_id'] option[disabled]").remove();
                        s.equipments.forEach(function (g) {

                            $(".popover-body .add_equipment_box .form select[name='equipment_id']").append('<option value="' + g.equipment_id + '" data-shift="' + (g.worker_shift ? g.worker_shift : g.worker_group_shift) + '" data-shortcode="' + g.equipment_type_short_code + '" data-colour="' + g.equipment_type_colour + '">' + g.equipment_type_short_code + " - " + g.equipment_name + '</option>');
                        });
                        $(".popover-body .add_equipment_box .form select[name='equipment_id']").multiselect({
                            maxHeight: 400,
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: true
                        });
                    }

                } else {
                    growl("Could not open link. Please try again.", "danger");
                    $(".loading").addClass("d-none");
                }
            },
            error: function () {
                growl("Could not get data. Please check your network.", "danger");
            }
        });
    });

    $(".add_gear").on("show.bs.popover", function (e) {
        active_date = $(e.target).attr("data-date");
        active_gang = $(e.target).attr("data-gang");
        active_shift = $(e.target).attr("data-shift");
        if ($(".popover-body .multiselect-native-select").length) {
            $(".popover-body .form select[multiple]").multiselect('destroy');
        }
        $(".popover.show").remove();

        $.ajax({
            url: "/service_requests/available_gears_ajax",
            dataType: "json",
            context: document.body,
            type: "POST",
            data: {
                date: active_date
            },
            success: function (s) {
                if (s.state) {
                    $(".popover-body .add_gear_box .loader").addClass("d-none");
                    $(".popover-body .add_gear_box .form").removeClass("d-none");

                    if (s.gears && s.gears.length) {
                        $(".popover-body .add_gear_box .form select[name='gear_id'] option[value!=''], .popover-body .add_gear_box .form select[name='gear_id'] option[disabled]").remove();
                        s.gears.forEach(function (g) {
                            $(".popover-body .add_gear_box .form select[name='gear_id']").append('<option value="' + g.gear_id + '" data-colour="' + g.gear_type_colour + '">' + g.gear_name + '</option>');
                        });

                        $(".popover-body .add_gear_box .form select[name='gear_id']").multiselect({
                            maxHeight: 400,
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: true
                        });
                    }

                } else {
                    growl("Could not get data. Please check your network.", "danger");
                    $(".loading").addClass("d-none");
                }
            },
            error: function () {
                growl("Could not get data. Please check your network.", "danger");
            }
        });
    });

    $(".clear_day").click(function (c) {
        $(this).parent().parent().find(".badge.badge-pill.d-inline-block").remove();
    });
    $(".copy_day").click(function (e) {
        e.preventDefault();
        var today = $(this).parent().parent();
        var tomorrow = today.next();
        today.find(".dropzone").each(function () {
            tomorrow.find(".dropzone[data-id='" + $(this).attr("data-id") + "']").html($(this).html());
        });
    })

    $(".card-body").on("click", "span.delete", function () {
        $(this).parent().remove();
    })

    $(".resources_counts .workers .badge").each(function () {
        resources_required[$(this).data("resource")] = $(this).data("resource-count");
    });
    $(".resources_counts .equipments .badge").each(function () {
        equipments_required[$(this).data("resource")] = $(this).data("resource-count");
    });
});

var resources_required = {};
var equipments_required = {};
var active_date;
var active_gang;
var active_shift;

function add_resource_groups() {
    if (active_date) {
        if ($(".popover-body select#form_worker_group_id").val()) {
            $.ajax({
                url: "/service_requests/available_resources_ajax",
                dataType: "json",
                context: document.body,
                type: "POST",
                data: {
                    group: $(".popover-body select#form_worker_group_id").val(),
                    date: active_date
                },
                success: function (s) {
                    if (s.state) {
                        var gangs = $(".card-body[data-dateshift='" + active_date + "-1'] .resources").length;
                        var current_gang = 1;
                        s.workers.forEach(function (worker, i) {
                            setTimeout(function () {
                                if ((worker.worker_shift || worker.worker_group_shift) && !$(".card-body[data-dateshift='" + active_date + "-" + (worker.worker_shift ? worker.worker_shift : worker.worker_group_shift) + "'] small[data-worker='" + worker.worker_id + "']").length && $(".card-body[data-dateshift='" + active_date + "-" + (worker.worker_shift ? worker.worker_shift : worker.worker_group_shift) + "'] small[data-worker-type='" + worker.resource_type_id + "']").length < resources_required[worker.resource_type_id]) {

                                    $(".card-body[data-dateshift='" + active_date + "-" + (worker.worker_shift ? worker.worker_shift : worker.worker_group_shift) + "'] .resources:eq(" + (current_gang - 1) + ")").append('<small class="badge badge-pill badge-info mr-1 d-inline-block" data-worker="' + worker.worker_id + '" data-worker-type="' + worker.resource_type_id + '" style="background: ' + worker.resource_type_colour + '"><i class="fas fa-fw fa-user-cog"></i> ' + worker.worker_name + ' <span class="delete"><i class="fa fa-trash"></i></span><input type="hidden" name="workers[' + active_date + '][' + current_gang + '][' + (worker.worker_shift ? worker.worker_shift : worker.worker_group_shift) + '][]" value="' + worker.worker_id + '" /></small>');
                                }
                                current_gang++;
                                if (current_gang > gangs) {
                                    current_gang = 1;
                                }
                            }, 50 * i);
                        })
                    }
                }
            });
        }
        if ($(".popover-body select#form_worker_id").val()) {
            $(".popover-body select#form_worker_id").val().forEach(function (w) {
                if ($(".popover-body select#form_worker_id option[value='" + w + "']").attr("data-shift") && !$(".card[data-date='" + active_date + "'][data-shift='" + $(".popover-body select#form_worker_id option[value='" + w + "']").attr("data-shift") + "'][data-gang='" + active_gang + "'] small[data-worker='" + w + "']").length) {
                    $(".card[data-date='" + active_date + "'][data-shift='" + $(".popover-body select#form_worker_id option[value='" + w + "']").attr("data-shift") + "'][data-gang='" + active_gang + "'] .resources").append('<small class="badge badge-pill badge-info mr-1 d-inline-block" data-worker="' + w + '" style="background: ' + $(".popover-body select#form_worker_id option[value='" + w + "']").attr("data-colour") + '"><i class="fas fa-fw fa-user-cog"></i> ' + $(".popover-body select#form_worker_id option[value='" + w + "']").html() + ' <span class="delete"><i class="fa fa-trash"></i></span><input type="hidden" name="workers[' + active_date + '][' + active_gang + '][' + $(".popover-body select#form_worker_id option[value='" + w + "']").attr("data-shift") + '][]" value="' + w + '" /></small>');
                }
            })
        }
        if ($(".popover-body .multiselect-native-select").length) {
            $(".popover-body .form select[multiple]").multiselect('destroy');
        }
        $(".popover.show").remove();
    }
}

function add_equipment_groups() {
    if (active_date) {
        if ($(".popover-body select#form_equipment_group_id").val()) {
            $.ajax({
                url: "/service_requests/available_equipments_ajax",
                dataType: "json",
                context: document.body,
                type: "POST",
                data: {
                    group: $(".popover-body select#form_equipment_group_id").val(),
                    date: active_date
                },
                success: function (s) {
                    if (s.state) {
                        s.equipments.forEach(function (equipment) {

                            if (!$(".card[data-date='" + active_date + "'][data-shift='" + active_shift + "'][data-gang='" + active_gang + "'] small[data-equipment='" + equipment.equipment_id + "']").length && $(".card[data-date='" + active_date + "'][data-shift='" + active_shift + "'][data-gang='" + active_gang + "'] small[data-worker-type='" + equipment.equipment_type_id + "']").length < equipments_required[equipment.equipment_type_id]) {
                                $(".card[data-date='" + active_date + "'][data-shift='" + active_shift + "'][data-gang='" + active_gang + "'] .equipments").append('<small class="badge badge-pill badge-info mr-1 d-inline-block" data-equipment-type="' + equipment.equipment_type_id + '" data-equipment="' + equipment.equipment_id + '" style="background: ' + equipment.equipment_type_colour + '"><i class="fas fa-fw fa-tools"></i> ' + equipment.equipment_type_short_code + " - " + equipment.equipment_name + ' <span class="delete"><i class="fa fa-trash"></i></span><input type="hidden" name="equipments[' + active_date + '][' + active_gang + '][' + active_shift + '][]" value="' + equipment.equipment_id + '" /></small>');
                            }
                        })
                    }
                }
            });
        }
        if ($(".popover-body select#form_equipment_id").val()) {
            $(".popover-body select#form_equipment_id").val().forEach(function (w) {

                if (!$(".card[data-date='" + active_date + "'][data-shift='" + active_shift + "'][data-gang='" + active_gang + "'] small[data-equipment='" + w + "']").length) {


                    $(".card[data-date='" + active_date + "'][data-shift='" + active_shift + "'][data-gang='" + active_gang + "'] .equipments").append('<small class="badge badge-pill badge-info mr-1 d-inline-block" data-equipment="' + w + '" data-equipment-type="' + $(".popover-body select#form_equipment_id option[value='" + w + "']").attr("data-equipment-type") + '" style="background: ' + $(".popover-body select#form_equipment_id option[value='" + w + "']").attr("data-colour") + '"><i class="fas fa-fw fa-tools"></i> ' + $(".popover-body select#form_equipment_id option[value='" + w + "']").html() + ' <span class="delete"><i class="fa fa-trash"></i></span><input type="hidden" name="equipments[' + active_date + '][' + active_gang + '][' + active_shift + '][]" value="' + w + '" /></small>');

                }
            });
        }
        if ($(".popover-body .multiselect-native-select").length) {
            $(".popover-body .form select[multiple]").multiselect('destroy');
        }
        $(".popover.show").remove();
    }
}

function add_gear_groups() {
    if (active_date) {
        if ($(".popover-body select#form_gear_id").val()) {
            $(".popover-body select#form_gear_id").val().forEach(function (w) {
                if (!$(".card[data-date='" + active_date + "'][data-shift='" + active_shift + "'][data-gang='" + active_gang + "'] small[data-gear='" + w + "']").length) {
                    $(".card[data-date='" + active_date + "'][data-shift='" + active_shift + "'][data-gang='" + active_gang + "'] .gears").append('<small class="badge badge-pill badge-info mr-1 d-inline-block" data-gear="' + w + '" style="background: ' + $(".popover-body select#form_gear_id option[value='" + w + "']").attr("data-colour") + '"><i class="fas fa-fw fa-cogs"></i> ' + $(".popover-body select#form_gear_id option[value='" + w + "']").html() + ' <span class="delete"><i class="fa fa-trash"></i></span><input type="hidden" name="gears[' + active_date + '][' + active_gang + '][' + active_shift + '][]" value="' + w + '" /></small>');
                }
            });
        }

        if ($(".popover-body .multiselect-native-select").length) {
            $(".popover-body .form select[multiple]").multiselect('destroy');
        }
        $(".popover.show").remove();
    }
}