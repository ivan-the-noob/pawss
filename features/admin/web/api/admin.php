<?php

session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../users/web/api/login.php");
    exit();
}
$email = $_SESSION['email'];

if (isset($_GET['action']) && $_GET['action'] === 'getPayments') {
    header('Content-Type: text/plain'); // Change content type to text/plain for plain data output
    include '../../../../db.php';

    $currentYear = date('Y');
    $selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('n');
    $lastMonth = $selectedMonth - 1;
    if ($lastMonth < 1) {
        $lastMonth = 12;
        $currentYear -= 1;
    }

    // Sanitize selected month to prevent SQL injection
    $selectedMonth = filter_var($selectedMonth, FILTER_VALIDATE_INT);
    $lastMonth = filter_var($lastMonth, FILTER_VALIDATE_INT);

    try {
        $sql = "
            SELECT MONTH(created_at) as month, YEAR(created_at) as year, SUM(payment) as total
            FROM (
                SELECT created_at, payment FROM appointment WHERE status = 'finish'
                UNION ALL
                SELECT created_at, sales_amount as payment FROM manual_input
            ) AS all_sales
            WHERE (YEAR(created_at) = $currentYear AND MONTH(created_at) = $selectedMonth) 
               OR (YEAR(created_at) = $currentYear AND MONTH(created_at) = $lastMonth)
            GROUP BY YEAR(created_at), MONTH(created_at)
            ORDER BY YEAR(created_at), MONTH(created_at)";

        // If fetching data for all months
        if ($selectedMonth === 'all') {
            $sql = "
                SELECT MONTH(created_at) as month, YEAR(created_at) as year, SUM(payment) as total
                FROM (
                    SELECT created_at, payment FROM appointment WHERE status = 'finish'
                    UNION ALL
                    SELECT created_at, sales_amount as payment FROM manual_input
                ) AS all_sales
                WHERE YEAR(created_at) = $currentYear
                GROUP BY YEAR(created_at), MONTH(created_at)
                ORDER BY MONTH(created_at)";
        }

        $result = $conn->query($sql);

        // Initialize the payments array
        $payments = [
            'currentMonth' => array_fill(0, 12, 0),
            'lastMonth' => array_fill(0, 12, 0),
        ];

        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $monthIndex = intval($row['month']) - 1;

                    if ($row['year'] == $currentYear && $row['month'] == $selectedMonth) {
                        $payments['currentMonth'][$monthIndex] = floatval($row['total']);
                    }

                    if ($row['year'] == $currentYear && $row['month'] == $lastMonth) {
                        $payments['lastMonth'][$monthIndex] = floatval($row['total']);
                    }
                }
            }
        } else {
            // If query fails
            echo "Error: Database query failed: " . $conn->error;
            exit;
        }

        $conn->close();

        // Return the data as plain text (comma-separated)
        echo implode(',', $payments['currentMonth']) . "\n" . implode(',', $payments['lastMonth']);
        exit;
    } catch (Exception $e) {
        // Handle any errors that may occur
        echo "Error: An error occurred: " . $e->getMessage();
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../css/index.css">
      <link rel="icon" href="../../../../assets/img/logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!--Navigation Links-->
    <div class="navbar flex-column bg-white shadow-sm p-3 collapse d-md-flex" id="navbar">
        <div class="navbar-links">
            <a class="navbar-brand d-none d-md-block logo-container" href="#">
                <img src="../../../../assets/img/logo.png" alt="Logo">
            </a>
            <a href="#dashboard" class="navbar-highlight">
                <i class="fa-solid fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="users.php">
                <i class="fa-solid fa-users"></i>
                <span>Users</span>
            </a>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center" id="checkoutDropdowns" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span class="ms-2">Booking</span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="checkoutDropdowns">
                    <li><a class="dropdown-item" href="app-req.php"><i class="fa-solid fa-calendar-check"></i> <span>Pending Bookings</span></a></li>
                    <li><a class="dropdown-item" href="app-waiting.php"><i class="fa-solid fa-calendar-check"></i> <span>Waiting Bookings</span></a></li>
                    <li><a class="dropdown-item" href="app-ongoing.php"><i class="fa-solid fa-calendar-check"></i> <span>On Going Bookings</span></a></li>
                    <li><a class="dropdown-item" href="app-finish.php"><i class="fa-solid fa-calendar-check"></i> <span>Finished Bookings</span></a></li>
                    <li><a class="dropdown-item" href="app-cancel.php"><i class="fa-solid fa-calendar-check"></i> <span>Cancelled Bookings</span></a></li>
                   
                </ul>
            </div>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center dropdown-toggle" id="checkoutDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span class="ms-2">Checkout</span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="checkoutDropdown">
                    <li><a class="dropdown-item" href="pending_checkout.php"><i class="fa-solid fa-calendar-check"></i> <span>Pending CheckOut</span></a></li>
                    <li><a class="dropdown-item" href="to-ship_checkout.php"><i class="fa-solid fa-calendar-check"></i> <span>To-Ship</span></a></li>
                    <li><a class="dropdown-item" href="to-receive.php"><i class="fa-solid fa-calendar-check"></i> <span>To-Receive</span></a></li>
                    <li><a class="dropdown-item" href="delivered_checkout.php"><i class="fa-solid fa-calendar-check"></i> <span>Delivered</span></a></li>
                    <li><a class="dropdown-item" href="decline.php"><i class="fa-solid fa-calendar-check"></i> <span>Declined</span></a></li>
                </ul>
            </div> 

           

            <div class="maintenance">
                <p class="maintenance-text">Maintenance</p>
                <a href="service-list.php">
                    <i class="fa-solid fa-list"></i>
                    <span>Service List</span>
                </a>
                <a href="product.php">
                    <i class="fa-solid fa-box"></i>
                    <span>Product</span>
                </a>
                <a href="admin-user.php">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Admin User List</span>
                </a>
                 <a href="review-list.php">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Review List</span>
                </a>
                 <a href="contact-section.php">
                    <i class="fa-solid fa-phone"></i>
                    <span>Contact List</span>
                </a>
            </div>

        </div>
    </div>
    <!--Navigation Links End-->
    <div class="content flex-grow-1">
        <div class="header">
            <button class="navbar-toggler d-block d-md-none" type="button" onclick="toggleMenu()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    style="stroke: black; fill: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
                    </path>
                </svg>
            </button>

            <!--Notification and Profile Admin-->
            <div class="profile-admin">
                <div class="dropdown">
                    <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="../../../../assets/img/vet logo.png"
                            style="width: 40px; height: 40px; object-fit: cover;">
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../../../users/web/api/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!--Notification and Profile Admin End-->
        <!--Pos Card with graphs-->
        <div class="dashboard">
            <div class="d-flex justify-content-between">
                <h3>Dashboard</h3>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#salesModal">
                    + Sales
                </button>
            </div>
            <?php
require '../../../../db.php';

// Set default dates to today's date if no dates are selected
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$sql_users = "
    SELECT COUNT(*) AS total_users 
    FROM users 
    WHERE role = 'user' 
    AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'
";
$result_users = $conn->query($sql_users);
$total_users = $result_users->fetch_assoc()['total_users'];

// 2. Get Total Booked with date filter
$sql_booked = "SELECT COUNT(*) AS total_booked FROM appointment WHERE DATE(appointment_date) BETWEEN '$start_date' AND '$end_date'";
$result_booked = $conn->query($sql_booked);
$total_booked = $result_booked->fetch_assoc()['total_booked'];

// 3. Get Total Sales with date filter
$sql_sales = "SELECT SUM(payment) AS total_sales FROM appointment WHERE DATE(appointment_date) BETWEEN '$start_date' AND '$end_date'";
$result_sales = $conn->query($sql_sales);
$total_sales = $result_sales->fetch_assoc()['total_sales'] ?? 0;

// 4. Get Total Checkout with date filter
$sql_checkout = "SELECT COUNT(*) AS total_checkout FROM checkout WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
$result_checkout = $conn->query($sql_checkout);
$total_checkout = $result_checkout->fetch_assoc()['total_checkout'];
?>

<!-- Date Filter Form -->
<div class="container row col-md-12 d-flex">
    <form method="GET" action="" class="container d-flex gap-5">
        <div class="col-md-2 m-1 d-flex">
            <label for="start_date">From:</label>
            <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
        </div>
        <div class="col-md-2 m-1 d-flex gap-2">
            <label for="end_date">To:</label>
            <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
            <button type="submit" class="btn btn-primary" style="height: 40px;">Filter</button>
        </div>
    </form>
</div>
                

<div class="row card-box">
    <div class="col-12 col-md-6 col-lg-3 cc">
        <div class="card">
            <div class="cards">
                <div class="card-text">
                    <p>Total Users</p>
                    <h5><?php echo $total_users; ?></h5>
                </div>
                <div class="logo">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="trend card-up"><i class="fa-solid fa-arrow-trend-up"> 8.5 % </i> Up from yesterday
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3 cc">
        <div class="card">
            <div class="cards">
                <div class="card-text">
                    <p>Total Booked</p>
                    <h5><?php echo $total_booked; ?></h5>
                </div>
                <div class="logo">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </div>
            <div class="trend card-up"><i class="fa-solid fa-arrow-trend-up"> 1.3 % </i> Up from yesterday
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3 cc">
        <div class="card">
       
            <div class="cards">
           
                <div class="card-text">
                    <p>Total Sales</p>
                    <h5>₱<?php echo number_format($total_sales, 2); ?></h5>
                </div>
                <div class="logo">
                    <i class="fa-solid fa-peso-sign"></i>
                </div>
            </div>
            <div class="trend card-down"><i class="fa-solid fa-arrow-trend-down"> 4.3 % </i> Down from yesterday</div>

                
           
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3 cc">
        <div class="card">
            <div class="cards">
                <div class="card-text">
                    <p>Total Checkout</p>
                    <h5><?php echo $total_checkout; ?></h5>
                </div>
                <div class="logo">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="trend card-up"><i class="fa-solid fa-arrow-trend-up"> 8.5 % </i> Up from yesterday
            </div>
        </div>
    </div>
</div>

           
            <div class="flex-container">
            <div class="chart-container">
    <div class="mt-2 chart-button">
    <div class="d-flex gap-2 justify-content-center mb-3">
 
        <button id="exportBtn" class="btn btn-success">Export</button>
    </div>
    </div>
    <canvas id="salesChart"></canvas>
</div>

<script>
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param) || '';
}

