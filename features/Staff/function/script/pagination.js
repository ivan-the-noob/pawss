document.addEventListener('DOMContentLoaded', function() {
    const rowsPerPage = 10;
    const rows = document.querySelectorAll('tbody tr');
    const totalPages = Math.ceil(rows.length / rowsPerPage);
    const paginationControls = document.getElementById('paginationControls');
    const pageNumbers = document.getElementById('pageNumbers');
    let currentPage = 1;

    function updatePageNumbers() {
        pageNumbers.innerHTML = ''; // Clear existing page numbers
        for (let i = currentPage; i <= Math.min(currentPage + 2, totalPages); i++) {
            const li = document.createElement('li');
            li.className = 'page-item';
            li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            if (i === currentPage) {
                li.classList.add('active');
            }
            pageNumbers.appendChild(li);
        }
    }

    function showPage(pageNumber) {
        if (pageNumber < 1 || pageNumber > totalPages) return;
        currentPage = pageNumber;

        const start = (pageNumber - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.forEach((row, index) => {
            if (index >= start && index < end) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        updatePageNumbers();
    }

    paginationControls.addEventListener('click', function(e) {
        e.preventDefault();
        const page = e.target.getAttribute('data-page');
        if (page === 'prev') {
            showPage(currentPage - 1);
        } else if (page === 'next') {
            showPage(currentPage + 1);
        } else if (page) {
            showPage(parseInt(page));
        }
    });

    // Show the first page by default
    showPage(1);
});