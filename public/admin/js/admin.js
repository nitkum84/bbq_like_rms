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

    document.querySelectorAll('[data-searchable-select]').forEach((select) => {
        if (select.dataset.searchInitialized === 'true') {
            return;
        }

        select.dataset.searchInitialized = 'true';
        const wrapper = document.createElement('div');
        wrapper.className = 'searchable-select-wrapper';

        const searchInput = document.createElement('input');
        searchInput.type = 'search';
        searchInput.className = 'form-control form-control-sm searchable-select-input';
        searchInput.placeholder = select.dataset.placeholder || 'Search options';

        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(searchInput);
        wrapper.appendChild(select);

        const options = Array.from(select.options).map((option) => ({
            value: option.value,
            text: option.textContent,
            selected: option.selected,
            disabled: option.disabled,
        }));

        const renderOptions = (query) => {
            const normalizedQuery = (query || '').trim().toLowerCase();
            const currentValue = select.value;
            select.innerHTML = '';

            options
                .filter((option) => !normalizedQuery || option.text.toLowerCase().includes(normalizedQuery) || option.value === currentValue)
                .forEach((option) => {
                    const nextOption = document.createElement('option');
                    nextOption.value = option.value;
                    nextOption.textContent = option.text;
                    nextOption.disabled = option.disabled;
                    nextOption.selected = option.value === currentValue;
                    select.appendChild(nextOption);
                });
        };

        searchInput.addEventListener('input', () => renderOptions(searchInput.value));
    });

    const quickUserForm = document.querySelector('[data-quick-user-form]');
    if (quickUserForm) {
        const userSelect = document.querySelector('select[name="user_id"]');
        const errorBox = document.querySelector('[data-quick-user-error]');
        const modalElement = document.getElementById('quickUserModal');

        quickUserForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            errorBox.classList.add('d-none');
            errorBox.textContent = '';

            const formData = new FormData(quickUserForm);

            try {
                const response = await fetch(quickUserForm.dataset.quickUserUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const payload = await response.json();

                if (!response.ok) {
                    throw new Error(payload.message || 'Unable to create customer.');
                }

                const option = document.createElement('option');
                option.value = String(payload.id);
                option.textContent = `${payload.name} - ${payload.email}${payload.mobile ? ` - ${payload.mobile}` : ''}`;
                option.selected = true;
                userSelect.appendChild(option);
                userSelect.value = String(payload.id);
                userSelect.dispatchEvent(new Event('change'));
                quickUserForm.reset();

                if (modalElement) {
                    bootstrap.Modal.getOrCreateInstance(modalElement).hide();
                }
            } catch (error) {
                errorBox.textContent = error.message;
                errorBox.classList.remove('d-none');
            }
        });
    }

    const subtotalInput = document.getElementById('bookingSubtotal');
    const discountInput = document.getElementById('bookingDiscountTotal');
    const gstRateInput = document.getElementById('bookingGstRate');
    const gstAmountInput = document.getElementById('bookingGstAmount');
    const finalTotalInput = document.getElementById('bookingFinalTotal');
    const totalAmountInput = document.getElementById('bookingTotalAmount');

    if (subtotalInput && discountInput && gstRateInput && gstAmountInput && finalTotalInput && totalAmountInput) {
        const updateBookingTotals = () => {
            const subtotal = Number(subtotalInput.value || 0);
            const discount = Number(discountInput.value || 0);
            const gstRate = Number(gstRateInput.value || 0);
            const preTax = Math.max(subtotal - discount, 0);
            const gstAmount = preTax * (gstRate / 100);
            const finalTotal = preTax + gstAmount;

            gstAmountInput.value = gstAmount.toFixed(2);
            finalTotalInput.value = finalTotal.toFixed(2);
            totalAmountInput.value = finalTotal.toFixed(2);
        };

        [subtotalInput, discountInput, gstRateInput].forEach((input) => {
            input.addEventListener('input', updateBookingTotals);
        });

        updateBookingTotals();
    }
});
