<?php
require '../../../../db.php';

if (isset($_GET['start_date'], $_GET['end_date'])) {
    $start = $_GET['start_date'];
    $end = $_GET['end_date'];

    // Set headers to download as Excel file
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=products_export_" . date("Ymd") . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Excel table headers
    echo "<table border='1'>";
    echo "<tr>
            <th>Product Name</th>
            <th>Description</th>
            <th>Cost</th>
            <th>Type</th>
            <th>Quantity</th>
            <th>Created At</th>
          </tr>";

    $stmt = $conn->prepare("SELECT product_name, description, cost, type, quantity, created_at FROM product WHERE created_at BETWEEN ? AND ?");
    $stmt->bind_param("ss", $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['product_name']}</td>";
        echo "<td>{$row['description']}</td>";
        echo "<td>{$row['cost']}</td>";
        echo "<td>{$row['type']}</td>";
        echo "<td>{$row['quantity']}</td>";
        echo "<td>" . date('Y-m-d', strtotime($row['created_at'])) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    exit;
} else {
    echo "Invalid date range.";
}
?>
