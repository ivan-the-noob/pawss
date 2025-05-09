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
          var currentHour = new Date().getHours(); // Get current hour

          // If the date is in the past OR it's today and time is 5PM or later
          if (date < today || (date.getTime() === today.getTime() && currentHour >= 17)) {
            dayCell.style.opacity = '1';  
            dayCell.style.pointerEvents = 'none';  
            dayCell.style.cursor = 'default';
            dayCell.style.color = 'black';  
            dayCell.style.backgroundColor = '#FBF9FA';  
          } 
          // If the date is beyond 14 days from today, make it non-clickable
          else if (date > maxDate) {
            dayCell.style.opacity = '1';
            dayCell.style.pointerEvents = 'none';
            dayCell.style.cursor = 'default';
            dayCell.style.backgroundColor = '#FBF9FA';
          } 
          // Dates within the valid range (today to maxDate) remain interactive
          else {
            dayCell.classList.add('fc-daygrid-day-button');
            dayCell.addEventListener('click', function () {
              var options = { year: 'numeric', month: 'long', day: 'numeric' };
              var formattedDate = new Date(info.date).toLocaleDateString('en-US', options);
              document.getElementById('modalContent').textContent = formattedDate;

              // Set the selected date in the hidden input field
              document.getElementById('appointment_date').value = dateString;

              var modal = new bootstrap.Modal(document.getElementById('dayModal'));
              modal.show();
            });

            // Set background color for available dates
            dayCell.style.backgroundColor = 'green'; 
            dayCell.addEventListener('mouseenter', function() {
              dayCell.style.backgroundColor = 'lightgreen';  
            });
            dayCell.addEventListener('mouseleave', function() {
              dayCell.style.backgroundColor = 'green';  
            });
          }

          // Check if the date is blocked
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
