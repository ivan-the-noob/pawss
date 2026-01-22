<?php
require_once '../../../../db.php'; // Make sure this initializes $conn (MySQLi connection)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['created_at'])) {
    $createdAt = $_POST['created_at'];

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE checkout SET status = 'received-order' WHERE created_at = ?");
    if ($stmt) {
        $stmt->bind_param("s", $createdAt);
        $stmt->execute();
        $stmt->close();

        // Redirect back to the page with the orders
        header("Location: ../../web/api/my-orders.php"); // Replace with your actual page
        exit();
    } else {
        echo "Failed to prepare the statement.";
    }
} else {
    echo "Invalid request.";
}
