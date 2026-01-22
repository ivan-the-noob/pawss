<?php
require '../../../../db.php';

$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// If no start or end date is provided, use the current month
if (!$startDate || !$endDate) {
    $currentYear = date('Y');
    $currentMonth = date('m');

    // Set start and end dates for the current month
    $startDate = "$currentYear-$currentMonth-01";
    $endDate = "$currentYear-$currentMonth-31";
}

$data = [];

// Query to fetch data for the given date range (current month if no params are passed)
$query = "
    SELECT DATE_FORMAT(appointment_date, '%Y-%m') AS month, SUM(payment) AS total_sales
    FROM appointment
    WHERE appointment_date BETWEEN '$startDate' AND '$endDate'
    GROUP BY month
    ORDER BY month ASC
";

// Fetch data from DB
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $data[$row['month']] = (float)$row['total_sales'];
}

$conn->close();

// Build the response for the requested month
$months = [];
$start = new DateTime(date('Y-m-01', strtotime($startDate)));
$end = new DateTime(date('Y-m-01', strtotime($endDate)));
$end->modify('+1 month'); // Include the last month

while ($start < $end) {
    $month = $start->format('Y-m');
    $months[] = [
        'month' => $month,
        'total_sales' => $data[$month] ?? 0
    ];
    $start->modify('+1 month');
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($months);
?>
