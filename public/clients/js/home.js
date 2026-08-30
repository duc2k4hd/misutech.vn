(() => {
    function updateCountdown() {
        const now = new Date();
        const endOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59, 999);
        const diff = Math.max(0, Math.floor((endOfDay - now) / 1000));

        const hours = Math.floor(diff / 3600);
        const minutes = Math.floor((diff % 3600) / 60);
        const seconds = diff % 60;

        const hoursEl = document.querySelector('[data-countdown="hours"]');
        const minutesEl = document.querySelector('[data-countdown="minutes"]');
        const secondsEl = document.querySelector('[data-countdown="seconds"]');

        if (hoursEl) hoursEl.textContent = String(hours).padStart(2, "0");
        if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, "0");
        if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, "0");

        if (diff <= 0) {
            setTimeout(() => location.reload(), 1000);
        }
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);

    const slides = document.querySelectorAll(".misutech_home_hero_slide");
    const dots = document.querySelectorAll(".misutech_home_hero_dot");
    let currentSlide = 0;
    let slideInterval;

    function showSlide(index) {
        if (slides.length === 0) return;
        
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.style.opacity = '1';
                slide.style.transform = 'scale(1)';
                slide.style.pointerEvents = 'auto';
                slide.style.zIndex = '1';
                if(dots[i]) dots[i].setAttribute("aria-current", "true");
            } else {
                slide.style.opacity = '0';
                slide.style.transform = 'scale(1.05)';
                slide.style.pointerEvents = 'none';
                slide.style.zIndex = '0';
                if(dots[i]) dots[i].setAttribute("aria-current", "false");
            }
        });
        currentSlide = index;
    }

    function nextSlide() {
        if (slides.length <= 1) return;
        showSlide((currentSlide + 1) % slides.length);
    }

    function startSlider() {
        if (slides.length > 1) {
            slideInterval = setInterval(nextSlide, 5000);
        }
    }

    function resetSlider() {
        clearInterval(slideInterval);
        startSlider();
    }

    dots.forEach((dot, index) => {
        dot.addEventListener("click", () => {
            showSlide(index);
            resetSlider();
        });
    });

    startSlider();

    document.querySelectorAll(".misutech_home_section_tab").forEach((tab) => {
        tab.addEventListener("click", () => {
            const tabList = tab.closest(".misutech_home_section_tabs");
            tabList
                .querySelectorAll(".misutech_home_section_tab")
                .forEach((item) => item.setAttribute("aria-selected", "false"));
            tab.setAttribute("aria-selected", "true");
            const toast = document.querySelector(".misutech_home_toast");
            if (toast) {
                toast.textContent = "Đang hiển thị: " + tab.textContent.trim();
                toast.setAttribute("aria-hidden", "false");
                setTimeout(
                    () => toast.setAttribute("aria-hidden", "true"),
                    1800,
                );
            }
        });
    });

    document
        .querySelectorAll(
            ".misutech_home_quick_item, .misutech_home_shop_category",
        )
        .forEach((button) => {
            button.addEventListener("click", () => {
                const category =
                    button.dataset.category || button.textContent.trim();
                const toast = document.querySelector(".misutech_home_toast");
                if (toast) {
                    toast.textContent = "Đã chọn danh mục: " + category;
                    toast.setAttribute("aria-hidden", "false");
                    setTimeout(
                        () => toast.setAttribute("aria-hidden", "true"),
                        1800,
                    );
                }
                const catalog = document.querySelector(
                    "#misutech_home_catalog_0",
                );
                if (catalog) catalog.scrollIntoView({ behavior: "smooth" });
            });
        });
})();
