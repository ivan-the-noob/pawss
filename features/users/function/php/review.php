<?php 
session_start();
require '../../../../db.php';

// Check if user is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['profile_picture'])) {
    echo "You need to be logged in to submit a review.";
    exit();
}

$email = $_SESSION['email'];
$profile = $_SESSION['profile_picture'];
$review = $_POST['comment'] ?? '';

// Check if the review is empty or only spaces
if (empty(trim($review))) {
    header("Location: ../../../../index.php?status=empty");
    exit();
}

// Get today's date in Y-m-d format
$date_today = date('Y-m-d'); 

// Check if the user has already submitted a review today by checking the `last_reviewed` field
$stmt = $conn->prepare("SELECT * FROM review WHERE email = ? AND last_reviewed = ?");
$stmt->bind_param("ss", $email, $date_today);
$stmt->execute();
$result = $stmt->get_result();

// Check if a review has been submitted today
$has_reviewed_today = $result->num_rows > 0;

// If a review has been submitted today, redirect with warning
if ($has_reviewed_today) {
    header("Location: ../../../../index.php?status=already_reviewed");
} else {
    // Proceed to insert the review if no review exists for today
    $stmt = $conn->prepare("INSERT INTO review (email, profile_picture, review, last_reviewed) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $profile, $review, $date_today);

    if ($stmt->execute()) {
        header("Location: ../../../../index.php?status=success");
    } else {
        header("Location: ../../../../index.php?status=error");
    }
}

$stmt->close();
$conn->close();

?>
