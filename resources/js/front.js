const sidebar = document.querySelector('.front-sidebar');
const openButton = document.querySelector('[data-sidebar-open]');
const closeButtons = document.querySelectorAll('[data-sidebar-close]');

if (sidebar && openButton) {
    const toggleSidebar = (open) => {
        sidebar.classList.toggle('is-open', open);
        sidebar.setAttribute('aria-hidden', String(!open));
        openButton.setAttribute('aria-expanded', String(open));
        document.body.classList.toggle('sidebar-open', open);
    };

    openButton.addEventListener('click', () => toggleSidebar(true));
    closeButtons.forEach((button) => {
        button.addEventListener('click', () => toggleSidebar(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            toggleSidebar(false);
        }
    });
}

const slides = Array.from(document.querySelectorAll('[data-hero-slide]'));
const dots = Array.from(document.querySelectorAll('[data-hero-dot]'));

if (slides.length > 0 && dots.length === slides.length) {
    let activeIndex = 0;
    let intervalId;

    const setActiveSlide = (index) => {
        activeIndex = index;

        slides.forEach((slide, slideIndex) => {
            slide.classList.toggle('is-active', slideIndex === index);
        });

        dots.forEach((dot, dotIndex) => {
            dot.classList.toggle('is-active', dotIndex === index);
        });
    };

    const startSlider = () => {
        window.clearInterval(intervalId);
        intervalId = window.setInterval(() => {
            const nextIndex = (activeIndex + 1) % slides.length;
            setActiveSlide(nextIndex);
        }, 5000);
    };

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            setActiveSlide(index);
            startSlider();
        });
    });

    setActiveSlide(0);
    startSlider();
}

