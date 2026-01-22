<?php
require '../../../../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $service_type = $_POST['service_type'];
    $service_name = $_POST['service_name'];
    $cost = $_POST['cost'];
    $discount = $_POST['discount'];
    $info = $_POST['info'];

    if (!empty($service_type) && !empty($service_name) && !empty($cost)) {
        try {
            $sql = "UPDATE service_list 
                    SET service_type = ?, service_name = ?, cost = ?, discount = ?, info = ? 
                    WHERE id = ?";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }

            $stmt->bind_param("sssssi", $service_type, $service_name, $cost, $discount, $info, $id);

            if ($stmt->execute()) {
                header("Location: ../../web/api/service-list.php?status=success");
                exit();
            } else {
                echo "Error updating the record.";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Please fill in all required fields.";
    }
}

$conn->close();
?>
