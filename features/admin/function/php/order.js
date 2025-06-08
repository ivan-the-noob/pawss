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
    var button = $(event.relatedTarget); // Get the clicked button
    var modal = $(this); // Get the modal itself

    // Get data attributes of the button clicked
    var id = button.data('id');
    var name = button.data('name');
    var email = button.data('email');
    var contactNum = button.data('contact-num');
    var addressSearch = button.data('address-search');
    var paymentMethod = button.data('payment-method');
    var screenshot = button.data('screenshot');
    var referenceId = button.data('reference_id');
    var shippingFee = button.data('shipping-fee');
    var totalAmount = button.data('total-amount');
    var productName = button.data('product-name');
var productImg = button.data('product-img');
var quantity = button.data('quantity');
var cost = button.data('cost');
var subTotal = button.data('sub-total');
    var latitude = button.data('latitude');
    var longitude = button.data('longitude');

    // Reset modal content each time before updating it
    modal.find('#modalId').val(id);
    modal.find('#modalName').val(name);
    modal.find('#modalEmail').val(email);
    modal.find('#modalContactNum').val(contactNum);
    modal.find('#modalAddressSearch').val(addressSearch);
    modal.find('#modalPaymentMethod').val(paymentMethod);
    
    var screenshotPath = "../../../../assets/img/check-out/" + screenshot; 
    modal.find('#modalScreenshot').attr('src', screenshotPath);
    modal.find('#modalReferenceId').val(referenceId);
    modal.find('#modalShippingFee').val(shippingFee);
    modal.find('#modalTotalAmount').val(totalAmount);
    
    // Set the products inside the modal
  var productCardHtml = `
    <div class="row">
        <div class="col-md-4">
            <img src="../../../../assets/img/product/${productImg}" class="img-fluid" alt="${productName}">
        </div>
        <div class="col-md-8">
            <h5 class="card-title">${productName}</h5>
            <div class="d-flex justify-content-between">
                <p class="card-text">Qty: ${quantity}x</p>
                <p class="card-text">₱${parseFloat(subTotal).toFixed(2)}</p>
            </div>
        </div>
    </div>
    <hr>
`;

modal.find('#productCards').html(productCardHtml);



    // Initialize the map if coordinates exist
    if (latitude && longitude) {
        initMap(latitude, longitude);
    }
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

$('#viewModal').on('shown.bs.modal', function() {
    var modal = $(this);
    modal.find('#declineButton').off('click').on('click', function() {
        $.ajax({
            url: '../../function/php/delete_user.php', 
            type: 'POST',
            data: { user_id: modal.find('#modalId').val() },
            success: function(response) {
                alert('Appointment declined and deleted.');
                location.reload(); 
            },
            error: function() {
                alert('Error occurred while declining the appointment.');
            }
        });
    });
});
