function showGcashImage(imageUrl) {
    const fullImageUrl = "../../../../assets/img/gcash/" + imageUrl;
    document.getElementById('gcashImage').src = fullImageUrl;
}


function acceptAppointment(button, status) {
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
     
                window.location.href = 'app-waiting.php';
            } else {
                window.location.href = 'app-waiting.php';
            }
        } else {
            alert('Error: ' + xhr.status);
        }
    };
}

// Keep the original updateStatus function for cancel operations
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
                if (status === 'waiting') {
                    // Redirect for waiting status
                    window.location.href = 'app-waiting.php';
                } else if (status === 'on-going') {
                     window.location.href = 'app-ongoing.php';

                } else {
                    // Default behavior for other statuses
                    window.location.reload();
                }
            } else {
                alert('Error updating status');
            }
        } else {
            alert('Error: ' + xhr.status);
        }
    };
}
function finishAppointment(button, status) {
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
                window.location.href = 'app-finish.php';
            } else {
                window.location.href = 'app-finish.php'; // or show an error message
            }
        } else {
          window.location.href = 'app-finish.php';
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