function fetchData(start, end) {
    const url = `../../function/php/fetch_sales_data.php?start_date=${start}&end_date=${end}`;
    fetch(url)
        .then(res => res.json())
        .then(data => {
            const labels = data.map(item => {
                const [year, month] = item.month.split('-');
                const date = new Date(year, month - 1);
                return date.toLocaleString('default', { month: 'short' });
            });
            const salesData = data.map(item => item.total_sales);
            updateChart(labels, salesData);
        })
        .catch(err => console.error('Fetch error:', err));
}

function updateChart(labels, salesData) {
    salesChart.data.labels = labels;
    salesChart.data.datasets[0].data = salesData;
    salesChart.update();
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
            legend: { display: true }
        },
        scales: {
            x: { title: { display: true, text: 'Month' } },
            y: { title: { display: true, text: 'Sales (₱)' }, beginAtZero: true }
        }
    }
});

// Initial chart load using query params
const start = getQueryParam('start_date');
const end = getQueryParam('end_date');
if (start && end) {
    fetchData(start, end);
}

document.getElementById('exportBtn').addEventListener('click', function () {
    const labels = salesChart.data.labels;
    const data = salesChart.data.datasets[0].data;

    const rows = [['Month', 'Total Sales']];
    for (let i = 0; i < labels.length; i++) {
        rows.push([labels[i], data[i]]);
    }

    const worksheet = XLSX.utils.aoa_to_sheet(rows);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Sales Data');

    XLSX.writeFile(workbook, 'sales_data.xlsx');
});
</script>


                <div class="global-container">
                <?php 
                    require '../../../../db.php';

                    $sql = "SELECT * FROM global_reports ORDER BY cur_time DESC"; 
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Output the reports
                        while ($row = $result->fetch_assoc()) {
                            $message = $row['message'];
                            $time = $row['cur_time']; // Assuming current_time is a TIMESTAMP column
                            
                            
                            // Display the message and time
                            echo "<div class='report'>";
                            echo "<p>$message<span class='report-time'> $time</span></p><hr>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No reports available.</p>";
                    }

                    $conn->close();
                    ?>

   
