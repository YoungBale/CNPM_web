<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Sale - VAB';
    $trangHienTai = 'sale';
    include 'dautrang.php';
}
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section vab-reveal fade-in"><div class="container"><h1>KHUYẾN MÃI</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item active">Sale</li></ol></nav>
</div></section>

<section class="container">
    <div class="sale-banner vab-reveal fade-up" style="background: linear-gradient(rgba(17, 24, 39, 0.4), rgba(17, 24, 39, 0.4)), url('assets/images/banners/bannergiamgia.png') no-repeat center center; background-size: cover; padding: 50px 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); border-radius: 16px;">
        <div class="sale-banner-card">
            <h2>Đại Tiệc Khuyến Mãi - VAB Sport</h2>
            <p>
                Sở hữu các dòng sản phẩm thời trang thể thao, giày chạy bộ và phụ kiện chính hãng chất lượng cao từ các thương hiệu hàng đầu với ưu đãi giảm giá tốt nhất. Cơ hội vàng nâng tầm phong cách tập luyện của bạn!
            </p>
        </div>
    </div>
</section>

<section class="pb-5"><div class="container">
    <div class="section-title d-flex justify-content-between align-items-center flex-wrap vab-reveal fade-right" style="gap: 15px; margin-bottom: 30px;">
        <h2 style="margin-bottom: 0;">SẢN PHẨM ĐANG GIẢM GIÁ</h2>
        <div class="d-flex align-items-center">
            <label class="me-2 mb-0" style="font-size:14px; white-space: nowrap;">Sắp xếp:</label>
            <select class="sort-select-sale form-select form-select-sm" style="width: auto; border-radius: 20px; padding: 5px 30px 5px 15px; font-size: 14px; border: 1px solid #dee2e6; cursor: pointer; outline: none;">
                <option value="default" <?= ($currentSort ?? 'default') === 'default' ? 'selected' : '' ?>>Mặc định</option>
                <option value="price_asc" <?= ($currentSort ?? 'default') === 'price_asc' ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                <option value="price_desc" <?= ($currentSort ?? 'default') === 'price_desc' ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
            </select>
        </div>
    </div>
    <div class="row g-4">
        <?php if (!empty($products)): ?>
            <?php 
            $productDelay = 1;
            foreach($products as $product): 
                $productId = (int)$product['MaSP'];
                $discountPercent = 0;
                if ($product['Gia'] > 0 && !empty($product['GiaKhuyenMai'])) {
                    $discountPercent = round((($product['Gia'] - $product['GiaKhuyenMai']) / $product['Gia']) * 100);
                }
                $imageUrl = ProductModel::getImageUrl($product);
                
                // Stagger delay from 1 to 4
                $delayAttr = $productDelay <= 4 ? ' data-delay="' . $productDelay . '"' : ' data-delay="4"';
                $productDelay++;
                if ($productDelay > 4) $productDelay = 1; // Reset loop delay for the next row
            ?>
            <div class="col-lg-3 col-md-6 col-6 vab-reveal fade-up"<?= $delayAttr ?>>
                <div class="product-card">
                    <div class="product-image">
                        <?php if ($discountPercent > 0): ?>
                            <span class="product-badge badge-sale">-<?= $discountPercent ?>%</span>
                        <?php endif; ?>
                        <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>">
                        <?php include __DIR__ . '/partials/product-actions.php'; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?= htmlspecialchars($product['category'] ?? 'Sale') ?></div>
                        <a href="index.php?page=product-detail&id=<?= $productId ?>" class="product-name"><?= htmlspecialchars($product['TenSP']) ?></a>
                        <div class="product-price">
                            <?php if (!empty($product['GiaKhuyenMai']) && $product['GiaKhuyenMai'] > 0): ?>
                                <span class="current-price"><?= number_format($product['GiaKhuyenMai'],0,',',',') ?>đ</span>
                                <span class="old-price"><?= number_format($product['Gia'],0,',',',') ?>đ</span>
                                <span class="discount">-<?= $discountPercent ?>%</span>
                            <?php else: ?>
                                <span class="current-price"><?= number_format($product['Gia'],0,',',',') ?>đ</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if ($totalPages > 1): ?>
            <div class="pagination-wrapper d-flex justify-content-center mt-5 mb-3 w-100 vab-reveal fade-in">
                <nav aria-label="Page navigation">
                    <ul class="pagination align-items-center" style="gap: 8px; list-style: none; display: flex; padding: 0;">
                        <!-- Nút Quay lại -->
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <?php if ($currentPage <= 1): ?>
                                <span class="page-link" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid #dee2e6; color: #ccc; cursor: not-allowed; background: #f8f9fa;">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            <?php else: ?>
                                <a class="page-link" href="index.php?page=sale&sort=<?= urlencode($currentSort) ?>&p=<?= $currentPage - 1 ?>" aria-label="Previous" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid #dee2e6; color: var(--color-primary); background: #fff; text-decoration: none !important;">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                        </li>
                        
                        <!-- Các trang số -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i === $currentPage) ? 'active' : '' ?>">
                                <?php if ($i === $currentPage): ?>
                                    <span class="page-link" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: var(--color-secondary); border-color: var(--color-secondary); color: #fff; font-weight: bold; box-shadow: var(--shadow-btn); text-decoration: none !important;"><?= $i ?></span>
                                <?php else: ?>
                                    <a class="page-link" href="index.php?page=sale&sort=<?= urlencode($currentSort) ?>&p=<?= $i ?>" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid #dee2e6; color: var(--color-primary); background: #fff; text-decoration: none !important;"><?= $i ?></a>
                                <?php endif; ?>
                            </li>
                        <?php endfor; ?>
                        
                        <!-- Nút Tiếp theo -->
                        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                            <?php if ($currentPage >= $totalPages): ?>
                                <span class="page-link" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid #dee2e6; color: #ccc; cursor: not-allowed; background: #f8f9fa;">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            <?php else: ?>
                                <a class="page-link" href="index.php?page=sale&sort=<?= urlencode($currentSort) ?>&p=<?= $currentPage + 1 ?>" aria-label="Next" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid #dee2e6; color: var(--color-primary); background: #fff; text-decoration: none !important;">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-5 w-100">
                <p class="text-muted">Hiện tại chưa có chương trình khuyến mãi nào.</p>
            </div>
        <?php endif; ?>
    </div>
</div></section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectEl = document.querySelector('.sort-select-sale');
    if (selectEl) {
        selectEl.addEventListener('change', function() {
            window.location.href = 'index.php?page=sale&sort=' + this.value;
        });
    }
});
</script>

<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>