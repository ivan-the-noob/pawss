document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById('salesChart').getContext('2d');
    
    // Initial chart data
    var chartData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [
            {
                label: 'Current Month Sales',
                data: Array(12).fill(0), // Initial empty data for the current month
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                fill: true
            },
            {
                label: 'Last Month Sales',
                data: Array(12).fill(0), // Initial empty data for the last month
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                fill: true
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
                    max: 100000, // Adjust this as per your requirements
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + '₱'; // Format the y-axis with pesos
                        }
                    }
                }
            }
        }
    });

    // Function to fetch payment data using jQuery.ajax
    function fetchPaymentData(month) {
        var currentMonthData = Array(12).fill(0); // Default empty data for current month
        var lastMonthData = Array(12).fill(0); // Default empty data for last month
        var allMonthData = Array(12).fill(0); // Default empty data for all months
    
        let url = `?action=getPayments&month=${month}`;
    
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                console.log("Raw Data from Server:", data);
        
                if (typeof data !== 'string' || !data.trim()) {
                    console.error('Received invalid data format:', data);
                    alert('Invalid data format received from the server.');
                    return;
                }
        
                const dataArr = data.split('\n');
        
                // Check if there are at least two rows of data (current month, last month)
                if (dataArr.length < 2) {
                    console.error('Insufficient data received:', dataArr);
                    alert('Insufficient data received from the server.');
                    return;
                }
        
                // Split each row into an array of numbers
                currentMonthData = dataArr[0].split(',').map(Number);  // Current month data
                lastMonthData = dataArr[1].split(',').map(Number);     // Last month data
        
                // For "all" months, ensure the third row exists (i.e., data for all months)
                if (month === "all") {
                    if (dataArr.length >= 3) {
                        allMonthData = dataArr[2].split(',').map(Number); // Get all months data
                    } else {
                        console.error('All months data not available:', dataArr);
                        alert('Data for all months is missing.');
                    }
                }
        
                // Ensure that all months are represented in the allMonthData
                allMonthData = allMonthData.map(value => value || 0); // Fill missing months with zero
        
                // Update chart data for the selected month
                if (month === "all") {
                    salesChart.data.datasets[0].data = allMonthData; // All months data
                    salesChart.data.datasets[1].data = allMonthData; // Optionally compare last month's data
                } else {
                    salesChart.data.datasets[0].data = currentMonthData;
                    salesChart.data.datasets[1].data = lastMonthData;
                }
        
                salesChart.update();
            },
            error: function(xhr, status, error) {
                console.log('Error fetching data:', error);
                alert('An error occurred while fetching the data. Please try again later.');
            }
        });
    }

    // Handle the search button click
    $('#searchButton').click(function() {
        var selectedMonth = $('#monthSelect').val();
        
        // Log the selected month to the console
        console.log("Selected Month:", selectedMonth);
        
        // Fetch data for selected month or all months
        fetchPaymentData(selectedMonth === "all" ? "all" : selectedMonth);
    });

    // Fetch data for the current month immediately when the page loads
    const currentMonth = new Date().getMonth() + 1; // Get current month (1-based)
    fetchPaymentData(currentMonth); // Fetch and display data for current month only

    // Excel export functionality
    $('#exportBtn').click(function() {
        const currentMonthData = salesChart.data.datasets[0].data; // Current month sales data
        const lastMonthData = salesChart.data.datasets[1].data; // Last month sales data
        const labels = salesChart.data.labels; // Labels (Jan, Feb, Mar, etc.)

        let data = [
            ['Month', 'Current Month Sales', 'Last Month Sales'] // Added Last Month Sales column
        ];

        // Add both current and last month sales data for export
        for (let i = 0; i < labels.length; i++) {
            data.push([labels[i], currentMonthData[i], lastMonthData[i]]);
        }

        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sales Data');
        XLSX.writeFile(wb, 'sales_data.xlsx');
    });
});
