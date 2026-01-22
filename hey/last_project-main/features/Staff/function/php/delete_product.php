<?php
require '../../../../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productId = $_POST['id'];

    // Get the product name before deletion
    $stmt = $conn->prepare("SELECT product_name FROM product WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->bind_result($productName);
    $stmt->fetch();
    $stmt->close();

    // Now delete the product
    $stmt = $conn->prepare("DELETE FROM product WHERE id = ?");
    $stmt->bind_param("i", $productId);

    if ($stmt->execute()) {
        // Pass product name as URL parameter
        header("Location: ../../web/api/product.php?deleted=" . urlencode($productName));
        exit();
    } else {
        echo "Error deleting product: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