const formatCurrency = (value) => `Rs. ${Number(value || 0).toFixed(2)}`;
const formatDateParts = (dateString) => {
    const date = new Date(`${dateString}T00:00:00`);

    return {
        shortDay: date.toLocaleDateString('en-US', { weekday: 'short' }).toUpperCase(),
        day: date.toLocaleDateString('en-US', { day: '2-digit' }),
        shortMonth: date.toLocaleDateString('en-US', { month: 'short' }).toUpperCase(),
        full: date.toLocaleDateString('en-US', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' }),
    };
};

const reservationDrawer = document.querySelector('[data-reservation-drawer]');
const reservationOpenButtons = document.querySelectorAll('[data-reservation-open]');
const reservationCloseButtons = document.querySelectorAll('[data-reservation-close]');
const reservationForm = document.querySelector('[data-reservation-form]');

if (reservationDrawer && reservationForm) {
    const bootstrap = JSON.parse(reservationForm.dataset.reservationBootstrap || '{}');
    const feedback = reservationDrawer.querySelector('[data-reservation-feedback]');
    const slotList = reservationForm.querySelector('[data-slot-list]');
    const dateList = reservationForm.querySelector('[data-date-list]');
    const foodOptionsList = reservationForm.querySelector('[data-food-options]');
    const packageField = reservationForm.querySelector('[data-package-field]');
    const packageSelect = reservationForm.querySelector('[data-package-select]');
    const mealInput = reservationForm.querySelector('input[name="meal_type"]');
    const slotInput = reservationForm.querySelector('input[name="slot_id"]');
    const dateInput = reservationForm.querySelector('input[name="date"]');
    const guestsInput = reservationForm.querySelector('input[name="guests"]');
    const foodInput = reservationForm.querySelector('input[name="food_preference"]');
    const pricePerGuest = reservationForm.querySelector('[data-price-per-guest]');
    const totalPrice = reservationForm.querySelector('[data-total-price]');
    const priceLabel = reservationForm.querySelector('[data-price-label]');
    const summaryDatetime = reservationForm.querySelector('[data-summary-datetime]');
    const summaryGuests = reservationForm.querySelector('[data-summary-guests]');
    const summaryFood = reservationForm.querySelector('[data-summary-food]');
    const summaryTotal = reservationForm.querySelector('[data-summary-total]');
    const summaryRestaurant = reservationForm.querySelector('[data-summary-restaurant]');
    const mealButtons = Array.from(reservationForm.querySelectorAll('[data-meal-option]'));

    let state = {
        mealType: bootstrap.defaultMealType || 'lunch',
        foodPreference: bootstrap.defaultFoodPreference || 'veg',
        selectedSlotId: null,
        quote: null,
    };

    const toggleDrawer = (open) => {
        reservationDrawer.classList.toggle('is-open', open);
        reservationDrawer.setAttribute('aria-hidden', String(!open));
        document.body.classList.toggle('sidebar-open', open);
    };

    const setFeedback = (message = '', isError = false) => {
        if (!feedback) {
            return;
        }

        feedback.textContent = message;
        feedback.classList.toggle('is-visible', Boolean(message));
        feedback.style.background = isError ? 'rgba(217, 72, 29, 0.12)' : 'rgba(45, 90, 70, 0.12)';
        feedback.style.color = isError ? 'var(--front-accent-dark)' : 'var(--front-forest)';
    };

    const buildDateOptions = () => {
        const startDate = new Date(`${bootstrap.minDate || bootstrap.defaultDate}T00:00:00`);
        return Array.from({ length: 7 }, (_, index) => {
            const nextDate = new Date(startDate);
            nextDate.setDate(startDate.getDate() + index);
            return nextDate.toISOString().slice(0, 10);
        });
    };

    const renderDates = () => {
        if (!dateList) {
            return;
        }

        dateList.innerHTML = '';

        buildDateOptions().forEach((dateValue) => {
            const parts = formatDateParts(dateValue);
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `reservation-date-chip${dateInput.value === dateValue ? ' is-active' : ''}`;
            button.innerHTML = `<span>${parts.shortDay}</span><strong>${parts.day}</strong><small>${parts.shortMonth}</small>`;
            button.addEventListener('click', () => {
                dateInput.value = dateValue;
                renderDates();
                refreshQuote();
            });
            dateList.appendChild(button);
        });
    };

    const renderPackages = (packages = []) => {
        packageSelect.innerHTML = '<option value="">Select a package</option>';

        packages.forEach((pkg) => {
            const option = document.createElement('option');
            option.value = String(pkg.id);
            option.textContent = `${pkg.name} (${pkg.discount_percent}% off)`;
            packageSelect.appendChild(option);
        });

        if (state.foodPreference === 'packages' && packages.length > 0 && !packageSelect.value) {
            packageSelect.value = String(packages[0].id);
        }

        packageField.hidden = state.foodPreference !== 'packages';
    };

    const renderFoodOptions = (foodOptions = []) => {
        foodOptionsList.innerHTML = '';

        foodOptions.forEach((option) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `reservation-food-option${state.foodPreference === option.value ? ' is-active' : ''}`;
            button.dataset.foodOption = option.value;
            button.innerHTML = `<span>${option.label}</span><strong>${formatCurrency(option.price_per_guest)}</strong><small>${option.description}</small>`;
            button.addEventListener('click', () => {
                state.foodPreference = option.value;
                foodInput.value = option.value;
                renderFoodOptions(foodOptions);
                renderPackages(bootstrap.foodOptions?.packages || []);
                refreshQuote();
            });
            foodOptionsList.appendChild(button);
        });
    };

    const renderSlots = (slots = []) => {
        slotList.innerHTML = '';
        const activeSlot = slots.find((slot) => slot.id === state.selectedSlotId && slot.available) || slots.find((slot) => slot.available) || null;
        state.selectedSlotId = activeSlot ? activeSlot.id : null;
        slotInput.value = activeSlot ? String(activeSlot.id) : '';

        if (slots.length === 0 || !slots.some((slot) => slot.available)) {
            const emptyState = document.createElement('div');
            emptyState.className = 'reservation-slot-empty';
            emptyState.textContent = 'No slots available for selected date';
            slotList.appendChild(emptyState);
            return;
        }

        slots.forEach((slot) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `reservation-slot${state.selectedSlotId === slot.id ? ' is-active' : ''}`;
            button.disabled = !slot.available;
            button.innerHTML = `<strong>${slot.start_time}</strong><small>${slot.status_label}</small>`;
            button.addEventListener('click', () => {
                state.selectedSlotId = slot.id;
                slotInput.value = String(slot.id);
                renderSlots(slots);
                updateSummary();
            });
            slotList.appendChild(button);
        });
    };

    const updateSummary = () => {
        const selectedSlot = state.quote?.slots?.find((slot) => slot.id === state.selectedSlotId);
        const readableFood = state.foodPreference === 'nonveg'
            ? 'Non-Veg'
            : state.foodPreference.charAt(0).toUpperCase() + state.foodPreference.slice(1);
        const packageText = state.foodPreference === 'packages' && packageSelect.value
            ? ` - ${packageSelect.options[packageSelect.selectedIndex]?.textContent || ''}`
            : '';
        const dateParts = formatDateParts(dateInput.value);

        if (summaryRestaurant) {
            summaryRestaurant.textContent = bootstrap.restaurant?.name || 'Restaurant';
        }

        summaryDatetime.textContent = selectedSlot
            ? `${dateParts.full}, ${selectedSlot.start_time}`
            : 'Choose a date and slot';
        summaryGuests.textContent = `${guestsInput.value || 0} guest${Number(guestsInput.value || 0) === 1 ? '' : 's'}`;
        summaryFood.textContent = `${readableFood}${packageText}`;
        summaryTotal.textContent = formatCurrency(state.quote?.pricing?.total || 0);
    };

    const applyQuote = (quote) => {
        state.quote = quote;
        renderSlots(quote.slots || []);
        pricePerGuest.textContent = formatCurrency(quote.pricing?.price_per_guest || 0);
        totalPrice.textContent = formatCurrency(quote.pricing?.total || 0);
        priceLabel.textContent = quote.pricing?.pricing_label || 'Pricing unavailable';
        updateSummary();

        if (!quote.has_availability) {
            setFeedback('No slots are available for that selection yet. Try another date or meal.', true);
        } else {
            setFeedback('', false);
        }
    };

    const refreshQuote = async () => {
        const params = new URLSearchParams({
            date: dateInput.value,
            meal_type: mealInput.value,
            guests: guestsInput.value,
            food_preference: foodInput.value,
        });

        if (foodInput.value === 'packages' && packageSelect.value) {
            params.set('deals_bundle_id', packageSelect.value);
        }

        try {
            setFeedback('Checking live availability...', false);
            const response = await fetch(`${bootstrap.quoteUrl}?${params.toString()}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('Unable to load current slot availability.');
            }

            const quote = await response.json();
            applyQuote(quote);
        } catch (error) {
            setFeedback(error.message, true);
        }
    };

    reservationOpenButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            toggleDrawer(true);
            await refreshQuote();
        });
    });

    reservationCloseButtons.forEach((button) => {
        button.addEventListener('click', () => toggleDrawer(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            toggleDrawer(false);
        }
    });

    mealButtons.forEach((button) => {
        button.addEventListener('click', () => {
            state.mealType = button.dataset.mealOption;
            mealInput.value = state.mealType;
            mealButtons.forEach((item) => item.classList.toggle('is-active', item === button));
            refreshQuote();
        });
    });

    guestsInput.addEventListener('change', refreshQuote);
    packageSelect.addEventListener('change', refreshQuote);

    reservationForm.addEventListener('submit', (event) => {
        if (!slotInput.value) {
            event.preventDefault();
            setFeedback('Please choose an available slot before confirming.', true);
        }
    });

    renderDates();
    renderFoodOptions(bootstrap.foodOptions?.options || []);
    renderPackages(bootstrap.foodOptions?.packages || []);
    updateSummary();
}

const rescheduleForms = document.querySelectorAll('[data-reschedule-form]');

rescheduleForms.forEach((form) => {
    const dateInput = form.querySelector('input[name="date"]');
    const mealSelect = form.querySelector('select[name="meal_type"]');
    const slotSelect = form.querySelector('[data-reschedule-slot]');
    const quoteUrl = form.dataset.quoteUrl;

    const refreshSlots = async () => {
        const params = new URLSearchParams({
            date: dateInput.value,
            meal_type: mealSelect.value,
            guests: form.dataset.guests,
            food_preference: form.dataset.foodPreference,
        });

        if (form.dataset.packageId) {
            params.set('deals_bundle_id', form.dataset.packageId);
        }

        if (form.dataset.ignoreBookingId) {
            params.set('ignore_booking_id', form.dataset.ignoreBookingId);
        }

        const response = await fetch(`${quoteUrl}?${params.toString()}`, {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            return;
        }

        const quote = await response.json();
        slotSelect.innerHTML = '';

        quote.slots.filter((slot) => slot.available).forEach((slot) => {
            const option = document.createElement('option');
            option.value = String(slot.id);
            option.textContent = `${slot.label} (${slot.start_time})`;
            slotSelect.appendChild(option);
        });
    };

    dateInput.addEventListener('change', refreshSlots);
    mealSelect.addEventListener('change', refreshSlots);
    refreshSlots();
});
