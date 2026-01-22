<?php 
session_start();
require '../../../../db.php';

// Fallback values for guest
$email = $_SESSION['email'] ?? 'Guest';
$profile = $_SESSION['profile_picture'] ?? 'assets/img/profile.png';
$review = $_POST['comment'] ?? '';

// Check if the review is empty or only spaces
if (empty(trim($review))) {
    header("Location: ../../../../index.php?status=empty");
    exit();
}

// Get today's date in Y-m-d format
$date_today = date('Y-m-d'); 

// Check if the user has already submitted a review today
$stmt = $conn->prepare("SELECT * FROM review WHERE email = ? AND last_reviewed = ?");
$stmt->bind_param("ss", $email, $date_today);
$stmt->execute();
$result = $stmt->get_result();
$has_reviewed_today = $result->num_rows > 0;

if ($has_reviewed_today) {
    header("Location: ../../../../index.php?status=already_reviewed");
    exit();
}

// Proceed to insert the review
$stmt = $conn->prepare("INSERT INTO review (email, profile_picture, review, last_reviewed) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $email, $profile, $review, $date_today);

if ($stmt->execute()) {
    header("Location: ../../../../index.php?status=success");
} else {
    header("Location: ../../../../index.php?status=error");
}

$stmt->close();
$conn->close();
?>
