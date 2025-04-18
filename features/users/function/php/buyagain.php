<?php
session_start();
include '../../../../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderId = $_POST['id'];          // Existing order ID to update
    $quantityToAdd = $_POST['quantity']; // The quantity the user wants to add to the existing order
    $email = $_SESSION['email'];      // User's email

    // Get the current quantity of the product in the checkout table
    $stmt = $conn->prepare("SELECT quantity FROM checkout WHERE id = ? AND email = ?");
    $stmt->bind_param("is", $orderId, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Product exists, update the quantity
        $row = $result->fetch_assoc();
        $currentQuantity = $row['quantity'];
        $newQuantity = $currentQuantity + $quantityToAdd; // Add the new quantity to the existing one
        
        // Update the quantity in the checkout table
        $updateStmt = $conn->prepare("UPDATE checkout SET quantity = ? WHERE id = ? AND email = ?");
        $updateStmt->bind_param("iis", $newQuantity, $orderId, $email);
        
        if ($updateStmt->execute()) {
            header("Location: ../../web/api/my-orders.php?status=order-success");
            exit;
        } else {
            echo "Failed to update the quantity: " . $updateStmt->error;
        }
    } else {
        echo "Order not found or invalid email.";
    }
}
?>
