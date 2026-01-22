<?php
require '../../../../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['selected_items'])) {
    $selectedItems = $_POST['selected_items'];

    foreach ($selectedItems as $productId) {
        $stmt = $pdo->prepare("INSERT INTO checkout (product_id, product_name, total_price, quantity) 
                               SELECT product_id, product_name, total_price, quantity FROM cart WHERE product_id = ?");
        $stmt->execute([$productId]);
    }

    // Optionally clear these items from the cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE product_id IN (" . implode(',', array_fill(0, count($selectedItems), '?')) . ")");
    $stmt->execute($selectedItems);

    header('Location: ../../web/api/cart.php?message=Checkout successful');
    exit();
} else {
    header('Location: ../../web/api/cart.php?message=No items selected');
    exit();
}
?>