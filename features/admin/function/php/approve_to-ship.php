<?php
    include '../../../../db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id']; 

        $update_query = "UPDATE checkout SET status = 'to-receive' WHERE id = ?";

        if ($stmt = $conn->prepare($update_query)) {
            $stmt->bind_param("s", $id);
            
            if ($stmt->execute()) {
                $notificationMessage = "Your item has been picked up by courier. Please ready payment for COD.";
                $notificationSql = "INSERT INTO notification (email, message) VALUES (?, ?)";
                $notificationStmt = $conn->prepare($notificationSql);
                $notificationStmt->bind_param("ss", $email, $notificationMessage);
                $notificationStmt->execute();
                $notificationStmt->close();
                
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
