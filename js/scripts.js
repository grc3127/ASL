// Function to toggle sidebar (called by onclick="toggleSidebar()" in sidebar.php)
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('collapsed');
    }
}

// Main page loader that executes script tags in fetched HTML
function loadPage(pageName) {
    fetch(`student_pages/${pageName}.php`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Failed to load ${pageName}.php (${response.status})`);
            }
            return response.text();
        })
        .then(html => {
            const container = document.getElementById('page-content');
            if (!container) return;

            // Inject HTML into the wrapper
            container.innerHTML = html;

            // Re-evaluate script tags so JS inside loaded files executes
            const scripts = container.querySelectorAll('script');
            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');

                // Copy attributes (e.g. src, type)
                Array.from(oldScript.attributes).forEach(attr => {
                    newScript.setAttribute(attr.name, attr.value);
                });

                // Copy inline JavaScript code
                newScript.appendChild(document.createTextNode(oldScript.innerHTML));

                // Replace old script tag with the executable one
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });
        })
        .catch(err => {
            console.error('Error loading page:', err);
            const container = document.getElementById('page-content');
            if (container) {
                container.innerHTML = `
                    <div class="alert alert-danger m-4" role="alert">
                        <h4 class="alert-heading">Error Loading Page</h4>
                        <p>Could not load <strong>${pageName}</strong>. Please try again.</p>
                    </div>`;
            }
        });
}

// Event listeners setup on DOM load
document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.sidelinks');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            // Handle active class UI switching
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            // Load requested page
            const page = this.getAttribute('data-page');
            if (page) {
                loadPage(page);
            }
        });
    });

    // Automatically load initial active page (defaults to dashboard)
    const activeLink = document.querySelector('.sidelinks.active');
    const initialPage = activeLink ? activeLink.getAttribute('data-page') : 'dashboard';
    
    const pageContent = document.getElementById('page-content');
    if (pageContent && pageContent.children.length === 0) {
        loadPage(initialPage);
    }
});