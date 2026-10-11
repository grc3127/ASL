function toggleAdminSidebar() {
    const sidebar = document.getElementById('admin-sidebar');
    if (sidebar) sidebar.classList.toggle('collapsed');
}

function loadAdminPage(pageName) {
    const pageUrl = new URL(`admin_pages/${encodeURIComponent(pageName)}.php`, document.baseURI);
    fetch(pageUrl.href, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(async response => {
            if (!response.ok) throw new Error(`Unable to load ${pageName}.php (HTTP ${response.status}).`);
            if (response.redirected && /login\\.php/i.test(response.url)) throw new Error('Your session may have expired. Please sign in again.');
            const html = await response.text();
            if (/^\\s*<!doctype html|^\\s*<html/i.test(html) && /name=[\"']password[\"']/i.test(html)) throw new Error('The requested page returned a login page. Please sign in again.');
            return html;
        })
        .then(html => {
            const container = document.getElementById('admin-page-content');
            if (!container) return;
            container.innerHTML = html;

            container.querySelectorAll('script').forEach(oldScript => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr =>
                    newScript.setAttribute(attr.name, attr.value)
                );
                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });
        })
        .catch(error => {
            const container = document.getElementById('admin-page-content');
            if (container) {
                container.innerHTML =
                    `<div class="admin-card"><h2>Unable to load page</h2><p>${error.message}</p></div>`;
            }
        });
}

document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.admin-link[data-page]');

    links.forEach(link => {
        link.addEventListener('click', event => {
            event.preventDefault();

            links.forEach(item => item.classList.remove('active'));
            link.classList.add('active');

            loadAdminPage(link.dataset.page);
        });
    });

    const active = document.querySelector('.admin-link.active[data-page]');
    if (active) loadAdminPage(active.dataset.page);
});
