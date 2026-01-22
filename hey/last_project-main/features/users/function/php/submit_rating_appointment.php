<?php
include '../../../../db.php'; // DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'] ?? null;
    $comment = $_POST['comment'] ?? null;
    $email = $_POST['email'] ?? '';
    $id = $_POST['id'] ?? null; // Changed from checkout_id to id

    // Validate required fields
    if (!empty($rating) && !empty($email) && !empty($id)) {
        // Set comment to NULL if empty
        if (trim($comment) === '') {
            $comment = null;
        }

        // Insert rating
        $stmt = $conn->prepare("INSERT INTO rating (email, rating, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $email, $rating, $comment);

        if ($stmt->execute()) {
            // Update is_rated in checkout
            $update = $conn->prepare("UPDATE appointment SET is_rated = 1 WHERE id = ?");
            $update->bind_param("i", $id);
            $update->execute();

            header("Location: ../../web/api/my-app.php?rated=success");
            exit();
        } else {
            echo "Database error: " . $stmt->error;
        }
    } else {
        echo "Rating, email, and ID are required.";
    }
} else {
    echo "Invalid request.";
}
?>
