<?php
require '../../../../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $sql = "DELETE FROM service_list WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: ../../web/api/service-list.php?status=deleted");
            exit();
        } else {
            header("Location: ../../web/api/service-list.php?status=delete_error");
            exit();
        }
    } catch (Exception $e) {
        header("Location: ../../web/api/service-list.php?status=error&message=" . urlencode($e->getMessage()));
        exit();
    } finally {
        if (isset($stmt)) $stmt->close();
        $conn->close();
    }
} 
?>