<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../users/web/api/login.php");
    exit();
}

include '../../../../db.php'; 

$error = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param('sssi', $name, $email, $role, $id);

    try {
        if ($stmt->execute()) {
            $successMessage = "User updated successfully!";
            header("Location: ../../web/api/admin-user.php?message=" . urlencode($successMessage));
            exit();
        } else {
            $error = "Error: Could not update user.";
        }
    } catch (mysqli_sql_exception $e) {
        error_log($e->getMessage(), 0); 
        $error = "Error: Could not update user. Please try again.";
    }

    $stmt->close();
}

$conn->close();
?>
