<?php

require '../../../../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $service_type = $_POST['service_type'];
    $service_name = $_POST['service_name'];
    $cost = $_POST['cost'];
    $discount = $_POST['discount'];
    $info = $_POST['info'];  

    if (!empty($service_type) && !empty($service_name) && !empty($cost)) {
        try {
            $sql = "INSERT INTO service_list (service_type, service_name, cost, discount, info) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }

            $stmt->bind_param("sssss", $service_type, $service_name, $cost, $discount, $info);

            $stmt->execute();
            
            header('Location: ../../web/api/service-list.php');
            exit();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Please fill all required fields.";
    }
}

$conn->close();
?>
