var year;
var month;
var d = new Date();
year = d.getFullYear();
month = d.getMonth() + 1;
$(document).ready(function () {
	if (typeof workers_data != 'undefined') {
		google.charts.load('current', {
			packages: ['corechart']
		});
		google.charts.setOnLoadCallback(drawChart);
	}

	vessel_turn_around_time = vessel_turnaround_time_table(year, month);

	





	$('#service_requests').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: true,
		dom: 'tip',
		pageLength: 5,
		stateSave: true,
		ajax: {
			url: '/service_requests/active_ajax_list',
			type: 'POST',
			error: function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert('We are having trouble connecting to the API.');
				}
			}
		},
		drawCallback: initToggle,
		order: [[3, 'desc']],
		columns: [
			{
				data: 'service_request_number',
				createdCell: function (td, cellData, rowData, row, col) {
					if (!$('table.read-only').length) {
						$(td).html(
							'<a href="/service_requests/info?id=' +
							id_encode(rowData.service_request_id) +
							'" title="View SSR">' +
							cellData +
							'</a>'
						);
					}
				}
			},
			{
				data: null,
				createdCell: function (td, cellData, rowData) {
					$(td).html(rowData.wharf_id ? rowData.visit_scn + ' - ' + rowData.wharf_id : rowData.company_name);
				}
			},
			{
				data: 't_added',
				createdCell: function (td, cellData, rowData) {
					$(td).html(moment(cellData).format('DD-MM-Y'));
				}
			},

			{
				data: 'service_request_status',
				createdCell: function (td, cellData) {
					$(td).addClass().html(service_request_icons(cellData));
				}
			}
		]
	});

	equipments = $('#equipments').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: true,
		dom: 'tip',
		pageLength: 5,
		stateSave: true,
		ajax: {
			url: '/equipments/active_ajax_list',
			type: 'POST',
			error: function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert('We are having trouble connecting to the API.');
				}
			},
			data: function (d) {
				return $.extend({}, d, {
					equipment_type: $('.equipments_filter .btn-primary').data('filter')
				});
			}
		},
		drawCallback: initToggle,
		order: [[3, 'desc']],
		columns: [
			{
				data: 'operation_date',
				createdCell: function (td, cellData, rowData) {
					$(td).html(moment(cellData).format('DD-MM-Y'));
				}
			},
			{
				data: 'equipment_name',
				createdCell: function (td, cellData, rowData, row, col) {
					$(td).html(
						'<a href="/equipments/info?id=' +
						id_encode(rowData.equipment_id) +
						'" title="View equipment">' +
						cellData +
						'</a>'
					);
				}
			},
			{
				data: 'gang'
			},
			{
				data: 'shift'
			},
			{
				data: 'wharf_id'
			}
		]
	});

	workers = $('#workers').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: true,
		dom: 'tip',
		pageLength: 10,
		stateSave: true,
		ajax: {
			url: '/workers/active_ajax_list',
			type: 'POST',
			error: function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert('We are having trouble connecting to the API.');
				}
			},
			data: function (d) {
				return $.extend({}, d, {
					worker_type: $('.workers_filter .btn-primary').data('filter')
				});
			}
		},
		drawCallback: initToggle,
		order: [[3, 'desc']],
		columns: [
			{
				data: 'operation_date',
				createdCell: function (td, cellData, rowData) {
					$(td).html(moment(cellData).format('DD-MM-Y'));
				}
			},
			{
				data: 'worker_name',
				createdCell: function (td, cellData, rowData, row, col) {
					$(td).html(
						'<a href="/workers/info?id=' + id_encode(rowData.worker_id) + '" title="View worker">' + cellData + '</a>'
					);
				}
			},
			{
				data: 'gang'
			},
			{
				data: 'shift'
			},
			{
				data: 'wharf_id'
			}
		]
	});

	vessel_visits = $('#vessel_visits').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: true,
		dom: 'tip',
		pageLength: 10,
		stateSave: true,
		ajax: {
			url: '/vessel_visits/active_ajax_list',
			type: 'POST',
			error: function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert('We are having trouble connecting to the API.');
				}
			},
			data: function (d) {
				return $.extend({}, d, {
					visits_days: $('.vessels_filter .btn-primary').data('filter')
				});
			}
		},
		drawCallback: initToggle,
		order: [[0, 'asc']],
		columns: [
			{
				data: 'visit_eta',
				createdCell: function (td, cellData, rowData, row, col) {
					if (rowData.planning_status) {
						$(td).html(
							'<a href="/vessel_visits/performance?id=' +
							id_encode(rowData.vessel_visit_id) +
							'" title="View plan">' +
							cellData +
							'</a>'
						);
					} else {
						$(td).html(
							'<a href="/vessel_visits/info?id=' +
							id_encode(rowData.vessel_visit_id) +
							'" title="View plan">' +
							cellData +
							'</a>'
						);
					}
				}
			},
			{
				data: 'visit_scn',
				createdCell: function (td, cellData, rowData, row, col) {
					$(td).html(
						'<a href="../vessel_visits/info?id=' +
						id_encode(rowData.vessel_visit_id) +
						'">' +
						(cellData ? cellData : rowData.vessel_name) +
						'</a>'
					);
				}
			},
			{
				data: 'port_name'
			},
			{
				data: 'vessel_wharf_name'
			},
			{
				data: 'planning_status',
				createdCell: function (td, cellData) {
					$(td).addClass().html(service_request_icons(cellData));
				}
			}
		]
	});

	commodities_tonnage = $('#commodities_tonnage').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: true,
		dom: 'tip',
		pageLength: 5,
		stateSave: true,
		ajax: {
			url: '/commodities/active_ajax_list',
			type: 'POST',
			error: function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert('We are having trouble connecting to the API.');
				}
			},
			data: function (d) {
				return $.extend({}, d, {
					commodities_months: $('.commodities_filter .btn-primary').data('filter')
				});
			}
		},
		drawCallback: initToggle,
		columns: [
			{
				data: 'commodity_code'
			},
			{
				data: 'total_tonnage'
			}
		]
	});

	
	$.fn.dataTable.ext.errMode = 'none';

	$('.workers_filter .btn').click(function () {
		$('.workers_filter .btn-primary').removeAttr('disabled');
		$('.workers_filter .btn-primary').removeClass('btn-primary');
		$(this).addClass('btn-primary').attr('disabled', 'disabled');
		workers.ajax.reload();
	});

	$('.vessels_filter .btn').click(function () {
		$('.vessels_filter .btn-primary').removeAttr('disabled');
		$('.vessels_filter .btn-primary').removeClass('btn-primary');
		$(this).addClass('btn-primary').attr('disabled', 'disabled');
		vessel_visits.ajax.reload();
	});

	$('.equipments_filter .btn').click(function () {
		$('.equipments_filter .btn-primary').removeAttr('disabled');
		$('.equipments_filter .btn-primary').removeClass('btn-primary');
		$(this).addClass('btn-primary').attr('disabled', 'disabled');
		equipments.ajax.reload();
	});

	$('.commodities_filter .btn').click(function () {
		$('.commodities_filter .btn-primary').removeAttr('disabled');
		$('.commodities_filter .btn-primary').removeClass('btn-primary');
		$(this).addClass('btn-primary').attr('disabled', 'disabled');
		months = $('.commodities_filter .btn-primary').data('filter');
		commodities_tonnage.ajax.reload();
		generateTonnagePieChart(months);
	});

	$('.vessel_turn_around_filter .btn').click(function () {
		$('.vessel_turn_around_filter .btn-primary').removeAttr('disabled');
		$('.vessel_turn_around_filter .btn-primary').removeClass('btn-primary');
		$(this).addClass('btn-primary').attr('disabled', 'disabled');
		months = $('.vessel_turn_around_filter .btn-primary').data('filter');
		vessel_turn_around_time.ajax.reload();
	});

	$('input.date_picker_now, div.date_picker_now input').datepicker({
		dateFormat: 'dd/mm/yyyy',
		timepicker: false,
		maxDate: new Date()
	});

	$('.incidentlist-report a:first').click();

	$.ajax({
		method: "GET",
		url: "/dashboard/getdelayreport",
		success: function (response) {
			$('#getdelaydata').html(response);
			$('#getdelaydatatable').DataTable( {
				"paging": true,
				"pageLength": 10,
				"order":[[1, "desc"]]
			});
		}
	});
	
});

