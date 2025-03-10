<?php
session_start(); 

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../users/web/api/login.php");
    exit();
}

require '../../../../db.php';

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $userId = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "User deleted successfully.";
    } else {
        $_SESSION['message'] = "Error: Could not delete user.";
    }

    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid user ID.";
}

$conn->close();

header("Location: ../../../admin-user.php");
exit();
?>
