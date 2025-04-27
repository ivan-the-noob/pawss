<?php
include '../../../../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; 

    $update_query = "UPDATE checkout SET status = 'to-ship' WHERE id = ?";

    if ($stmt = $conn->prepare($update_query)) {
        $stmt->bind_param("s", $email);
        
        if ($stmt->execute()) {
            header("Location: ../../web/api/to-ship_checkout.php");
            exit;  
        } else {
            header("Location: ../../view_orders.php?message=Error occurred while approving the orders.");
            exit;
        }
    } else {
        header("Location: ../../view_orders.php?message=Database error.");
        exit;
    }
} else {
    header("Location: ../../view_orders.php?message=Invalid request.");
    exit;
}
?>
