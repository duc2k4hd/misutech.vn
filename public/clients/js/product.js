(function () {
  "use strict";

  let productImages = Array.from(document.querySelectorAll(".misutech_product_thumbnail"));
  const mainImage = document.querySelector(".misutech_product_main_image");
  const imageModal = document.querySelector('[data-modal="image"]');
  const modalImage = document.querySelector(".misutech_product_modal_image");
  const quantityInput = document.querySelector(".misutech_product_quantity_input");
  const cartCount = document.querySelector(".misutech_product_cart_count");
  const cartDrawer = document.querySelector(".misutech_product_cart_drawer");
  const cartOverlay = document.querySelector(".misutech_product_drawer_overlay");
  const cartItems = document.querySelector(".misutech_product_cart_items");
  const cartTotal = document.querySelector(".misutech_product_cart_total");
  const toast = document.querySelector(".misutech_product_toast");
  const relatedTrack = document.querySelector(".misutech_product_related_track");
  const relatedCards = Array.from(document.querySelectorAll(".misutech_product_related_card"));
  const relatedDots = document.querySelector(".misutech_product_related_dots");
  const backTop = document.querySelector(".misutech_product_back_top");
  let activeImageIndex = 0;
  let cartItemCount = 0;
  let cartValue = 0;
  let selectedReviewRating = 0;
  let relatedPage = 0;
  let relatedVisibleCount = 4;
  let quickViewCard = null;
  let toastTimer = null;
  let imageTimer = null;

  // Local In-Memory Cache (0ms latency, Zero server load)
  let localModelStore = {};
  let currentAbortController = null;

  // Khởi tạo data cache từ inline script (được render sẵn từ server)
  try {
    const embeddedScript = document.getElementById("misutech_embedded_models");
    if (embeddedScript && embeddedScript.textContent.trim()) {
      localModelStore = JSON.parse(embeddedScript.textContent);
    }
  } catch (e) {
    console.warn("Could not parse embedded models JSON:", e);
  }

  function showToast(message) {
    if (typeof window.showToast === "function") {
      window.showToast(message);
      return;
    }
    const toastEl = document.getElementById("misutech_global_toast") || document.querySelector(".misutech_toast, .misutech_home_toast, .misutech_product_toast");
    if (!toastEl) return;
    window.clearTimeout(toastTimer);
    toastEl.textContent = message;
    toastEl.removeAttribute("hidden");
    toastEl.setAttribute("aria-hidden", "false");
    toastEl.classList.add("show");
    toastTimer = window.setTimeout(() => {
      toastEl.classList.remove("show");
      toastEl.setAttribute("aria-hidden", "true");
      toastEl.setAttribute("hidden", "true");
      toastEl.textContent = "";
    }, 2300);
  }

  function setMainImage(index) {
    if (!productImages.length || !mainImage) return;
    const boundedIndex = (index + productImages.length) % productImages.length;
    const button = productImages[boundedIndex];
    if (!button) return;
    const newSource = button.dataset.image;
    window.clearTimeout(imageTimer);
    mainImage.classList.add("misutech_product_image_changing");
    imageTimer = window.setTimeout(() => {
      mainImage.src = newSource;
      mainImage.classList.remove("misutech_product_image_changing");
    }, 100);
    productImages.forEach((item) => item.classList.remove("misutech_product_active"));
    button.classList.add("misutech_product_active");
    activeImageIndex = boundedIndex;

    // Đồng bộ với Lightbox nếu đang mở
    updateLightboxActive(boundedIndex);
  }

  // =========================================================================
  // LUXURY PRODUCT GALLERY LIGHTBOX (Swipe, Touch gestures, Zoom & Pan)
  // =========================================================================
  let lbZoom = 1;
  let lbPanX = 0;
  let lbPanY = 0;
  let isPanning = false;
  let panStartX = 0;
  let panStartY = 0;
  let isSwiping = false;
  let swipeStartX = 0;
  let swipeCurrentX = 0;

  const lbCurrent = document.getElementById("misutech_lb_current");
  const lbTotal = document.getElementById("misutech_lb_total");
  const lbMainImg = document.getElementById("misutech_lb_main_img");
  const lbWrapper = document.getElementById("misutech_lb_wrapper");
  const lbStage = document.getElementById("misutech_lightbox_stage");
  const lbThumbsTrack = document.getElementById("misutech_lb_thumbs_track");

  function rebindGalleryEvents() {
    productImages = Array.from(document.querySelectorAll(".misutech_product_thumbnail"));
    productImages.forEach((button, index) => {
      button.addEventListener("click", () => setMainImage(index));
    });
    activeImageIndex = 0;
  }

  function getGalleryList() {
    productImages = Array.from(document.querySelectorAll(".misutech_product_thumbnail"));
    return productImages.map((btn) => btn.dataset.image || btn.querySelector("img")?.src).filter(Boolean);
  }

  function syncLightboxThumbs() {
    const list = getGalleryList();
    if (!lbThumbsTrack) return;
    lbThumbsTrack.innerHTML = "";
    if (lbTotal) lbTotal.textContent = String(list.length || 1);

    list.forEach((src, idx) => {
      const thumbBtn = document.createElement("button");
      thumbBtn.className = `misutech_lb_thumb_item ${idx === activeImageIndex ? "misutech_lb_active" : ""}`;
      thumbBtn.type = "button";
      thumbBtn.dataset.index = String(idx);
      thumbBtn.dataset.src = src;
      thumbBtn.setAttribute("aria-label", `Ảnh ${idx + 1}`);

      const img = document.createElement("img");
      img.src = src;
      img.alt = `Thumbnail ${idx + 1}`;
      img.draggable = false;
      thumbBtn.appendChild(img);

      thumbBtn.addEventListener("click", () => {
        setMainImage(idx);
        resetLbZoom();
      });

      lbThumbsTrack.appendChild(thumbBtn);
    });
  }

  function updateLightboxActive(index) {
    if (!lbMainImg) return;
    const list = getGalleryList();
    if (!list.length) return;
    const safeIdx = (index + list.length) % list.length;
    
    lbMainImg.classList.add("is-animating-switch");
    lbMainImg.src = list[safeIdx];
    if (lbCurrent) lbCurrent.textContent = String(safeIdx + 1);

    if (lbThumbsTrack) {
      const thumbs = lbThumbsTrack.querySelectorAll(".misutech_lb_thumb_item");
      thumbs.forEach((t, i) => {
        const isActive = i === safeIdx;
        t.classList.toggle("misutech_lb_active", isActive);
        if (isActive) {
          t.scrollIntoView({ behavior: "smooth", block: "nearest", inline: "center" });
        }
      });
    }

    window.setTimeout(() => {
      if (lbMainImg) lbMainImg.classList.remove("is-animating-switch");
    }, 250);
  }

  let lbRotation = 0;

  function applyLbTransform() {
    if (!lbMainImg) return;
    if (lbZoom > 1 || lbRotation !== 0) {
      if (lbZoom > 1) {
        lbMainImg.classList.add("is-zoomed");
      } else {
        lbMainImg.classList.remove("is-zoomed");
      }
      lbMainImg.style.transform = `translate3d(${lbPanX}px, ${lbPanY}px, 0) scale(${lbZoom}) rotate(${lbRotation}deg)`;
    } else {
      lbMainImg.classList.remove("is-zoomed");
      lbMainImg.style.transform = "";
    }
  }

  function rotateLbImage() {
    lbRotation = (lbRotation + 90) % 360;
    applyLbTransform();
  }

  function resetLbZoom() {
    lbZoom = 1;
    lbPanX = 0;
    lbPanY = 0;
    lbRotation = 0;
    isPanning = false;
    isSwiping = false;
    if (lbWrapper) lbWrapper.classList.remove("is-panning");
    if (lbMainImg) {
      lbMainImg.classList.remove("is-zoomed");
      lbMainImg.style.transform = "";
    }
  }

  function setLbZoom(newZoom) {
    lbZoom = Math.max(1, Math.min(3.5, newZoom));
    if (lbZoom === 1) {
      lbPanX = 0;
      lbPanY = 0;
    }
    applyLbTransform();
  }

  // Zoom & Rotate controls
  document.getElementById("misutech_lb_zoom_in")?.addEventListener("click", () => setLbZoom(lbZoom + 0.5));
  document.getElementById("misutech_lb_zoom_out")?.addEventListener("click", () => setLbZoom(lbZoom - 0.5));
  document.getElementById("misutech_lb_zoom_reset")?.addEventListener("click", rotateLbImage);

  // Fullscreen
  document.getElementById("misutech_lb_fullscreen")?.addEventListener("click", () => {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen?.().catch(() => {});
    } else {
      document.exitFullscreen?.().catch(() => {});
    }
  });

  // Prev / Next Navigation
  document.getElementById("misutech_lb_prev")?.addEventListener("click", () => {
    setMainImage(activeImageIndex - 1);
    resetLbZoom();
  });
  document.getElementById("misutech_lb_next")?.addEventListener("click", () => {
    setMainImage(activeImageIndex + 1);
    resetLbZoom();
  });

  // Double click / Double tap to Zoom
  let lastTapTime = 0;
  lbWrapper?.addEventListener("click", (e) => {
    const currentTime = new Date().getTime();
    const tapLength = currentTime - lastTapTime;
    if (tapLength < 300 && tapLength > 0) {
      // Double tap detected
      e.preventDefault();
      if (lbZoom > 1) {
        resetLbZoom();
      } else {
        setLbZoom(2);
      }
    }
    lastTapTime = currentTime;
  });

  // Wheel Zoom on Lightbox
  lbStage?.addEventListener("wheel", (e) => {
    if (!imageModal || imageModal.hidden) return;
    e.preventDefault();
    const delta = e.deltaY < 0 ? 0.25 : -0.25;
    setLbZoom(lbZoom + delta);
  }, { passive: false });

  // Touch & Mouse Drag / Swipe Handlers
  if (lbStage) {
    // Touch Start
    lbStage.addEventListener("touchstart", (e) => {
      if (e.touches.length === 1) {
        const touch = e.touches[0];
        if (lbZoom > 1) {
          isPanning = true;
          panStartX = touch.clientX - lbPanX;
          panStartY = touch.clientY - lbPanY;
        } else {
          isSwiping = true;
          swipeStartX = touch.clientX;
          swipeCurrentX = touch.clientX;
        }
      }
    }, { passive: true });

    // Touch Move
    lbStage.addEventListener("touchmove", (e) => {
      if (e.touches.length === 1) {
        const touch = e.touches[0];
        if (isPanning && lbZoom > 1) {
          lbPanX = touch.clientX - panStartX;
          lbPanY = touch.clientY - panStartY;
          applyLbTransform();
        } else if (isSwiping && lbZoom === 1 && lbMainImg) {
          swipeCurrentX = touch.clientX;
          const diff = swipeCurrentX - swipeStartX;
          // Hiệu ứng kéo vuốt mượt mà theo ngón tay
          lbMainImg.style.transform = `translate3d(${diff * 0.75}px, 0, 0)`;
        }
      }
    }, { passive: true });

    // Touch End
    lbStage.addEventListener("touchend", () => {
      if (isPanning) {
        isPanning = false;
      }
      if (isSwiping) {
        isSwiping = false;
        const diff = swipeCurrentX - swipeStartX;
        if (lbMainImg) lbMainImg.style.transform = "";
        if (Math.abs(diff) > 45) {
          if (diff > 0) {
            setMainImage(activeImageIndex - 1);
          } else {
            setMainImage(activeImageIndex + 1);
          }
        }
      }
    });

    // Mouse Drag Panning when zoomed
    lbStage.addEventListener("mousedown", (e) => {
      if (e.button !== 0) return;
      if (lbZoom > 1) {
        isPanning = true;
        panStartX = e.clientX - lbPanX;
        panStartY = e.clientY - lbPanY;
        lbWrapper?.classList.add("is-panning");
      } else {
        isSwiping = true;
        swipeStartX = e.clientX;
        swipeCurrentX = e.clientX;
      }
    });

    window.addEventListener("mousemove", (e) => {
      if (isPanning && lbZoom > 1) {
        lbPanX = e.clientX - panStartX;
        lbPanY = e.clientY - panStartY;
        applyLbTransform();
      } else if (isSwiping && lbZoom === 1 && lbMainImg) {
        swipeCurrentX = e.clientX;
        const diff = swipeCurrentX - swipeStartX;
        lbMainImg.style.transform = `translate3d(${diff * 0.65}px, 0, 0)`;
      }
    });

    window.addEventListener("mouseup", () => {
      if (isPanning) {
        isPanning = false;
        lbWrapper?.classList.remove("is-panning");
      }
      if (isSwiping) {
        isSwiping = false;
        const diff = swipeCurrentX - swipeStartX;
        if (lbMainImg) lbMainImg.style.transform = "";
        if (Math.abs(diff) > 60) {
          if (diff > 0) {
            setMainImage(activeImageIndex - 1);
          } else {
            setMainImage(activeImageIndex + 1);
          }
        }
      }
    });
  }

  function openModal(name) {
    const target = document.querySelector(`[data-modal="${name}"]`);
    if (!target) return;
    target.hidden = false;
    target.removeAttribute("hidden");
    target.style.display = name === "image" ? "flex" : "grid";
    document.body.classList.add("misutech_product_no_scroll");

    if (name === "image") {
      syncLightboxThumbs();
      resetLbZoom();
      updateLightboxActive(activeImageIndex);
    }

    const closeButton = target.querySelector("[data-close-modal]");
    if (closeButton) closeButton.focus();
  }

  function closeModal(target) {
    if (!target) return;
    target.hidden = true;
    target.setAttribute("hidden", "");
    target.style.display = "none";
    if (target.dataset?.modal === "image") {
      resetLbZoom();
    }
    if (!document.querySelector(".misutech_product_modal:not([hidden])")) {
      document.body.classList.remove("misutech_product_no_scroll");
    }
  }

  function openCart() {
    if (!cartDrawer) return;
    cartDrawer.hidden = false;
    // Force a microtick so CSS transitions work if drawer was hidden
    requestAnimationFrame(() => {
      cartDrawer.classList.add("misutech_product_open");
      cartDrawer.setAttribute("aria-hidden", "false");
    });
    if (cartOverlay) cartOverlay.hidden = false;
    document.body.classList.add("misutech_product_no_scroll");
  }

  function closeCart() {
    if (!cartDrawer) return;
    cartDrawer.classList.remove("misutech_product_open");
    cartDrawer.setAttribute("aria-hidden", "true");
    if (cartOverlay) cartOverlay.hidden = true;
    if (!document.querySelector(".misutech_product_modal:not([hidden])")) {
      document.body.classList.remove("misutech_product_no_scroll");
    }
    window.setTimeout(() => {
      if (cartDrawer && !cartDrawer.classList.contains("misutech_product_open")) {
        cartDrawer.hidden = true;
      }
    }, 280);
  }

  function addToCart(quantity, title, price, imageSource, openAfterAdd) {
    const safeQuantity = Math.max(1, Number(quantity) || 1);
    const numericPrice = Number(String(price).replace(/[^0-9.]/g, "")) || 0;
    cartItemCount += safeQuantity;
    cartValue += numericPrice * safeQuantity;
    if (cartCount) cartCount.textContent = String(cartItemCount);
    if (cartTotal) cartTotal.textContent = `${cartValue.toLocaleString('vi-VN')}đ`;
    if (cartItems) {
      cartItems.innerHTML = `
        <article class="misutech_product_cart_item">
          <img class="misutech_product_cart_item_image" src="${imageSource}" alt="${title}">
          <div>
            <h3 class="misutech_product_cart_item_title">${title}</h3>
            <p class="misutech_product_cart_item_meta">${safeQuantity} × ${price}</p>
          </div>
        </article>`;
    }
    showToast(`Đã thêm ${title} vào giỏ hàng.`);
    if (openAfterAdd) openCart();
  }

  // =========================================================================
  // HIGH-PERFORMANCE ZERO-LATENCY MODEL SWITCHER (Anti-Spam, 0ms Delay)
  // =========================================================================

  function renderModelData(data, targetUrl, pushState = true) {
    if (!data) return;

    // 1. Update Title & Meta in Document Head
    if (data.meta_title) document.title = data.meta_title;
    const metaDesc = document.querySelector('meta[name="description"]');
    if (metaDesc && data.meta_description) {
      metaDesc.setAttribute("content", data.meta_description);
    }

    // 2. Update Breadcrumb & Heading
    const breadcrumbCurrent = document.getElementById("misutech_breadcrumb_current");
    if (breadcrumbCurrent && data.name) breadcrumbCurrent.textContent = data.name;

    const productTitle = document.getElementById("misutech_product_title");
    if (productTitle && data.name) productTitle.textContent = data.name;

    // 3. Update Price Box
    const priceBox = document.getElementById("misutech_product_price_box");
    if (priceBox) {
      if (data.has_sale) {
        priceBox.innerHTML = `
          <span class="misutech_product_price_sale">${data.sale_price_formatted || (Number(data.sale_price).toLocaleString('vi-VN') + 'đ')}</span>
          <span class="misutech_product_price_original">${data.price_formatted || (Number(data.price).toLocaleString('vi-VN') + 'đ')}</span>
          <span class="misutech_product_price_badge">-${data.discount_percent || 0}%</span>
        `;
      } else {
        priceBox.innerHTML = `<span class="misutech_product_price_sale">${data.price_formatted || (Number(data.price).toLocaleString('vi-VN') + 'đ')}</span>`;
      }
    }

    // 4. Update Short Description
    const summaryEl = document.getElementById("misutech_product_summary");
    if (summaryEl) {
      summaryEl.innerHTML = data.short_description || "";
      summaryEl.style.display = data.short_description ? "" : "none";
    }

    // 5. Update SKU
    const skuEl = document.getElementById("misutech_product_sku");
    const skuRow = document.getElementById("misutech_sku_row");
    if (skuEl) skuEl.textContent = data.sku || "";
    if (skuRow) skuRow.style.display = data.sku ? "" : "none";

    // 6. Update Stock Status
    const stockDot = document.getElementById("misutech_product_stock_dot");
    const stockText = document.getElementById("misutech_product_stock_text");
    if (stockDot) {
      stockDot.className = `misutech_product_stock_dot ${data.is_active ? "" : "misutech_product_out_of_stock"}`;
    }
    if (stockText) {
      stockText.textContent = data.is_active ? "Còn hàng" : "Hết hàng";
    }

    // 7. Update Button Product IDs
    if (data.id) {
      document.querySelectorAll("[data-product-id]").forEach((btn) => {
        btn.dataset.productId = data.id;
      });
    }

    // 8. Update Description Tab Content
    const descPanel = document.getElementById("misutech_tab_panel_description");
    if (descPanel) {
      if (data.content && data.content.trim() !== "") {
        descPanel.innerHTML = `<div class="misutech_product_content_body">${data.content}</div>`;
      } else if (data.is_full_data) {
        descPanel.innerHTML = '<p class="misutech_product_no_content">Chưa có mô tả chi tiết cho sản phẩm này.</p>';
      }
    }

    // 9. Update Documents Tab Content (Embedded PDF Viewers)
    const docPanel = document.getElementById("misutech_tab_panel_document");
    if (docPanel) {
      if (data.catalogs && data.catalogs.length > 0) {
        let docsHtml = '<div class="misutech_product_catalog_viewers_list">';
        data.catalogs.forEach((doc) => {
          const docTitle = doc.filename || 'Tài liệu Catalog / Kỹ thuật';
          const downloadUrl = doc.download_url || (doc.id ? `/documents/${doc.id}/download` : doc.url);
          docsHtml += `
            <div class="misutech_product_catalog_item">
              <div class="misutech_product_catalog_header">
                <div class="misutech_product_catalog_title">
                  <span class="misutech_product_catalog_badge">PDF</span>
                  <strong>${docTitle}</strong>
                </div>
                <div class="misutech_product_catalog_actions">
                  <a href="${doc.url}" target="_blank" rel="noopener" class="misutech_product_catalog_btn">
                    Mở toàn màn hình ↗
                  </a>
                  <a href="${downloadUrl}" class="misutech_product_catalog_btn misutech_product_catalog_btn_primary">
                    Tải xuống ⤓
                  </a>
                </div>
              </div>
              <div class="misutech_product_catalog_frame_wrap">
                <iframe src="${doc.url}#navpanes=0&pagemode=none&view=FitH&toolbar=1" class="misutech_product_pdf_iframe" title="${docTitle}" loading="lazy"></iframe>
              </div>
            </div>
          `;
        });
        docsHtml += "</div>";
        docPanel.innerHTML = docsHtml;
      } else if (data.is_full_data) {
        docPanel.innerHTML = '<p class="misutech_product_no_content">Chưa có tài liệu catalog cho sản phẩm này.</p>';
      }
    }

    // 10. Update Gallery & Main Image
    const fallbackImage = '/storage/clients/imgs/products/no-image.png';
    const thumbUrl = data.thumbnail_url || fallbackImage;

    if (mainImage) {
      mainImage.src = thumbUrl;
      mainImage.alt = data.name || '';
    }
    if (modalImage) {
      modalImage.src = thumbUrl;
      modalImage.alt = data.name || '';
    }

    const thumbWrapper = document.getElementById("misutech_product_thumbnails_wrapper");
    if (thumbWrapper && (data.gallery || data.is_full_data)) {
      let thumbsHtml = `
        <button class="misutech_product_thumbnail misutech_product_active"
            type="button"
            data-image="${thumbUrl}"
            aria-label="Ảnh chính">
            <img class="misutech_product_thumbnail_image"
                src="${thumbUrl}"
                alt="${data.name || ''}"
                onerror="this.src='${fallbackImage}'">
        </button>
      `;
      if (data.gallery && data.gallery.length > 0) {
        data.gallery.forEach((img, i) => {
          thumbsHtml += `
            <button class="misutech_product_thumbnail"
                type="button"
                data-image="${img.url || fallbackImage}"
                aria-label="Ảnh ${i + 2}">
                <img class="misutech_product_thumbnail_image"
                    src="${img.url || fallbackImage}"
                    alt="${img.alt || data.name || ''}"
                    onerror="this.src='${fallbackImage}'">
            </button>
          `;
        });
      }
      thumbWrapper.innerHTML = thumbsHtml;
      rebindGalleryEvents();
      syncLightboxThumbs();
    }

    // 11. Update URL via HTML5 History API (Preserves direct URL access & SEO)
    const currentUrl = targetUrl || data.url || `/san-pham/${data.slug}`;
    if (pushState && currentUrl) {
      window.history.pushState({ slug: data.slug, name: data.name }, data.name || '', currentUrl);
    }

    // 12. Update Canonical & OpenGraph tags
    const canonicalLink = document.querySelector('link[rel="canonical"]');
    if (canonicalLink && currentUrl) canonicalLink.setAttribute("href", currentUrl);

    const ogTitle = document.querySelector('meta[property="og:title"]');
    if (ogTitle && data.meta_title) ogTitle.setAttribute("content", data.meta_title);

    const ogUrl = document.querySelector('meta[property="og:url"]');
    if (ogUrl && currentUrl) ogUrl.setAttribute("content", currentUrl);

    const ogImg = document.querySelector('meta[property="og:image"]');
    if (ogImg && thumbUrl) ogImg.setAttribute("content", thumbUrl);

    // 13. Update Share Links
    const shareInput = document.querySelector(".misutech_product_copy_input");
    if (shareInput && currentUrl) {
      shareInput.value = window.location.href;
    }
    const shareFb = document.querySelector(".misutech_product_share_fb");
    if (shareFb && currentUrl) {
      shareFb.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`;
    }
    const shareTw = document.querySelector(".misutech_product_share_twitter");
    if (shareTw && currentUrl) {
      shareTw.href = `https://twitter.com/intent/tweet?url=${encodeURIComponent(window.location.href)}&text=${encodeURIComponent(data.name || '')}`;
    }
  }

  async function switchProductModel(slug, targetUrl, pushState = true) {
    if (!slug) return;

    // 1. Highlight nút active tức thì
    document.querySelectorAll(".misutech_product_model_btn").forEach((btn) => {
      const isTarget = btn.dataset.slug === slug;
      btn.classList.toggle("misutech_product_model_active", isTarget);
      if (isTarget) {
        btn.setAttribute("aria-current", "page");
      } else {
        btn.removeAttribute("aria-current");
      }
    });

    const cachedData = localModelStore[slug];

    // 2. Render ngay lập tức dữ liệu đã có trong bộ nhớ (Tiêu đề, SKU, Giá, Ảnh, Tình trạng) -> 0ms delay
    if (cachedData) {
      renderModelData(cachedData, targetUrl, pushState);
    }

    // 3. Nếu dữ liệu đã ĐẦY ĐỦ (đã có content / is_full_data === true), dừng lại ngay!
    if (cachedData && cachedData.is_full_data) {
      return;
    }

    // 4. Nếu chưa có FULL DATA (chưa có nội dung mô tả chi tiết, catalog, gallery), fetch AJAX ngay lập tức
    if (currentAbortController) {
      currentAbortController.abort(); // Hủy request cũ nếu người dùng click liên tục
    }
    currentAbortController = new AbortController();

    // Hiển thị trạng thái tải nhẹ ở tab mô tả
    const descPanel = document.getElementById("misutech_tab_panel_description");
    if (descPanel && (!cachedData || !cachedData.content)) {
      descPanel.innerHTML = '<div style="padding: 40px 20px; text-align: center; color: #64748b; font-size: 14px;"><i class="fa fa-spinner fa-spin mr-2" style="font-size: 18px; color: #003b70;"></i> Đang tải thông tin mô tả chi tiết...</div>';
    }

    try {
      const response = await fetch(`/san-pham/${slug}?ajax=1`, {
        signal: currentAbortController.signal,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json",
        },
      });

      if (!response.ok) throw new Error("Network error");
      const data = await response.json();

      if (data && data.success) {
        data.is_full_data = true;
        localModelStore[slug] = Object.assign(localModelStore[slug] || {}, data);
        renderModelData(localModelStore[slug], targetUrl, pushState);
      }
    } catch (err) {
      if (err.name !== "AbortError") {
        console.error("Model fetch error:", err);
      }
    }
  }

  // Bind Model Switcher buttons
  function initModelSwitcher() {
    document.querySelectorAll(".misutech_product_model_btn").forEach((btn) => {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        const slug = this.dataset.slug;
        const targetUrl = this.href;
        if (!slug || this.classList.contains("misutech_product_model_active")) return;
        switchProductModel(slug, targetUrl, true);
      });
    });

    // Instant Live Quick Search Filter for Series Models
    const modelSearchInput = document.getElementById("misutechModelSearchInput");
    const modelSearchClear = document.getElementById("misutechModelSearchClear");
    const modelBtns = Array.from(document.querySelectorAll("#misutechSeriesModelsContainer .misutech_product_model_btn"));
    const modelEmptyMsg = document.getElementById("misutechSeriesSearchEmpty");
    const modelBadge = document.getElementById("misutechSeriesBadge");
    const totalModelCount = modelBtns.length;

    if (modelSearchInput) {
      modelSearchInput.addEventListener("input", () => {
        const q = modelSearchInput.value.trim().toLowerCase();
        if (modelSearchClear) {
          modelSearchClear.hidden = (q === "");
        }

        let visibleCount = 0;
        modelBtns.forEach((btn) => {
          const searchVal = btn.dataset.search || btn.textContent.toLowerCase();
          const isMatch = (q === "" || searchVal.includes(q));
          btn.style.display = isMatch ? "flex" : "none";
          if (isMatch) visibleCount++;
        });

        if (modelEmptyMsg) {
          modelEmptyMsg.hidden = (visibleCount > 0);
        }

        if (modelBadge) {
          if (q === "") {
            modelBadge.textContent = `${totalModelCount} model`;
          } else {
            modelBadge.textContent = `${visibleCount}/${totalModelCount} model`;
          }
        }
      });

      modelSearchClear?.addEventListener("click", () => {
        modelSearchInput.value = "";
        modelSearchInput.dispatchEvent(new Event("input"));
        modelSearchInput.focus();
      });
    }

    // Handle Browser Back / Forward buttons (popstate)
    window.addEventListener("popstate", function (e) {
      if (e.state && e.state.slug) {
        switchProductModel(e.state.slug, window.location.pathname, false);
      } else {
        const match = window.location.pathname.match(/\/san-pham\/([^/]+)/);
        if (match && match[1]) {
          switchProductModel(match[1], window.location.pathname, false);
        }
      }
    });
  }

  // Related products carousel
  function relatedCardsPerPage() {
    if (window.innerWidth <= 480) return 1;
    if (window.innerWidth <= 900) return 2;
    return 4;
  }

  function showRelatedPage(page) {
    if (!relatedTrack) return;
    const totalPages = Math.ceil(relatedCards.length / relatedVisibleCount);
    relatedPage = Math.max(0, Math.min(page, totalPages - 1));
    relatedTrack.style.transform = `translateX(-${relatedPage * 100}%)`;
    document.querySelectorAll(".misutech_product_related_dot").forEach((dot, index) => {
      dot.classList.toggle("misutech_product_active", index === relatedPage);
      dot.setAttribute("aria-pressed", String(index === relatedPage));
    });
  }

  function buildRelatedDots() {
    if (!relatedDots || !relatedCards.length) return;
    const newVisibleCount = relatedCardsPerPage();
    const totalPages = Math.ceil(relatedCards.length / newVisibleCount);
    relatedVisibleCount = newVisibleCount;
    relatedDots.innerHTML = "";
    for (let index = 0; index < totalPages; index += 1) {
      const button = document.createElement("button");
      button.className = "misutech_product_related_dot";
      button.type = "button";
      button.dataset.relatedPage = String(index);
      button.setAttribute("aria-label", `Related products page ${index + 1}`);
      button.addEventListener("click", () => showRelatedPage(index));
      relatedDots.appendChild(button);
    }
    showRelatedPage(Math.min(relatedPage, totalPages - 1));
  }

  // Initial event bindings
  rebindGalleryEvents();
  initModelSwitcher();

  document.querySelectorAll("[data-image-direction]").forEach((button) => {
    button.addEventListener("click", () => {
      setMainImage(activeImageIndex + Number(button.dataset.imageDirection));
    });
  });

  // Mở Lightbox xem toàn bộ ảnh
  document.querySelectorAll("[data-open-image], [data-open-modal='image'], .misutech_product_main_image_button, #misutech_main_img").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      openModal("image");
    });
  });

  // Ủy quyền sự kiện (Event Delegation) cho nút mở ảnh
  document.addEventListener("click", (e) => {
    const trigger = e.target.closest("[data-open-image], [data-open-modal='image'], .misutech_product_main_image_button, .misutech_product_zoom_hint");
    if (trigger) {
      e.preventDefault();
      openModal("image");
    }
  });

  document.querySelectorAll("[data-quantity-change]").forEach((button) => {
    button.addEventListener("click", () => {
      const nextValue = Number(quantityInput.value) + Number(button.dataset.quantityChange);
      quantityInput.value = String(Math.max(1, Math.min(99, nextValue)));
    });
  });

  quantityInput?.addEventListener("change", () => {
    quantityInput.value = String(Math.max(1, Math.min(99, Number(quantityInput.value) || 1)));
  });

  document.querySelector(".misutech_product_add_button")?.addEventListener("click", () => {
    const title = document.getElementById("misutech_product_title")?.textContent || "Sản phẩm";
    const price = document.getElementById("misutech_product_price_box")?.textContent?.trim() || "";
    addToCart(quantityInput ? quantityInput.value : 1, title, price, mainImage ? mainImage.src : "", true);
  });

  document.querySelector(".misutech_product_buy_now")?.addEventListener("click", () => {
    const title = document.getElementById("misutech_product_title")?.textContent || "Sản phẩm";
    const price = document.getElementById("misutech_product_price_box")?.textContent?.trim() || "";
    addToCart(quantityInput ? quantityInput.value : 1, title, price, mainImage ? mainImage.src : "", false);
    showToast("Sản phẩm đã sẵn sàng để thanh toán.");
  });

  document.querySelector(".misutech_product_wishlist_button")?.addEventListener("click", (event) => {
    const button = event.currentTarget;
    const active = button.classList.toggle("misutech_product_active");
    button.textContent = active ? "★" : "☆";
    showToast(active ? "Đã thêm vào danh sách yêu thích." : "Đã xóa khỏi danh sách yêu thích.");
  });

  document.querySelectorAll("[data-open-modal]").forEach((button) => {
    button.addEventListener("click", () => openModal(button.dataset.openModal));
  });

  document.querySelectorAll("[data-close-modal]").forEach((button) => {
    button.addEventListener("click", () => closeModal(button.closest(".misutech_product_modal")));
  });

  document.querySelectorAll(".misutech_product_modal").forEach((modal) => {
    modal.addEventListener("click", (event) => {
      if (event.target === modal) closeModal(modal);
    });
  });

  document.querySelector(".misutech_product_copy_button")?.addEventListener("click", async () => {
    const input = document.querySelector(".misutech_product_copy_input");
    if (!input) return;
    try {
      await navigator.clipboard.writeText(input.value);
    } catch (error) {
      input.select();
      document.execCommand("copy");
    }
    showToast("Đã sao chép liên kết sản phẩm.");
  });

  const shareCopyBtn = document.getElementById("misutech_share_copy_btn");
  if (shareCopyBtn) {
    shareCopyBtn.addEventListener("click", async () => {
      try {
        await navigator.clipboard.writeText(window.location.href);
      } catch (e) {
        const dummy = document.createElement("input");
        document.body.appendChild(dummy);
        dummy.value = window.location.href;
        dummy.select();
        document.execCommand("copy");
        document.body.removeChild(dummy);
      }

      const originalSvg = shareCopyBtn.innerHTML;
      shareCopyBtn.classList.add("copied");
      shareCopyBtn.innerHTML = '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';

      showToast("Đã sao chép liên kết sản phẩm!");

      setTimeout(() => {
        shareCopyBtn.classList.remove("copied");
        shareCopyBtn.innerHTML = originalSvg;
      }, 2000);
    });
  }

  const questionForm = document.querySelector(".misutech_product_question_form");
  if (questionForm) {
    questionForm.addEventListener("submit", async (event) => {
      event.preventDefault();
      const form = event.currentTarget;
      const submitBtn = form.querySelector(".misutech_product_modal_submit");
      const btnText = submitBtn ? submitBtn.querySelector(".btn-text") : null;
      const errorMsg = form.querySelector(".misutech_modal_error_msg");

      if (errorMsg) {
        errorMsg.style.display = "none";
        errorMsg.textContent = "";
      }

      if (submitBtn) {
        submitBtn.disabled = true;
        if (btnText) btnText.textContent = "ĐANG GỬI YÊU CẦU...";
      }

      try {
        const formData = new FormData(form);
        const response = await fetch(form.action, {
          method: "POST",
          body: formData,
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json"
          }
        });

        const result = await response.json();

        if (response.ok && result.success !== false) {
          form.reset();
          showToast(result.message || "Yêu cầu của bạn đã được gửi thành công. Đội ngũ MISUTECH sẽ liên hệ lại sớm nhất!");
          setTimeout(() => {
            closeModal(document.querySelector('[data-modal="question"]'));
          }, 800);
        } else {
          let errText = result.message || "Không thể gửi yêu cầu. Vui lòng kiểm tra lại thông tin.";
          if (result.errors) {
            const firstError = Object.values(result.errors)[0];
            if (Array.isArray(firstError)) errText = firstError[0];
          }
          if (errorMsg) {
            errorMsg.textContent = errText;
            errorMsg.style.display = "block";
          } else {
            showToast(errText);
          }
        }
      } catch (err) {
        console.error("Submit question error:", err);
        if (errorMsg) {
          errorMsg.textContent = "Có lỗi xảy ra khi kết nối máy chủ. Vui lòng thử lại hoặc gọi Hotline.";
          errorMsg.style.display = "block";
        } else {
          showToast("Có lỗi xảy ra khi kết nối. Vui lòng gọi trực tiếp Hotline.");
        }
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          if (btnText) btnText.textContent = "GỬI YÊU CẦU CHO KỸ SƯ";
        }
      }
    });
  }

  // Tab switching
  document.querySelectorAll(".misutech_product_tab_button").forEach((button) => {
    button.addEventListener("click", (e) => {
      e.preventDefault();
      const tabKey = button.dataset.tab;
      const targetPanel = document.querySelector(`[data-panel="${tabKey}"]`);
      if (!targetPanel) return;

      const tabsSection = document.querySelector(".misutech_product_tabs_section");
      const currentScrollY = window.scrollY;
      const tabsTop = tabsSection ? (tabsSection.getBoundingClientRect().top + window.scrollY - 110) : 0;

      // 1. Show target panel first to prevent body collapse
      targetPanel.hidden = false;
      targetPanel.classList.add("misutech_product_active");

      // 2. Hide other panels
      document.querySelectorAll(".misutech_product_tab_panel").forEach((panel) => {
        if (panel !== targetPanel) {
          panel.classList.remove("misutech_product_active");
          panel.hidden = true;
        }
      });

      // 3. Update tab buttons
      document.querySelectorAll(".misutech_product_tab_button").forEach((item) => {
        item.classList.remove("misutech_product_active");
        item.setAttribute("aria-selected", "false");
      });
      button.classList.add("misutech_product_active");
      button.setAttribute("aria-selected", "true");

      // 4. If current scroll was below tabs, smoothly keep user at the top of the tabs section
      if (tabsSection && currentScrollY > tabsTop) {
        window.scrollTo({ top: tabsTop, behavior: "smooth" });
      }

      // 5. Khi mở tab Tài liệu & Catalog, đảm bảo toàn bộ iframe có tham số chuẩn
      if (tabKey === "document") {
        targetPanel.querySelectorAll(".misutech_product_pdf_iframe").forEach((iframe) => {
          let src = iframe.getAttribute("src") || "";
          if (src && !src.includes("navpanes=0")) {
            const base = src.split("#")[0];
            iframe.setAttribute("src", `${base}#navpanes=0&pagemode=none&view=FitH&toolbar=1`);
          }
        });
      }
    });
  });

  // =========================================================================
  // REVIEW INTERACTION & SECURE AJAX SUBMISSION
  // =========================================================================
  const starRatingLabels = {
    1: "Rất tệ (1/5)",
    2: "Tệ (2/5)",
    3: "Bình thường (3/5)",
    4: "Tốt (4/5)",
    5: "Tuyệt vời (5/5)"
  };

  const starPickerBtns = document.querySelectorAll("#misutech_star_picker .star_btn");
  const selectedRatingInput = document.getElementById("misutech_selected_rating");
  const ratingLabelEl = document.getElementById("misutech_rating_label");
  const openReviewBtn = document.getElementById("misutech_btn_open_review");
  const reviewFormWrapper = document.getElementById("misutech_review_form_wrapper");
  const reviewForm = document.getElementById("misutech_review_form");

  if (openReviewBtn && reviewFormWrapper) {
    openReviewBtn.addEventListener("click", () => {
      reviewFormWrapper.scrollIntoView({ behavior: "smooth", block: "center" });
      const firstInput = reviewFormWrapper.querySelector("input[name='author_name']");
      if (firstInput) firstInput.focus();
    });
  }

  function setStarRating(rating) {
    const val = Math.max(1, Math.min(5, Number(rating) || 5));
    if (selectedRatingInput) selectedRatingInput.value = String(val);
    if (ratingLabelEl) ratingLabelEl.textContent = starRatingLabels[val] || `${val}/5`;

    starPickerBtns.forEach((btn) => {
      const star = Number(btn.dataset.star);
      btn.classList.toggle("active", star <= val);
    });
  }

  starPickerBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      setStarRating(btn.dataset.star);
    });
  });

  if (reviewForm) {
    reviewForm.addEventListener("submit", async (event) => {
      event.preventDefault();
      const form = event.currentTarget;
      const submitBtn = document.getElementById("misutech_review_submit_btn");
      const btnText = submitBtn ? submitBtn.querySelector(".btn_text") : null;
      const errorMsg = document.getElementById("misutech_review_error");

      if (errorMsg) {
        errorMsg.style.display = "none";
        errorMsg.textContent = "";
      }

      // Client-side quick checks
      const nameVal = form.querySelector("input[name='author_name']")?.value.trim() || "";
      const commentVal = form.querySelector("textarea[name='comment']")?.value.trim() || "";

      if (nameVal.length < 2) {
        if (errorMsg) {
          errorMsg.textContent = "Vui lòng nhập họ và tên của bạn (tối thiểu 2 ký tự).";
          errorMsg.style.display = "block";
        }
        return;
      }

      if (commentVal.length < 5) {
        if (errorMsg) {
          errorMsg.textContent = "Nội dung đánh giá phải có ít nhất 5 ký tự.";
          errorMsg.style.display = "block";
        }
        return;
      }

      if (submitBtn) {
        submitBtn.disabled = true;
        if (btnText) btnText.textContent = "ĐANG GỬI ĐÁNH GIÁ...";
      }

      try {
        const formData = new FormData(form);
        const response = await fetch(form.action, {
          method: "POST",
          body: formData,
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json"
          }
        });

        const result = await response.json();

        if (response.ok && result.success !== false) {
          showToast(result.message || "Đánh giá của bạn đã được gửi và đang chờ kiểm duyệt!");

          // Hiển thị banner thành công chờ duyệt
          let successBox = form.querySelector(".review_form_success");
          if (!successBox) {
            successBox = document.createElement("div");
            successBox.className = "review_form_success";
            successBox.style.cssText = "background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 6px; font-size: 13px; margin-top: 12px; display: flex; align-items: center; gap: 8px;";
            form.appendChild(successBox);
          }
          successBox.innerHTML = `<span>✓</span> <div><strong>Gửi đánh giá thành công!</strong> Đánh giá của bạn đã được tiếp nhận và sẽ được hiển thị công khai sau khi ban quản trị kiểm duyệt nội dung.</div>`;
          successBox.style.display = "flex";

          // Reset form sau 1s
          form.reset();
          setStarRating(5);
        } else {
          let errText = result.message || "Không thể gửi đánh giá. Vui lòng kiểm tra lại.";
          if (result.errors) {
            const firstError = Object.values(result.errors)[0];
            if (Array.isArray(firstError)) errText = firstError[0];
          }
          if (errorMsg) {
            errorMsg.textContent = errText;
            errorMsg.style.display = "block";
          } else {
            showToast(errText);
          }
        }
      } catch (err) {
        console.error("Submit review error:", err);
        if (errorMsg) {
          errorMsg.textContent = "Có lỗi kết nối máy chủ. Vui lòng thử lại sau.";
          errorMsg.style.display = "block";
        } else {
          showToast("Có lỗi kết nối. Vui lòng thử lại sau.");
        }
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          if (btnText) btnText.textContent = "GỬI ĐÁNH GIÁ";
        }
      }
    });
  }


  document.querySelectorAll("[data-related-wishlist]").forEach((button) => {
    button.addEventListener("click", () => {
      const active = button.classList.toggle("misutech_product_active");
      button.textContent = active ? "♥" : "♡";
      showToast(active ? "Đã thêm vào yêu thích." : "Đã xóa khỏi yêu thích.");
    });
  });

  document.querySelector(".misutech_product_cart_trigger")?.addEventListener("click", openCart);
  document.querySelector(".misutech_product_cart_close")?.addEventListener("click", closeCart);
  cartOverlay?.addEventListener("click", closeCart);

  backTop?.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
  window.addEventListener("scroll", () => {
    if (backTop) backTop.classList.toggle("misutech_product_visible", window.scrollY > 600);
  }, { passive: true });

  window.addEventListener("resize", () => {
    const newVisibleCount = relatedCardsPerPage();
    if (newVisibleCount !== relatedVisibleCount) buildRelatedDots();
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      document.querySelectorAll(".misutech_product_modal:not([hidden])").forEach(closeModal);
      closeCart();
    }
    const isLbOpen = imageModal && !imageModal.hidden;
    if (isLbOpen) {
      if (event.key === "ArrowLeft") {
        setMainImage(activeImageIndex - 1);
        resetLbZoom();
      } else if (event.key === "ArrowRight") {
        setMainImage(activeImageIndex + 1);
        resetLbZoom();
      } else if (event.key === "+" || event.key === "=") {
        setLbZoom(lbZoom + 0.5);
      } else if (event.key === "-") {
        setLbZoom(lbZoom - 0.5);
      } else if (event.key === "0") {
        resetLbZoom();
      } else if (event.key === "r" || event.key === "R") {
        rotateLbImage();
      } else if (event.key === "f" || event.key === "F") {
        document.getElementById("misutech_lb_fullscreen")?.click();
      }
    }
  });

  // Tự động tối ưu bảng trong nội dung mô tả sản phẩm (30/70 cho 2 cột & cuộn ngang mượt cho >= 3 cột)
  function initProductContentTables() {
    const tables = document.querySelectorAll(".misutech_product_content_body table, .misutech_product_tab_panel table");
    tables.forEach((table) => {
      // Phân loại số lượng cột từ hàng đầu tiên
      const firstRow = table.querySelector("tr");
      if (firstRow) {
        const cellCount = firstRow.children.length;
        if (cellCount === 2) {
          table.classList.add("misutech_table_2cols");
        } else if (cellCount >= 3) {
          table.classList.add("misutech_table_multicols");
        }
      }
      // Tự động bọc wrapper cuộn ngang mượt mà nếu chưa có
      if (!table.parentElement.classList.contains("misutech_product_table_wrapper")) {
        const wrapper = document.createElement("div");
        wrapper.className = "misutech_product_table_wrapper";
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
      }
    });
  }

  initProductContentTables();
  buildRelatedDots();
})();
