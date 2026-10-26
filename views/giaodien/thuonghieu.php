<?php
/**
 * =================================================================================
 * VIEW TRANG THƯƠNG HIỆU ĐỘNG (views/giaodien/thuonghieu.php)
 * =================================================================================
 * TRANG NÀY HIỂN THỊ DỮ LIỆU ĐỘNG CHO CÁC THƯƠNG HIỆU (Nike, Adidas, Puma...).
 * 
 * CƠ CHẾ KẾT XUẤT DỮ LIỆU ĐỘNG:
 * 1. Nhận tham số `brand_slug` (Ví dụ: nike, adidas, puma) từ URL thông qua $_GET.
 * 2. Thực hiện truy vấn trong bảng THUONGHIEU để lấy thông tin chi tiết (Tên thương hiệu, Mô tả/Slogan).
 * 3. Lấy tất cả sản phẩm thuộc thương hiệu đó từ CSDL thông qua `$productModel->getByBrand()`.
 * 4. Sử dụng hàm `array_filter()` để phân loại sản phẩm thành 4 nhóm nhỏ: Giày, Áo, Quần và Phụ kiện.
 * 5. Tự động kiểm tra file ảnh banner và logo cục bộ thông qua hàm `file_exists()`. Nếu không tìm thấy,
 *    hệ thống tự động sử dụng hình ảnh giả lập (Placeholder) hoặc gradient màu mặc định.
 * 6. Render từng nhóm sản phẩm thành các thanh trượt Slider độc lập.
 */

if (!isset($nhungTuController)) {
    header('Location: index.php');
    exit;
}

// 1. LẤY THÔNG TIN THƯƠNG HIỆU TỪ DATABASE
$brandSlug = $_GET['brand_slug'] ?? '';
$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM THUONGHIEU WHERE DuongDan = ? AND TrangThai = 1");
$stmt->execute([$brandSlug]);
$brandInfo = $stmt->fetch();

if (!$brandInfo) {
    echo '<section class="py-5"><div class="container text-center"><h2>Không tìm thấy thương hiệu hoặc thương hiệu đã bị ẩn</h2><a href="index.php" class="btn btn-secondary rounded-pill mt-3 px-4" style="background:var(--color-secondary);border:none;">Quay lại trang chủ</a></div></section>';
    return;
}

$brandName = $brandInfo['TenThuongHieu'];
// Lấy danh sách sản phẩm thuộc thương hiệu này
$products = $productModel->getByBrand($brandName);

// 2. PHÂN LOẠI SẢN PHẨM THEO DANH MỤC NHỎ (Dùng hàm array_filter của PHP)
$giay = array_filter($products, function($p) { return $p['category'] === 'Giày'; });
$ao = array_filter($products, function($p) { return $p['category'] === 'Áo'; });
$quan = array_filter($products, function($p) { return $p['category'] === 'Quần'; });
$phukien = array_filter($products, function($p) { return $p['category'] === 'Phụ kiện'; });

// Mảng cấu hình các nhóm danh mục để lặp hiển thị
$categories = [
    ['Giày ' . $brandName, $giay],
    ['Áo ' . $brandName, $ao],
    ['Quần ' . $brandName, $quan],
    ['Phụ kiện ' . $brandName, $phukien],
];
?>
<!-- ĐƯỜNG DẪN ĐIỀU HƯỚNG (BREADCRUMB) -->
<section class="breadcrumb-section vab-reveal fade-in"><div class="container"><h1><?= strtoupper($brandName) ?></h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item text-muted">Thương hiệu</li><li class="breadcrumb-item active"><?= htmlspecialchars($brandName) ?></li></ol></nav>
</div></section>

<!-- 3. BRAND HERO BANNER ĐỘNG -->
<?php
// Kiểm tra tệp ảnh banner của thương hiệu có tồn tại không
$bannerPath = 'assets/images/banners/banner-' . strtolower($brandSlug) . '.png';
if (!file_exists($bannerPath)) {
    $bannerPathJpg = 'assets/images/banners/banner-' . strtolower($brandSlug) . '.jpg';
    if (file_exists($bannerPathJpg)) {
        $bannerPath = $bannerPathJpg;
    } else {
        $bannerPath = ''; // Không có banner -> dùng background gradient mặc định ở style bên dưới
    }
}

// Kiểm tra tệp ảnh logo của thương hiệu có tồn tại không
$logoFile = 'assets/images/logo/logo-' . strtolower($brandSlug) . '.png';
if (!file_exists($logoFile)) {
    $logoFileJpg = 'assets/images/logo/logo-' . strtolower($brandSlug) . '.jpg';
    if (file_exists($logoFileJpg)) {
        $logoFile = $logoFileJpg;
    } else {
        $logoFile = "https://placehold.co/120x120/f5f0e8/0a1628?text=" . urlencode(strtoupper($brandName));
    }
}
?>
<section class="container">
    <div class="brand-hero vab-reveal fade-up" style="<?php if ($bannerPath): ?>background-image: url('<?= $bannerPath ?>'); background-size: cover; background-position: center;<?php else: ?>background:linear-gradient(135deg,#0a1628,#2d4b3c);<?php endif; ?>">
        <div class="brand-hero-content">
            <img src="<?= $logoFile ?>" alt="<?= htmlspecialchars($brandName) ?> Logo" class="brand-hero-logo">
            <h1><?= htmlspecialchars($brandName) ?></h1>
            <p class="brand-hero-desc"><?= htmlspecialchars($brandInfo['MoTa'] ?? '') ?></p>
        </div>
    </div>
</section>

<!-- 4. DANH SÁCH CÁC HÀNG SLIDER SẢN PHẨM PHÂN NHÓM -->
<section class="pb-5"><div class="container">
    <div class="section-title vab-reveal fade-right"><h2>SẢN PHẨM <?= strtoupper($brandName) ?></h2><p>Khám phá bộ sưu tập <?= htmlspecialchars($brandName) ?> mới nhất tại VAB</p></div>
    
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
                                    <span class="product-badge badge-new"><?= htmlspecialchars($brandName) ?></span>
                                    <img src="<?= ProductModel::getImageUrl($product) ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>">
                                    <?php $productId = $product['MaSP']; include __DIR__ . '/partials/product-actions.php'; ?>
                                </div>
                                <div class="product-info">
                                    <div class="product-category"><?= htmlspecialchars($brandName) ?></div>
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
