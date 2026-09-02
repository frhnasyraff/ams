$(document).ready(function () {
	if ($(".dropzone").length) {
		$(".dropzone").dropzone({
			acceptedFiles: "image/*",
			uploadMultiple: false,
			maxFiles: 1,
			queuecomplete: function (e) {
				window.location.reload();
			}
		});
	}

	$(".reset_font").click(function () {
		$.ajax({
			url: "/user/set_font",
			dataType: "json",
			context: document.body,
			type: "POST",
			data: {
				font: null
			},
			success: function (s) {
	
				if (s.state) {
					growl("Default font cleared.", "success");
					window.location.reload();
				} else {
					growl("Could not save selection.", "danger");
				}
			},
			error: function () {
				growl("Could not save selection.", "danger");
			}
		});
	});

	$('#font').fontselect().change(function () {

		// replace + signs with spaces for css
		var font = $(this).val().replace(/\+/g, ' ');

		// split font into family and weight
		font = font.split(':');

		// set family on paragraphs
		$('#wrapper').css('font-family', font[0]);
		$.ajax({
			url: "/user/set_font",
			dataType: "json",
			context: document.body,
			type: "POST",
			data: {
				font: font[0]
			},
			success: function (s) {
	
				if (s.state) {
					growl("Default font set successfully.", "success");
				} else {
					growl("Could not save selection.", "danger");
				}
			},
			error: function () {
				growl("Could not save selection.", "danger");
			}
		});
	});
	
	$(".colorPicker .color, .colorPicker .reset_color").click(function () {
		var colour;
		if (!$(this).hasClass("reset_color")) {
			colour = $(this).css("background-color").replace(/rgb\((.*)\)/, "rgba($1,0.9)");
			$("style#override").html('.text-primary { color: ' + colour + ' !important; } .bg-gradient-primary { background-color: ' + colour + '; background-image: none; } .btn-primary { background-color: ' + colour + '; border-color: ' + colour + '; }');

			$("#color-block").wheelColorPicker('setValue', colour);

		} else {
			$("style#override").html("");
		}
		set_user_colour(colour);
	});

	$("#color-block").on('colorchange', function (e) {
		var c = hexToRgb($(this).val());
		colour = "rgba(" + c.r + "," + c.g + "," + c.b + ",0.9)";
		$("style#override").html('.text-primary { color: ' + colour + ' !important; } .bg-gradient-primary { background-color: ' + colour + '; background-image: none; } .btn-primary { background-color: ' + colour + '; border-color: ' + colour + '; }');

	});
});
Dropzone.autoDiscover = false;
setInterval(function () {
	if (colour != last_colour) {
		set_user_colour(colour);

	}
}, 3000);

var last_colour = '';
var colour;

function set_user_colour(colour, t = 0) {
	last_colour = colour;
	$.ajax({
		url: "/user/set_color",
		dataType: "json",
		context: document.body,
		type: "POST",
		data: {
			colour: colour
		},
		success: function (s) {

			if (s.state) {
				growl("Default colour set successfully.", "success");
			} else {
				growl("Could not save selection.", "danger");
			}
		},
		error: function () {
			growl("Could not save selection.", "danger");
		}
	});
}