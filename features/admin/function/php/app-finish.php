<?php
include '../../../../db.php';

$sql = "SELECT * FROM appointment WHERE status = 'finish'";
$result = $conn->query($sql);


if (!$result) {
    echo "Error: " . $conn->error;
} else {
    if ($result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            $id = $row['id']; 
            echo "<tr>";
            echo "<td>$count</td>";
            echo "<td>" . htmlspecialchars($row['owner_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['service']) . "</td>"; 
            
            echo "<td>";
            echo "<button class='btn btn-info' data-toggle='modal' data-target='#viewModal' 
            data-id='" . $id . "' 
            data-owner='" . htmlspecialchars($row['owner_name']) . "' 
            data-email='" . htmlspecialchars($row['email']) . "' 
            data-service='" . htmlspecialchars($row['service']) . "' 
            data-contact='" . htmlspecialchars($row['contact_num']) . "' 
            data-barangay='" . htmlspecialchars($row['barangay']) . "' 
            data-pet-type='" . htmlspecialchars($row['pet_type']) . "' 
            data-breed='" . htmlspecialchars($row['breed']) . "' 
            data-age='" . htmlspecialchars($row['age']) . "' 
            data-payment='" . htmlspecialchars($row['payment']) . "' 
            data-appointment-time='" . htmlspecialchars($row['appointment_time']) . "' 
            data-appointment-date='" . htmlspecialchars($row['appointment_date']) . "' 
            data-latitude='" . htmlspecialchars($row['latitude']) . "' 
            data-longitude='" . htmlspecialchars($row['longitude']) . "' 
            data-add-info='" . htmlspecialchars($row['add_info']) . "'>View</button> ";
            echo "<form action='../../function/php/delete_user.php' method='POST' style='display:inline;'>";
            echo "<input type='hidden' name='user_id' value='" . $id . "' />";
            echo "</form>";
            echo "</td>";

            echo "</tr>";
            $count++;
        }
    } else {
        echo "<tr><td colspan='5'>No appointments found</td></tr>";
    }
    $result->free();
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
            <div class="modal-header d-flex justify-content-between">
                <h5 class="modal-title" id="viewModalLabel">Appointment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mt-4 mb-4">
                        <h6>Owner Information</h6>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalOwner">Name:</label>
                            <input type="text" class="form-control" id="modalOwner" readonly>
                        </div>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalContact">Contact Number:</label>
                            <input type="text" class="form-control" id="modalContact" readonly>
                        </div>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalEmail">Email:</label>
                            <input type="text" class="form-control" id="modalEmail" readonly>
                        </div>
                    </div>

                    <div class="col-md-6 mt-4 mb-4">
                        <h6>Address Information</h6>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalBarangay">Barangay:</label>
                            <input type="text" class="form-control" id="modalBarangay" readonly>
                        </div>
                        <h6>Location Map</h6>
                        <div id="map" style="height: 300px; width: 100%;"></div>
                        <div class="form-group mt-2 mb-2">
                            <label for="add-info">Street Name</label>
                            <input type="text" class="form-control" id="add-info" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    
                    <div class="col-md-6 mt-4 mb-4">
                        <h6>Pet Information</h6>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalPetType">Pet Type:</label>
                            <input type="text" class="form-control" id="modalPetType" readonly>
                        </div>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalBreed">Breed:</label>
                            <input type="text" class="form-control" id="modalBreed" readonly>
                        </div>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalAge">Age:</label>
                            <input type="text" class="form-control" id="modalAge" readonly>
                        </div>
                    </div>

                    <div class="col-md-6 mt-4 mb-4">
                        <h6>Services</h6>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalService">Service:</label>
                            <input type="text" class="form-control" id="modalService" readonly>
                        </div>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalAppointmentTime">Appointment Time:</label>
                            <input type="text" class="form-control" id="modalAppointmentTime" readonly>
                        </div>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalAppointmentDate">Appointment Date:</label>
                            <input type="text" class="form-control" id="modalAppointmentDate" readonly>
                        </div>
                        <div class="form-group mt-2 mb-2">
                            <label for="modalPayment">Total Payment:</label>
                            <input type="text" class="form-control" id="modalPayment" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <form id="appointmentForm" action="../../function/php/approve_appointment.php" method="POST">
                <input type="hidden" name="appointment_id" id="modalUserId">
                <input type="hidden" name="owner_name" id="modalOwnerHidden">
                <input type="hidden" name="email" id="modalEmailHidden">
                <input type="hidden" name="service" id="modalServiceHidden">
                <input type="hidden" name="contact_num" id="modalContactHidden">
                <input type="hidden" name="barangay" id="modalBarangayHidden">
                <input type="hidden" name="pet_type" id="modalPetTypeHidden">
                <input type="hidden" name="breed" id="modalBreedHidden">
                <input type="hidden" name="age" id="modalAgeHidden">
                <input type="hidden" name="payment" id="modalPaymentHidden">
                <input type="hidden" name="appointment_time" id="modalAppointmentTimeHidden">
                <input type="hidden" name="appointment_date" id="modalAppointmentDateHidden">
                <input type="hidden" name="latitude" id="modalLatitude">
                <input type="hidden" name="longitude" id="modalLongitude">
                <input type="hidden" name="add_info" id="add-info-hidden">
                                
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="declineButton" onclick="submitDecline()">Decline</button>
                    <button type="submit" class="btn btn-primary">Approve</button>
                </div>
            </form>

            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>



function submitDecline() {
    var form = document.getElementById('appointmentForm');
    var id = document.getElementById('modalUserId').value; 

    form.action = '../../function/php/delete_appointment.php'; 
    form.method = 'POST';

    var idInput = document.createElement("input");
    idInput.type = "hidden";
    idInput.name = "id"; 
    idInput.value = id;
    form.appendChild(idInput);

    form.submit();
}



$('#viewModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id'); 
    $('#modalUserId').val(id); 
});
    $('#viewModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); 
    var owner = button.data('owner');
    var email = button.data('email');
    var service = button.data('service');
    var contact = button.data('contact');
    var barangay = button.data('barangay');
    var petType = button.data('pet-type');
    var breed = button.data('breed');
    var age = button.data('age');
    var payment = button.data('payment');
    var appointmentTime = button.data('appointment-time');
    var appointmentDate = button.data('appointment-date');
    var latitude = button.data('latitude');
    var longitude = button.data('longitude');
    var additionalInfo = button.data('add-info'); 

    var modal = $(this);
    modal.find('#modalOwner').val(owner);
    modal.find('#modalEmail').val(email);
    modal.find('#modalService').val(service);
    modal.find('#modalContact').val(contact);
    modal.find('#modalBarangay').val(barangay);
    modal.find('#modalPetType').val(petType);
    modal.find('#modalBreed').val(breed);
    modal.find('#modalAge').val(age);
    modal.find('#modalPayment').val(payment);
    modal.find('#modalAppointmentTime').val(appointmentTime);
    modal.find('#modalAppointmentDate').val(appointmentDate);
    modal.find('#modalLatitude').val(latitude);
    modal.find('#modalLongitude').val(longitude);
    modal.find('#add-info').val(additionalInfo);

    initMap(latitude, longitude);
});

