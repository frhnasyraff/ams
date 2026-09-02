$.ajax({
    url: "/order_summary/getAssetsUnServiceable",
    method: "GET",
    dataType: "json",

    success: function(response) {
        console.log(response);

        const totalCount = response.total;

        // Update total count display
        $('#pie-chart-asset-faulty').html(totalCount);
        $('#faulty-box h2').text(totalCount);
        $('#asset-summary-faulty-badge').text(totalCount);

        // ✅ Filter equipment types with count > 0
        const filteredTypes = response.equipment_types.filter(item => parseInt(item.equipment_count, 10) > 0);

        // Prepare data for chart
        const labels = filteredTypes.map(item => item.name);
        const data = filteredTypes.map(item => parseInt(item.equipment_count, 10));
        const backgroundColors = filteredTypes.map(item => item.color);

        // Generate the pie chart
        new Chart(prepareSummaryChartCanvas('pie-chart-faulty'), {
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
                    position: 'right',
                    labels: {
                        fontColor: "#000"
                    }
                },
                title: { display: false,
                    fontColor: "#fff",
                    fontSize: 20,
                    text: 'Unserviceable Assets'
                },
                plugins: {
                    datalabels: { display: false, formatter: value => value,
                        color: '#fff'
                    }
                }
            }
        });

        const breakdownList = document.querySelector('#breakdown-list-faulty');
        breakdownList.innerHTML = '';

        const top5 = filteredTypes;

        if (top5.length === 0) {
            breakdownList.innerHTML += `
                <div class="breakdown-item summary-metric-pill summary-metric-empty">
                    <span class="type">No unserviceable assets found.</span>
                    <strong class="total">0</strong>
                </div>
            `;
        } else {
            top5.forEach(item => {
                breakdownList.innerHTML += `
                    <div class="breakdown-item summary-metric-pill">
                        <span class="summary-metric-dot" style="background-color: ${item.color || '#ef4444'};"></span>
                        <span class="type">${item.name}</span>
                        <strong class="total">${item.equipment_count}</strong>
                    </div>
                `;
            });
        }
    }
});





