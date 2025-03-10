<?php 

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email']; 


$sql = "SELECT COUNT(*) as count FROM cart WHERE email = ? AND status = 1";  
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email); 
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$newCartData = $row['count'] ? $row['count'] : 0; 

?>