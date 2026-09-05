(function () {
    "use strict";

    const productGrid = document.querySelector(".misutech_home_product_grid");
    const visibleCount = document.querySelector("#visible-count");
    const progressValue = document.querySelector(".misutech_home_progress_value");
    const loadMoreButton = document.querySelector(".misutech_home_load_more");
    const searchInput = document.querySelector(".misutech_home_search_input");
    const sortSelect = document.querySelector(".misutech_home_sort_select");
    const toast = document.querySelector(".misutech_home_toast");
    const backdrop = document.getElementById("misutech_filter_backdrop");
    const clearQuickBtn = document.getElementById("misutech_quick_clear_btn");

    // Badges
    const badgeTotal = document.getElementById("filter_badge_total");
    const badgePrice = document.getElementById("filter_badge_price");
    const badgeBrand = document.getElementById("filter_badge_brand");

    // State
    let selectedCategory = window.currentCategorySlug || "";
    let selectedSort = (sortSelect ? sortSelect.value : "featured") || "featured";
    let selectedBrands = new Set();
    let selectedPriceRanges = new Set();
    let toastTimer = null;
    let activeFilterController = null;
    let countDebounceTimer = null;

    // Initialize state from existing checked inputs & active tiles
    document.querySelectorAll(".misutech_brand_cb:checked").forEach(cb => selectedBrands.add(cb.value));
    document.querySelectorAll(".misutech_brand_card_tile.is_active").forEach(tile => {
        if (tile.dataset.brandSlug) selectedBrands.add(tile.dataset.brandSlug);
    });
    document.querySelectorAll(".misutech_price_cb:checked").forEach(cb => selectedPriceRanges.add(cb.value));

    // =========================================================================
    // TOAST NOTIFICATION
    // =========================================================================
    function showToast(message) {
        if (!toast) return;
        window.clearTimeout(toastTimer);
        toast.textContent = message;
        toast.removeAttribute("hidden");
        toast.setAttribute("aria-hidden", "false");
        toast.classList.add("show");
        toastTimer = window.setTimeout(() => {
            toast.classList.remove("show");
            toast.setAttribute("aria-hidden", "true");
            toast.setAttribute("hidden", "true");
            toast.textContent = "";
        }, 2200);
    }

    // =========================================================================
    // SCROLL TO TOP OF PRODUCT CATALOG
    // =========================================================================
    function scrollToProductTop() {
        const catalogEl = document.getElementById("misutech_home_products") || document.querySelector(".misutech_home_product_area");
        if (catalogEl) {
            const offset = 80;
            const bodyRect = document.body.getBoundingClientRect().top;
            const elemRect = catalogEl.getBoundingClientRect().top;
            const elementPosition = elemRect - bodyRect;
            const offsetPosition = Math.max(0, elementPosition - offset);
            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth"
            });
        }
    }

    // =========================================================================
    // MODAL OPEN / CLOSE CONTROLLER
    // =========================================================================
    function openModal(modalId) {
        closeAllModals(false);
        const modal = document.getElementById(modalId);
        if (!modal) return;

        if (backdrop) backdrop.classList.add("is_open");
        modal.classList.add("is_open");
        document.body.style.overflow = "hidden";
        updateLiveCount();
    }

    function closeAllModals(unlockScroll = true) {
        document.querySelectorAll(".misutech_filter_modal.is_open").forEach(modal => {
            modal.classList.remove("is_open");
        });
        if (backdrop) backdrop.classList.remove("is_open");
        if (unlockScroll) {
            document.body.style.overflow = "";
        }
    }

    // Trigger buttons to open modals
    document.querySelectorAll("[data-open-modal]").forEach(btn => {
        btn.addEventListener("click", () => {
            const target = btn.dataset.openModal;
            openModal(target);
        });
    });

    // Close buttons
    document.querySelectorAll("[data-close-modal]").forEach(btn => {
        btn.addEventListener("click", () => closeAllModals(true));
    });

    if (backdrop) {
        backdrop.addEventListener("click", () => closeAllModals(true));
    }

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeAllModals(true);
    });

    // =========================================================================
    // UI SYNCHRONIZATION & BADGES
    // =========================================================================
    function syncUI() {
        // Sync Brand Checkboxes
        document.querySelectorAll(".misutech_brand_cb").forEach(cb => {
            cb.checked = selectedBrands.has(cb.value);
        });

        // Sync Brand Quick Grid Tiles
        document.querySelectorAll(".misutech_brand_card_tile").forEach(tile => {
            const slug = tile.dataset.brandSlug;
            if (slug && selectedBrands.has(slug)) {
                tile.classList.add("is_active");
            } else {
                tile.classList.remove("is_active");
            }
        });

        // Sync Price Checkboxes
        document.querySelectorAll(".misutech_price_cb").forEach(cb => {
            cb.checked = selectedPriceRanges.has(cb.value);
        });

        // Sync Sort UI
        document.querySelectorAll("[data-sort-value]").forEach(el => {
            if (el.dataset.sortValue === selectedSort) {
                el.classList.add("is_active");
            } else {
                el.classList.remove("is_active");
            }
        });
        if (sortSelect && sortSelect.value !== selectedSort) {
            sortSelect.value = selectedSort;
        }

        // Update Badges
        const brandCount = selectedBrands.size;
        const priceCount = selectedPriceRanges.size;
        const totalFilters = brandCount + priceCount + (selectedSort !== "featured" ? 1 : 0);

        if (badgeBrand) {
            badgeBrand.textContent = brandCount;
            badgeBrand.style.display = brandCount > 0 ? "inline-flex" : "none";
        }
        if (badgePrice) {
            badgePrice.textContent = priceCount;
            badgePrice.style.display = priceCount > 0 ? "inline-flex" : "none";
        }
        if (badgeTotal) {
            badgeTotal.textContent = totalFilters;
            badgeTotal.style.display = totalFilters > 0 ? "inline-flex" : "none";
        }
        if (clearQuickBtn) {
            clearQuickBtn.style.display = totalFilters > 0 ? "inline-flex" : "none";
        }

        // Chip button highlight
        document.querySelectorAll('[data-open-modal="modal-brands"]').forEach(btn => {
            btn.classList.toggle("is_active", brandCount > 0);
        });
        document.querySelectorAll('[data-open-modal="modal-price"]').forEach(btn => {
            btn.classList.toggle("is_active", priceCount > 0);
        });
        document.querySelectorAll('[data-open-modal="modal-all-filters"]').forEach(btn => {
            btn.classList.toggle("is_active", totalFilters > 0);
        });
    }

    // =========================================================================
    // LIVE RESULT COUNT CALCULATION
    // =========================================================================
    function updateLiveCount() {
        window.clearTimeout(countDebounceTimer);
        countDebounceTimer = window.setTimeout(() => {
            const params = buildQueryParams(0);
            fetch(`/api/v1/products/count?${params.toString()}`)
                .then(res => res.json())
                .then(data => {
                    const count = data.count || 0;
                    document.querySelectorAll(".misutech_live_count").forEach(el => {
                        el.textContent = count;
                    });
                })
                .catch(() => {});
        }, 120);
    }

    // =========================================================================
    // FILTER QUERY BUILDER & FETCH
    // =========================================================================
    function buildQueryParams(offset = 0) {
        const q = searchInput ? searchInput.value.trim() : "";
        const category = selectedCategory || window.currentCategorySlug || "";
        const brands = Array.from(selectedBrands).join(",");
        const priceRanges = Array.from(selectedPriceRanges).join(",");

        const params = new URLSearchParams();
        if (q) params.append("tim-kiem", q);
        if (category) params.append("category", category);
        if (brands) params.append("brands", brands);
        if (priceRanges) params.append("price_ranges", priceRanges);
        if (selectedSort && selectedSort !== "featured") params.append("sort", selectedSort);
        params.append("offset", offset);

        return params;
    }

    function applyFilters(isLoadMore = false) {
        if (!productGrid) return;

        const offset = (isLoadMore && loadMoreButton) ? (loadMoreButton.getAttribute("data-offset") || 0) : 0;
        const params = buildQueryParams(offset);
        const url = `/api/v1/products/load-more?${params.toString()}`;

        if (activeFilterController && !isLoadMore) {
            activeFilterController.abort();
        }
        activeFilterController = new AbortController();

        if (loadMoreButton && isLoadMore) {
            loadMoreButton.innerText = "ĐANG TẢI...";
            loadMoreButton.disabled = true;
        } else if (!isLoadMore) {
            productGrid.style.opacity = "0.4";
            productGrid.style.transition = "opacity 0.2s ease";
        }

        fetch(url, { signal: activeFilterController.signal })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (!isLoadMore) {
                    productGrid.innerHTML = data.html || '<div class="misutech_home_no_products">Không có sản phẩm nào phù hợp với bộ lọc.</div>';
                    productGrid.style.opacity = "1";
                } else {
                    if (data.html) productGrid.insertAdjacentHTML("beforeend", data.html);
                }

                const total = data.total || 0;
                let newVisible = parseInt(offset) + (data.count || 0);

                if (visibleCount) visibleCount.innerText = newVisible;
                const totalCountEl = document.getElementById("total-count");
                if (totalCountEl) totalCountEl.innerText = total;

                const loadArea = document.querySelector(".misutech_home_load_area");
                if (loadArea) {
                    if (total === 0) {
                        loadArea.style.display = "none";
                    } else {
                        loadArea.style.display = "block";
                        if (progressValue) {
                            progressValue.style.width = `${Math.min(100, (newVisible / total) * 100)}%`;
                        }
                        if (loadMoreButton) {
                            loadMoreButton.setAttribute("data-offset", newVisible);
                            loadMoreButton.style.display = (newVisible >= total || data.count === 0) ? "none" : "inline-block";
                        }
                    }
                }

                if (loadMoreButton && isLoadMore) {
                    loadMoreButton.innerText = "TẢI THÊM SẢN PHẨM";
                    loadMoreButton.disabled = false;
                }

                // Update live count displays
                document.querySelectorAll(".misutech_live_count").forEach(el => {
                    el.textContent = total;
                });
            })
            .catch(err => {
                if (err.name === "AbortError") return;
                console.error(err);
                if (loadMoreButton && isLoadMore) {
                    loadMoreButton.innerText = "TẢI THÊM SẢN PHẨM";
                    loadMoreButton.disabled = false;
                }
                productGrid.style.opacity = "1";
            });
    }

    // =========================================================================
    // EVENT LISTENERS
    // =========================================================================

    // 1. Brand Card Quick Tiles (Screenshot 4)
    document.querySelectorAll(".misutech_brand_card_tile").forEach(tile => {
        tile.addEventListener("click", () => {
            const slug = tile.dataset.brandSlug;
            if (!slug) return;

            if (selectedBrands.has(slug)) {
                selectedBrands.delete(slug);
            } else {
                selectedBrands.add(slug);
            }

            syncUI();
            applyFilters();
            scrollToProductTop();
        });
    });

    // 2. Brand Checkboxes (Both Sidebar & Modals)
    document.querySelectorAll(".misutech_brand_cb").forEach(cb => {
        cb.addEventListener("change", () => {
            if (cb.checked) {
                selectedBrands.add(cb.value);
            } else {
                selectedBrands.delete(cb.value);
            }
            syncUI();

            if (cb.closest(".misutech_desktop_sidebar_filters")) {
                applyFilters();
            } else {
                updateLiveCount();
            }
        });
    });

    // 3. Price Checkboxes (Both Sidebar & Modals)
    document.querySelectorAll(".misutech_price_cb").forEach(cb => {
        cb.addEventListener("change", () => {
            if (cb.checked) {
                selectedPriceRanges.add(cb.value);
            } else {
                selectedPriceRanges.delete(cb.value);
            }
            syncUI();

            if (cb.closest(".misutech_desktop_sidebar_filters")) {
                applyFilters();
            } else {
                updateLiveCount();
            }
        });
    });

    // 4. Sort selection chips & list items
    document.querySelectorAll("[data-sort-value]").forEach(el => {
        el.addEventListener("click", () => {
            selectedSort = el.dataset.sortValue;
            syncUI();
            updateLiveCount();
            // If inside sort drawer, auto-apply & close
            if (el.closest("#modal-sort")) {
                closeAllModals(true);
                applyFilters();
                scrollToProductTop();
            }
        });
    });

    if (sortSelect) {
        sortSelect.addEventListener("change", () => {
            selectedSort = sortSelect.value;
            syncUI();
            applyFilters();
        });
    }

    // 5. Reset Buttons (Sidebar & Modals)
    document.querySelectorAll("[data-reset-modal]").forEach(btn => {
        btn.addEventListener("click", () => {
            const type = btn.dataset.resetModal;
            if (type === "price") {
                selectedPriceRanges.clear();
            } else if (type === "brands") {
                selectedBrands.clear();
            } else if (type === "all") {
                selectedBrands.clear();
                selectedPriceRanges.clear();
                selectedSort = "featured";
            }
            syncUI();

            if (btn.closest(".misutech_desktop_sidebar_filters")) {
                applyFilters();
            } else {
                updateLiveCount();
            }
        });
    });

    // 6. Clear All Buttons (Mobile Quick Clear & Desktop Sidebar Clear)
    const desktopClearBtn = document.getElementById("desktop_clear_filters_btn");
    [clearQuickBtn, desktopClearBtn].forEach(btn => {
        if (!btn) return;
        btn.addEventListener("click", () => {
            selectedBrands.clear();
            selectedPriceRanges.clear();
            selectedSort = "featured";
            if (searchInput) searchInput.value = "";
            syncUI();
            applyFilters();
            scrollToProductTop();
            showToast("Đã xóa tất cả bộ lọc.");
        });
    });

    // 7. Modal Apply ("Xem X kết quả") Buttons
    document.querySelectorAll("[data-apply-modal]").forEach(btn => {
        btn.addEventListener("click", () => {
            closeAllModals(true);
            applyFilters();
            scrollToProductTop();
        });
    });

    // 8. Brand Live Search (Both Desktop Sidebar & Modals)
    document.querySelectorAll(".misutech_brand_search_input").forEach(input => {
        input.addEventListener("input", () => {
            const query = input.value.toLowerCase().trim();
            const container = input.closest(".misutech_filter_modal") || input.closest(".misutech_desktop_sidebar_filters") || document;

            container.querySelectorAll(".misutech_brand_check_item").forEach(item => {
                const name = item.getAttribute("data-brand-name") || item.textContent.toLowerCase();
                if (name.includes(query)) {
                    item.style.display = "flex";
                } else {
                    item.style.display = "none";
                }
            });
        });
    });

    // 9. Category Card Carousel / Selection
    document.querySelectorAll(".misutech_home_category_card").forEach((button) => {
        if (button.dataset.category === selectedCategory) {
            button.classList.add("misutech_home_active");
            button.setAttribute("aria-pressed", "true");
        } else {
            button.setAttribute("aria-pressed", "false");
        }

        button.addEventListener("click", (e) => {
            if (e.target.tagName.toLowerCase() === "a" || e.target.closest("a")) {
                return;
            }
            const wasSelected = button.classList.contains("misutech_home_active");
            document.querySelectorAll(".misutech_home_category_card").forEach((item) => {
                item.classList.remove("misutech_home_active");
                item.setAttribute("aria-pressed", "false");
            });
            selectedCategory = wasSelected ? "" : button.dataset.category;
            if (!wasSelected) {
                button.classList.add("misutech_home_active");
                button.setAttribute("aria-pressed", "true");
            }

            if (window.currentCategorySlug && selectedCategory && selectedCategory !== window.currentCategorySlug) {
                const link = button.querySelector("a");
                if (link) {
                    window.location.href = link.href;
                    return;
                }
            }

            applyFilters();
            scrollToProductTop();
        });
    });

    // Category Carousel arrows
    const categoryTrack = document.querySelector(".misutech_home_category_track");
    const categoryArrows = document.querySelectorAll(".misutech_home_category_arrow");
    let isCarouselMoving = false;

    categoryArrows.forEach((button) => {
        button.addEventListener("click", () => {
            if (isCarouselMoving || !categoryTrack) return;
            const direction = Number(button.dataset.categoryDirection) || 1;
            const cards = categoryTrack.querySelectorAll(".misutech_home_category_card");
            if (cards.length < 2) return;

            isCarouselMoving = true;

            if (direction === 1) {
                const firstCard = categoryTrack.firstElementChild;
                const cardWidth = firstCard.getBoundingClientRect().width;
                categoryTrack.style.transition = "transform 0.35s ease";
                categoryTrack.style.transform = `translateX(-${cardWidth}px)`;

                setTimeout(() => {
                    categoryTrack.style.transition = "none";
                    categoryTrack.style.transform = "translateX(0)";
                    categoryTrack.appendChild(firstCard);
                    isCarouselMoving = false;
                }, 350);
            } else {
                const lastCard = categoryTrack.lastElementChild;
                categoryTrack.prepend(lastCard);
                const cardWidth = lastCard.getBoundingClientRect().width;
                categoryTrack.style.transition = "none";
                categoryTrack.style.transform = `translateX(-${cardWidth}px)`;
                void categoryTrack.offsetWidth;

                categoryTrack.style.transition = "transform 0.35s ease";
                categoryTrack.style.transform = "translateX(0)";

                setTimeout(() => {
                    categoryTrack.style.transition = "none";
                    isCarouselMoving = false;
                }, 350);
            }
        });
    });

    // View Columns switcher (3, 2, 1)
    document.querySelectorAll(".misutech_home_view_button").forEach((button) => {
        button.addEventListener("click", () => {
            document.querySelectorAll(".misutech_home_view_button").forEach((item) =>
                item.setAttribute("aria-pressed", "false")
            );
            button.setAttribute("aria-pressed", "true");
            if (productGrid) {
                productGrid.dataset.gridColumns = button.dataset.columns;
            }
        });
    });

    // Load More Button
    let lastLoadTime = 0;
    if (loadMoreButton) {
        loadMoreButton.addEventListener("click", function () {
            const now = Date.now();
            if (now - lastLoadTime < 1500) {
                return;
            }
            lastLoadTime = now;
            applyFilters(true);
        });
    }

    // Initial sync
    syncUI();
})();
