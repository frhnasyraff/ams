var ganttData = [];
var service_request_years = [];
var statusColors = Object.freeze({
  draft: '#5f9ea0',
  new: '#5f9ea0',
  planned: '#ff8c00',
  in_progress: '#ff8c00',
  approved: '#00008b',
  completed: '#006400',
  ended: '#006400',
  cancelled: '#8b0000',
  rejected: '#8b0000'
});
var service_requests_list;
var year;
var month;
var d = new Date();
year = d.getFullYear();
month = d.getMonth() + 1;
var statusValue = 'all';
var sr_type = 'all';
if (month < 10) month = '0' + month;

$(document).ready(function () {
  for (var i = d.getFullYear(); i >= d.getFullYear() - 15; i--) {
    var option = '<option value=' + parseInt(i) + '>' + parseInt(i) + '</option>';
    $('[id*=service_request_year_id]').append(option);
  }

  $('#service_request_month_id [value=' + month + ']').attr('selected', 'true');
  $('#service_request_year_id [value=' + year + ']').attr('selected', 'true');
  ajaxCall(statusValue, sr_type, year, month);

  $('.service_request_status_filter .btn').click(function () {
    $('.service_request_status_filter .btn-primary').removeAttr('disabled');
    $('.service_request_status_filter .btn-primary').removeClass('btn-primary');
    $(this).addClass('btn-primary').attr('disabled', 'disabled');
    statusValue = $(this).context.dataset.filter;
    ajaxCall(statusValue, sr_type, year, month);
  });

  $('.service_request_type_filter .btn').click(function () {
    $('.service_request_type_filter .btn-primary').removeAttr('disabled');
    $('.service_request_type_filter .btn-primary').removeClass('btn-primary');
    $(this).addClass('btn-primary').attr('disabled', 'disabled');
    sr_type = $(this).context.dataset.filter;

    ajaxCall(statusValue, sr_type, year, month);
  });
});

function ajaxCall(statusValue, sr_type, year, month) {
  $.ajax({
    url: '/service_requests/ajax_gantt_list',
    type: 'POST',
    dataType: 'json',
    data: {
      service_request_type: sr_type,
      service_request_status: statusValue,
      planning_status: statusValue,
      year: year,
      month: month
    },
    success: generateGanttChart,
    error: function (error) {
      alert('Something is Wrong' + error);
    },
    async: true
  });
}

function generateGanttChart(series) {
  var ganttData = [];

  if (series.data && series.data.length == 0) {
    $('#ganttChart').empty();
    $('#ganttChart').append('<h2>Data Not available..!</h2>');
  } else {
    series.data.forEach((d) => {
      if (d.t_start != null && d.t_end != null) {
        let op_started = d.t_start.split('-');
        let op_ended = d.t_end.split('-');
        let status;
        if (d.service_request_type == 'vessel') {
          status = d.planning_status ? d.planning_status : 'new';
        } else {
          status = d.service_request_status ? d.service_request_status : 'new';
        }

        const nameTit =
          d.vessel_name +
          ',' +
          d.service_request_number +
          ',' +
          d.cargo_type_name +
          ',' +
          d.company_name +
          ',' +
          d.number_gangs +
          ',' +
          d.wharf_name;

        ganttData.push({
          id: d.service_request_id,
          name: d.service_request_type == 'vessel' ? d.vessel_name : 'WH -' + d.location_id,
          title: nameTit,
          start_date: d.t_start,
          end_date: d.t_end,
          status: status,
          series: [
            {
              name: d.service_request_type.toUpperCase(),
              start: new Date(op_started[0], op_started[1] - 1, op_started[2]),
              end: new Date(op_ended[0], op_ended[1] - 1, op_ended[2]),
              color: statusColors[status.toLowerCase()]
            }
          ]
        });
      }
    });
    $('#ganttChart').empty();
    $('#ganttChart').ganttView({
      viewType: 'Y/M',
      data: ganttData,
      slideWidth: 780,
      behavior: {
        draggable: false
      }
    });
  }
}

$(function () {
  $('#service_request_year_id').change(function () {
    year = $(this).val();
    ajaxCall(statusValue, sr_type, year, month);
  });

  $('#service_request_month_id').change(function () {
    month = $(this).val();
    ajaxCall(statusValue, sr_type, year, month);
  });
});

function open_ssr(id) {
  window.location.href = '/info?id=' + id;
}
