// PI CHART ASSET
$.ajax({
    url: "/preventive_maintenance/preventive_table_list",
    method: "GET",
    dataType: "json",
    success: function (response) {
        console.log("Response Data:", response);

        if (!response || Object.keys(response).length === 0) {
            console.error("Empty response received.");
            return;
        }

        // Update the total count display (total number of assets)
        $("#pie-chart-preventive-total").html(response.summary.total_preventive_count);

        // Prepare data for the chart
        const labels = ["Require Maintenance (Last 30 Days)", "In Progress", "Completed"];
        const data = [
            response.summary.preventive_in_maintenance, // Require Maintenance
            response.summary.preventive_in_progress_count, // In Progress
            response.summary.preventive_complete_count // Completed
        ];
        const backgroundColors = ["#E5C582", "#F8A102", "#523500"];

        // Generate the pie chart
        new Chart(document.getElementById("pie-chart-preventive"), {
            type: "doughnut",
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    hoverBackgroundColor: backgroundColors,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                cutoutPercentage: 60, // Hole in the middle of the doughnut chart
                plugins: {
                    legend: {
                        display: true,
                        position: "right"
                    },
                    title: {
                        display: true,
                        fontColor: "#fff",
                        fontSize: 20,
                        text: "Preventive Maintenance Summary"
                    },
                    datalabels: {
                        
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        
                    },
                }
            }
        });
    }
});
