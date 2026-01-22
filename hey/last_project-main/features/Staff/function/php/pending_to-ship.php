<?php
include '../../../../db.php';

$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT c.*, u.latitude, u.longitude 
        FROM checkout c 
        LEFT JOIN users u ON c.email = u.email
        WHERE c.status = 'to-ship'
        ORDER BY c.created_at DESC
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$count = $offset + 1;
$totalRows = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total = $row['sub_total'] + $row['shipping_fee'];
        echo "<tr>";
        echo "<td>$count</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
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
        echo "</td>";
        echo "</tr>";
        $count++;
    }

    // Pagination
    $countSql = "SELECT COUNT(*) AS total FROM checkout WHERE status = 'to-ship'";
    $countResult = $conn->query($countSql);
    $totalRows = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);
} else {
    echo "<tr><td colspan='4'>No records found</td></tr>";
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
                            <div id="map" style="height: 300px; width: 100%;" class="map"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
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
                    
                    <div class="col-md-4">
                            <div id="productCards" class="row product_order"></div>
                        </div>
                </div>
                <!-- Hidden Inputs -->
                <form id="appointmentForm" action="../../function/php/approve_to-ship.php" method="POST">
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
<script>




   

</script>