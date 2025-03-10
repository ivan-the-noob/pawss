<?php
include '../../../../db.php';

$id = $_POST['appointment_id']; 

$sql_update = "UPDATE appointment SET status = 'on-going' WHERE id = ?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    
    header("Location: ../../web/api/app-req.php?message=Appointment status updated to 'waiting' successfully");
    exit();
} else {
    echo "Error updating status: " . $conn->error;
}

$conn->close();
?>
