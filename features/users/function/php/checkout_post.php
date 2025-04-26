<?php
session_start();
require '../../../../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Sanitize and retrieve POST data
    $name = $_POST['name'];
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : null; 
    $contact_num = $_POST['contact-num'];
    $address_search = $_POST['address-search'];
    $payment_method = $_POST['paymentMethod'];
    $screenshot = isset($_FILES['screenshot']) ? $_FILES['screenshot']['name'] : null;
    $reference_id = isset($_POST['reference']) ? $_POST['reference'] : null;

    // Decode JSON-encoded arrays
    $product_names = json_decode($_POST['product_name'][0], true);
    $quantities = json_decode($_POST['quantity'][0], true);
    $product_imgs = json_decode($_POST['product_img'][0], true);
    $costs = json_decode($_POST['cost'][0], true); // Added line for cost

    $shipping_fee = isset($_POST['shipping-fee']) ? $_POST['shipping-fee'] : 0;
    $total_amount = isset($_POST['total-amount']) ? $_POST['total-amount'] : 0;
    
    // Add 'from_cart' value: true if the order is from the cart
    $from_cart = isset($_POST['from_cart']) && $_POST['from_cart'] == 'true' ? 1 : 0;

    // Handle file upload if screenshot is provided
    if ($screenshot) {
        $upload_dir = "../../../../assets/img/product/";
        $upload_file = $upload_dir . basename($_FILES["screenshot"]["name"]);
        if (!move_uploaded_file($_FILES["screenshot"]["tmp_name"], $upload_file)) {
            echo "Failed to upload screenshot.";
        }
    }

    // Prepare the SQL statement for insertion
    $stmt = $conn->prepare(
        "INSERT INTO checkout 
        (name, email, contact_num, address_search, payment_method, screenshot, reference_id, 
        product_name, quantity, sub_total, shipping_fee, total_amount, product_img, cost, from_cart)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) "
    );

    foreach ($product_names as $index => $product_name) {
        $quantity = $quantities[$index];
        $sub_total = $quantity * 49.00; // Example computation (replace with your logic)
        $product_img = $product_imgs[$index];
        $cost = $costs[$index]; // Get the cost for the current product

        // Bind parameters to the SQL statement
        $stmt->bind_param(
            "ssssssssdddssss", 
            $name, $email, $contact_num, $address_search, $payment_method, 
            $screenshot, $reference_id, $product_name, $quantity, $sub_total, 
            $shipping_fee, $total_amount, $product_img, $cost, $from_cart
        );

        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }

        // After inserting the checkout, update the product quantity
        $updateProductSql = "SELECT quantity FROM product WHERE product_name = ?";
        $updateProductStmt = $conn->prepare($updateProductSql);
        $updateProductStmt->bind_param("s", $product_name);
        $updateProductStmt->execute();
        $updateProductResult = $updateProductStmt->get_result();

        if ($updateProductResult->num_rows > 0) {
            $productRow = $updateProductResult->fetch_assoc();
            $newQuantity = $productRow['quantity'] - $quantity;

            if ($newQuantity >= 0) {
                // Update the product quantity in the product table
                $updateQuantitySql = "UPDATE product SET quantity = ? WHERE product_name = ?";
                $updateQuantityStmt = $conn->prepare($updateQuantitySql);
                $updateQuantityStmt->bind_param("is", $newQuantity, $product_name);
                $updateQuantityStmt->execute();
                $updateQuantityStmt->close();
            } else {
                echo "Error: Not enough stock for product: $product_name";
            }
        }

        $updateProductStmt->close();
    }

    if ($stmt->error) {
        echo "Final Error: " . $stmt->error;
    } else {
        Header('Location:../../web/api/my-orders.php');
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
