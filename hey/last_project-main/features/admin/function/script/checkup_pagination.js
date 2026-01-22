document.addEventListener('DOMContentLoaded', function() {
    const rowsPerPage = 8; // Number of cards per page
    const rows = document.querySelectorAll('.card-wrapper'); // Select all card wrappers
    const totalPages = Math.ceil(rows.length / rowsPerPage); // Calculate total pages
    const paginationControls = document.getElementById('paginationControls');
    const pageNumbers = document.getElementById('pageNumbers');
    let currentPage = 1;

    function updatePageNumbers() {
        pageNumbers.innerHTML = ''; // Clear existing page numbers
        for (let i = 1; i <= totalPages; i++) {
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
        if (pageNumber < 1 || pageNumber > totalPages) return; // Check page bounds
        currentPage = pageNumber;

        const start = (pageNumber - 1) * rowsPerPage; // Calculate start index
        const end = start + rowsPerPage; // Calculate end index

        rows.forEach((row, index) => {
            row.style.display = (index >= start && index < end) ? '' : 'none'; // Show/hide rows
        });

        updatePageNumbers(); // Update page numbers
    }

    paginationControls.addEventListener('click', function(e) {
        e.preventDefault();
        const page = e.target.getAttribute('data-page');
        if (page === 'prev') {
            showPage(currentPage - 1); // Previous page
        } else if (page === 'next') {
            showPage(currentPage + 1); // Next page
        } else if (page) {
            showPage(parseInt(page)); // Specific page
        }
    });

    // Show the first page by default
    showPage(1);
});