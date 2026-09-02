var bootstrapToggle = {
	size: "small",
	onstyle: "success",
	offstyle: "danger",
	style: "slow status-text-toggle",
	on: 'Active',
	off: 'Inactive'
};

// Keep pagination consistent across every DataTable in the application.
// `simple_numbers` matches the audit log: Previous, a compact page window,
// an ellipsis when needed, the final page, and Next.
if ($.fn.dataTable) {
	$.extend(true, $.fn.dataTable.defaults, {
		pagingType: 'simple_numbers',
		language: {
			paginate: {
				previous: 'Previous',
				next: 'Next'
			}
		}
	});
}

function standardizeStateActions(scope) {
	var area = scope ? $(scope) : $(document);

	area.find('input[type="checkbox"][data-toggle="toggle"]').each(function () {
		var input = $(this);
		var toggle = input.closest('.toggle');
		if (!toggle.length) return;
		var cell = toggle.closest('td');
		if (!cell.length || !input.is('[data-id]')) return;

		function syncActionLabel() {
			var actionLabel = input.prop('checked') ? 'Deactivate' : 'Activate';
			toggle.attr('aria-label', actionLabel)
				.attr('title', actionLabel + ' this record')
				.attr('data-action-label', actionLabel);
			toggle.find('.toggle-on').text('Deactivate');
			toggle.find('.toggle-off').text('Activate');
		}

		toggle.addClass('status-text-toggle')
			.removeClass('status-switch-toggle');
		syncActionLabel();
		input.off('change.statusTextAction').on('change.statusTextAction', function () {
			syncActionLabel();
		});

		cell.addClass('status-only-action-cell');
		cell.find('a, button').not(toggle).filter(function () {
			var action = (($(this).attr('class') || '') + ' ' + ($(this).attr('title') || '') + ' ' + ($(this).attr('href') || '')).toLowerCase();
			return /delete|trash|remove/.test(action) || $(this).find('.fa-trash, .fa-times').length > 0;
		}).remove();
	});
}

// Some legacy tables initialise their own toggles inside a DataTable draw.
// Convert those controls after every redraw as well as on first page load.
$(document).on('draw.dt', function (event, settings) {
	setTimeout(function () {
		standardizeStateActions(settings && settings.nTableWrapper ? settings.nTableWrapper : document);
	}, 0);
});

$(function () {
	setTimeout(function () {
		standardizeStateActions();
	}, 0);
});

var tinymce_settings = {
	selector: "textarea.tinymce",
	width: '100%',
	mode : "textareas",

	height: 250,
	plugins: "template,link,lists",
	statusbar: false,
	menubar: true,
	inline_styles : false,
	/*templates: [
	   {title: 'Some title 1', description: 'Some desc 1', content: 'My content'},
	   {title: 'Some title 2', description: 'Some desc 2', url: 'development.html'}
	 ],*/
	   toolbar: 'undo redo |  | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat ',

	toolbar: "undo redo| formatselect | cut copy paste | bold italic underline | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | link | forecolor backcolor | removeformat"
};

function pad(num, size) {
	return ('000000000' + num).substr(-size);
}

function initToggle(settings) {
	$(".tooltip.fade.show").remove();

	$('[data-toggle="tooltip"],.tip').tooltip();

	$('[data-toggle="toggle"]').bootstrapToggle(bootstrapToggle).change(function () {
		var that = $(this);
		$.ajax({
			url: "/" + settings.sInstance + "/state_ajax",
			dataType: "json",
			context: document.body,
			type: "POST",
			data: {
				id: that.data("id"),
				active: (that.prop("checked") ? 1 : 0)
			},
			success: function (s) {

				if (s.state) {
					growl((that.prop("checked") ? "Activated" : "Deactivated") + " successfully", "success");
				} else {
					that.prop('checked', (that.prop("checked") ? 0 : 1)).bootstrapToggle('destroy').bootstrapToggle(bootstrapToggle);
					standardizeStateActions(that.closest('td'));
					growl("Could not save changes", "danger");
				}
			},
			error: function () {
				that.bootstrapToggle('toggle');
				growl("Could not save changes", "danger");
			}
		});
	});
	standardizeStateActions();
}

function growl(txt, type) {
	$.bootstrapGrowl(txt, {
		type: type,
		offset: {
			from: 'bottom',
			amount: 20
		},
		align: 'left',
	});
}

function id_encode(str) {
	return btoa("STeVe-" + str);
}

