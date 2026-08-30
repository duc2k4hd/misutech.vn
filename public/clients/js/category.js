(function () {
    "use strict";

    const productGrid = document.querySelector(".misutech_home_product_grid");
    const productCards = Array.from(
        document.querySelectorAll(".misutech_home_product_card"),
    );
    const extraProducts = productCards.filter((card) =>
        card.classList.contains("misutech_home_product_extra"),
    );
    const visibleCount = document.querySelector("#visible-count");
    const progressValue = document.querySelector(
        ".misutech_home_progress_value",
    );
    const noResults = document.querySelector(".misutech_home_no_results");
    const loadMoreButton = document.querySelector(".misutech_home_load_more");
    const searchForm = document.querySelector(".misutech_home_search");
    const searchInput = document.querySelector(".misutech_home_search_input");
    const sortSelect = document.querySelector(".misutech_home_sort_select");
    const toast = document.querySelector(".misutech_home_toast");
    const priceRanges = Array.from(
        document.querySelectorAll(".misutech_home_price_range"),
    );
    const priceInputs = Array.from(
        document.querySelectorAll(".misutech_home_price_input"),
    );
    const totalCatalogueProducts = 25;
    let extraProductsLoaded = false;
    let selectedCategory = window.currentCategorySlug || "";
    let selectedColor = "";
    let selectedSize = "";
    let toastTimer = null;

    productCards.forEach((card, index) => {
        card.dataset.originalOrder = String(index);
    });

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

    function selectedCheckboxValues(filterName) {
        return Array.from(
            document.querySelectorAll(
                `.misutech_home_filter_checkbox[data-filter="${filterName}"]:checked`,
            ),
        ).map((input) => input.value);
    }

    function getMaxPrice() {
        const maxInput = document.querySelector('[data-price-input="max"]');
        const val = maxInput && maxInput.hasAttribute('max') ? Number(maxInput.getAttribute('max')) : 0;
        return val > 0 ? val : 999999999;
    }

    function currentPriceValues() {
        const minEl = document.querySelector('[data-price-input="min"]');
        const maxEl = document.querySelector('[data-price-input="max"]');
        const maxLimit = getMaxPrice();
        const minimum = minEl ? (Number(minEl.value) || 0) : 0;
        const maximum = maxEl ? (Number(maxEl.value) || maxLimit) : maxLimit;
        return { minimum, maximum, maxLimit };
    }

    let activeFilterController = null;

    function applyFilters(isLoadMore = false) {
        if (!productGrid) return;

        const q = searchInput ? searchInput.value.trim() : '';
        const category = selectedCategory || window.currentCategorySlug || '';
        const brands = selectedCheckboxValues("brand").join(',');
        const prices = currentPriceValues();
        const sort = sortSelect ? sortSelect.value : 'featured';

        let offset = 0;
        if (isLoadMore && loadMoreButton) {
            offset = loadMoreButton.getAttribute('data-offset') || 0;
        }

        const params = new URLSearchParams();
        if (q) params.append('tim-kiem', q);
        if (category) params.append('category', category);
        if (brands) params.append('brands', brands);
        
        // Chỉ gửi filter giá khi thực sự có sự điều chỉnh giới hạn giá khác 0 và khác maxLimit
        if (prices.minimum > 0) {
            params.append('min_price', prices.minimum);
        }
        if (prices.maximum < prices.maxLimit && prices.maximum > 0 && prices.maxLimit > 1) {
            params.append('max_price', prices.maximum);
        }
        
        if (sort !== 'featured') params.append('sort', sort);
        params.append('offset', offset);

        const url = `/api/v1/products/load-more?${params.toString()}`;

        if (activeFilterController && !isLoadMore) {
            activeFilterController.abort();
        }
        activeFilterController = new AbortController();

        if (loadMoreButton && isLoadMore) {
            loadMoreButton.innerText = 'ĐANG TẢI...';
            loadMoreButton.disabled = true;
        } else if (!isLoadMore) {
            productGrid.style.opacity = '0.5';
        }

        fetch(url, { signal: activeFilterController.signal })
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                if (!isLoadMore) {
                    productGrid.innerHTML = data.html || '<div class="misutech_home_no_products">Không có sản phẩm nào phù hợp với bộ lọc.</div>';
                    productGrid.style.opacity = '1';
                } else {
                    if (data.html) productGrid.insertAdjacentHTML('beforeend', data.html);
                }

                if (visibleCount && progressValue) {
                    let newVisible = parseInt(offset) + (data.count || 0);
                    let total = data.total || 0;

                    visibleCount.innerText = newVisible;
                    
                    const totalCountEl = document.getElementById('total-count');
                    if (totalCountEl) totalCountEl.innerText = total;

                    const loadArea = document.querySelector('.misutech_home_load_area');

                    if (total === 0) {
                        if (loadArea) loadArea.style.display = 'none';
                    } else {
                        if (loadArea) loadArea.style.display = 'block';
                        if (loadMoreButton) {
                            loadMoreButton.setAttribute('data-offset', newVisible);
                            if (newVisible >= total || data.count === 0) {
                                loadMoreButton.style.display = 'none';
                            } else {
                                loadMoreButton.style.display = 'inline-block';
                            }
                        }
                        progressValue.style.width = `${Math.min(100, (newVisible / total) * 100)}%`;
                    }
                }

                if (loadMoreButton && isLoadMore) {
                    loadMoreButton.innerText = 'TẢI THÊM SẢN PHẨM';
                    loadMoreButton.disabled = false;
                }
            })
            .catch(err => {
                if (err.name === 'AbortError') return;
                console.error(err);
                if (loadMoreButton && isLoadMore) {
                    loadMoreButton.innerText = 'TẢI THÊM SẢN PHẨM';
                    loadMoreButton.disabled = false;
                }
                productGrid.style.opacity = '1';
            });
    }

    function setPricePair(changedSide, rawValue) {
        const minimumRange = document.querySelector('[data-price="min"]');
        const maximumRange = document.querySelector('[data-price="max"]');
        const minimumInput = document.querySelector('[data-price-input="min"]');
        const maximumInput = document.querySelector('[data-price-input="max"]');
        const boundedValue = Math.max(0, Math.min(getMaxPrice(), Number(rawValue) || 0));

        if (changedSide === "min") {
            const value = Math.min(boundedValue, Number(maximumInput.value));
            minimumRange.value = String(value);
            minimumInput.value = String(value);
        } else {
            const value = Math.max(boundedValue, Number(minimumInput.value));
            maximumRange.value = String(value);
            maximumInput.value = String(value);
        }
    }

    function resetGroup(groupName) {
        if (groupName === "availability" || groupName === "brand") {
            document
                .querySelectorAll(
                    `.misutech_home_filter_checkbox[data-filter="${groupName}"]`,
                )
                .forEach((input) => {
                    input.checked = false;
                });
        }
        
        if (groupName === "category") {
            selectedCategory = window.currentCategorySlug || "";
            document.querySelectorAll(".misutech_home_category_card").forEach(btn => {
                btn.classList.remove("misutech_home_active");
                btn.setAttribute("aria-pressed", "false");
                if (btn.dataset.category === selectedCategory) {
                    btn.classList.add("misutech_home_active");
                    btn.setAttribute("aria-pressed", "true");
                }
            });
        }

        if (groupName === "price") {
            setPricePair("min", 0);
            setPricePair("max", getMaxPrice());
        }

        if (groupName === "color") {
            selectedColor = "";
            document
                .querySelectorAll(".misutech_home_color_button")
                .forEach((button) =>
                    button.classList.remove("misutech_home_active"),
                );
        }

        if (groupName === "size") {
            selectedSize = "";
            document
                .querySelectorAll(".misutech_home_size_button")
                .forEach((button) =>
                    button.classList.remove("misutech_home_active"),
                );
        }

        applyFilters();
    }



    document
        .querySelectorAll(".misutech_home_filter_checkbox")
        .forEach((input) => {
            input.addEventListener("change", () => applyFilters());
        });

    priceRanges.forEach((range) => {
        // Chỉ cập nhật hiển thị lúc kéo
        range.addEventListener("input", () =>
            setPricePair(range.dataset.price, range.value),
        );
        // Khi buông chuột thả ra thì mới filter
        range.addEventListener("change", () => applyFilters());
    });

    priceInputs.forEach((input) => {
        input.addEventListener("change", () => {
            setPricePair(input.dataset.priceInput, input.value);
            applyFilters();
        });
    });

    document
        .querySelectorAll(".misutech_home_filter_reset")
        .forEach((button) => {
            button.addEventListener("click", () =>
                resetGroup(button.dataset.reset),
            );
        });

    document
        .querySelector(".misutech_home_clear_filters")
        .addEventListener("click", () => {
            document
                .querySelectorAll(".misutech_home_filter_checkbox")
                .forEach((input) => {
                    input.checked = false;
                });
            document
                .querySelectorAll(
                    ".misutech_home_color_button, .misutech_home_size_button",
                )
                .forEach((button) => {
                    button.classList.remove("misutech_home_active");
                    button.setAttribute("aria-pressed", "false");
                });
            selectedColor = "";
            selectedSize = "";
            searchInput.value = "";
            setPricePair("min", 0);
            setPricePair("max", getMaxPrice());
            sortSelect.value = "featured";
            
            // Re-highlight the original category card if it exists
            if (selectedCategory) {
                document.querySelectorAll(".misutech_home_category_card").forEach((btn) => {
                    if (btn.dataset.category === selectedCategory) {
                        btn.classList.add("misutech_home_active");
                        btn.setAttribute("aria-pressed", "true");
                    }
                });
            }
            
            applyFilters();
        });

    document
        .querySelectorAll(".misutech_home_color_button")
        .forEach((button) => {
            button.addEventListener("click", () => {
                const wasSelected = button.classList.contains(
                    "misutech_home_active",
                );
                document
                    .querySelectorAll(".misutech_home_color_button")
                    .forEach((item) =>
                        item.classList.remove("misutech_home_active"),
                    );
                selectedColor = wasSelected ? "" : button.dataset.color;
                if (!wasSelected) button.classList.add("misutech_home_active");
                applyFilters();
            });
        });

    document
        .querySelectorAll(".misutech_home_size_button")
        .forEach((button) => {
            button.addEventListener("click", () => {
                const wasSelected = button.classList.contains(
                    "misutech_home_active",
                );
                document
                    .querySelectorAll(".misutech_home_size_button")
                    .forEach((item) =>
                        item.classList.remove("misutech_home_active"),
                    );
                selectedSize = wasSelected ? "" : button.dataset.size;
                if (!wasSelected) button.classList.add("misutech_home_active");
                applyFilters();
            });
        });

    document
        .querySelectorAll(".misutech_home_category_card")
        .forEach((button) => {
            if (button.dataset.category === selectedCategory) {
                button.classList.add("misutech_home_active");
                button.setAttribute("aria-pressed", "true");
            } else {
                button.setAttribute("aria-pressed", "false");
            }
            button.addEventListener("click", (e) => {
                // If they click the link, let it navigate
                if (e.target.tagName.toLowerCase() === 'a' || e.target.closest('a')) {
                    return;
                }
                const wasSelected = button.classList.contains(
                    "misutech_home_active",
                );
                document
                    .querySelectorAll(".misutech_home_category_card")
                    .forEach((item) => {
                        item.classList.remove("misutech_home_active");
                        item.setAttribute("aria-pressed", "false");
                    });
                selectedCategory = wasSelected ? "" : button.dataset.category;
                if (!wasSelected) {
                    button.classList.add("misutech_home_active");
                    button.setAttribute("aria-pressed", "true");
                }
                
                if (window.currentCategorySlug && selectedCategory && selectedCategory !== window.currentCategorySlug) {
                    const link = button.querySelector('a');
                    if (link) {
                        window.location.href = link.href;
                        return;
                    }
                }
                
                applyFilters();
                document
                    .querySelector(".misutech_home_product_area")
                    .scrollIntoView({ behavior: "smooth", block: "start" });
            });
        });

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
                // Next (Bấm bên Phải): Trượt sang trái 1 ô, sau đó nối thẻ đầu tiên xuống cuối
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
                // Prev (Bấm bên Trái): Nối thẻ cuối cùng lên đầu ngay lập tức, rồi trượt mượt về 0
                const lastCard = categoryTrack.lastElementChild;
                categoryTrack.prepend(lastCard);

                const cardWidth = lastCard.getBoundingClientRect().width;
                categoryTrack.style.transition = "none";
                categoryTrack.style.transform = `translateX(-${cardWidth}px)`;

                // Force reflow để trình duyệt nhận diện vị trí xuất phát
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

    sortSelect.addEventListener("change", () => {
        applyFilters();
    });

    document
        .querySelectorAll(".misutech_home_view_button")
        .forEach((button) => {
            button.addEventListener("click", () => {
                document
                    .querySelectorAll(".misutech_home_view_button")
                    .forEach((item) =>
                        item.setAttribute("aria-pressed", "false"),
                    );
                button.setAttribute("aria-pressed", "true");
                productGrid.dataset.gridColumns = button.dataset.columns;
            });
        });

    let lastLoadTime = 0;
    
    if (loadMoreButton) {
        loadMoreButton.addEventListener("click", function() {
            const now = Date.now();
            if (now - lastLoadTime < 2000) {
                alert('Vui lòng chậm lại, thử lại sau ít giây.');
                return;
            }
            lastLoadTime = now;
            applyFilters(true);
        });
    }

    document.querySelectorAll("[data-cart]").forEach((button) => {
        button.addEventListener("click", () => {
            const counter = document.querySelector(".misutech_home_cart_count");
            counter.textContent = String(Number(counter.textContent) + 1);
            showToast("Đã thêm sản phẩm vào giỏ hàng.");
        });
    });

    document.querySelectorAll("[data-favorite]").forEach((button) => {
        button.addEventListener("click", () => {
            button.classList.toggle("misutech_home_active");
            button.textContent = button.classList.contains(
                "misutech_home_active",
            )
                ? "♥"
                : "♡";
            showToast(
                button.classList.contains("misutech_home_active")
                    ? "Đã thêm vào danh sách yêu thích."
                    : "Đã xóa khỏi danh sách yêu thích.",
            );
        });
    });

    const seeMoreBtn = document.querySelector(".misutech_home_see_more");
    if (seeMoreBtn) {
        seeMoreBtn.addEventListener("click", (event) => {
            const moreText = document.querySelector(
                ".misutech_home_description_more",
            );
            if (moreText) {
                const willExpand = moreText.hidden;
                moreText.hidden = !willExpand;
                event.currentTarget.textContent = willExpand
                    ? "THU GỌN −"
                    : "XEM THÊM +";
                event.currentTarget.setAttribute(
                    "aria-expanded",
                    String(willExpand),
                );
            }
        });
    }
})();
