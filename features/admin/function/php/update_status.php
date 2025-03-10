<?php
    require '../../../../db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];
        $status = $_POST['status'];
        if (empty($id) || empty($status)) {
            echo 'error: missing id or status';
            exit;
        }
        $query = "UPDATE appointment SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            echo 'error: prepare failed'; 
            exit;
        }
        $stmt->bind_param('si', $status, $id);
        if ($stmt->execute()) {
            echo 'success'; 
        } else {
            echo 'error: ' . $stmt->error; 
        }
        $stmt->close();
        $conn->close();
    }
?>
