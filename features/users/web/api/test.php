<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Selection</title>
    <style>
        .add-info {
            display: none;
            margin-top: 10px;
            padding: 10px;
            background: #f0f0f0;
        }
    </style>
</head>
<body>

<form>
    <label for="service">Select a Service</label>
    <select class="form-control" id="service" name="service" required onchange="updatePayment()">
        <!-- Clinic Services -->
        <optgroup label="Clinic Services">
            <option value="clinic1" data-type="clinic">Clinic Service 1 - ₱500</option>
            <option value="clinic2" data-type="clinic">Clinic Service 2 - ₱600</option>
        </optgroup>
        <!-- Home Services -->
        <optgroup label="Home Services">
            <option value="home1" data-type="home">Home Service 1 - ₱700</option>
            <option value="home2" data-type="home">Home Service 2 - ₱800</option>
        </optgroup>
    </select>
</form>

<div class="add-info">
    <strong>Additional Info:</strong> This appears only for home services.
</div>

<script>
function updatePayment() {
    const select = document.getElementById('service');
    const selectedOption = select.options[select.selectedIndex];
    const addInfoDiv = document.querySelector('.add-info');
    const type = selectedOption.getAttribute('data-type');

    if (type === 'home') {
        addInfoDiv.style.display = 'block';
    } else {
        addInfoDiv.style.display = 'none';
    }
}
</script>

</body>
</html>
