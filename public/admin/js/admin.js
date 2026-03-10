document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle (mobile)
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (mobileSidebarToggle && sidebar) {
        mobileSidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !mobileSidebarToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }

    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 4000);
    });

    // Confirm delete
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm || 'Are you sure?')) e.preventDefault();
        });
    });

    // CSRF setup for AJAX
    const csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) {
        window.csrfToken = csrf.getAttribute('content');
    }

    // Toggle status via AJAX
    document.querySelectorAll('[data-toggle-url]').forEach(btn => {
        btn.addEventListener('click', function() {
            fetch(this.dataset.toggleUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Content-Type': 'application/json' }
            }).then(r => r.json()).then(d => {
                if (d.success) location.reload();
            });
        });
    });
});
