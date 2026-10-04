function toggleAdminSidebar() {
    const sidebar = document.getElementById('admin-sidebar');
    if (sidebar) sidebar.classList.toggle('collapsed');
}

function loadAdminPage(pageName) {
    fetch(`admin_pages/${pageName}.php`)
        .then(response => {
            if (!response.ok) throw new Error(`Failed to load ${pageName}.php (${response.status})`);
            return response.text();
        })
        .then(html => {
            const container = document.getElementById('admin-page-content');
            if (!container) return;
            container.innerHTML = html;

            container.querySelectorAll('script').forEach(oldScript => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });
        })
        .catch(err => {
            console.error(err);
            const container = document.getElementById('admin-page-content');
            if (container) container.innerHTML =
                `<div class="admin-card"><h2>Unable to load page</h2><p>${err.message}</p></div>`;
        });
}

document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.admin-link');

    links.forEach(link => {
        link.addEventListener('click', event => {
            event.preventDefault();
            links.forEach(item => item.classList.remove('active'));
            link.classList.add('active');
            const page = link.dataset.page;
            if (page) loadAdminPage(page);
        });
    });

    const active = document.querySelector('.admin-link.active');
    if (active) loadAdminPage(active.dataset.page);
});
