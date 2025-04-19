<?php
require '../../../../db.php';

if (isset($_POST['id']) && isset($_POST['status'])) {
    $orderId = $_POST['id'];
    $status = strtolower(trim($_POST['status']));

    if ($conn) {
        $stmt = $conn->prepare("UPDATE checkout SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId); 
        
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        echo 'Database connection failed.';
    }
} else {
    echo 'Missing order ID or status.';
}
?>
