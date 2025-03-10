
document.getElementById('currentBtn').addEventListener('click', function () {
    document.querySelectorAll('.past-appointment').forEach(function (item) {
        item.style.display = 'none';
    });
    document.querySelectorAll('.current-appointment').forEach(function (item) {
        item.style.display = 'block';
    });
});

document.getElementById('pastBtn').addEventListener('click', function () {
    document.querySelectorAll('.current-appointment').forEach(function (item) {
        item.style.display = 'none';
    });
    document.querySelectorAll('.past-appointment').forEach(function (item) {
        item.style.display = 'block';
    });
});
document.getElementById('currentBtn').click();