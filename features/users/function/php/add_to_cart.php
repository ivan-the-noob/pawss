<?php
session_start();
require '../../../../db.php';

if (!isset($_SESSION['email'])) {
    die("User is not logged in.");
}

$productId = $_POST['product_id'];
$productName = $_POST['product_name'];
$productPrice = $_POST['product_price'];
$quantity = $_POST['quantity'];
$totalPrice = $_POST['total_price'];
$productImage = $_POST['product_image'];
$email = $_SESSION['email'];

// Check if the product already exists in the cart for the logged-in user
$sql = "SELECT * FROM cart WHERE product_id = ? AND email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $productId, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $existingProduct = $result->fetch_assoc();
    $newQuantity = $existingProduct['quantity'] + $quantity;
    $newTotalPrice = $newQuantity * $productPrice;

    $updateSql = "UPDATE cart SET quantity = ?, total_price = ? WHERE product_id = ? AND email = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("idis", $newQuantity, $newTotalPrice, $productId, $email);

    if ($updateStmt->execute()) {
        header("Location: ../../web/api/cart.php?message=Product quantity updated successfully.");
    } else {
        header("Location: ../../web/api/cart.php?message=Error updating product quantity: " . $conn->error);
    }
    $updateStmt->close();
} else {
    $insertSql = "INSERT INTO cart (product_id, product_name, product_price, quantity, total_price, product_image, email)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("isidiss", $productId, $productName, $productPrice, $quantity, $totalPrice, $productImage, $email);

    if ($insertStmt->execute()) {
        header("Location: ../../web/api/cart.php?message=Product added to cart successfully.");
    } else {
        header("Location: ../../web/api/cart.php?message=Error adding product to cart: " . $conn->error);
    }
    $insertStmt->close();
}

$stmt->close();
$conn->close();
?>
