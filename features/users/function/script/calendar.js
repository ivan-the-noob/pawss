document.addEventListener('DOMContentLoaded', function () {
  var calendarEl = document.getElementById('appointmentCalendar');

  // AJAX request to fetch blocked dates from the server
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '../../function/php/fetch_appointments.php', true); // Adjust the path to the PHP file
  xhr.onload = function () {
    if (xhr.status === 200) {
      // Get the blocked dates from the response and convert to an array of date strings (YYYY-MM-DD)
      var blockedDates = xhr.responseText.split(',').map(function(date) {
        return date.trim(); // Ensure no extra spaces
      });

      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: '', // No button on the left
          center: 'title', // Center the title
          right: 'prev,next', // Show prev and next buttons
        },
        dayCellDidMount: function (info) {
          var dayCell = info.el;
          var date = new Date(info.date); // Get the current date for the cell
          var today = new Date();
          today.setHours(0, 0, 0, 0); // Reset the time of 'today' to midnight
          var maxDate = new Date(today);
          maxDate.setDate(today.getDate() + 14); // Set maxDate to 14 days from today

          var dateString = date.toISOString().split('T')[0]; // Format date as YYYY-MM-DD

          // If the date is in the past, set opacity to 70% and disable clicking
          if (date < today) {
            dayCell.style.opacity = '1';  // Set opacity to 70% for past dates
            dayCell.style.pointerEvents = 'none';  // Disable interaction (non-clickable)
            dayCell.style.cursor = 'default';
            dayCell.style.color = 'black';  // Change cursor to default (non-interactive)
            dayCell.style.backgroundColor = '#FBF9FA';  // Ensure past dates don't have a green background
          } 
          // If the date is beyond 14 days from today, make it non-clickable
          else if (date > maxDate) {
            dayCell.style.opacity = '1'; // Reduce opacity for future dates beyond 14 days
            dayCell.style.pointerEvents = 'none'; // Disable interaction with future dates
            dayCell.style.cursor = 'default'; // Change cursor to default
            dayCell.style.backgroundColor = '#FBF9FA'; // Ensure the background is cleared for dates outside the valid range
          } 
          // Dates within the valid range (today to maxDate) remain interactive
          else {
            dayCell.classList.add('fc-daygrid-day-button');
            dayCell.addEventListener('click', function () {
              var options = { year: 'numeric', month: 'long', day: 'numeric' };
              var formattedDate = new Date(info.date).toLocaleDateString('en-US', options);
              document.getElementById('modalContent').textContent = formattedDate;
              
              // Set the selected date in the hidden input field
              document.getElementById('appointment_date').value = dateString;  // Set the value of the hidden input field with dateString
              
              var modal = new bootstrap.Modal(document.getElementById('dayModal'));
              modal.show();
            });

            // Set background color for available dates (future dates within valid range)
            dayCell.style.backgroundColor = 'green'; 
            dayCell.addEventListener('mouseenter', function() {
              dayCell.style.backgroundColor = 'lightgreen';  
            });
            dayCell.addEventListener('mouseleave', function() {
              dayCell.style.backgroundColor = 'green';  
            });
          }

          // Check if the date is blocked (e.g., if it is in the blockedDates array)
          if (blockedDates.includes(dateString)) {
            dayCell.style.backgroundColor = 'red';
            dayCell.style.pointerEvents = 'none'; 
            dayCell.style.cursor = 'default'; 
          }
        }
      });

      calendar.render();
    }
  };
  xhr.send();
});