function drawChart() {
	var chart = new google.visualization.PieChart(document.getElementById('workerschart'));
	chart.draw(google.visualization.arrayToDataTable(workers_data), {
		title: 'Workers utilization today',
		legend: 'none'
	});
	chart = new google.visualization.PieChart(document.getElementById('equipmentschart'));
	chart.draw(google.visualization.arrayToDataTable(equipments_data), {
		title: 'Equipments utilization today',
		legend: 'none'
	});
	generateTonnagePieChart((months = 1));
}
var vessel_visits;
var equipments;
var workers;
var vessel_turn_around_time;

function generateTonnagePieChart(months) {
	$.ajax({
		type: 'post',
		url: '/commodities/pie_chart_ajax_list',
		dataType: 'JSON',
		error: function (xhr, error, thrown) {
			if (xhr.responseJSON && xhr.responseJSON.redirect) {
				window.location.href = xhr.responseJSON.redirect;
			} else {
				alert('We are having trouble connecting to the API.' + error);
			}
		},
		data: {
			commodities_months: months
		},
		success: commodityPieChart
	});
}

function commodityPieChart(series) {
	var tonnage_commodity_data = [['commodity_code', 'total_tonnage']];
	if (series.data && series.data.length > 0) {
		series.data.forEach((ele) => {
			tonnage_commodity_data.push([ele.commodity_code, Math.round(ele.total_tonnage)]);
		});

		chart = new google.visualization.PieChart(document.getElementById('tonnageByCommodityPieChart'));
		chart.draw(google.visualization.arrayToDataTable(tonnage_commodity_data), {
			title: 'Tonnage by Commodity',
			legend: 'none'
		});
	} else {
		$('#tonnageByCommodityPieChart').prepend('<div class="no_data">Tonnage Data not Available</p>');
		if ($('#tonnageByCommodityPieChart').children('div').eq(1)) {
			$('#tonnageByCommodityPieChart').children('div').eq(1).hide();
		}
	}
}

