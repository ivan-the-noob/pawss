<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: features/users/web/api/login.php");
    exit();
}
$email = $_SESSION['email']; 
require '../../../../db.php'; 
$sql = "UPDATE cart SET status = 0 WHERE email = ? AND status = 1";  
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email); 

if ($stmt->execute()) {
    header("Location: ../../web/api/cart.php"); 
    exit();
} else {
    echo "Error updating status: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
