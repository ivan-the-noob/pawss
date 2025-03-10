<?php
include '../../../../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id']; 

    $update_query = "UPDATE checkout SET status = 'cancel' WHERE id = ?";

    if ($stmt = $conn->prepare($update_query)) {
        $stmt->bind_param("i", $id); 
        
        if ($stmt->execute()) {
            header("Location: ../../web/api/decline.php");
            exit;
        } else {
            header("Location: ../../view_orders.php?message=Error occurred while declining the order.");
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
