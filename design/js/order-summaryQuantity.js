$.ajax({
    "url": ((typeof base_url !== 'undefined' ? base_url : '/').replace(/\/+$/, '/') + 'order_summary/getAssetsQuantity'),
    "method": "GET",
    "dataType": "json",

    success: function (response) {
        console.log(response);
        const totalCount = response.equipment_types.reduce((sum, item) => sum + parseInt(item.in_use_count, 10), 0);

        // Clear the previous total count display
        $(`#pie-chart-asset-quantity`).html(totalCount);

        // Prepare data for the chart
        const labels = response.equipment_types.map(item => item.name);
        const data = response.equipment_types.map(item => item.in_use_count);
        const backgroundColors = response.equipment_types.map(item => item.color);

        // Generate the pie chart
        new Chart(prepareSummaryChartCanvas('pie-chart-quantity'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    hoverBackgroundColor: backgroundColors, // ✅ keeps hover colors correct
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutoutPercentage: 60, // ✅ v2 syntax (not cutout)
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
                    text: 'Asset Quantity'
                },
                plugins: {
                    datalabels: { display: false, formatter: (value) => value,
                        color: '#fff',
                    }
                }
            }
        });


        const breakdownList = document.querySelector('#assets-quantity');
        breakdownList.innerHTML = '';

        response.equipment_types.forEach((item) => {
            breakdownList.innerHTML += `
                <div class="breakdown-item summary-metric-pill">
                    <span class="summary-metric-dot" style="background-color: ${item.color || '#38bdf8'};"></span>
                    <span class="type">${item.name}</span>
                    <strong class="total">${item.in_use_count}</strong>
                </div>
            `;
        });

    }
});





