document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.lang-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.lang-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.lang-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            const panel = document.querySelector(`[data-lang-panel="${btn.dataset.lang}"]`);
            if (panel) panel.classList.add('active');
        });
    });

    document.querySelectorAll('.js-confirm-delete').forEach(form => {
        form.addEventListener('submit', (e) => {
            const msg = form.dataset.confirm || 'متأكد؟';
            if (!confirm(msg)) e.preventDefault();
        });
    });
});
