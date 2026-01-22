<?php
include '../../../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; 

    $sql = "UPDATE appointment SET status = 'cancel' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        
        header("Location: ../../web/api/app-req.php?update_message=Appointment status changed to 'cancel' successfully");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Invalid request method.";
}
?>
