<?php
require '../../../../db.php';

$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

if ($startDate && $endDate) {
    // Filter by date range
    $query = "
        SELECT DATE_FORMAT(appointment_date, '%Y-%m') AS month, SUM(payment) AS total_sales
        FROM appointment
        WHERE appointment_date BETWEEN '$startDate' AND '$endDate'
        GROUP BY month
        ORDER BY month ASC
    ";
} else {
    // Default: fetch all data
    $query = "
        SELECT DATE_FORMAT(appointment_date, '%Y-%m') AS month, SUM(payment) AS total_sales
        FROM appointment
        GROUP BY month
        ORDER BY month ASC
    ";
}

$result = $conn->query($query);

// Fetch database results
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[$row['month']] = (float)$row['total_sales'];
}

$conn->close();

// Determine the range of months to return
$months = [];

if ($startDate && $endDate) {
    // Generate months between start and end
    $start = new DateTime(date('Y-m-01', strtotime($startDate)));
    $end = new DateTime(date('Y-m-01', strtotime($endDate)));
    $end->modify('+1 month'); // include the last month

    while ($start < $end) {
        $month = $start->format('Y-m');
        $months[] = [
            'month' => $month,
            'total_sales' => $data[$month] ?? 0
        ];
        $start->modify('+1 month');
    }
} else {
    // All months of current year (default behavior)
    $currentYear = date('Y');
    for ($i = 1; $i <= 12; $i++) {
        $month = sprintf('%s-%02d', $currentYear, $i);
        $months[] = [
            'month' => $month,
            'total_sales' => $data[$month] ?? 0
        ];
    }
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($months);
?>
