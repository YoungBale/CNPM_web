<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Tìm kiếm - VAB';
    $trangHienTai = 'search';
    include 'dautrang.php';
}
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section"><div class="container"><h1>TÌM KIẾM</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item"><a href="index.php?page=products">Sản phẩm</a></li><li class="breadcrumb-item active">Tìm kiếm: "<?= htmlspecialchars($keyword) ?>"</li></ol></nav>
</div></section>

<!-- SEARCH RESULTS -->
<section class="pb-5"><div class="container">
    <div class="products-header">
        <span class="products-count">Tìm thấy <strong><?= $totalResults ?></strong> kết quả cho "<strong><?= htmlspecialchars($keyword) ?></strong>"</span>
        <a href="index.php?page=products" class="btn btn-outline-dark rounded-pill px-3" style="font-size:13px;"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
    </div>
    <?php if (!empty($products)): ?>
    <div class="row g-3">
        <?php foreach($products as $product): ?>
        <div class="col-lg-3 col-md-6 col-6 product-item" data-id="<?= (int)$product['MaSP'] ?>">
            <div class="product-card">
                <div class="product-image">
                    <img src="<?= ProductModel::getImageUrl($product) ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>">
                    <?php $productId = $product['MaSP']; include __DIR__ . '/partials/product-actions.php'; ?>
                </div>
                <div class="product-info">
                    <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
                    <a href="index.php?page=product-detail&id=<?= (int)$product['MaSP'] ?>" class="product-name"><?= htmlspecialchars($product['TenSP']) ?></a>
                    <div class="product-price">
                        <?php if (!empty($product['GiaKhuyenMai']) && $product['GiaKhuyenMai'] > 0): ?>
                        <span class="current-price"><?= number_format($product['GiaKhuyenMai'],0,',',',') ?>đ</span>
                        <span class="old-price"><?= number_format($product['Gia'],0,',',',') ?>đ</span>
                        <?php else: ?>
                        <span class="current-price"><?= number_format($product['Gia'],0,',',',') ?>đ</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-search fa-4x text-muted mb-4"></i>
        <h4 class="fw-bold">Không tìm thấy sản phẩm</h4>
        <p class="text-muted">Không có kết quả nào cho "<strong><?= htmlspecialchars($keyword) ?></strong>"</p>
        <p class="text-muted">Vui lòng thử lại với từ khóa khác.</p>
        <a href="index.php?page=products" class="btn btn-secondary rounded-pill px-4 mt-3" style="background:var(--color-secondary);border:none;">Xem tất cả sản phẩm</a>
    </div>
    <?php endif; ?>
</div></section>
<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>