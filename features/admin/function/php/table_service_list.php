<?php
require '../../../../db.php';

try {
    // Fetch services
    $sql = "SELECT * FROM service_list";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Prepare statement for services failed: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
    $stmt->close();


    

    // Display service list
    if ($services) {
        foreach ($services as $service) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($service['id']) . "</td>"; 
            echo "<td>" . htmlspecialchars($service['service_type']) . "</td>"; 
            echo "<td>" . htmlspecialchars($service['service_name']) . "</td>";
            echo "<td>₱" . htmlspecialchars($service['cost']) . "</td>";
            echo "<td>" . htmlspecialchars($service['discount']) . "%</td>";
            echo "<td>" . htmlspecialchars($service['info']) . "</td>";
            
            echo '<td>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal' . $service['id'] . '"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal' . $service['id'] . '"><i class="fas fa-trash-alt"></i></button>
                  </td>';
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No services found.</td></tr>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
