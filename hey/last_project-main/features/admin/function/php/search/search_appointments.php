<?php
include '../../../../../db.php';

// Get the search query from the URL
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Sanitize the query to prevent SQL injection
$query = $conn->real_escape_string($query);

// Update the SQL query to include a WHERE clause to filter results based on the search query
$sql = "SELECT owner_name, email, service, id, service_category, contact_num, barangay, pet_type, breed, age, payment, appointment_time, appointment_date, latitude, longitude, add_info FROM appointment WHERE owner_name LIKE '%$query%' OR email LIKE '%$query%' OR service LIKE '%$query%'";

$result = $conn->query($sql);

// Check for query execution errors
if (!$result) {
    echo json_encode(['error' => "Error: " . $conn->error]);
    exit;
}

// Check if there are any results
$appointments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row; // Add each row to the appointments array
    }
}

// Return the appointments as a JSON response
echo json_encode($appointments);
?>
