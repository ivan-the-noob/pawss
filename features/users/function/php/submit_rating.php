<?php
include '../../../../db.php'; // Replace with your actual DB connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = $_POST['appointment_id']; // Must be included in the modal form
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Basic validation
    if (!empty($rating) && is_numeric($appointmentId)) {
        // Insert rating into the rating table
        $stmt = $conn->prepare("INSERT INTO rating (appointment_id, rating, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $appointmentId, $rating, $comment);
        $stmt->execute();

        // Update is_rated in the appointment table
        $update = $conn->prepare("UPDATE appointment SET is_rated = 1 WHERE id = ?");
        $update->bind_param("i", $appointmentId);
        $update->execute();

        // Redirect after successful submission
        header("Location: ../../web/api/my-app.php?rated=success");
        exit();
    } else {
        echo "Rating or appointment ID is missing.";
    }
} else {
    echo "Invalid request.";
}
?>
