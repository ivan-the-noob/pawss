<?php
include '../../../../db.php';


$sql = "SELECT c.*, u.latitude, u.longitude, c.screenshot, c.reference_id 
        FROM checkout c 
        LEFT JOIN users u ON c.email = u.email
        WHERE c.status = 'orders'";
$result = $conn->query($sql);

if (!$result) {
    echo "Error: " . $conn->error;
} else {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $email = $row['email'];
        if (!isset($data[$email])) {
            $data[$email] = [
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
                'products' => [],
                'total_amount' => 0, 
            ];
        }

        $data[$email]['products'][] = [
            'product_name' => $row['product_name'],
            'product_img' => $row['product_img'],
            'quantity' => $row['quantity'],
            'cost' => $row['cost'],
            'sub_total' => $row['sub_total'],
        ];

        $data[$email]['total_amount'] += $row['sub_total'];
    }

    foreach ($data as $email => &$details) {
        $details['total_amount'] += $details['shipping_fee'];
    }

    $count = 1;
    foreach ($data as $email => $details) {
        echo "<tr>";
        echo "<td>$count</td>";
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
            data-reference_id='" . htmlspecialchars($details['reference_id']) . "'>
            View</button>";
        echo "<button type='submit' class='btn btn-primary'>Approve</button>";
        echo "<button type='button' class='btn btn-danger' onclick='submitDeclineForm(" . htmlspecialchars($details['id']) . ")'>Decline</button>";
    

        echo "</td>";
        echo "</tr>";
        $count++;
    }
}

if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
    echo "
    <script src='https://unpkg.com/sweetalert/dist/sweetalert.min.js'></script>
    <script>
        swal({
            title: 'Success!',
            text: '$message',
            icon: 'success',
            button: 'OK',
        });
    </script>
    ";
} else if (isset($_GET['delete_message'])) {
    $deleteMessage = htmlspecialchars($_GET['delete_message']);
    echo "
    <script src='https://unpkg.com/sweetalert/dist/sweetalert.min.js'></script>
    <script>
       swal({
            title: 'Appointment Deleted!',
            text: '$deleteMessage',
            icon: 'error',
            button: 'OK',
            icon: '../../../../assets/img/icon/trash.gif'
        });
    </script>
    ";
}
?>

<!-- Bootstrap Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header justify-content-between">
                <h5 class="modal-title" id="viewModalLabel">Order Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Owner Information</h5>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalId">ID:</label>
                            <input type="text" class="form-control form-order" id="modalId" readonly>
                        </div>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalName">Name:</label>
                            <input type="text" class="form-control form-order" id="modalName" readonly>
                        </div>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalEmail">Email:</label>
                            <input type="text" class="form-control form-order" id="modalEmail" readonly>
                        </div>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalContactNum">Contact Number:</label>
                            <input type="text" class="form-control form-order" id="modalContactNum" readonly>
                        </div>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalAddressSearch">Address Search:</label>
                            <input type="text" class="form-control form-order" id="modalAddressSearch" readonly>
                        </div>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalMap">Location:</label>
                            <div id="map" style="height: 300px; wi form-orderdth: 100%;" class="map"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h5>Payment</h5>
                        <div class="form-group">
                            <label for="modalPaymentMethod">Payment Method:</label>
                            <input type="text" class="form-control form-order" id="modalPaymentMethod" readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalScreenshot">Screenshot:</label>
                            <img id="modalScreenshot" src="" class="img-fluid screenshots" alt="Screenshot">
                        </div>
                        <div class="form-group">
                            <label for="modalReferenceId">Reference ID:</label>
                            <input type="text" class="form-control form-order" id="modalReferenceId" readonly>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                            <div id="productCards" class="row product_order"></div>
                        </div>
                </div>
                <!-- Hidden Inputs -->
                <form id="appointmentForm" action="../../function/php/approve_order.php" method="POST">
                <input type="hidden" name="id" id="hiddenId">
                <input type="hidden" name="name" id="hiddenName">
                <input type="hidden" name="email" id="hiddenEmail">
                <input type="hidden" name="contact_num" id="hiddenContactNum">
                <input type="hidden" name="address_search" id="hiddenAddressSearch">
                <input type="hidden" name="payment_method" id="hiddenPaymentMethod">
                <input type="hidden" name="screenshot" id="hiddenScreenshot">
                <input type="hidden" name="reference_id" id="hiddenReferenceId">
                <input type="hidden" name="product_name" id="hiddenProductName">
                <input type="hidden" name="cost" id="hiddenCost">
                <input type="hidden" name="sub_total" id="hiddenSubTotal">
                <input type="hidden" name="shipping_fee" id="hiddenShippingFee">
                <input type="hidden" name="total_amount" id="hiddenTotalAmount">
                <input type="hidden" name="product_img" id="hiddenProductImg">
                <input type="hidden" name="status" id="hiddenStatus">
                <input type="hidden" name="quantity" id="hiddenQuantity">

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" onclick="submitDeclineForm()">Decline</button>
                        <button type="submit" class="btn btn-primary">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="../../function/script/decline_order.js"></script>
