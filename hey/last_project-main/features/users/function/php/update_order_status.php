<?php
session_start();
require '../../../../db.php';

if (isset($_POST['id']) && isset($_POST['status'])) {
    $orderId = $_POST['id'];
    $status = strtolower(trim($_POST['status']));
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : null;

    if (!$email) {
        echo 'error: User not logged in';
        exit();
    }

    if ($conn) {
        // First, get the created_at timestamp of the specific order
        $getCreatedAtQuery = "SELECT created_at FROM checkout WHERE id = ? AND email = ?";
        $getCreatedAtStmt = $conn->prepare($getCreatedAtQuery);
        $getCreatedAtStmt->bind_param("is", $orderId, $email);
        $getCreatedAtStmt->execute();
        $createdAtResult = $getCreatedAtStmt->get_result();
        
        if ($createdAtRow = $createdAtResult->fetch_assoc()) {
            $createdAt = $createdAtRow['created_at'];
            
            // Get ALL orders with the same created_at and email
            $getAllOrdersQuery = "SELECT id, product_name, quantity FROM checkout WHERE created_at = ? AND email = ?";
            $getAllOrdersStmt = $conn->prepare($getAllOrdersQuery);
            $getAllOrdersStmt->bind_param("ss", $createdAt, $email);
            $getAllOrdersStmt->execute();
            $allOrdersResult = $getAllOrdersStmt->get_result();
            
            $ordersToUpdate = [];
            while ($orderRow = $allOrdersResult->fetch_assoc()) {
                $ordersToUpdate[] = $orderRow;
            }
            
            if (empty($ordersToUpdate)) {
                echo 'error: No orders found';
                exit();
            }
            
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Update ALL orders with the same created_at timestamp to 'cancel'
                $updateStmt = $conn->prepare("UPDATE checkout SET status = ? WHERE created_at = ? AND email = ?");
                $updateStmt->bind_param("sss", $status, $createdAt, $email);
                
                if ($updateStmt->execute()) {
                    // Restore quantity for ALL products in the order group
                    foreach ($ordersToUpdate as $order) {
                        $productName = $order['product_name'];
                        $quantity = $order['quantity'];
                        
                        // Update product quantity
                        $updateProductStmt = $conn->prepare("UPDATE product SET quantity = quantity + ? WHERE product_name = ?");
                        $updateProductStmt->bind_param("is", $quantity, $productName);
                        
                        if (!$updateProductStmt->execute()) {
                            throw new Exception("Failed to update product quantity for: $productName");
                        }
                        $updateProductStmt->close();
                    }
                    
                    $conn->commit();
                    echo 'success';
                } else {
                    $conn->rollback();
                    echo 'error: Failed to update order status';
                }
                $updateStmt->close();
                
            } catch (Exception $e) {
                $conn->rollback();
                echo 'error: ' . $e->getMessage();
            }
            
            $getAllOrdersStmt->close();
        } else {
            echo 'error: Order not found or does not belong to user';
        }
        $getCreatedAtStmt->close();
    } else {
        echo 'Database connection failed.';
    }
} else {
    echo 'Missing order ID or status.';
}
?>