$('#viewModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        modal.find('#modalOwner').val(button.data('owner'));
        modal.find('#modalContact').val(button.data('contact'));
        modal.find('#modalEmail').val(button.data('email'));
        modal.find('#modalBarangay').val(button.data('barangay'));
        modal.find('#modalPetType').val(button.data('pet-type'));
        modal.find('#modalBreed').val(button.data('breed'));
        modal.find('#modalAge').val(button.data('age'));
        modal.find('#modalService').val(button.data('service'));
        modal.find('#modalAppointmentTime').val(button.data('appointment-time'));
        modal.find('#modalAppointmentDate').val(button.data('appointment-date'));
        modal.find('#modalPayment').val(button.data('payment'));
        modal.find('#modalUserId').val(button.data('id'));
        modal.find('#modalOwnerHidden').val(button.data('owner'));
        modal.find('#modalEmailHidden').val(button.data('email'));
        modal.find('#modalServiceHidden').val(button.data('service'));
        modal.find('#modalContactHidden').val(button.data('contact'));
        modal.find('#modalBarangayHidden').val(button.data('barangay'));
        modal.find('#modalPetTypeHidden').val(button.data('pet-type'));
        modal.find('#modalBreedHidden').val(button.data('breed'));
        modal.find('#modalAgeHidden').val(button.data('age'));
        modal.find('#modalPaymentHidden').val(button.data('payment'));
        modal.find('#modalAppointmentTimeHidden').val(button.data('appointment-time'));
        modal.find('#modalAppointmentDateHidden').val(button.data('appointment-date'));
        modal.find('#modalLatitude').val(button.data('latitude'));
        modal.find('#modalLongitude').val(button.data('longitude'));
        modal.find('#add-info-hidden').val(button.data('add-info'));
    });

    function initMap(lat, lng) {
        var mapOptions = {
            center: new google.maps.LatLng(lat, lng), 
            zoom: 18,
            mapTypeId: google.maps.MapTypeId.ROADMAP 
        };

        var map = new google.maps.Map(document.getElementById('map'), mapOptions);

        var marker = new google.maps.Marker({
            position: {lat: parseFloat(lat), lng: parseFloat(lng)},
            map: map,
            title: 'Appointment Location'
        });
    }

    modal.find('#declineButton').off('click').on('click', function() {
        $.ajax({
            url: '../../function/php/delete_user.php', 
            type: 'POST',
            data: { user_id: id },
            success: function(response) {
                alert('Appointment declined and deleted.');
                location.reload(); 
            },
            error: function() {
                alert('Error occurred while declining the appointment.');
            }
        });
    });

   

</script>