function vessel_turnaround_time_table(year, month) {
	return $('#vessel_turn_around_time').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: true,
		dom: 'tip',
		pageLength: 10,
		stateSave: true,
		ajax: {
			url: '/vessel_visits/vessel_turn_around_time_ajax_list',
			type: 'POST',
			error: function (xhr, error, thrown) {
				if (xhr.responseJSON && xhr.responseJSON.redirect) {
					window.location.href = xhr.responseJSON.redirect;
				} else {
					alert('We are having trouble connecting to the API.');
				}
			},
			data: function (d) {
				return $.extend({}, d, {
					vessel_turn_around_months: $('.vessel_turn_around_filter .btn-primary').data('filter')
					// year: year,
					// month: month
				});
			}
		},
		drawCallback: initToggle,
		order: [[3, 'desc']],
		columns: [
			{
				data: 'vessel_name'
			},
			{
				data: 'operation_started'
			},
			{
				data: 'total_manpower'
			},
			{
				data: 'total_equipments'
			},
			{
				data: 'total_gears'
			},
			{
				data: 'commodity_code'
			},
			{
				data: 'total_quantity'
			},
			{
				data: 'turn_working_time'
			}

			// {
			// 	"data":"total_delay"
			// }
		]
	});
}






$('.getincidents').click(function () {
	$id = $(this).data("id");
	$('.getincidents').removeClass('active');
	$(this).addClass('active')
	$.ajax({
		method: "POST",
		url: "/dashboard/getincidentinfo",
		data: { 'id': $id },
		dataType: 'json',
		success: function (response) {
			if (response.status == 1) {		
				filepath = "";
				if(response.data.hasOwnProperty('filename'))
				{
					filepath = "/storage/INCIDENT-"+response.data.incident_request_id+"/"+response.data.filename;
				}
				msgDiv = '<div class="row mt-3 ml-2 small"><div class="col-sm-4 small"><h5 class="mb-3 font-weight-bold text-primary"><a  href="/incidents/info?id=' +id_encode(response.data.incident_request_id) +'">'+ ((response.data.vessel_name == null || response.data.vessel_name == "null") ? " - " : response.data.vessel_name) +'</a></h5>';
				msgDiv = msgDiv + '<h6 class="m-0 font-weight-bold"><a  href="/incidents/info?id=' +id_encode(response.data.incident_request_id) +'">'+response.data.worker_location_name +'</a></h6>';
				msgDiv = msgDiv + '<p>Location Of Incident</p><h6 class="m-0 font-weight-bold">' + response.data.incident_datetime +'</h6>';
				msgDiv = msgDiv + '<p>Time Of Incident</p> <h6 class="m-0 font-weight-bold">'+response.data.incident_type +'</h6><p>Type Of Incident</p></div>';
				msgDiv = msgDiv + '<div class="col-sm-6">';
				if(filepath != "")
				{
					msgDiv = msgDiv + '<img class="mt-3 border border-dark" height="150" width="150" src="'+ filepath +'" alt="">'; 
				}
				msgDiv = msgDiv + '</div></div>';
			}
			else {
				msgDiv = '<div class="err alert alert-danger">' + failed + '</div>';
			}
			$('#getincidentsdata').html(msgDiv);
		}
	})
});

