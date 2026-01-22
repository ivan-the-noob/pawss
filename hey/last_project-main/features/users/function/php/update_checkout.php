<?php
// Ensure the session is started
session_start();

// Include your database connection here
include('../../../../db.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the posted data
    $order_id = $_POST['id'];  // Now we are using 'id' instead of 'order_id'
    $quantity = $_POST['quantity'];

    // Update the quantity in the database
    $sql_update = "UPDATE checkout SET quantity = '$quantity' WHERE id = '$order_id' AND email = '{$_SESSION['email']}'";

    if ($conn->query($sql_update) === TRUE) {
        echo "Order quantity updated successfully!";
    } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
    }

    // Redirect back to the page with updated information
    header("Location: ../../web/api/my-orders.php");
    exit;
}
?>
