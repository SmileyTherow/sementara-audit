document.querySelectorAll('[data-menu]').forEach(button => button.addEventListener('click', () => { const menu = document.getElementById(button.dataset.menu); const expanded = menu.classList.toggle('open'); button.setAttribute('aria-expanded', String(expanded)); }));
const dialog = document.getElementById('confirmation');
let pendingForm;
document.querySelectorAll('form[data-confirm]').forEach(form => form.addEventListener('submit', event => { if (form.dataset.confirmed) return; event.preventDefault(); pendingForm = form; document.getElementById('confirmation-text').textContent = form.dataset.confirm; dialog.showModal(); }));
dialog?.addEventListener('close', () => { if (dialog.returnValue === 'confirm' && pendingForm) { pendingForm.dataset.confirmed = 'true'; pendingForm.requestSubmit(); } pendingForm = null; });
document.querySelectorAll('[data-print]').forEach(button => button.addEventListener('click', () => window.print()));
document.querySelectorAll('[data-grade]').forEach(input => input.addEventListener('change', () => { input.closest('label').previousElementSibling.disabled = !input.checked; }));
document.querySelectorAll('[data-trend]').forEach(select => select.addEventListener('change', () => { document.querySelectorAll('.chart-col').forEach(column => { column.hidden = select.value === '6' && Number(column.dataset.index) < 6; }); }));

document.querySelectorAll('[data-product-gallery]').forEach(gallery => {
    const main = gallery.querySelector('[data-gallery-main]');
    gallery.querySelectorAll('[data-gallery-src]').forEach(button => {
        button.addEventListener('click', () => {
            main.src = button.dataset.gallerySrc;
            main.alt = button.dataset.galleryAlt;
            gallery.querySelectorAll('[data-gallery-src]').forEach(item => {
                const selected = item === button;
                item.classList.toggle('is-active', selected);
                item.setAttribute('aria-pressed', String(selected));
            });
        });
    });
});
