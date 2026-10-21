<?php
if (!class_exists('ProductModel')) {
    include './models/ProductModel.php';
}
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Thanh toán - VAB';
    $trangHienTai = 'checkout';
    include 'dautrang.php';
}
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section"><div class="container"><h1>THANH TOÁN</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item"><a href="index.php?page=cart">Giỏ hàng</a></li><li class="breadcrumb-item active">Thanh toán</li></ol></nav>
</div></section>

<section class="pb-5"><div class="container"><div class="row">
    <div class="col-lg-8">
        <div class="admin-table-wrapper p-4">
            <h5 class="fw-bold mb-4">Thông tin khách hàng</h5>
            <form id="checkoutForm" method="POST" action="index.php?page=checkout">
                <div class="row">
                    <div class="col-md-6"><div class="form-group"><label>Họ tên</label><input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($currentUser['HoTenKH'] ?? '') ?>" required></div></div>
                    <div class="col-md-6"><div class="form-group"><label>Số điện thoại</label><input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($currentUser['SDT'] ?? '') ?>" required></div></div>
                </div>
                <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>" required></div>
                <div class="form-group"><label>Địa chỉ nhận hàng</label><input type="text" name="address" class="form-control" placeholder="Số nhà, đường, phường, quận, thành phố" value="<?= htmlspecialchars($currentUser['DiaChi'] ?? '') ?>" required></div>
                <div class="form-group"><label>Ghi chú đơn hàng</label><textarea name="note" class="form-control" rows="3"></textarea></div>
                <h5 class="fw-bold mt-4 mb-3">Phương thức thanh toán</h5>
                <div class="payment-methods">
                    <div class="payment-option active">
                        <input type="radio" name="payment_method" value="COD" checked><label>Thanh toán khi nhận hàng (COD)</label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="sepay"><label><i class="fas fa-qrcode text-warning me-2" style="color: #f25c19 !important;"></i>Chuyển khoản VietQR tự động (SePay)</label>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="order-summary">
            <h5>Tóm tắt đơn hàng</h5>
            <?php
            $cartItems = $_SESSION['giohang'] ?? [];
            $total = 0;
            if (empty($cartItems)):
            ?>
            <p class="text-muted py-3">Không có sản phẩm nào trong giỏ hàng.</p>
            <?php else: ?>
            <?php foreach($cartItems as $it): 
                $sub = $it['DonGia'] * $it['SoLuongSP']; 
                $total += $sub; 
            ?>
            <div class="summary-item">
                <img src="<?= ProductModel::getImageUrl($it, 60, 60) ?>" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:8px;margin-right:12px;">
                <div>
                    <p class="fw-bold mb-1" style="font-size:14px;"><?= htmlspecialchars($it['TenSP']) ?></p>
                    <small class="text-muted">
                        SL: <?= $it['SoLuongSP'] ?> x <?= number_format($it['DonGia'],0,',',',') ?>đ
                        <?php if (!empty($it['KichCo'])): ?>
                            | Size: <?= htmlspecialchars($it['KichCo']) ?>
                        <?php endif; ?>
                    </small>
                </div>
            </div>
            <?php endforeach; ?>
            <hr>
            <!-- Discount code section -->
            <div class="coupon-section mb-3">
                <label class="form-label fw-bold mb-2">Mã giảm giá</label>
                <div class="input-group">
                    <input type="text" id="couponCodeInput" class="form-control" placeholder="Nhập mã giảm giá..." style="background: #1e1e1e; border: 1px solid #333; color: #fff; border-radius: 8px 0 0 8px;">
                    <button type="button" id="applyCouponBtn" class="btn btn-primary" style="background: var(--color-primary); border: none; border-radius: 0 8px 8px 0;">Áp dụng</button>
                </div>
                <div id="couponMessage" class="mt-2 small" style="display: none;"></div>
            </div>

            <!-- Applied coupon info (hidden by default if none is in session) -->
            <?php 
            $appliedCoupon = $_SESSION['applied_coupon'] ?? null;
            $discountAmount = 0;
            if ($appliedCoupon) {
                $discountAmount = $appliedCoupon['discount'];
            }
            ?>
            <div id="appliedCouponInfo" class="mb-3" style="<?= $appliedCoupon ? '' : 'display: none;' ?>">
                <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: rgba(242, 92, 25, 0.1); border: 1px dashed #f37021;">
                    <div>
                        <span class="fw-bold text-warning" id="appliedCouponCode"><?= $appliedCoupon ? htmlspecialchars($appliedCoupon['code']) : '' ?></span>
                        <small class="text-muted d-block" style="font-size:11px;">Đã áp dụng thành công</small>
                    </div>
                    <button type="button" id="removeCouponBtn" class="btn btn-sm btn-link text-danger p-0" style="text-decoration: none;"><i class="fas fa-trash-alt"></i> Hủy</button>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-between"><span>Tạm tính:</span><span class="fw-bold"><?= number_format($total,0,',',',') ?>đ</span></div>
            <div class="d-flex justify-content-between" id="discountRow" style="<?= $discountAmount > 0 ? '' : 'display: none;' ?>">
                <span>Giảm giá:</span>
                <span class="fw-bold text-warning" id="checkoutDiscount">-<?= number_format($discountAmount,0,',',',') ?>đ</span>
            </div>
            <div class="d-flex justify-content-between"><span>Phí vận chuyển:</span><span class="text-success fw-bold">Miễn phí</span></div>
            <hr>
            <div class="d-flex justify-content-between fs-5"><span class="fw-bold">Tổng cộng:</span><span class="fw-bold text-danger" id="checkoutTotal"><?= number_format($total - $discountAmount,0,',',',') ?>đ</span></div>
            <button type="submit" form="checkoutForm" class="btn btn-secondary w-100 mt-3 rounded-pill py-3 fw-bold" style="background:var(--color-secondary);border:none;">Đặt hàng</button>
            <?php endif; ?>
        </div>
    </div>
