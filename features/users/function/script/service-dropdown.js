document.addEventListener('DOMContentLoaded', function () {
    const serviceCategoryDropdown = document.getElementById('serviceCategoryDropdown');
    const serviceDropdown = document.getElementById('serviceDropdown');
    const medicalServices = document.querySelector('.medical-services');
    const nonMedicalServices = document.querySelector('.nonMedical-services');
    const totalPayment = document.getElementById('totalPayment');

    // Handle service category selection
    document.querySelectorAll('#serviceCategoryDropdown + .dropdown-menu .dropdown-item').forEach(item => {
        item.addEventListener('click', function () {
            const selectedCategory = this.getAttribute('data-value');
            serviceCategoryDropdown.textContent = this.textContent;

            // Show or hide services based on the selected category
            if (selectedCategory === 'medical') {
                medicalServices.style.display = 'block';
                nonMedicalServices.style.display = 'none';
            } else if (selectedCategory === 'nonMedical') {
                medicalServices.style.display = 'none';
                nonMedicalServices.style.display = 'block';
            }

            // Reset service dropdown and total payment
            serviceDropdown.textContent = 'Select Service';
            totalPayment.textContent = '₱0.00';
        });
    });

    // Handle service selection and update total payment
    document.querySelectorAll('#serviceDropdown + .dropdown-menu .dropdown-item').forEach(item => {
        item.addEventListener('click', function () {
            const selectedService = this.textContent;
            const selectedValue = this.getAttribute('data-value');
            serviceDropdown.textContent = selectedService;
            totalPayment.textContent = `₱${selectedValue}`;
        });
    });
});

