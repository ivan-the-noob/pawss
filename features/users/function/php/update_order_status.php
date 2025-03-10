<?php
require '../../../../db.php';

if (isset($_POST['id']) && isset($_POST['status'])) {
    $orderId = $_POST['id'];
    $status = $_POST['status'];

    if ($conn) {
        $stmt = $conn->prepare("UPDATE checkout SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId); 
        
        if ($stmt->execute()) {
            header("Location: ../../web/api/my-orders.php?tab=cancelled-orders");
            exit();
        } else {
            echo 'Error: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        echo 'Database connection failed.';
    }
} else {
    echo 'Missing order ID or status.';
}
?>
