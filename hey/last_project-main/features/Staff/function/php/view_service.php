<?php

require 'db.php';

try {
    $sql = "SELECT * FROM service_list";  
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    $services = $result->fetch_all(MYSQLI_ASSOC);  
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$stmt->close();
$conn->close();
?>