</div>
            </div>
        </div>

        <div class="modal fade" id="salesModal" tabindex="-1" aria-labelledby="salesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salesModalLabel">Add Sales</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="salesForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="salesAmount" class="form-label">Sales Amount:</label>
                        <input type="number" step="0.01" class="form-control" id="salesAmount" name="salesAmount" required>
                    </div>
                    <div class="mb-3">
                        <label for="salesMonth" class="form-label">Month:</label>
                        <select class="form-select" id="salesMonth" name="salesMonth" required>
                            <option value="" disabled selected>Select Month</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="salesYear" class="form-label">Year:</label>
                        <input type="number" class="form-control" id="salesYear" name="salesYear" 
                               min="2000" max="2100" value="<?= date('Y') ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById("salesForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const salesAmount = document.getElementById("salesAmount").value;
    const salesMonth = document.getElementById("salesMonth").value;
    const salesYear = document.getElementById("salesYear").value;

    const form = new FormData();
    form.append("action", "addSales");
    form.append("salesAmount", salesAmount);
    form.append("salesMonth", salesMonth);
    form.append("salesYear", salesYear);

    fetch("../../function/php/add_sales.php", {
        method: "POST",
        body: form,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Sales added successfully!");
            location.reload();
        } else {
            alert("Failed to add sales: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
});
</script>

        <!--Pos Card with graphs End-->

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../../function/script/toggle-menu.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>