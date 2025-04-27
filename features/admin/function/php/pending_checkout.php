<?php
include '../../../../db.php';

$limit = 10;  // Declare limit for pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Get the current page
$offset = ($page - 1) * $limit; // Calculate the offset for pagination

$sql = "SELECT c.*, u.latitude, u.longitude, c.screenshot, c.reference_id, p.product_name, p.product_img, p.quantity, p.cost, p.sub_total
        FROM checkout c
        LEFT JOIN users u ON c.email = u.email
        LEFT JOIN product p ON c.id = p.id  -- Assuming a products table with checkout_id
        WHERE c.status = 'orders'";
$result = $conn->query($sql);

$data = [];

if ($result) {
    // Loop through the query results
    while ($row = $result->fetch_assoc()) {
        $email = $row['email'];
        $uniqueKey = $row['id']; // Use 'id' as a unique key

        if (!isset($data[$uniqueKey])) {
            // Initialize the data entry with an empty products array
            $data[$uniqueKey] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'email' => $row['email'],
                'contact_num' => $row['contact_num'],
                'address_search' => $row['address_search'],
                'payment_method' => $row['payment_method'],
                'shipping_fee' => $row['shipping_fee'],
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'screenshot' => $row['screenshot'],
                'reference_id' => $row['reference_id'],
                'products' => [], // Initialize products array
                'total_amount' => 0,
            ];
        }

        // Add product details to the corresponding entry
        $data[$uniqueKey]['products'][] = [
            'product_name' => $row['product_name'],
            'product_img' => $row['product_img'],
            'quantity' => $row['quantity'],
            'cost' => $row['cost'],
            'sub_total' => $row['sub_total'],
        ];

        // Calculate the total amount for the order
        $data[$uniqueKey]['total_amount'] += $row['sub_total'];
    }

    foreach ($data as &$details) {
        // Include the shipping fee in the total amount
        $details['total_amount'] += $details['shipping_fee'];
    }

    // Pagination setup
    $totalRows = count($data);
    $totalPages = ceil($totalRows / $limit);
    $paginatedData = array_slice($data, $offset, $limit, true);

    // Output the paginated data as table rows
    $count = $offset + 1;
    foreach ($paginatedData as $details) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($details['id']) . "</td>";
        echo "<td>" . htmlspecialchars($details['name']) . "</td>";
        echo "<td>" . htmlspecialchars($details['email']) . "</td>";
        echo "<td class='d-flex gap-2 justify-content-center'>";
        echo "<button class='btn btn-info' data-toggle='modal' data-target='#viewModal'
        data-id='" . htmlspecialchars($details['id']) . "'
        data-name='" . htmlspecialchars($details['name']) . "'
        data-email='" . htmlspecialchars($details['email']) . "'
        data-contact-num='" . htmlspecialchars($details['contact_num']) . "'
        data-address-search='" . htmlspecialchars($details['address_search']) . "'
        data-payment-method='" . htmlspecialchars($details['payment_method']) . "'
        data-products='" . htmlspecialchars(json_encode($details['products'])) . "'
        data-shipping-fee='" . htmlspecialchars($details['shipping_fee']) . "'
        data-total-amount='" . htmlspecialchars($details['total_amount']) . "'
        data-latitude='" . htmlspecialchars($details['latitude']) . "' 
        data-longitude='" . htmlspecialchars($details['longitude']) . "'
        data-screenshot='" . htmlspecialchars($details['screenshot']) . "'
        data-reference_id='" . htmlspecialchars($details['reference_id']) . "'>View</button>";
        echo "</td>";
        echo "</tr>";
        $count++;
    }
} else {
    echo "Error: " . $conn->error;
}
?>
