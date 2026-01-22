<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../../../db.php';

// Verify connection
if ($conn->connect_error) {
    die(json_encode(['error' => "DB Connection failed: " . $conn->connect_error]));
}

header('Content-Type: application/json');

try {
    $startDate = $_GET['start_date'] ?? '';
    $endDate = $_GET['end_date'] ?? '';

    if (empty($startDate) || empty($endDate)) {
        throw new Exception('Missing date parameters');
    }

    $response = ['appointments' => [], 'checkout' => []];

    // Appointments
    $stmt = $conn->prepare("SELECT email, service, payment, appointment_date 
                           FROM appointment 
                           WHERE appointment_date BETWEEN ? AND ?");
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $response['appointments'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Checkout
    $stmt = $conn->prepare("SELECT email, product_name, quantity, sub_total, created_at
                           FROM checkout 
                           WHERE created_at BETWEEN ? AND ?");
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $response['checkout'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();