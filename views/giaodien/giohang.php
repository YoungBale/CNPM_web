<?php
if (!class_exists('ProductModel')) {
    include './models/ProductModel.php';
}
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Giỏ hàng - VAB';
    $trangHienTai = 'cart';
    include 'dautrang.php';
}
$cartItems = $cartItems ?? ($_SESSION['giohang'] ?? []);
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['DonGia'] * $item['SoLuongSP'];
}
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section"><div class="container"><h1>GIỎ HÀNG</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item active">Giỏ hàng</li></ol></nav>
</div></section>

<section class="pb-5"><div class="container"><div class="row">
    <div class="col-lg-8">
        <?php if (!empty($cartItems)): ?>
        <div class="admin-table-wrapper">
            <table class="cart-table">
                <thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Giá</th><th>Tổng</th><th></th></tr></thead>
                <tbody>
                    <?php foreach($cartItems as $key => $item):
                        $rowTotal = $item['DonGia'] * $item['SoLuongSP'];
                    ?>
                    <tr data-id="<?= htmlspecialchars($key) ?>">
                        <td data-label="Sản phẩm">
                            <div class="cart-product">
                                <img src="<?= ProductModel::getImageUrl($item, 80, 80) ?>" alt="<?= htmlspecialchars($item['TenSP']) ?>">
                                <div>
                                    <span class="product-name"><?= htmlspecialchars($item['TenSP']) ?></span>
                                    <?php if (!empty($item['KichCo'])): ?>
                                        <br><small class="text-muted">Size: <?= htmlspecialchars($item['KichCo']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td data-label="Số lượng">
                            <div class="qty-control-sm">
                                <button class="qty-minus">-</button>
                                <input type="text" value="<?= (int)$item['SoLuongSP'] ?>" readonly>
                                <button class="qty-plus">+</button>
                            </div>
                        </td>
                        <td data-label="Giá"><span class="cart-price"><?= number_format($item['DonGia'],0,',',',') ?>đ</span></td>
                        <td data-label="Tổng"><span class="cart-row-total"><?= number_format($rowTotal,0,',',',') ?>đ</span></td>
                        <td><button class="btn-remove" title="Xóa"><i class="fas fa-times"></i></button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
            <p class="text-muted">Giỏ hàng của bạn đang trống.</p>
            <a href="index.php?page=products" class="btn btn-secondary rounded-pill px-4 mt-2" style="background:var(--color-secondary);border:none;">Tiếp tục mua hàng</a>
        </div>
        <?php endif; ?>
        <?php if (!empty($cartItems)): ?>
        <div class="mt-3 d-flex gap-2">
            <a href="index.php?page=products" class="btn btn-outline-dark rounded-pill px-4"><i class="fas fa-arrow-left me-2"></i>Tiếp tục mua hàng</a>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="cart-total">
            <h4>Tổng thanh toán</h4>
            <div class="total-row"><span>Tạm tính</span><span id="cart-subtotal"><?= number_format($subtotal,0,',',',') ?>đ</span></div>
            <div class="total-row"><span>Phí vận chuyển</span><span>Miễn phí</span></div>
            <hr>
            <div class="total-row total"><span>Tổng cộng</span><span id="cart-total"><?= number_format($subtotal,0,',',',') ?>đ</span></div>
            <?php if (!empty($cartItems)): ?>
            <a href="index.php?page=checkout" class="btn btn-secondary w-100 mt-3 rounded-pill py-3 fw-bold" style="background:var(--color-secondary);border:none;">Tiến hành thanh toán</a>
            <?php endif; ?>
        </div>
    </div>
</div></div></section>
<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>
