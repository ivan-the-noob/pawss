<?php
require '../../../../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    if (empty($id) || empty($status)) {
        echo 'error: missing id or status';
        exit;
    }

    // Update appointment status
    $query = "UPDATE appointment SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo 'error: prepare failed';
        exit;
    }

    $stmt->bind_param('si', $status, $id);

    if ($stmt->execute()) {
        // If status is "waiting" (accepted), insert into notification
        if ($status === 'waiting') {
            // Get email from appointment (if needed)
            $select = $conn->prepare("SELECT email FROM appointment WHERE id = ?");
            $select->bind_param('i', $id);
            $select->execute();
            $result = $select->get_result();
            $row = $result->fetch_assoc();
            $email = $row['email'] ?? 'N/A';
            $select->close();

            // Notification message
            $message = "Your appointment has been accepted. Please wait for further instructions.";

            // Insert into notification table
            $insert = $conn->prepare("INSERT INTO notification (id, email, message) VALUES (?, ?, ?)");
            $insert->bind_param('iss', $id, $email, $message);
            $insert->execute();
            $insert->close();
        }

        echo 'success';
    } else {
        echo 'error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
