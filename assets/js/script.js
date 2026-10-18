/* ==========================================
   VAB - THỜI TRANG THỂ THAO
   Main JavaScript - Giao diện khách hàng
   ========================================== */

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ===== STICKY HEADER SHADOW =====
    const siteHeader = document.querySelector('.site-header');
    if (siteHeader) {
        window.addEventListener('scroll', function() {
            siteHeader.classList.toggle('is-scrolled', window.scrollY > 10);
        }, { passive: true });
    }

    // ===== HERO BANNER — SLIDE COUNTER + ANIMATIONS =====
    const heroCarousel = document.getElementById('heroCarousel');
    const slideCounter = document.getElementById('currentSlide');

    if (heroCarousel && slideCounter) {
        const slides = heroCarousel.querySelectorAll('.carousel-item');
        const total = String(slides.length).padStart(2, '0');

        heroCarousel.addEventListener('slid.bs.carousel', function(e) {
            slideCounter.textContent = String(e.to + 1).padStart(2, '0');
        });
    }

    // Reset Ken-Burns zoom on non-active slides when they become active
    if (heroCarousel) {
        heroCarousel.addEventListener('slide.bs.carousel', function(e) {
            const nextSlide = e.relatedTarget;
            const img = nextSlide && nextSlide.querySelector('picture img');
            if (img) {
                // Force reflow to restart the CSS transition
                img.style.transition = 'none';
                img.style.transform = 'scale(1.04)';
                requestAnimationFrame(function() {
                    requestAnimationFrame(function() {
                        img.style.transition = '';
                    });
                });
            }
        });
    }

    // ===== PRODUCT SLIDER ARROWS =====
    document.querySelectorAll('.product-slider-wrap').forEach(function(wrap) {
        const viewport = wrap.querySelector('.product-slider-viewport');
        const prevBtn = wrap.querySelector('.product-slider-prev');
        const nextBtn = wrap.querySelector('.product-slider-next');

        if (!viewport || !prevBtn || !nextBtn) return;

        function getScrollStep() {
            const item = viewport.querySelector('.product-slider-item');
            if (!item) return 280;
            const gap = parseFloat(getComputedStyle(viewport.querySelector('.product-slider-track')).gap) || 20;
            return item.offsetWidth + gap;
        }

        function updateSliderButtons() {
            const maxScroll = viewport.scrollWidth - viewport.clientWidth;
            prevBtn.disabled = viewport.scrollLeft <= 4;
            nextBtn.disabled = viewport.scrollLeft >= maxScroll - 4;
        }

        prevBtn.addEventListener('click', function() {
            viewport.scrollBy({ left: -getScrollStep(), behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', function() {
            viewport.scrollBy({ left: getScrollStep(), behavior: 'smooth' });
        });

        viewport.addEventListener('scroll', updateSliderButtons, { passive: true });
        window.addEventListener('resize', updateSliderButtons);
        updateSliderButtons();
    });

    // ===== MOBILE MENU TOGGLE =====
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navMenu = document.querySelector('.nav-menu');

    if (navbarToggler && navMenu) {
        navbarToggler.addEventListener('click', function() {
            navMenu.classList.toggle('show');
        });
    }

    // ===== MOBILE DROPDOWN TOGGLE =====
    const dropdownParents = document.querySelectorAll('.nav-menu > li.has-dropdown > a');
    dropdownParents.forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (window.innerWidth <= 991.98) {
                e.preventDefault();
                const dropdown = this.nextElementSibling;
                if (dropdown) {
                    dropdown.classList.toggle('show');
                }
            }
        });
    });

    // ===== PRODUCT FILTER BUTTONS =====
    const filterButtons = document.querySelectorAll('.btn-filter');
    filterButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            filterButtons.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
        });
    });

    // ===== SIZE SELECTOR (Product Detail) =====
    const sizeButtons = document.querySelectorAll('.size-btn');
    const stockInfo = document.querySelector('.size-stock-info');
    const btnAddCartDetail = document.querySelector('.product-detail-info .btn-add-cart');

    function updateStockDisplay() {
        if (!stockInfo) return;
        const activeSizeBtn = document.querySelector('.size-options .size-btn.active');
        if (activeSizeBtn) {
            const stock = parseInt(activeSizeBtn.dataset.stock) || 0;
            if (stock > 0) {
                stockInfo.innerHTML = `<span class="text-success"><i class="fas fa-check-circle me-1"></i>Còn hàng (Còn ${stock} sản phẩm)</span>`;
                if (btnAddCartDetail) {
                    if (btnAddCartDetail.tagName === 'A') {
                        btnAddCartDetail.style.pointerEvents = 'auto';
                    } else {
                        btnAddCartDetail.disabled = false;
                    }
                    btnAddCartDetail.style.opacity = '1';
                    btnAddCartDetail.style.background = '';
                    btnAddCartDetail.style.borderColor = '';
                    btnAddCartDetail.style.color = '';
                    btnAddCartDetail.style.cursor = 'pointer';
                    btnAddCartDetail.innerHTML = `<i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng`;
                }
            } else {
                stockInfo.innerHTML = `<span class="text-danger"><i class="fas fa-times-circle me-1"></i>Hết hàng kích thước này</span>`;
                if (btnAddCartDetail) {
                    if (btnAddCartDetail.tagName === 'A') {
                        btnAddCartDetail.style.pointerEvents = 'none';
                    } else {
                        btnAddCartDetail.disabled = true;
                    }
                    btnAddCartDetail.style.opacity = '0.5';
                    btnAddCartDetail.style.background = '#dee2e6';
                    btnAddCartDetail.style.borderColor = '#dee2e6';
                    btnAddCartDetail.style.color = '#6c757d';
                    btnAddCartDetail.style.cursor = 'not-allowed';
                    btnAddCartDetail.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i>Hết hàng`;
                }
            }

            // Tự động điều chỉnh số lượng đang chọn nếu vượt quá tồn kho của size được chọn
            const qtyInput = document.querySelector('.add-to-cart-form .qty-control input');
            if (qtyInput) {
                let currentQty = parseInt(qtyInput.value) || 1;
                if (stock > 0) {
                    if (currentQty > stock) {
                        qtyInput.value = stock;
                    }
                } else {
                    qtyInput.value = 1;
                }
            }
        } else {
            if (sizeButtons.length > 0) {
                stockInfo.innerHTML = `<span class="text-warning"><i class="fas fa-info-circle me-1"></i>Vui lòng chọn kích thước</span>`;
                if (btnAddCartDetail) {
                    if (btnAddCartDetail.tagName === 'A') {
                        btnAddCartDetail.style.pointerEvents = 'none';
                    } else {
                        btnAddCartDetail.disabled = true;
                    }
                    btnAddCartDetail.style.opacity = '0.5';
                    btnAddCartDetail.innerHTML = `<i class="fas fa-shopping-cart me-2"></i>Chọn kích thước`;
                }
            }
        }
    }

    sizeButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (this.classList.contains('disabled')) return;
            sizeButtons.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            
            // Cập nhật size vào input ẩn #selected-size
            const selectedSizeInput = document.getElementById('selected-size');
            if (selectedSizeInput) {
                selectedSizeInput.value = this.dataset.size || this.textContent.trim();
            }
            
            updateStockDisplay();
        });
    });

    // Run on initial load
    updateStockDisplay();

    // ===== QUANTITY CONTROLS =====
    const qtyControls = document.querySelectorAll('.qty-control');
    qtyControls.forEach(function(control) {
        const minusBtn = control.querySelector('.qty-minus');
        const plusBtn = control.querySelector('.qty-plus');
        const input = control.querySelector('input');

        if (minusBtn && input) {
            minusBtn.addEventListener('click', function() {
                let val = parseInt(input.value) || 1;
                if (val > 1) {
                    input.value = val - 1;
                }
            });
        }

        if (plusBtn && input) {
            plusBtn.addEventListener('click', function() {
                let val = parseInt(input.value) || 1;
                let maxVal = 99;

                // Nếu nằm trong form chi tiết sản phẩm, giới hạn theo tồn kho size hoặc tổng tồn kho chung
                const form = control.closest('.add-to-cart-form');
                if (form) {
                    const activeSizeBtn = form.querySelector('.size-options .size-btn.active');
                    if (activeSizeBtn) {
                        maxVal = parseInt(activeSizeBtn.dataset.stock) || 0;
                    } else {
                        const sizeButtons = form.querySelectorAll('.size-options .size-btn');
                        if (sizeButtons.length > 0) {
                            showToast('Vui lòng chọn kích thước trước!', 'error');
                            return;
                        }
                        maxVal = parseInt(input.dataset.maxStock) || 99;
                    }
                }

                if (val < maxVal) {
                    input.value = val + 1;
                } else {
                    showToast('Đã đạt số lượng tồn kho tối đa!', 'error');
                }
            });
        }

        if (input) {
            // Lắng nghe sự kiện thay đổi giá trị khi người dùng nhập thủ công
            input.addEventListener('change', function() {
                let val = parseInt(input.value) || 1;
                let maxVal = 99;

                const form = control.closest('.add-to-cart-form');
                if (form) {
                    const activeSizeBtn = form.querySelector('.size-options .size-btn.active');
                    if (activeSizeBtn) {
                        maxVal = parseInt(activeSizeBtn.dataset.stock) || 0;
                    } else {
                        maxVal = parseInt(input.dataset.maxStock) || 99;
                    }
                }

                if (val < 1) {
                    input.value = 1;
                } else if (val > maxVal) {
                    input.value = maxVal;
                    showToast('Số lượng yêu cầu vượt quá tồn kho. Đã chuyển về số lượng tối đa: ' + maxVal, 'error');
                } else {
                    input.value = val;
                }
            });

            // Ngăn chặn gõ ký tự không phải số
            input.addEventListener('keypress', function(e) {
                if (e.key < '0' || e.key > '9') {
                    e.preventDefault();
                }
            });
        }
    });

    // ===== CART QUANTITY CONTROLS (Small) =====
    const qtyControlsSm = document.querySelectorAll('.qty-control-sm');
    qtyControlsSm.forEach(function(control) {
        const minusBtn = control.querySelector('.qty-minus');
        const plusBtn = control.querySelector('.qty-plus');
        const input = control.querySelector('input');
        const row = control.closest('tr');
        if (!row) return;
        const itemId = row.dataset.id;

        if (minusBtn && input) {
            minusBtn.addEventListener('click', function() {
                let val = parseInt(input.value) || 1;
                if (val > 1) {
                    window.location.href = 'index.php?page=cart-update&id=' + itemId + '&qty=' + (val - 1);
                }
            });
        }

        if (plusBtn && input) {
            plusBtn.addEventListener('click', function() {
                let val = parseInt(input.value) || 1;
                if (val < 99) {
                    window.location.href = 'index.php?page=cart-update&id=' + itemId + '&qty=' + (val + 1);
                }
            });
        }
    });

    // ===== REMOVE CART ITEM =====
    const removeButtons = document.querySelectorAll('.btn-remove');
    removeButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            if (row) {
                const itemId = row.dataset.id;
                if (confirm('Bạn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                    window.location.href = 'index.php?page=cart-remove&id=' + itemId;
                }
            }
        });
    });

    // ===== TOAST UTILITY =====
    function showToast(message, type) {
        if (!type) type = 'success';
        var container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        var toast = document.createElement('div');
        toast.className = 'custom-toast ' + type;
        
        var iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        
        toast.innerHTML = 
            '<i class="' + iconClass + ' toast-icon"></i>' +
            '<span class="toast-message">' + message + '</span>' +
            '<i class="fas fa-times toast-close"></i>';

        container.appendChild(toast);

        // Slide in transition
        setTimeout(function() {
            toast.classList.add('show');
        }, 10);

        // Close on button click
        var closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                dismissToast(toast);
            });
        }

        // Auto dismiss
        var timeoutId = setTimeout(function() {
            dismissToast(toast);
        }, 4000);

        function dismissToast(el) {
            el.classList.remove('show');
            el.addEventListener('transitionend', function() {
                el.remove();
                if (container.children.length === 0) {
                    container.remove();
                }
            }, { once: true });
        }
    }

    // ===== UPDATE CART BADGE =====
    function updateCartBadge(count) {
        var badges = document.querySelectorAll('.cart-badge');
        badges.forEach(function(badge) {
            var countVal = parseInt(count) || 0;
            if (countVal > 0) {
                badge.textContent = countVal;
                badge.style.display = '';
            } else {
                badge.textContent = '0';
                badge.style.display = 'none';
            }
        });
    }

    // ===== AJAX ADD TO CART FROM LISTINGS =====
    document.addEventListener('click', function(e) {
        var addCartLink = e.target.closest('a.btn-add-cart');
        if (addCartLink) {
            if (addCartLink.classList.contains('disabled')) {
                return;
            }
            e.preventDefault();
            
            var href = addCartLink.getAttribute('href');
            if (!href || href === 'javascript:void(0)') return;
            
            // Build URL with ajax=1 and cache-buster
            var url = new URL(href, window.location.origin + window.location.pathname);
            url.searchParams.set('ajax', '1');
            url.searchParams.set('t', Date.now().toString());

            var icon = addCartLink.querySelector('i');
            var originalClass = icon ? icon.className : '';
            if (icon) {
                icon.className = 'fas fa-spinner fa-spin';
            }
            addCartLink.style.pointerEvents = 'none';

            fetch(url.toString())
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        showToast(data.message, 'success');
                        updateCartBadge(data.cartCount);
                    } else {
                        showToast(data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng.', 'error');
                    }
                })
                .catch(function(error) {
                    console.error(error);
                    showToast('Không thể kết nối đến máy chủ.', 'error');
                })
                .finally(function() {
                    if (icon) {
                        icon.className = originalClass;
                    }
                    addCartLink.style.pointerEvents = 'auto';
                });
        }
    });

    // ===== AJAX ADD TO CART FROM DETAIL FORM =====
    var detailForm = document.querySelector('.add-to-cart-form');
    if (detailForm) {
        detailForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate size selection
            var sizeButtons = detailForm.querySelectorAll('.size-options .size-btn');
            var selectedSizeInput = document.getElementById('selected-size');
            if (sizeButtons.length > 0) {
                var activeSizeBtn = detailForm.querySelector('.size-options .size-btn.active');
                if (!activeSizeBtn || !selectedSizeInput || !selectedSizeInput.value) {
                    showToast('Vui lòng chọn kích thước (size) trước khi thêm vào giỏ hàng!', 'error');
                    return;
                }
            }

            var action = detailForm.getAttribute('action') || 'index.php';
            var formData = new FormData(detailForm);
            var params = new URLSearchParams();
            
            for (var pair of formData.entries()) {
                params.append(pair[0], pair[1]);
            }
            params.set('ajax', '1');
            params.set('t', Date.now().toString());

            var submitBtn = detailForm.querySelector('button[type="submit"]');
            var originalHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
            }

            var fetchUrl = action + '?' + params.toString();

            fetch(fetchUrl)
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        showToast(data.message, 'success');
                        updateCartBadge(data.cartCount);
                    } else {
                        showToast(data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng.', 'error');
                    }
                })
                .catch(function(error) {
                    console.error(error);
                    showToast('Không thể kết nối đến máy chủ.', 'error');
                })
                .finally(function() {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHtml;
                    }
                });
        });
    }

    // ===== THUMBNAIL CLICK (Product Detail) =====
    const thumbItems = document.querySelectorAll('.thumb-item');
    const mainImage = document.querySelector('.main-image');

    thumbItems.forEach(function(thumb) {
        thumb.addEventListener('click', function() {
            thumbItems.forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            if (mainImage && this.src) {
                mainImage.src = this.src;
            }
        });
    });

    // ===== PAYMENT METHOD SELECT =====
    const paymentOptions = document.querySelectorAll('.payment-option');
    paymentOptions.forEach(function(option) {
        option.addEventListener('click', function() {
            paymentOptions.forEach(function(o) { o.classList.remove('active'); });
            this.classList.add('active');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        });
    });

    // ===== SCROLL TO TOP =====
    const scrollBtn = document.getElementById('scroll-to-top');
    if (scrollBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollBtn.classList.add('show');
            } else {
                scrollBtn.classList.remove('show');
            }
        });

        scrollBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ===== LOAD MORE PRODUCTS =====
    const loadMoreBtn = document.getElementById('btn-load-more');
    const productList = document.getElementById('product-list');
    const loadMoreInfo = document.getElementById('load-more-info');
    const shownCount = document.getElementById('shown-count');

    if (loadMoreBtn && productList) {
        // Update initial shown count
        if (shownCount) {
            const items = productList.querySelectorAll('.product-item');
            shownCount.textContent = items.length;
        }

        loadMoreBtn.addEventListener('click', function() {
            const btn = this;
            const offset = parseInt(btn.dataset.offset) || 0;
            const limit = parseInt(btn.dataset.limit) || 12;
            const textEl = btn.querySelector('.btn-load-more-text');
            const spinnerEl = btn.querySelector('.btn-load-more-spinner');
            const url = 'index.php?page=load-more&offset=' + offset + '&limit=' + limit;

            // Show loading state
            btn.disabled = true;
            if (textEl) textEl.style.display = 'none';
            if (spinnerEl) spinnerEl.style.display = 'inline';

            fetch(url)
                .then(function(response) { return response.text(); })
                .then(function(html) {
                    // Create temp container to parse response
                    var temp = document.createElement('div');
                    temp.innerHTML = html;

                    // Append product items (skip data rows)
                    var items = temp.querySelectorAll('.product-item');
                    items.forEach(function(item) {
                        productList.appendChild(item);
                    });

                    // Get response metadata
                    var dataEl = temp.querySelector('.load-more-data');
                    if (dataEl) {
                        var newLoaded = parseInt(dataEl.dataset.loaded) || 0;
                        var total = parseInt(dataEl.dataset.total) || 0;
                        var hasMore = dataEl.dataset.hasmore === '1';

                        // Update button offset
                        btn.dataset.offset = newLoaded;

                        // Update shown count
                        if (shownCount) {
                            shownCount.textContent = newLoaded;
                        }

                        // Update info text
                        if (loadMoreInfo) {
                            loadMoreInfo.textContent = 'Đã hiển thị ' + newLoaded + ' / ' + total + ' sản phẩm';
                        }

                        // Hide button if no more products
                        if (!hasMore) {
                            var wrap = document.getElementById('load-more-wrap');
                            if (wrap) wrap.style.display = 'none';
                        }
                    }

                    // Reset button state
                    btn.disabled = false;
                    if (textEl) textEl.style.display = 'inline';
                    if (spinnerEl) spinnerEl.style.display = 'none';
                })
                .catch(function() {
                    // Reset button state on error
                    btn.disabled = false;
                    if (textEl) textEl.style.display = 'inline';
                    if (spinnerEl) spinnerEl.style.display = 'none';
                    alert('Có lỗi xảy ra khi tải thêm sản phẩm. Vui lòng thử lại.');
                });
        });
    }

    // ===== FORM VALIDATION (AUTH FORMS) =====
    const authForms = document.querySelectorAll('.auth-card form');
    authForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            let valid = true;
            const inputs = this.querySelectorAll('input[required]');

            inputs.forEach(function(input) {
                const errorEl = input.parentElement.querySelector('.error-message');
                if (errorEl) errorEl.remove();

                if (!input.value.trim()) {
                    const error = document.createElement('div');
                    error.className = 'error-message';
                    error.style.cssText = 'color: #dc3545; font-size: 12px; margin-top: 4px;';
                    error.textContent = 'Vui lòng nhập trường này';
                    input.parentElement.appendChild(error);
                    input.style.borderColor = '#dc3545';
                    valid = false;
                } else {
                    input.style.borderColor = '';
                }

                // Email validation
                if (input.type === 'email' && input.value.trim()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(input.value.trim())) {
                        const error = document.createElement('div');
                        error.className = 'error-message';
                        error.style.cssText = 'color: #dc3545; font-size: 12px; margin-top: 4px;';
                        error.textContent = 'Email không hợp lệ';
                        input.parentElement.appendChild(error);
                        input.style.borderColor = '#dc3545';
                        valid = false;
                    }
                }

                // Password match
                if (input.id === 'confirm-password') {
                    const password = document.getElementById('register-password');
                    if (password && input.value !== password.value) {
                        const error = document.createElement('div');
                        error.className = 'error-message';
                        error.style.cssText = 'color: #dc3545; font-size: 12px; margin-top: 4px;';
                        error.textContent = 'Mật khẩu không khớp';
                        input.parentElement.appendChild(error);
                        input.style.borderColor = '#dc3545';
                        valid = false;
                    }
                }
            });

            if (valid) {
                form.submit();
            }
        });

        // Remove error on input
        const inputs = form.querySelectorAll('input');
        inputs.forEach(function(input) {
            input.addEventListener('input', function() {
                this.style.borderColor = '';
                const error = this.parentElement.querySelector('.error-message');
                if (error) error.remove();
            });
        });
    });

    // ===== DYNAMIC HEADER HEIGHT FOR STICKY FILTER =====
    function updateHeaderHeight() {
        const header = document.querySelector('.site-header');
        if (header) {
            const height = header.offsetHeight;
            document.documentElement.style.setProperty('--header-height', height + 'px');
        }
    }
    updateHeaderHeight();
    window.addEventListener('resize', updateHeaderHeight);
    window.addEventListener('scroll', updateHeaderHeight, { passive: true });

    // ===== PRODUCT SEARCH SUGGESTIONS (AJAX) =====
    const searchInput = document.getElementById('search-input');
    const suggestionsDropdown = document.getElementById('search-suggestions');
    let debounceTimer = null;

    if (searchInput && suggestionsDropdown) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            clearTimeout(debounceTimer);
            if (query.length < 2) {
                suggestionsDropdown.innerHTML = '';
                suggestionsDropdown.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(() => {
                // Show loading state
                suggestionsDropdown.innerHTML = '<div class="suggestion-loading"><i class="fas fa-spinner fa-spin"></i>Đang tìm kiếm...</div>';
                suggestionsDropdown.style.display = 'block';

                fetch('index.php?page=suggest&keyword=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            suggestionsDropdown.innerHTML = '<div class="suggestion-empty">Không tìm thấy sản phẩm nào</div>';
                            return;
                        }

                        let html = '';
                        data.forEach(item => {
                            const formattedPrice = new Intl.NumberFormat('vi-VN').format(item.price) + 'đ';
                            let priceHtml = '';
                            if (item.promo_price) {
                                const formattedPromo = new Intl.NumberFormat('vi-VN').format(item.promo_price) + 'đ';
                                priceHtml = `
                                    <span class="suggestion-price-promo">${formattedPromo}</span>
                                    <span class="suggestion-price-original">${formattedPrice}</span>
                                `;
                            } else {
                                priceHtml = `<span class="suggestion-price-promo">${formattedPrice}</span>`;
                            }

                            html += `
                                <div class="suggestion-item" data-id="${item.id}">
                                    <img src="${item.image}" alt="${item.name}" class="suggestion-img">
                                    <div class="suggestion-info">
                                        <h6 class="suggestion-name">${item.name}</h6>
                                        <div class="suggestion-price">${priceHtml}</div>
                                    </div>
                                </div>
                            `;
                        });
                        suggestionsDropdown.innerHTML = html;

                        // Click event on item
                        suggestionsDropdown.querySelectorAll('.suggestion-item').forEach(el => {
                            el.addEventListener('click', function() {
                                const productId = this.dataset.id;
                                window.location.href = 'index.php?page=product-detail&id=' + productId;
                            });
                        });
                    })
                    .catch(err => {
                        console.error('Error fetching search suggestions:', err);
                        suggestionsDropdown.style.display = 'none';
                    });
            }, 250);
        });

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsDropdown.contains(e.target)) {
                suggestionsDropdown.style.display = 'none';
            }
        });

        // Show again if focused and has input
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2 && suggestionsDropdown.children.length > 0) {
                suggestionsDropdown.style.display = 'block';
            }
        });

        // Escape key to close
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                suggestionsDropdown.style.display = 'none';
            }
        });
    }
});

/* ==========================================
   VAB SCROLL REVEAL — IntersectionObserver
   Runs independently of DOMContentLoaded
   block so it can also catch late-rendered
   elements (e.g. after AJAX load more).
   ========================================== */
(function() {
    'use strict';

    // Skip if browser doesn't support IntersectionObserver (very old IE)
    if (!('IntersectionObserver' in window)) {
        // Fallback: show everything immediately
        document.querySelectorAll('.vab-reveal').forEach(function(el) {
            el.classList.add('is-visible');
        });
        return;
    }

    var revealObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target); // Play once only
            }
        });
    }, {
        threshold: 0.10,      // Trigger when 10% of element is visible
        rootMargin: '0px 0px -40px 0px'  // Slight offset from bottom
    });

    function observeRevealElements() {
        document.querySelectorAll('.vab-reveal:not(.is-visible)').forEach(function(el) {
            revealObserver.observe(el);
        });
    }

    // Initial observe on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', observeRevealElements);
    } else {
        observeRevealElements();
    }

    // Re-observe after Load More products are injected into the DOM
    // (MutationObserver watches for new .vab-reveal elements)
    var mutationObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element nodes only
                    if (node.classList && node.classList.contains('vab-reveal') && !node.classList.contains('is-visible')) {
                        revealObserver.observe(node);
                    }
                    // Also check descendants
                    node.querySelectorAll && node.querySelectorAll('.vab-reveal:not(.is-visible)').forEach(function(child) {
                        revealObserver.observe(child);
                    });
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        mutationObserver.observe(document.body, { childList: true, subtree: true });
    });
}());