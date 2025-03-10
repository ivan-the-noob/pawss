<?php
require '../../../../db.php';

// Check if a specific month is selected
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';

// If a month is selected, modify the query to filter by that month
if ($selectedMonth) {
    $query = "
        SELECT DATE_FORMAT(appointment_date, '%Y-%m') AS month, SUM(payment) AS total_sales
        FROM appointment
        WHERE appointment_date LIKE '$selectedMonth%'
        GROUP BY month
        ORDER BY month ASC
    ";
} else {
    // Default query for all months
    $query = "
        SELECT DATE_FORMAT(appointment_date, '%Y-%m') AS month, SUM(payment) AS total_sales
        FROM appointment
        WHERE YEAR(appointment_date) = YEAR(CURDATE())
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

// Generate all months for the current year
$currentYear = date('Y');
$months = [];
for ($i = 1; $i <= 12; $i++) {
    $month = sprintf('%s-%02d', $currentYear, $i); // Format: YYYY-MM
    $months[] = [
        'month' => $month,
        'total_sales' => $data[$month] ?? 0, // Use 0 if no data exists for the month
    ];
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($months);
?>
