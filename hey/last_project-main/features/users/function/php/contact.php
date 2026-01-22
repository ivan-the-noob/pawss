<?php
session_start();
require '../../../../db.php'; 

$email = $_SESSION['email'] ?? ''; 
$message = $_POST['comment'] ?? ''; 
$date_today = date('Y-m-d');

if (empty(trim($message))) {
    header("Location: ../../../../index.php?status=empty");
    exit();
}

$stmt = $conn->prepare("SELECT id FROM contact WHERE email = ? AND last_reviewed = ?");
$stmt->bind_param("ss", $email, $date_today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: ../../../../index.php?status=already_submitted");
} else {
    $stmt = $conn->prepare("INSERT INTO contact (email, message, last_reviewed) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $message, $date_today);

    if ($stmt->execute()) {
        header("Location: ../../../../index.php?status=success");
    } else {
        header("Location: ../../../../index.php?status=error");
    }
}

$stmt->close();
$conn->close();
?>
