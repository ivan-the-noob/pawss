document.addEventListener('DOMContentLoaded', function () {
    const itemsPerPage = 2;
    const allListItems = document.querySelectorAll('#historyList .list-group-item');
    const paginationControls = document.getElementById('paginationControls');
    let currentPageItems = [];

    function updatePaginationControls() {
        const pageCount = Math.ceil(currentPageItems.length / itemsPerPage);
        paginationControls.innerHTML = '';

        if (pageCount > 1) {
            for (let i = 1; i <= pageCount; i++) {
                const li = document.createElement('li');
                li.classList.add('page-item');
                const a = document.createElement('a');
                a.classList.add('page-link');
                a.href = '#';
                a.dataset.page = i;
                a.textContent = i;
                a.addEventListener('click', function (e) {
                    e.preventDefault();
                    showPage(parseInt(this.dataset.page));
                    document.querySelectorAll('#paginationControls .page-item').forEach(item => item.classList.remove('active'));
                    this.parentElement.classList.add('active');
                });
                li.appendChild(a);
                paginationControls.appendChild(li);
            }
            paginationControls.querySelector('.page-item').classList.add('active');
        }
    }

    function showPage(page) {
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        currentPageItems.forEach((item, index) => {
            if (index >= start && index < end) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function filterAppointments(appointmentType) {
        allListItems.forEach(item => item.style.display = 'none');
        currentPageItems = Array.from(allListItems).filter(item => item.classList.contains(appointmentType));
        updatePaginationControls();
        if (currentPageItems.length > 0) {
            showPage(1);
        }
    }

    document.getElementById('currentBtn').addEventListener('click', function () {
        filterAppointments('current-appointment');
    });

    document.getElementById('pastBtn').addEventListener('click', function () {
        filterAppointments('past-appointment');
    });

    filterAppointments('current-appointment');
});