</div></div></section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const couponInput = document.getElementById('couponCodeInput');
    const applyBtn = document.getElementById('applyCouponBtn');
    const removeBtn = document.getElementById('removeCouponBtn');
    const couponMsg = document.getElementById('couponMessage');
    const appliedInfo = document.getElementById('appliedCouponInfo');
    const appliedCodeLabel = document.getElementById('appliedCouponCode');
    const discountRow = document.getElementById('discountRow');
    const discountVal = document.getElementById('checkoutDiscount');
    const totalVal = document.getElementById('checkoutTotal');

    function showMessage(msg, isError = false) {
        couponMsg.textContent = msg;
        couponMsg.style.color = isError ? '#ff4d4d' : '#28a745';
        couponMsg.style.display = 'block';
    }

    function clearMessage() {
        couponMsg.textContent = '';
        couponMsg.style.display = 'none';
    }

    applyBtn.addEventListener('click', function() {
        const code = couponInput.value.trim();
        if (!code) {
            showMessage('Vui lòng nhập mã giảm giá.', true);
            return;
        }

        clearMessage();
        applyBtn.disabled = true;

        fetch('index.php?page=apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'code=' + encodeURIComponent(code)
        })
        .then(response => response.json())
        .then(data => {
            applyBtn.disabled = false;
            
            if (data.status === 'success') {
                showMessage(data.message, false);
                couponInput.value = '';
                
                // Show applied info
                appliedCodeLabel.textContent = data.code;
                appliedInfo.style.display = 'block';
                
                // Show discount row
                discountVal.textContent = '-' + data.discountFormatted;
                discountRow.style.display = 'flex';
                
                // Update total
                totalVal.textContent = data.newTotalFormatted;
            } else {
                showMessage(data.message, true);
            }
        })
        .catch(err => {
            applyBtn.disabled = false;
            showMessage('Đã xảy ra lỗi khi áp dụng mã giảm giá. Vui lòng thử lại.', true);
        });
    });

    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            clearMessage();
            removeBtn.disabled = true;

            fetch('index.php?page=remove-coupon')
            .then(response => response.json())
            .then(data => {
                removeBtn.disabled = false;
                
                if (data.status === 'success') {
                    showMessage(data.message, false);
                    
                    // Hide applied info
                    appliedInfo.style.display = 'none';
                    appliedCodeLabel.textContent = '';
                    
                    // Hide discount row
                    discountRow.style.display = 'none';
                    discountVal.textContent = '-0đ';
                    
                    // Reset total
                    totalVal.textContent = data.newTotalFormatted;
                } else {
                    showMessage(data.message, true);
                }
            })
            .catch(err => {
                removeBtn.disabled = false;
                showMessage('Đã xảy ra lỗi khi hủy áp dụng mã. Vui lòng thử lại.', true);
            });
        });
    }
});
</script>
<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>