function round(num) {
	return Math.round((num + Number.EPSILON) * 100) / 100
}
$(document).ready(function () {
	$('input[type="text"], textarea').change(function () {
		this.value = $.trim(this.value);
	});
	$('input.uppercase, .uppercase input, .uppercase textarea').change(function () {
		this.value = $.trim(this.value).toUpperCase();
	});
	$('[data-toggle="tooltip"],.tip').tooltip();

	if ($(".copy_clipboard").length) {
		$.fn.modal.Constructor.prototype._enforceFocus = function() {};
				var clipboard = new ClipboardJS('.copy_clipboard');
clipboard.on('success', function(e) {
	console.log(e);
//	e.clearSelection();

	growl("Copied. You can now paste.", "success");
});
clipboard.on('error', function(e) {
	growl("Copy to clipboard failed", "danger");
});
	}
	if ($('input.date_picker, div.date_picker input, div.date_picker_time input, input.date_picker_time, div.date_picker_range input').length) {
		$('input.date_picker, div.date_picker input').datepicker({
			range: false,
			autoClose: true,
			multipleDatesSeparator: ' to ',
			dateFormat: "dd/mm/yyyy",
			inline: false,
			timepicker: false,
			onSelect: function (d, dd) {
				/*if (dd.length > 0) {
					if (dd.length == 2) {
						date_f = dd[0].toDateString();
						date_t = dd[1].toDateString();
					} else {
						date_f = date_t = dd[0].toDateString();
					}
					$(".filter_date.active").removeClass("active");
					bookings.ajax.reload();
					$(".tooltip.show").remove();
				}*/
			}
		});
		$('input.date_picker_time, div.date_picker_time input').datepicker({
			range: false,
			autoClose: true,
			multipleDatesSeparator: ' to ',
			dateFormat: "dd/mm/yyyy",
			timeFormat: "hh:ii",
			timepicker: true,
			inline: false
		});
	}
	if (window.location.hash && $(".nav-item[href='" + window.location.hash + "']").length) {
        $(".nav-item[href='" + window.location.hash + "']").trigger("click");
    } else if (window.location.hash && $("*[data-target='" + window.location.hash + "']").length) {
        $(window.location.hash).modal("show");
    }
    $("#ui-datepicker-div").wrap('<datepicker />');

$("a.remark_link").click(function() {
	$(".loading").removeClass("d-none");
	var that = $(this);
	$.ajax({
		url: "/user/read_message",
		dataType: "json",
		context: document.body,
		type: "POST",
		data: {
			table: that.data("table"),
			record: that.data("record"),
			t: that.data("t")
		},
		success: function (s) {
			if (s.state) {
				window.location.href = that.data("href");
			} else {
				growl("Could not open link. Please try again.", "danger");
				$(".loading").addClass("d-none");
			}
		},
		error: function () {
			$(".loading").addClass("d-none");
			growl("Could not switch branches. Please try again.", "danger");
		}
	});
})

})

function service_requests(v) {
	var service_requests = {new: "New, awaiting approval", approved: "Approved, planning resources", planned: "Planned", draft: "Draft", in_progress: "In progress", completed: "Completed", cancelled: "Cancelled"};
return service_requests[v];
}

function hexToRgb(hex) {
	var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	return result ? {
		r: parseInt(result[1], 16),
		g: parseInt(result[2], 16),
		b: parseInt(result[3], 16)
	} : null;
}

function service_request_icons(v) {
	var service_requests = {new: '<small class="text-dark"><i class="fa fa-file"></i></small>', approved: '<small class="text-warning"><i class="fa fa-check"></i></small>', planned: '<small class="text-info"><i class="fa fa-calendar"></i></small>', in_progress: '<small class="text-success"><i class="fa fa-file"></i></small>', completed: '<small class="text-success"><i class="fa fa-check-double"></i></small>'};
return service_requests[v];
}

function get_merchant_addresses(type) {
    if ($("#" + type).val()) {
        $("." + type + "_addresses").html("Fetching addresses...");

        $.ajax({
            url: "/bookings/party_addresses_ajax",
            dataType: "json",
            context: document.body,
            type: "POST",
            data: {
                merchant: $("#" + type).val()
            },
            success: function (s) {
                if (s.state) {
                    if (!s.addresses.length) {
                        $("." + type + "_addresses").html("No addresses found.");
                    } else {
                        $("." + type + "_addresses").html("");
                    }
                    s.addresses.forEach(function (a) {
                        if (a.address_country) {
                            $("." + type + "_addresses").append('<button type="button" class="btn btn-sm tip ' + type + '_address btn-' + ($("#" + type + "_address").val() == a.merchant_address_id ? "success" : 'link') + '" title="' + a.address_line_1 + '" data-id="' + a.merchant_address_id + '">' + a.address_city + ", " + a.address_country + (a.person_contact ? " (" + a.person_contact + ")" : '') + '</button>');
                        }
                    });
                    if ($("." + type + "_addresses").html() == "") {
                        $("." + type + "_addresses").html("<div class=\"alert alert-danger small\">A valid address is required for the " + type.replace(/_/g, " ") + ". <a href=\"../merchants/info?id=" + id_encode(s.addresses[0].merchant_id_k) + "\" target=\"_blank\">Click here</a> to add a new address.</div>")
                    }
                } else {
                    growl("Could not fetch the party's addresses. Please try again.", "danger");
                }
            },
            error: function () {
                growl("Could not fetch the party's addresses. Please try again.", "danger");
            }
        });
    } else {
        $("#" + type + "_address").val("");
        $("." + type + "_addresses").html("Please choose a valid party from the dropdown");
    }
}
