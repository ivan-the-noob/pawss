<?php 
require '../../../../db.php';
if (isset($_POST['cancel_appointment'])) {
    $appointmentId = $_POST['appointment_id'];
    $reason = $_POST['cancel_reason'];

    // Update status to 'cancel' and save the reason
    $stmt = $conn->prepare("UPDATE appointment SET status = 'cancel', cancel_reason = ? WHERE id = ?");
    $stmt->bind_param("si", $reason, $appointmentId);
    $stmt->execute();

    // Redirect back or show a success message
    header("Location: ../../web/api/appointment.php");
    exit();
}

?>