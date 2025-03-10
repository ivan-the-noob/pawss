function filterServices() {
    var medicalCheckbox = document.getElementById('medical-checkbox');
    var nonMedicalCheckbox = document.getElementById('non-medical-checkbox');
    
    medicalCheckbox.addEventListener('change', function() {
      if (medicalCheckbox.checked) {
        nonMedicalCheckbox.checked = false;
      }
      updateServiceDisplay();
    });
  
    nonMedicalCheckbox.addEventListener('change', function() {
      if (nonMedicalCheckbox.checked) {
        medicalCheckbox.checked = false;
      }
      updateServiceDisplay();
    });
  
    updateServiceDisplay();
  }
  
  function updateServiceDisplay() {
    var medicalCheckbox = document.getElementById('medical-checkbox');
    var nonMedicalCheckbox = document.getElementById('non-medical-checkbox');
    
    var medicalServices = document.querySelectorAll('.medical-service');
    var nonMedicalServices = document.querySelectorAll('.non-medical-service');
  
    medicalServices.forEach(function(service) {
      service.style.display = medicalCheckbox.checked ? 'block' : 'none';
    });
    
    nonMedicalServices.forEach(function(service) {
      service.style.display = nonMedicalCheckbox.checked ? 'block' : 'none';
    });
  }
  
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('medical-checkbox').checked = true;
    filterServices();
  });