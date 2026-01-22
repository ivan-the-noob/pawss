<?php
include '../../../../db.php'; // DB connection

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'] ?? null;
    $comment = $_POST['comment'] ?? null;
    $email = $_POST['email'] ?? '';
    $id = $_POST['id'] ?? null; // this is checkout.id

    // Validate required fields
    if (!empty($rating) && !empty($email) && !empty($id)) {
        // Check if this order is already rated
        $check = $conn->prepare("SELECT id FROM rating WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            // Already rated
            header("Location: ../../web/api/my-orders.php?rated=exists");
            exit();
        }

        // Set comment to NULL if empty
        if (trim($comment) === '') {
            $comment = null;
        }

        // Insert rating
        $stmt = $conn->prepare("INSERT INTO rating (email, rating, comment, id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sisi", $email, $rating, $comment, $id);

        if ($stmt->execute()) {
            // Update is_rated in checkout table
            $update = $conn->prepare("UPDATE checkout SET is_rated = 1 WHERE id = ?");
            $update->bind_param("i", $id);
            $update->execute();

            header("Location: ../../web/api/my-orders.php?rated=success");
            exit();
        } else {
            echo "Database error during insert: " . $stmt->error;
        }
    } else {
        echo "Rating, email, and checkout ID are required.";
    }
} else {
    echo "Invalid request method.";
}
?>
