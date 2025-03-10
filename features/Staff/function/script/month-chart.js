document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById('salesChart').getContext('2d');
    var currentDataset = [30, 40, 50, 60, 35, 80, 55, 45, 50, 40, 60, 65];
    var lastMonthDataset = [20, 30, 40, 50, 30, 70, 50, 40, 45, 35, 55, 60];

    var chartData = {
        labels: ['Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'],
        datasets: [
            {
                label: 'Current Month Sales',
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
                label: 'Last Month Sales',
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
                    max: 100,
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

    drawDataset(0, currentDataset, 50);
    drawDataset(1, lastMonthDataset, 50);
});
