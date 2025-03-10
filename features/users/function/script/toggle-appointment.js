document.addEventListener("DOMContentLoaded", function () {
    var showBookedHistory = localStorage.getItem('showBookedHistory');
    var appointmentSection = document.getElementById("appointmentSection");
    var bookedHistorySection = document.getElementById("bookedHistorySection");
    var toggleViewBtn = document.getElementById("toggleViewBtn");

    if (showBookedHistory === 'true') {
        appointmentSection.style.display = "none";
        bookedHistorySection.style.display = "flex";
        toggleViewBtn.textContent = "Show Calendar";
        localStorage.removeItem('showBookedHistory');
    }
});

document.getElementById("toggleViewBtn").addEventListener("click", function () {
    var appointmentSection = document.getElementById("appointmentSection");
    var bookedHistorySection = document.getElementById("bookedHistorySection");
    var toggleViewBtn = document.getElementById("toggleViewBtn");

    if (appointmentSection.style.display === "none") {
        appointmentSection.style.display = "block";
        bookedHistorySection.style.display = "none";
        toggleViewBtn.textContent = "My Appointment";
    } else {
        appointmentSection.style.display = "none";
        bookedHistorySection.style.display = "flex";
        toggleViewBtn.textContent = "Show Calendar";
    }
});
