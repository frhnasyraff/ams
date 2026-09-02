const urlParamss = new URLSearchParams(window.location.search);
const start_datee = urlParamss.get('start_date');
const colorss = window.colorsObject;

// PIE CHART ASSET Maintenance
$.ajax({
    url: "/order_summary/getAssetsMaintenance",
    method: "GET",
    dataType: "json",
    success: function (response) {
        console.log(response);

        function getRandomColor() {
            return '#886002';
        }

        // Totals
        const totalCorrective = response.reduce((sum, item) => sum + Number(item.corrective_maintenance), 0);
        const totalPreventive = response.reduce((sum, item) => sum + Number(item.preventive_maintenance), 0);
        const totalMaintenance = totalCorrective + totalPreventive;

        // Update donut total in center
        $("#pie-chart-asset-maintenance").html(totalMaintenance);
        $("#asset-summary-maintenance-badge").html(totalMaintenance);

        // Chart data
        const labels = response.map(item => item.equipment_type);
        const data = response.map(item =>
            Number(item.corrective_maintenance) + Number(item.preventive_maintenance)
        );
        const backgroundColors = response.map(item => item.color ? item.color : getRandomColor());

        // Generate the pie chart
        new Chart(prepareSummaryChartCanvas('pie-chart-maintenance'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutoutPercentage: 60,
                legend: {
                    display: false,
                    position: 'right'
                },
                title: { display: false,
                    fontColor: '#fff',
                    fontSize: 20,
                    text: 'Asset Maintenance'
                },
                plugins: {
                    datalabels: { display: false, formatter: (value) => value,
                        color: '#fff',
                    }
                }
            }
        });

        const breakdownListMaintenance = document.querySelector('#breakdown-list-maintenance');
        breakdownListMaintenance.innerHTML = '';

        if (response.length === 0) {
            breakdownListMaintenance.innerHTML = `
                <div class="breakdown-item summary-metric-pill summary-metric-empty">
                    <span class="type">No maintenance activity found.</span>
                    <strong class="total">0</strong>
                </div>
            `;
        }

        response.forEach((item) => {
            const corrective = Number(item.corrective_maintenance) || 0;
            const preventive = Number(item.preventive_maintenance) || 0;
            breakdownListMaintenance.innerHTML += `
                <div class="breakdown-item summary-metric-pill">
                    <span class="summary-metric-dot" style="background-color: ${item.color || '#f59e0b'};"></span>
                    <span class="type">${item.equipment_type}</span>
                    <strong class="total">${corrective + preventive}</strong>
                </div>
            `;
        });
    }
});




