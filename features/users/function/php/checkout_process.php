<?php

require '../../../../db.php';

session_start();

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contactNum = $_POST['contact-num'];
    $addressSearch = $_POST['address-search'];
    $paymentMethod = $_POST['paymentMethod'] ?? 'null';
    $screenshot = $_FILES['screenshot']['name'] ?? '';
    $referenceId = $_POST['reference'] ?? '';
    $productName = $_POST['product-name'];
    $quantity = $_POST['quantity'];
    $cost = $_POST['cost'];
    $subTotal = $_POST['sub-total'] ?? 'null';
    $shippingFee = $_POST['shipping-fee'];
    $totalAmount = $_POST['total-amount'];
    $email = $_SESSION['email'] ?? ''; 

    // Fetch product image
    $sql = "SELECT product_img, quantity FROM product WHERE product_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productName);  
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $productImg = $row['product_img']; 
        $availableQuantity = $row['quantity'];
    } else {
        $productImg = 'default_image.jpg'; 
        $availableQuantity = 0; 
    }

    // Check if enough stock is available
    if ($availableQuantity >= $quantity) {
        // Insert checkout record, including created_at
        $sql = "INSERT INTO checkout (name, contact_num, address_search, payment_method, screenshot, reference_id, product_name, quantity, cost, sub_total, shipping_fee, total_amount, product_img, email, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssssssss", 
            $name, $contactNum, $addressSearch, $paymentMethod, $screenshot, 
            $referenceId, $productName, $quantity, $cost, $subTotal, 
            $shippingFee, $totalAmount, $productImg, $email
        );

        if ($screenshot) {
            move_uploaded_file($_FILES['screenshot']['tmp_name'], '../../../../assets/img/check-out/' . $screenshot);
        }

        if ($stmt->execute()) {
            // Update product quantity in product table
            $newQuantity = $availableQuantity - $quantity;
            $updateSql = "UPDATE product SET quantity = ? WHERE product_name = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("is", $newQuantity, $productName);
            $updateStmt->execute();
            $updateStmt->close();

            // Insert notification after successful checkout
            $notificationMessage = "Check Out Successfully, wait for confirmation.";
            $notifSql = "INSERT INTO notification (email, message) VALUES (?, ?)";
            $notifStmt = $conn->prepare($notifSql);
            $notifStmt->bind_param("ss", $email, $notificationMessage);
            $notifStmt->execute();
            $notifStmt->close();
        
            header("Location: ../../web/api/my-orders.php");
            exit();
        }
    } else {
        // Not enough stock
        echo "Not enough stock available for this product.";
    }

    $stmt->close();
    $conn->close();
}
?>
