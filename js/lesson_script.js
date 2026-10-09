/*
 * Student lesson interactions.
 * This file is loaded once by student_dashboard.php because lessons.php is
 * fetched into #page-content rather than opened as a full document.
 */
(function () {
    'use strict';

    function initializeLessonPagination(scope) {
        const root = scope || document;
        const grid = root.querySelector('#alphabetGrid');
        const pagination = root.querySelector('#alphabetPagination');
        if (!grid || !pagination || pagination.dataset.initialized === 'true') return;

        const cards = Array.from(grid.querySelectorAll('.lesson-card'));
        const itemsPerPage = 4;
        const totalPages = Math.ceil(cards.length / itemsPerPage);
        let currentPage = 1;
        pagination.dataset.initialized = 'true';

        function renderPage(page) {
            currentPage = Math.min(Math.max(1, page), Math.max(1, totalPages));
            const start = (currentPage - 1) * itemsPerPage;
            cards.forEach((card, index) => {
                const visible = index >= start && index < start + itemsPerPage;
                card.hidden = !visible;
                card.classList.toggle('d-none', !visible);
            });

            pagination.replaceChildren();
            const addButton = (label, pageNumber, disabled, ariaLabel) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.disabled = disabled;
                if (ariaLabel) button.setAttribute('aria-label', ariaLabel);
                if (pageNumber === currentPage && label !== '‹' && label !== '›') {
                    button.classList.add('active');
                    button.setAttribute('aria-current', 'page');
                }
                button.addEventListener('click', () => renderPage(pageNumber));
                pagination.appendChild(button);
            };

            addButton('‹', currentPage - 1, currentPage === 1, 'Previous page');
            for (let page = 1; page <= totalPages; page++) {
                addButton(String(page), page, false);
            }
            addButton('›', currentPage + 1, currentPage === totalPages, 'Next page');
        }

        renderPage(1);
    }

    function toggleCategory(button) {
        const targetId = button.getAttribute('data-target');
        const panel = targetId ? document.getElementById(targetId) : null;
        if (!panel) return;

        const expanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', String(!expanded));
        panel.hidden = expanded;
        panel.classList.toggle('is-open', !expanded);
        const icon = button.querySelector('.category-chevron i');
        if (icon) icon.className = expanded ? 'bi bi-chevron-down' : 'bi bi-chevron-up';
    }

    // Delegated listener remains active after the page loader replaces #page-content.
    document.addEventListener('click', function (event) {
        const toggle = event.target.closest('.category-toggle');
        if (toggle) {
            event.preventDefault();
            toggleCategory(toggle);
            return;
        }

        const playButton = event.target.closest('.lesson-play');
        if (playButton) {
            event.preventDefault();
            if (playButton.classList.contains('locked-play')) {
                showLessonMessage('This lesson is not available yet.');
            } else {
                const card = playButton.closest('.lesson-card');
                const lesson = card?.querySelector('.lesson-name-row h2')?.textContent?.trim() || 'this lesson';
                showLessonMessage('Lesson media for ' + lesson + ' will appear here when it is added.');
            }
            return;
        }

        const nextButton = event.target.closest('.lesson-next');
        if (nextButton) {
            event.preventDefault();
            const card = nextButton.closest('.lesson-card');
            const lesson = card?.querySelector('.lesson-name-row h2')?.textContent?.trim() || 'this lesson';
            showLessonMessage('You selected ' + lesson + '. Lesson content can be connected here.');
        }
    });

    function showLessonMessage(message) {
        let notice = document.getElementById('lesson-interaction-notice');
        if (!notice) {
            notice = document.createElement('div');
            notice.id = 'lesson-interaction-notice';
            notice.className = 'alert alert-info shadow-sm';
            notice.setAttribute('role', 'status');
            notice.style.cssText = 'position:fixed;right:20px;bottom:20px;z-index:1080;max-width:min(360px,calc(100vw - 40px));margin:0;';
            document.body.appendChild(notice);
        }
        notice.textContent = message;
        notice.hidden = false;
        if (notice._hideTimer) clearTimeout(notice._hideTimer);
        notice._hideTimer = setTimeout(() => { notice.hidden = true; }, 3500);
    }

    // The fragment is injected after DOMContentLoaded, so initialize whenever it appears.
    document.addEventListener('DOMContentLoaded', () => initializeLessonPagination(document));
    const observer = new MutationObserver(mutations => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (node.nodeType !== Node.ELEMENT_NODE) continue;
                if (node.matches?.('#alphabetGrid') || node.querySelector?.('#alphabetGrid')) {
                    initializeLessonPagination(node.matches('#alphabetGrid') ? node.parentElement : node);
                }
            }
        }
    });
    observer.observe(document.documentElement, { childList: true, subtree: true });
})();
