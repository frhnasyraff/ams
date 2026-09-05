const lurlParams = new URLSearchParams(window.location.search);
const lstart_datee = lurlParams.get('start_date');
const lcolors = window.colorsObject;

// PIE CHART ASSET Location
$.ajax({
    url: ((typeof base_url !== 'undefined' ? base_url : '/').replace(/\/+$/, '/') + 'Order_summary/getAssetsLocation'),
    method: "GET",
    dataType: "json",
    success: function (response) {
        console.log(response);

        // Update the total asset count display (center of donut)
        $('#pie-chart-asset-location').html(response.total_locations);

        // Prepare data for the pie chart
        const labels = response.locations.map(item => item.name);
        const data = response.locations.map(item => Number(item.total_assets));
        const backgroundColors = response.locations.map(item => item.colour || '#888');

        // Generate the pie chart
        new Chart(prepareSummaryChartCanvas('pie-chart-location'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    hoverBackgroundColor: backgroundColors, // ✅ Fix black hover issue
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutoutPercentage: 60, // ✅ v2 syntax
                legend: {
                    display: false,
                    position: 'right',
                    labels: {
                        fontColor: "#000",
                    }
                },
                title: { display: false,
                    fontColor: '#fff',
                    fontSize: 20,
                    text: 'Assets by Location'
                },
                plugins: {
                    datalabels: { display: false, formatter: (value) => value,
                        color: '#fff',
                    }
                }
            }
        });


        const breakdownListLocation = document.querySelector('#breakdown-list-location');
        breakdownListLocation.innerHTML = '';

        response.locations.forEach((item) => {
            breakdownListLocation.innerHTML += `
                <div class="breakdown-item summary-metric-pill">
                    <span class="summary-metric-dot" style="background-color: ${item.colour || '#38bdf8'};"></span>
                    <span class="type">${item.name || 'Unassigned'}</span>
                    <strong class="total">${item.total_assets}</strong>
                </div>
            `;
        });
    }
});





