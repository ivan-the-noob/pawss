<?php

require '../../../../db.php';

session_start();

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

    $sql = "SELECT product_img FROM product WHERE product_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productName);  
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $productImg = $row['product_img']; 
    } else {
        $productImg = 'default_image.jpg'; 
    }

    $sql = "INSERT INTO checkout (name, contact_num, address_search, payment_method, screenshot, reference_id, product_name, quantity, cost, sub_total, shipping_fee, total_amount, product_img, email)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
        header("Location: ../../web/api/my-orders.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
