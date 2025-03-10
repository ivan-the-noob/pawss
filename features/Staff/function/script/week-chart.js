document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById('weekSalesChart').getContext('2d');
    var currentWeekDataset = [10, 15, 20, 25];
    var lastWeekDataset = [5, 10, 15, 20];

    var chartData = {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        datasets: [
            {
                label: 'Current Week Sales',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                fill: true,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
            },
            {
                label: 'Last Week Sales',
                data: [],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                fill: true,
                pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(255, 99, 132, 1)'
            }
        ]
    };

    var salesChart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 30,
                    ticks: {
                        callback: function(value, index, values) {
                            return value + 'k';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.raw + 'k';
                            return label;
                        }
                    }
                }
            }
        }
    });

    function drawDataset(datasetIndex, data, interval) {
        let i = 0;
        function addData() {
            if (i < data.length) {
                salesChart.data.datasets[datasetIndex].data.push(data[i]);
                salesChart.update();
                i++;
                setTimeout(addData, interval);
            }
        }
        addData();
    }

    drawDataset(0, currentWeekDataset, 200);
    drawDataset(1, lastWeekDataset, 200);
});
