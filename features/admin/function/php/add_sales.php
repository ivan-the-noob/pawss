<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addSales') {
    include '../../../../db.php';

    $salesAmount = isset($_POST['salesAmount']) ? floatval($_POST['salesAmount']) : 0;
    $salesDate = isset($_POST['salesDate']) ? $_POST['salesDate'] : null;

    if ($salesAmount > 0 && $salesDate) {
        $sql = "INSERT INTO manual_input (created_at, sales_amount) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sd", $salesDate, $salesAmount);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Database error."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid input."]);
    }

    $conn->close();
    exit;
}
?>
