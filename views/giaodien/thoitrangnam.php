<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Thời trang nam - VAB';
    $trangHienTai = 'men';
    include 'dautrang.php';
}

if (!class_exists('ProductModel')) {
    require './models/ProductModel.php';
}
$productModel = new ProductModel();
$products = $productModel->getByGender('Nam');

// Lọc sản phẩm theo danh mục nhỏ để hiển thị dạng nhóm
$aoNam = array_filter($products, function($p) { return $p['category'] === 'Áo'; });
$quanNam = array_filter($products, function($p) { return $p['category'] === 'Quần'; });
$giayNam = array_filter($products, function($p) { return $p['category'] === 'Giày'; });
$phuKienNam = array_filter($products, function($p) { return $p['category'] === 'Phụ kiện'; });

$categories = [
    ['Áo thể thao nam', $aoNam],
    ['Quần thể thao nam', $quanNam],
    ['Giày nam', $giayNam],
    ['Phụ kiện nam', $phuKienNam],
];
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section vab-reveal fade-in"><div class="container"><h1>THỜI TRANG NAM</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item active">Thời trang nam</li></ol></nav>
</div></section>

<!-- Category Banner -->
<section class="container"><div class="category-banner men-banner vab-reveal fade-up">
    <div class="banner-content">
        <h1>THỜI TRANG THỂ THAO<br>NAM</h1>
        <p>Phong cách mạnh mẽ, năng động cho phái mạnh. Từ phòng gym đến sân cỏ, VAB mang đến cho bạn những trang phục thể thao đỉnh cao.</p>
        <a href="index.php?page=products" class="btn btn-category">Khám phá ngay</a>
    </div>
</div></section>

<section class="pb-5"><div class="container">
    <?php foreach($categories as $cat): ?>
    <div class="category-group vab-reveal fade-up">
        <h3 class="category-title"><?= $cat[0] ?></h3>
        <div class="product-slider-wrap">
            <button type="button" class="product-slider-arrow product-slider-prev" aria-label="Lùi">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="product-slider-viewport">
                <div class="product-slider-track">
                    <?php if (!empty($cat[1])): foreach($cat[1] as $product): ?>
                        <div class="product-slider-item">
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
                    <?php endforeach; else: ?>
                        <div style="width:100%;text-align:center;padding:48px 0;color:var(--color-gray);">
                            <i class="fas fa-box-open" style="font-size:40px;opacity:.4;margin-bottom:12px;display:block;"></i>
                            Chưa có sản phẩm nào thuộc nhóm này.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <button type="button" class="product-slider-arrow product-slider-next" aria-label="Tiến">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div></section>
<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>