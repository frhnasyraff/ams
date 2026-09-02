// Define color palette from "Olga Bailey"
const olgaBaileyColors = {
    type1: "#FF5733",
    type2: "#33FF57",
    type3: "#3357FF",
    type4: "#FF33A8",
    type5: "#FFC300",
    // Add additional colors as needed
};

// AJAX request to get store summary data
$.ajax({
    url: "/order_summary/getStoreSummary",
    method: "GET",
    dataType: "json",

    success: function (response) {
        console.log(response);
        const totalCount = response.equipment_types.reduce((sum, item) => sum + parseInt(item.in_use_count, 10), 0);

        // Clear the previous total count display
        $(`#pie-chart-store-summary-total`).html(totalCount);

        // Prepare data for the chart
        const labels = response.equipment_types.map(item => item.name);
        const data = response.equipment_types.map(item => item.in_use_count);

        // Set colors based on Olga Bailey's color palette
        const backgroundColors = response.equipment_types.map((item, index) => {
            return olgaBaileyColors[`type${index + 1}`] || '#cccccc'; // default color if not defined
        });

        // Generate the pie chart
        new Chart(document.getElementById('pie-chart-store-summary'), {
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
                cutoutPercentage: 60,
                legend: {
                    display: false,
                    position: 'right',
                    labels: {
                        fontColor: "#000",
                    }
                },
                title: {
                    display: true,
                    fontColor: "#fff",
                    fontSize: 20,
                    text: 'Store Summary'
                },
                plugins: {
                    datalabels: {
                        formatter: (value) => value,
                        color: '#fff',
                    }
                }
            },
        });

        // Update the breakdown list with labels, counts, and colors
        const breakdownList = document.querySelector('#breakdown-list-store-summary');
        breakdownList.innerHTML = '';

        // Create the heading row for the breakdown list
        breakdownList.innerHTML += `
    <div class="breakdown-item breakdown-headings">
        <div class="type-heading" style="color: #fff;">Type</div>
        <div class="total-heading" style="color: #fff;">Total</div>
        <div class="percent-heading" style="color: #fff;">Color</div>
    </div>
`;

        response.equipment_types.forEach((item, index) => {   // <-- include index here
            const itemColor = olgaBaileyColors[`type${index + 1}`] || '#cccccc';

            const breakdownItem = `
        <div class="breakdown-item">
            <div class="type" style="color: ${itemColor};">${item.name}</div>
            <div class="total">${item.in_use_count}</div>
            <div class="percent" style="background-color: ${itemColor}; color: #fff; padding: 9px;"></div>
        </div>
        <hr>
    `;

            breakdownList.innerHTML += breakdownItem;
        });

    }
});
