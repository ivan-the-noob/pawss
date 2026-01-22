<?php
include '../../../../db.php'; // your db connection

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

if (!$startDate || !$endDate) {
    echo json_encode(['error' => 'Missing date range']);
    exit;
}

// Get appointments
$appointments = [];
$sql1 = $conn->prepare("SELECT email, service, payment FROM appointment WHERE created_at BETWEEN ? AND ?");
$sql1->bind_param("ss", $startDate, $endDate);
$sql1->execute();
$res1 = $sql1->get_result();
while ($row = $res1->fetch_assoc()) {
    $appointments[] = $row;
}
$sql1->close();

// Get product checkouts
$products = [];
$sql2 = $conn->prepare("SELECT email, product_name, quantity, total_amount FROM checkout WHERE created_at BETWEEN ? AND ?");
$sql2->bind_param("ss", $startDate, $endDate);
$sql2->execute();
$res2 = $sql2->get_result();
while ($row = $res2->fetch_assoc()) {
    $products[] = $row;
}
$sql2->close();

$conn->close();

// Output both in one JSON
echo json_encode([
    'appointments' => $appointments,
    'products' => $products,
    'date_range' => [
        'start' => $startDate,
        'end' => $endDate
    ]
]);
