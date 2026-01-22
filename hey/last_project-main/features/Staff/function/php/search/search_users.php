<?php
include '../../../../../db.php';

// Get the search query from the URL
$search = isset($_GET['query']) ? $_GET['query'] : '';

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE name LIKE ? OR email LIKE ?");
$searchTerm = "%$search%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Return the results as a JSON response
header('Content-Type: application/json');
echo json_encode($users);

$stmt->close();
$conn->close();
?>
