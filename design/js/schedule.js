$(function () {

    function maintenanceUrl(path) {
        const parts = window.location.pathname.split('/').filter(Boolean);
        const base = parts.length > 1 ? '/' + parts[0] : '';
        return base + '/' + String(path).replace(/^\//, '');
    }

    const parameters = new URLSearchParams(window.location.search);
    var filter = parameters.get('filter');
    if (filter == null) {
        filter = 'corrective';
    }
    
    console.log("Selected Filter:", filter);
    if (filter === 'preventive') {
        $(".wrapper:has(.ticket-number)").hide();
        $(".wrapper:has(.issue-date)").hide();
    }else{
        $(".wrapper:has(.reminder-date)").hide();
    }
    var calendarEl = document.getElementById('fullcalendar');

    // new order Event Source
    var plannedOrderEvents = {
        id: 1,
        backgroundColor: "#c82222",
        borderColor: "#c82222",
        events: []
    };

    // progress order Event Source
    var progressOrderEvents = {
        id: 2,
        backgroundColor: "#08AEDE",
        borderColor: "#08AEDE",
        events: []
    };

    // completed order Event Source
    var completedOrderEvents = {
        id: 3,
        backgroundColor: "#1FC84E",
        borderColor: "#1FC84E",
        events: []
    };

    var config = {
        headerToolbar: {
            left: 'timeGridDay,timeGridWeek,dayGridMonth',
            center: 'title',
            right: "prev,today,next",
        },
        buttonText: {
            today: 'Today',
            month: 'Month',
            week: 'Week',
            day: 'Day'
        },
        initialView: 'dayGridMonth',
        timeZone: 'UTC',
        fixedWeekCount: false,
        dayMaxEvents: 3,
        hiddenDays: [],
        displayEventTime: false,
        eventDisplay: 'list-item',
        navLinks: true,
        events: [],
        eventSources: [],
        datesSet: function (data) {
            var date = new Date(data.view.getCurrentData().currentDate);
            var current_year = date.getUTCFullYear();
            var current_month = date.getMonth() + 1;
            getMonthlyOrders(current_year, current_month);
            getEvents(current_year, current_month);
        },
        eventClick: function (info) {
            console.log("Event clicked:", info.event);
            console.log("Event data:", info.event.extendedProps);
            
            var data = info.event.extendedProps.data;
            if (!data) {
                console.error("No data found in event");
                return;
            }

            var statusClass = "";
            var statusText = "";
            var modalId = "";

            // Status mapping – now PENDING uses the planned (red) modal
            switch (data.final_status) {
                case "IN-MAINTENANCE":
                    statusClass = "planned";
                    statusText = "In Maintenance";
                    modalId = "#plannedOrderScheduleModal";
                    break;
                case "PENDING":
                    statusClass = "planned";      // red
                    statusText = "PENDING";
                    modalId = "#plannedOrderScheduleModal"; // red, not progress
                    break;
                case "in_progress":
                    statusClass = "progresss";
                    statusText = "In-Progress";
                    modalId = "#progressOrderScheduleModal";
                    break;
                case "complete":
                    statusClass = "completed";
                    statusText = "Completed";
                    modalId = "#completedOrderScheduleModal";
                    break;
                default:
                    statusClass = "unknown";
                    statusText = "Unknown Status";
                    modalId = "#progressOrderScheduleModal";
            }

            // ✅ Get the correct date
            var issueDate = data.maintenance_date || data.issue_date || 'N/A';
            var reminderDate = data.interval || data.reminder_date || data.update_date || 'N/A';
            var ticketNumber = data.ticket_number || 'PM-' + (data.equipment_id || 'N/A');

            // Populate modal fields
            $(modalId + " .status").text(statusText);
            $(modalId + " .ticket-number").text(ticketNumber);
            $(modalId + " .issue-date").text(issueDate);
            $(modalId + " .reminder-date").text(reminderDate);
            $(modalId + " .equipment-name").text(data.equipment_name || 'N/A');
            $(modalId + " .equipment-type-name").text(data.equipment_type_name || 'N/A');
            $(modalId + " .equipment-registration").text(data.equipment_registration || 'N/A');
            $(modalId + " .store-location-name").text(data.store_location_name || 'N/A');

            // ✅ Update the card’s CSS class to reflect the correct color
            var card = $(modalId).find('.schedule-card');
            card.removeClass('planned progresss completed');
            card.addClass(statusClass);

            // ✅ Details button URL
            var detailsUrl = '#';
            if (data.equipment_id && data.equipment_maintenance_id) {
                detailsUrl = maintenanceUrl('Assets_Item_maintenance/task_details/' + data.equipment_id + '/' + data.equipment_maintenance_id);
                console.log('🔗 Generated URL:', detailsUrl);
            }

            var detailsButton = $(modalId).find('.details-btn');
            if (detailsButton.length > 0 && detailsUrl !== '#') {
                detailsButton.attr('href', detailsUrl);
                detailsButton.show();
            } else {
                detailsButton.hide();
            }

            // ✅ Items display
            var itemsContainer = $(modalId + " .item-box");
            itemsContainer.html("");

            if (data.items && data.items.length > 0) {
                var tableHtml = `
                    <table class='item-table'>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Manufacturer</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                data.items.forEach((item, index) => {
                    var itemName = item.item_name || "No Name";
                    var manufacturerName = item.manufacturer_name || "No Manufacturer";

                    tableHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${itemName}</td>
                            <td>${manufacturerName}</td>
                        </tr>
                    `;
                });

                tableHtml += `</tbody></table>`;
                itemsContainer.append(tableHtml);
            } else {
                itemsContainer.html("<p>No Items Found</p>");
            }

            $(modalId).modal("show");
        }, 
        
        dateClick: function (info) {
            var selectedDate = info.dateStr;
            $.ajax({
                url: maintenanceUrl('Assets_Item_maintenance/getDateOrders'),
                method: 'GET',
                data: {
                    date: selectedDate,
                    filter: filter
                },
                success: function (response) {
                    renderAgenda(response);
                }
            });
        }
    };

    function renderAgenda(response) {
        var html = typeof response === 'string' ? response.trim() : '';
        if (html) {
            $("#date-orders-container").html(html);
            return;
        }

        $("#date-orders-container").html(
            '<div class="maintenance-empty-agenda">' +
                '<i class="far fa-calendar-check"></i>' +
                '<strong>No maintenance scheduled</strong>' +
                '<span>Select another date or change the schedule type.</span>' +
            '</div>'
        );
    }




    function getMonthlyOrders(current_year, current_month) {
        $.ajax({
            url: maintenanceUrl('Assets_Item_maintenance/getMonthlyOrders'),
            method: 'GET',
            data: {
                current_year: current_year,
                current_month: current_month,
                filter: filter
            },
            success: function (response) {
                renderAgenda(response);
            }
        });
    }

    getMonthlyOrders(new Date().getFullYear(), new Date().getMonth() + 1);
    
    function getEvents(current_year, current_month) {
    $.ajax({
        url: maintenanceUrl('Assets_Item_maintenance/getEvents'),
        method: 'GET',
        dataType: 'json',
        async: false,
        data: {
            current_year: current_year,
            current_month: current_month,
            'filter': filter
        },
        success: function (response) {
            var plannedOrders = response.plannedOrders;
            var progresOrders = response.progressOrders;
            var completedOrders = response.completedOrders;

            plannedOrderEvents.events = plannedOrders;
            progressOrderEvents.events = progresOrders;
            completedOrderEvents.events = completedOrders;

            config.eventSources.push(plannedOrderEvents);
            config.eventSources.push(progressOrderEvents);
            config.eventSources.push(completedOrderEvents);
        }
    });
}
getEvents(new Date().getFullYear(), new Date().getMonth() + 1);

    // initialize the calendar
    var calendar = new FullCalendar.Calendar(calendarEl, config);
    calendar.render();
});
