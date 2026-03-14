const sidebar = document.querySelector(".front-sidebar");
const openButton = document.querySelector("[data-sidebar-open]");
const closeButtons = document.querySelectorAll("[data-sidebar-close]");

if (sidebar && openButton) {
    const toggleSidebar = (open) => {
        sidebar.classList.toggle("is-open", open);
        sidebar.setAttribute("aria-hidden", String(!open));
        openButton.setAttribute("aria-expanded", String(open));
        document.body.classList.toggle("sidebar-open", open);
    };

    openButton.addEventListener("click", () => toggleSidebar(true));
    closeButtons.forEach((button) => {
        button.addEventListener("click", () => toggleSidebar(false));
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            toggleSidebar(false);
        }
    });
}

const slides = Array.from(document.querySelectorAll("[data-hero-slide]"));
const dots = Array.from(document.querySelectorAll("[data-hero-dot]"));

if (slides.length > 0 && dots.length === slides.length) {
    let activeIndex = 0;
    let intervalId;

    const setActiveSlide = (index) => {
        activeIndex = index;

        slides.forEach((slide, slideIndex) => {
            slide.classList.toggle("is-active", slideIndex === index);
        });

        dots.forEach((dot, dotIndex) => {
            dot.classList.toggle("is-active", dotIndex === index);
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
        dot.addEventListener("click", () => {
            setActiveSlide(index);
            startSlider();
        });
    });

    setActiveSlide(0);
    startSlider();
}
