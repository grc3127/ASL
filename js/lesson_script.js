
(function initLessonsPage() {
    // 1. Accordion Toggle
    document.querySelectorAll('.category-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-target');
            const targetContent = document.getElementById(targetId);
            const chevronIcon = button.querySelector('.category-chevron i');
            const isExpanded = button.getAttribute('aria-expanded') === 'true';

            if (isExpanded) {
                button.setAttribute('aria-expanded', 'false');
                targetContent.classList.remove('is-open');
                targetContent.setAttribute('hidden', '');
                if (chevronIcon) chevronIcon.className = 'bi bi-chevron-down';
            } else {
                button.setAttribute('aria-expanded', 'true');
                targetContent.removeAttribute('hidden');
                targetContent.classList.add('is-open');
                if (chevronIcon) chevronIcon.className = 'bi bi-chevron-up';
            }
        });
    });

    // 2. Alphabet Pagination (4 cards per page)
    const itemsPerPage = 4;
    const grid = document.getElementById('alphabetGrid');
    const paginationNav = document.getElementById('alphabetPagination');

    if (!grid || !paginationNav) return;

    const cards = Array.from(grid.querySelectorAll('.lesson-card'));
    const totalPages = Math.ceil(cards.length / itemsPerPage);
    let currentPage = 1;

    function renderPage(page) {
        currentPage = page;
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        cards.forEach((card, index) => {
            if (index >= start && index < end) {
                card.removeAttribute('hidden');
                card.classList.remove('d-none');
            } else {
                card.setAttribute('hidden', '');
                card.classList.add('d-none');
            }
        });

        renderControls();
    }

    function renderControls() {
        paginationNav.innerHTML = '';

        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.setAttribute('aria-label', 'Previous page');
        prevBtn.disabled = (currentPage === 1);
        prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage > 1) renderPage(currentPage - 1);
        });
        paginationNav.appendChild(prevBtn);

        // Page Number buttons
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.type = 'button';
            pageBtn.textContent = i;
            if (i === currentPage) pageBtn.classList.add('active');
            pageBtn.addEventListener('click', (e) => {
                e.preventDefault();
                renderPage(i);
            });
            paginationNav.appendChild(pageBtn);
        }

        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.setAttribute('aria-label', 'Next page');
        nextBtn.disabled = (currentPage === totalPages);
        nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage < totalPages) renderPage(currentPage + 1);
        });
        paginationNav.appendChild(nextBtn);
    }

    renderPage(1);
})();
