<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Line Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script> <!-- Include SheetJS -->
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <label for="monthSelect">Select Month:</label>
        <select id="monthSelect">
            <option value="">All Months</option>
            <option value="2024-01">January</option>
            <option value="2024-02">February</option>
            <option value="2024-03">March</option>
            <option value="2024-04">April</option>
            <option value="2024-05">May</option>
            <option value="2024-06">June</option>
            <option value="2024-07">July</option>
            <option value="2024-08">August</option>
            <option value="2024-09">September</option>
            <option value="2024-10">October</option>
            <option value="2024-11">November</option>
            <option value="2024-12">December</option>
        </select>
        <button id="searchBtn">Search</button>
        <button id="exportBtn">Export to Excel</button> <!-- Export Button -->
    </div>

    <div class="chart-container" style="width: 80%; margin: auto;">
        <canvas id="salesChart"></canvas>
    </div>

    <script>
function fetchData(selectedMonth = '') {
    const url = selectedMonth 
        ? `../../function/php/fetch_sales_data.php?month=${selectedMonth}` 
        : `../../function/php/fetch_sales_data.php`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => {
                const [year, month] = item.month.split('-'); 
                const date = new Date(year, month - 1); 
                return date.toLocaleString('default', { month: 'short' }); 
            });

            const salesData = data.map(item => item.total_sales);

            updateChart(labels, salesData);
        })
        .catch(error => console.error('Error fetching data:', error));
}

function updateChart(labels, salesData) {
    salesChart.data.labels = labels;
    salesChart.data.datasets[0].data = salesData;
    salesChart.data.datasets[0].spanGaps = true; 
    salesChart.data.datasets[0].fill = '-1';

    salesChart.options.scales.y.min = 0; 
    salesChart.options.scales.y.max = 100000; 

    salesChart.update();
}

function fetchAdditionalData(selectedMonth = '') {
    const url = selectedMonth 
        ? `../../function/php/fetch_appointment_data.php?month=${selectedMonth}` 
        : `../../function/php/fetch_appointment_data.php`;

    return fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                console.log('No appointment data found.');
            }
            return data; 
        })
        .catch(error => {
            console.error('Error fetching appointment data:', error);
        });
}

function fetchAndExportData() {
    fetch('path_to_your_api_or_backend_endpoint')
        .then(response => response.json())
        .then(data => {
            if (data && data.data && Array.isArray(data.data)) {
                exportToExcel(data.labels, data.salesData, data.data);
            } else {
                console.error("Error: No valid data found or unexpected response format");
                alert("No appointment data found.");
            }
        })
        .catch(error => {
            console.error('Error fetching appointment data:', error);
            alert('Error fetching appointment data.');
        });
}

function exportToExcel(labels, salesData, additionalData) {
    const selectedMonth = document.getElementById('monthSelect').value;

    if (selectedMonth) {
        const selectedIndex = labels.findIndex(label => label.toLowerCase().includes(selectedMonth.toLowerCase()));

        if (selectedIndex !== -1) {
            labels = [labels[selectedIndex]];  
            salesData = [salesData[selectedIndex]]; 
        }

        additionalData = additionalData.filter(item => {
            const appointmentMonth = item.appointment_date.slice(0, 7); 
            return appointmentMonth === selectedMonth;
        });
    }

    const worksheetData = labels.map((label, index) => [label, formatCurrency(salesData[index])]);
    const worksheet = XLSX.utils.aoa_to_sheet([['Month', 'Sales (₱)'], ...worksheetData]);

    const additionalDataHeaders = ['Owner Name', 'Email', 'Payment Option', 'Payment', 'Appointment Date'];
    const additionalDataRows = Array.isArray(additionalData) 
        ? additionalData.map(item => [
            item.owner_name, 
            item.email, 
            item.payment_option, 
            formatCurrency(item.payment), 
            item.appointment_date
        ]) 
        : [];

    const finalData = [
        ...worksheetData, 
        [], 
        ...[additionalDataHeaders], 
        ...additionalDataRows 
    ];

    const finalWorksheet = XLSX.utils.aoa_to_sheet([['Month', 'Sales (₱)'], ...worksheetData, [], additionalDataHeaders, ...additionalDataRows]);

    const colWidth = 15; 

    finalWorksheet['!cols'] = [
        { wch: colWidth },
        { wch: colWidth }, 
        { wch: colWidth }, 
        { wch: colWidth }, 
        { wch: colWidth }, 
        { wch: colWidth }, 
        { wch: colWidth }, 
    ];

    const finalWorkbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(finalWorkbook, finalWorksheet, 'Sales and Appointment Data');

    XLSX.writeFile(finalWorkbook, 'sales_and_appointment_data.xlsx');
}

function formatCurrency(value) {
    return new Intl.NumberFormat().format(value);
}


const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Total Sales',
            data: [],
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderWidth: 2,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Month'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Sales Amount ($)'
                }
            }
        }
    }
});

document.getElementById('searchBtn').addEventListener('click', () => {
    const selectedMonth = document.getElementById('monthSelect').value;
    fetchData(selectedMonth);
});

document.getElementById('exportBtn').addEventListener('click', () => {
    const selectedMonth = document.getElementById('monthSelect').value;
    fetchAdditionalData(selectedMonth).then(additionalData => {
        const labels = salesChart.data.labels;
        const salesData = salesChart.data.datasets[0].data;
        exportToExcel(labels, salesData, additionalData); 
    });
});

fetchData();
    </script>
</body>
</html>
