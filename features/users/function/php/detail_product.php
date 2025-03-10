<?php 

// product_detail_script.php
require '../../../../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the query to fetch the specific product
    $stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch the product details if found
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product); // Return as JSON
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
    $stmt->close();
}

$conn->close(); 

?>