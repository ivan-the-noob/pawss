<?php
require '../../../../db.php';

$month = isset($_GET['month']) ? $_GET['month'] : '';

// Check if month is not provided, which means "All"
if ($month === '') {
    // Prepare the SQL query to fetch all appointment data
    $sql = "SELECT owner_name, email, payment_option, payment, appointment_date 
            FROM appointment";
} else {
    // If a specific month is selected, fetch only appointments from that month
    $sql = "SELECT owner_name, email, payment_option, payment, appointment_date 
            FROM appointment 
            WHERE DATE_FORMAT(appointment_date, '%Y-%m') = ?";
}

// Prepare and bind the statement
$stmt = $conn->prepare($sql);

if ($month !== '') {
    $stmt->bind_param('s', $month);  // Bind the month parameter as a string
}

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if any records were found
if ($result->num_rows > 0) {
    // Fetch all records as an associative array
    $appointments = $result->fetch_all(MYSQLI_ASSOC);

    // Return the appointments data as JSON
    echo json_encode($appointments);
} else {
    // Return an empty array as JSON if no records are found
    echo json_encode([]);
}

// Close the connection
$stmt->close();
$conn->close();
?>
