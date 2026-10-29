/* ==========================================
   VAB - ADMIN JAVASCRIPT
   ========================================== */

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ===== SIDEBAR TOGGLE (Mobile) =====
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose  = document.getElementById('sidebarClose');
    const sidebar       = document.getElementById('adminSidebar');
    const overlay       = document.getElementById('sidebarOverlay');

    function openSidebar() {
        if (sidebar)  sidebar.classList.add('show');
        if (overlay)  overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (sidebar)  sidebar.classList.remove('show');
        if (overlay)  overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            if (sidebar.classList.contains('show')) closeSidebar();
            else openSidebar();
        });
    }

    if (sidebarClose)  sidebarClose.addEventListener('click', closeSidebar);
    if (overlay)       overlay.addEventListener('click', closeSidebar);

    // ===== ADMIN PROFILE DROPDOWN =====
    const adminProfile = document.querySelector('.admin-profile');
    const profileDropdown = document.querySelector('.profile-dropdown');

    if (adminProfile && profileDropdown) {
        adminProfile.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
        });
    }

    // ===== CONFIRM DELETE MODAL =====
    let currentDeleteCallback = null;

    window.showConfirmModal = function(message, callback) {
        const overlay = document.getElementById('confirmOverlay');
        const messageEl = document.getElementById('confirmMessage');
        const yesBtn = document.getElementById('confirmYes');
        const noBtn = document.getElementById('confirmNo');

        if (!overlay || !messageEl || !yesBtn || !noBtn) return;

        messageEl.textContent = message || 'Bạn có chắc chắn muốn xóa?';
        overlay.classList.add('show');
        currentDeleteCallback = callback;

        yesBtn.onclick = function() {
            if (typeof currentDeleteCallback === 'function') {
                currentDeleteCallback();
            }
            overlay.classList.remove('show');
            showToast('Đã xóa thành công!', 'success');
        };

        noBtn.onclick = function() {
            overlay.classList.remove('show');
            currentDeleteCallback = null;
        };

        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                overlay.classList.remove('show');
                currentDeleteCallback = null;
            }
        });
    };

    // ===== DELETE BUTTONS =====
    const deleteButtons = document.querySelectorAll('.btn-delete[data-confirm]');
    deleteButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const message = this.getAttribute('data-confirm') || 'Bạn có chắc chắn muốn xóa?';
            showConfirmModal(message, function() {
                const row = btn.closest('tr');
                if (row) {
                    row.remove();
                }
                // Update count if exists
                updateRowNumbers();
            });
        });
    });

    // ===== TOAST NOTIFICATION =====
    window.showToast = function(message, type) {
        const toast = document.getElementById('toastNotification');
        if (!toast) return;

        toast.textContent = message || 'Thành công!';
        toast.className = 'toast-notification';
        if (type === 'error') {
            toast.classList.add('error');
        }
        toast.classList.add('show');

        setTimeout(function() {
            toast.classList.remove('show');
        }, 3000);
    };

    // ===== IMAGE PREVIEW =====
    const imageInputs = document.querySelectorAll('input[type="file"][data-preview]');
    imageInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const previewId = this.getAttribute('data-preview');
            const preview = document.getElementById(previewId);
            if (!preview) return;

            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // ===== ORDER STATUS CHANGE =====
    const statusSelects = document.querySelectorAll('.order-status-select');
    statusSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            const newStatus = this.options[this.selectedIndex].text;
            const orderId = this.getAttribute('data-order-id') || '';
            showToast('Đơn hàng ' + orderId + ' đã chuyển sang trạng thái: ' + newStatus, 'success');
        });
    });

    // ===== FORM VALIDATION =====
    const adminForms = document.querySelectorAll('.admin-form-wrapper form');
    adminForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const requiredInputs = this.querySelectorAll('[required]');

            requiredInputs.forEach(function(input) {
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
            });

            if (!valid) {
                e.preventDefault();
            } else {
                const formType = this.getAttribute('data-form-type') || '';
                if (formType === 'add') {
                    showToast('Thêm mới thành công!', 'success');
                } else if (formType === 'edit') {
                    showToast('Cập nhật thành công!', 'success');
                } else {
                    showToast('Lưu thành công!', 'success');
                }
            }
        });

        // Remove error on input
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(function(input) {
            input.addEventListener('input', function() {
                this.style.borderColor = '';
                const error = this.parentElement.querySelector('.error-message');
                if (error) error.remove();
            });
            input.addEventListener('change', function() {
                this.style.borderColor = '';
                const error = this.parentElement.querySelector('.error-message');
                if (error) error.remove();
            });
        });
    });

    // ===== UPDATE ROW NUMBERS =====
    function updateRowNumbers() {
        // This is a placeholder for re-indexing rows after deletion
    }

    // ===== SIDEBAR ACTIVE LINK =====
    const sidebarLinks = document.querySelectorAll('.sidebar-menu .menu-item');
    sidebarLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            // Allow normal navigation for actual links
            const href = this.getAttribute('href');
            if (href && href !== '#') {
                return true;
            }
        });
    });

    // ===== PRINT ORDER =====
    const printBtn = document.getElementById('printOrder');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            window.print();
        });
    }

    // ===== ADD CATEGORY / BRAND INLINE FORM =====
    const addCategoryBtn = document.getElementById('addCategoryBtn');
    const categoryForm = document.getElementById('categoryForm');
    if (addCategoryBtn && categoryForm) {
        addCategoryBtn.addEventListener('click', function() {
            categoryForm.style.display = categoryForm.style.display === 'none' ? 'block' : 'none';
        });
    }

    const addBrandBtn = document.getElementById('addBrandBtn');
    const brandForm = document.getElementById('brandForm');
    if (addBrandBtn && brandForm) {
        addBrandBtn.addEventListener('click', function() {
            brandForm.style.display = brandForm.style.display === 'none' ? 'block' : 'none';
        });
    }

    // ===== ADMIN LOGIN =====
    const loginForm = document.getElementById('adminLoginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const errorEl = document.getElementById('loginError');
            if (errorEl) {
                errorEl.classList.add('show');
                setTimeout(function() {
                    errorEl.classList.remove('show');
                }, 3000);
            }
        });
    }

    // ===== FILTER SELECT CHANGE =====
    const filterSelects = document.querySelectorAll('.table-actions .form-select');
    filterSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            // In a real app, this would filter the table
            // For static demo, just show toast
            showToast('Đã lọc theo: ' + this.options[this.selectedIndex].text, 'success');
        });
    });

    // ===== SEARCH INPUT =====
    const searchInputs = document.querySelectorAll('.table-actions input[type="search"]');
    searchInputs.forEach(function(input) {
        input.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                showToast('Đã tìm kiếm: ' + this.value, 'success');
            }
        });
    });

    // ===== MULTIPLE IMAGES PREVIEW =====
    const subImagesInput = document.getElementById('subImagesInput');
    const subImagesPreview = document.getElementById('subImagesPreview');
    if (subImagesInput && subImagesPreview) {
        subImagesInput.addEventListener('change', function() {
            subImagesPreview.innerHTML = '';
            const files = Array.from(this.files).slice(0, 3); // Giới hạn tối đa 3 ảnh chi tiết
            files.forEach(function(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgContainer = document.createElement('div');
                    imgContainer.style.cssText = 'position: relative; width: 80px; height: 80px; border-radius: 6px; overflow: hidden; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; background: #f8f9fa;';
                    imgContainer.innerHTML = '<img src="' + e.target.result + '" style="max-width: 100%; max-height: 100%; object-fit: cover;">';
                    subImagesPreview.appendChild(imgContainer);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});