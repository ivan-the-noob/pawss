function showGcashImage(imageUrl) {
    const fullImageUrl = "../../../../assets/img/gcash/" + imageUrl;
    document.getElementById('gcashImage').src = fullImageUrl;
}

function updateStatus(button, status) {
var appointmentId = button.getAttribute('data-id'); 
console.log("Appointment ID: " + appointmentId + ", Status: " + status);
var xhr = new XMLHttpRequest();
xhr.open('POST', '../../function/php/update_status.php', true);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
xhr.send('id=' + appointmentId + '&status=' + status);

xhr.onload = function() {
    if (xhr.status === 200) {
        var response = xhr.responseText;
        console.log("Response: " + response); 
        if (response === 'success') {
            alert('Status updated to ' + status); 
            location.reload(); 
        } else {
            alert('Failed to update status');
        }
    } else {
        alert('Error: ' + xhr.status);
    }
};
}

let map;

function showMap(lat, lng) {
    const location = { lat: parseFloat(lat), lng: parseFloat(lng) };

    if (map) {
        map.setCenter(location);
    } else {
        map = new google.maps.Map(document.getElementById('map'), {
            center: location,
            zoom: 15
        });
    }

    new google.maps.Marker({
        position: location,
        map: map,
        title: 'Appointment Location'
    });
}
