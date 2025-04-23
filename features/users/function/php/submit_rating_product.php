<?php
session_start();
require_once '../../../../db.php'; // update path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['id'];
    $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    // Validate
    if ($rating < 1 || $rating > 5) {
        // You can set an error message in session and redirect back
        header('Location: ../../web/api/my-orders.php?rated=invalid');
        exit;
    }

    // Option 1: Save rating to a separate table
    $stmt = $conn->prepare("INSERT INTO product_ratings (order_id, rating, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $order_id, $rating, $comment);
    $stmt->execute();

    // Option 2: Update the order to mark it as rated
    $update = $conn->prepare("UPDATE checkout SET is_rated = 1 WHERE id = ?");
    $update->bind_param("i", $order_id);
    $update->execute();

    // Redirect back with success flag
    header('Location: ../../web/api/my-orders.php?rated=success');
    exit;
} else {
    header('Location: ../../web/api/my-orders.php');
    exit;
}
