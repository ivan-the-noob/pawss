<?php
include '../../../../db.php';

// Function to check and update order statuses
function checkOrderStatuses($conn) {
    // Get all orders with 'to-receive' status older than 5 days
    $fiveDaysAgo = date('Y-m-d H:i:s', strtotime('-5 days'));
    $sql = "SELECT * FROM checkout WHERE status = 'to-receive' AND created_at <= '$fiveDaysAgo'";
    $result = $conn->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        $orderDate = new DateTime($row['created_at']);
        $currentDate = new DateTime();
        $daysDiff = $currentDate->diff($orderDate)->days;
        
        // If more than 5 days but less than 7 days
        if ($daysDiff > 5 && $daysDiff <= 7) {
            // Check if notification already sent
            $checkNotification = "SELECT * FROM notification WHERE email = '{$row['email']}' AND message LIKE 'Your product has been delivered%'";
            $notificationResult = $conn->query($checkNotification);
            
            if ($notificationResult->num_rows == 0) {
                // Send notification
                $message = "Your product has been delivered, please double check and click receive.";
                $insertNotification = "INSERT INTO notification (email, message, created_at) 
                                      VALUES ('{$row['email']}', '$message', NOW())";
                $conn->query($insertNotification);
            }
        } 
        // If more than 7 days
        elseif ($daysDiff > 7) {
            // Update status to 'received-order'
            $updateStatus = "UPDATE checkout SET status = 'received-order' WHERE id = {$row['id']}";
            $conn->query($updateStatus);
            
            // Add completion notification
            $message = "Your order has been automatically marked as received after 7 days.";
            $insertNotification = "INSERT INTO notification (email, message, created_at) 
                                  VALUES ('{$row['email']}', '$message', NOW())";
            $conn->query($insertNotification);
        }
    }
}

// Run the status check
checkOrderStatuses($conn);

$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT c.*, u.latitude, u.longitude 
        FROM checkout c 
        LEFT JOIN users u ON c.email = u.email
        WHERE c.status = 'to-receive'
        ORDER BY c.created_at DESC
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$count = $offset + 1;
$totalRows = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total = $row['sub_total'] + $row['shipping_fee'];
        
        // Check if this email has a pending notification
 $notificationSql = "SELECT * FROM notification WHERE email = '{$row['email']}' AND message LIKE 'Your product has been delivered%'";
        $notificationResult = $conn->query($notificationSql);
        $hasNotification = $notificationResult->num_rows > 0;
        
        echo "<tr" . ($hasNotification ? " class='table-warning'" : "") . ">";
        echo "<td>$count</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        
        // Add days info column
        $orderDate = new DateTime($row['created_at']);
        $currentDate = new DateTime();
        $daysDiff = $currentDate->diff($orderDate)->days;
      
        
        echo "<td class='d-flex gap-2 justify-content-center'>";
        echo "<button class='btn btn-info' data-toggle='modal' data-target='#viewModal'
            data-id='" . htmlspecialchars($row['id']) . "'
            data-name='" . htmlspecialchars($row['name']) . "'
            data-email='" . htmlspecialchars($row['email']) . "'
            data-contact-num='" . htmlspecialchars($row['contact_num']) . "'
            data-address-search='" . htmlspecialchars($row['address_search']) . "'
            data-payment-method='" . htmlspecialchars($row['payment_method']) . "'
            data-products='" . htmlspecialchars(json_encode([[
                'product_name' => $row['product_name'],
                'product_img' => $row['product_img'],
                'quantity' => $row['quantity'],
                'cost' => $row['cost'],
                'sub_total' => $row['sub_total']
            ]])) . "'
            data-shipping-fee='" . htmlspecialchars($row['shipping_fee']) . "'
            data-total-amount='" . htmlspecialchars($total) . "'
            data-latitude='" . htmlspecialchars($row['latitude']) . "'
            data-longitude='" . htmlspecialchars($row['longitude']) . "'
            data-screenshot='" . htmlspecialchars($row['screenshot']) . "'
            data-reference_id='" . htmlspecialchars($row['reference_id']) . "'>View</button>";
        
        // Add notification badge if needed
        if ($hasNotification) {
            echo "<span class='badge bg-danger'>!</span>";
        }
        
        echo "</td>";
        echo "</tr>";
        $count++;
    }

    // Pagination
    $countSql = "SELECT COUNT(*) AS total FROM checkout WHERE status = 'to-receive'";
    $countResult = $conn->query($countSql);
    $totalRows = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);
} else {
    echo "<tr><td colspan='5'>No records found</td></tr>";
}
?>

<?php if (isset($totalPages) && $totalPages > 1): ?>
    <ul class="pagination justify-content-end mt-3 px-lg-5" id="paginationControls">
        <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
            <a class="page-link" href="?page=<?php echo $page - 1; ?>">&lt;</a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
            <a class="page-link" href="?page=<?php echo $page + 1; ?>">&gt;</a>
        </li>
    </ul>
<?php endif; ?>

<?php
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
} elseif (isset($_GET['delete_message'])) {
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
                    <div class="col-md-6">
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
                            <div id="map" style="height: 300px; width: 100%;" class="map"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Payment</h5>
                        <div class="form-group">
                            <label for="modalPaymentMethod">Payment Method:</label>
                            <input type="text" class="form-control form-order" id="modalPaymentMethod" readonly>
                        </div>
                       <div id="gcashFields" style="display: none;">
                        <div class="form-group">
                            <label for="modalScreenshot">Screenshot:</label>
                            <img id="modalScreenshot" src="" class="img-fluid screenshots" alt="Screenshot">
                        </div>
                        <div class="form-group">
                            <label for="modalReferenceId">Reference ID:</label>
                            <input type="text" class="form-control form-order" id="modalReferenceId" readonly>
                        </div>
                    </div>
                    <script>
                        $('#viewModal').on('show.bs.modal', function (event) {
                            var button = $(event.relatedTarget);

                            var paymentMethod = button.data('payment-method');
                            var screenshot = button.data('screenshot');
                            var referenceId = button.data('reference_id');

                            if (paymentMethod === 'gcash') {
                                $('#gcashFields').show();
                                $('#modalScreenshot').attr('src', screenshot);
                                $('#modalReferenceId').val(referenceId);
                            } else {
                                $('#gcashFields').hide();
                                $('#modalScreenshot').attr('src', '');
                                $('#modalReferenceId').val('');
                            }
                        });
                        </script>
                    </div>
                </div>
                <!-- Hidden Inputs -->
                <form id="appointmentForm" action="../../function/php/approve_to-receive.php" method="POST">
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