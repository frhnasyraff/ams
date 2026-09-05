const urlParams = new URLSearchParams(window.location.search);
const start_date = urlParams.get('start_date');
const colors = window.colorsObject;

function showServiceableEmpty() {
    $('#pie-chart-asset-total').html('0');
    const breakdownList = document.querySelector('#breakdown-list-asset-summary');
    if (breakdownList) {
        breakdownList.innerHTML = '<div class="breakdown-item summary-metric-pill summary-metric-empty"><span class="type">No serviceable assets found.</span><strong class="total">0</strong></div>';
    }
}

// PIE CHART FOR SERVICEABLE ASSETS
$.ajax({
    url: ((typeof base_url !== 'undefined' ? base_url : '/').replace(/\/+$/, '/') + 'Order_summary/getAssetsServiceable'),
    method: "GET",
    dataType: "json",

    success: function (response) {
        console.log(response);
        if (!response || !Array.isArray(response.equipment_types)) { showServiceableEmpty(); return; }

        // Update total count display
        $('#pie-chart-asset-total').html(response.total);

        // ✅ Filter only items with count > 0
        const filteredTypes = response.equipment_types.filter(item => item.equipment_count > 0);

        // Prepare chart data
        const labels = filteredTypes.map(item => item.name);
        const data = filteredTypes.map(item => item.equipment_count);
        const backgroundColors = filteredTypes.map(item => item.color);

        // Render pie chart
        new Chart(prepareSummaryChartCanvas('pie-chart-asset'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutoutPercentage: 60,
                legend: {
                    display: false,
                    position: 'right',
                    labels: {
                        fontColor: "#000"
                    }
                },
                title: { display: false,
                    fontColor: "#fff",
                    fontSize: 20,
                    text: 'Asset Serviceable'
                },
                plugins: {
                    datalabels: { display: false, formatter: value => value,
                        color: '#fff'
                    }
                }
            }
        });

        const breakdownList = document.querySelector('#breakdown-list-asset-summary');
        breakdownList.innerHTML = '';

        const allItems = filteredTypes;

        if (allItems.length === 0) {
            breakdownList.innerHTML += `
                <div class="breakdown-item summary-metric-pill summary-metric-empty">
                    <span class="type">No serviceable assets found.</span>
                    <strong class="total">0</strong>
                </div>
            `;
        } else {
            allItems.forEach(item => {
                breakdownList.innerHTML += `
                    <div class="breakdown-item summary-metric-pill">
                        <span class="summary-metric-dot" style="background-color: ${item.color || '#38bdf8'};"></span>
                        <span class="type">${item.name}</span>
                        <strong class="total">${item.equipment_count}</strong>
                    </div>
                `;
            });
        }

    },
    error: showServiceableEmpty
});




$(document).ready(function () {
    $('#home').DataTable({
        "processing": true,
        "responsive": true,
        "autoWidth": true,
        "pageLength": 5,  // Set default to 5
        "lengthMenu": [5, 10, 25, 50],
        "stateSave": false,
        "pagingType": "simple_numbers",
        "ajax": {
            "url": ((typeof base_url !== 'undefined' ? base_url : '/').replace(/\/+$/, '/') + 'order_summary/home_table_data'),
            "type": "GET",
            "error": function (xhr, error, thrown) {
                if (xhr.responseJSON && xhr.responseJSON.redirect) {
                    window.location.href = xhr.responseJSON.redirect;
                } else {
                    alert("We are having trouble connecting to the API.");
                }
            }
        },
        drawCallback: initToggle,
        "order": [
            [1, "asc"]
        ],
        "columns": [
            {
                "data": "equipment_name"
            },

            {
                "data": "state_name"
            },

            {
                "data": "equipment_status"
            }

        ]

    });

});







