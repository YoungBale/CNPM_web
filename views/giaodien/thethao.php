<?php
/**
 * =================================================================================
 * VIEW TRANG BỘ MÔN THỂ THAO ĐỘNG (views/giaodien/thethao.php)
 * =================================================================================
 * TRANG NÀY HIỂN THỊ DỮ LIỆU ĐỘNG CHO CÁC BỘ MÔN THỂ THAO (Chạy bộ, Gym, Bóng đá, Bóng rổ...).
 * 
 * CƠ CHẾ KẾT XUẤT DỮ LIỆU ĐỘNG:
 * 1. Nhận tham số `sport_slug` (Ví dụ: sport-running, sport-football) qua $_GET.
 * 2. Thực hiện truy vấn trong bảng THETHAO để lấy thông tin chi tiết (Tên bộ môn, Mô tả).
 * 3. Lấy tất cả sản phẩm thuộc bộ môn đó từ CSDL thông qua `$productModel->getBySport()`.
 * 4. Sử dụng hàm `array_filter()` để phân loại sản phẩm thành các nhóm nhỏ: Áo, Quần, Giày và Phụ kiện.
 * 5. Ánh xạ từ viết tắt sang ảnh banner tương ứng (ví dụ: running -> banner2.png, football -> banner-dabanh.png).
 * 6. Kiểm tra sự tồn tại của file banner cục bộ. Nếu không có banner, dùng background gradient mặc định.
 * 7. Render từng nhóm sản phẩm thành các thanh trượt Slider độc lập.
 */

if (!isset($nhungTuController)) {
    header('Location: index.php');
    exit;
}

// 1. LẤY THÔNG TIN BỘ MÔN THỂ THAO TỪ DATABASE
$sportSlug = $_GET['sport_slug'] ?? '';
$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM THETHAO WHERE DuongDan = ? AND TrangThai = 1");
$stmt->execute([$sportSlug]);
$sportInfo = $stmt->fetch();

if (!$sportInfo) {
    echo '<section class="py-5"><div class="container text-center"><h2>Không tìm thấy bộ môn thể thao hoặc bộ môn này đã bị ẩn</h2><a href="index.php" class="btn btn-secondary rounded-pill mt-3 px-4" style="background:var(--color-secondary);border:none;">Quay lại trang chủ</a></div></section>';
    return;
}

$sportName = $sportInfo['TenSport'];
// Lấy danh sách sản phẩm thuộc bộ môn thể thao này
$products = $productModel->getBySport($sportName);

// 2. PHÂN LOẠI SẢN PHẨM THEO DANH MỤC NHỎ (Dùng hàm array_filter của PHP)
$ao = array_filter($products, function($p) { return $p['category'] === 'Áo'; });
$quan = array_filter($products, function($p) { return $p['category'] === 'Quần'; });
$giay = array_filter($products, function($p) { return $p['category'] === 'Giày'; });
$phukien = array_filter($products, function($p) { return $p['category'] === 'Phụ kiện'; });

// Mảng cấu hình các nhóm danh mục để lặp hiển thị
$categories = [
    ['Áo ' . strtolower($sportName), $ao],
    ['Quần ' . strtolower($sportName), $quan],
    ['Giày ' . strtolower($sportName), $giay],
    ['Phụ kiện ' . strtolower($sportName), $phukien],
];
?>
<!-- ĐƯỜNG DẪN ĐIỀU HƯỚNG (BREADCRUMB) -->
<section class="breadcrumb-section vab-reveal fade-in"><div class="container"><h1><?= strtoupper($sportName) ?></h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item"><a href="#">Thể thao</a></li><li class="breadcrumb-item active"><?= htmlspecialchars($sportName) ?></li></ol></nav>
</div></section>

<!-- 3. BANNER THỂ THAO ĐỘNG THEO BỘ MÔN -->
<?php
// Cắt bỏ tiền tố 'sport-' ở slug để lấy từ khóa gốc (ví dụ: 'sport-running' cắt thành 'running')
$shortSlug = strpos($sportSlug, 'sport-') === 0 ? substr($sportSlug, 6) : $sportSlug;

// Bảng ánh xạ từ khóa sang tệp ảnh banner tương ứng trong thư mục assets
$bannerMap = [
    'running' => 'banner2.png',
    'gym' => 'banner-gym.png',
    'football' => 'banner-dabanh.png',
    'basketball' => 'banner-bongro.png',
    'tennis' => 'banner-tennis.png',
];
$bannerImage = isset($bannerMap[$shortSlug]) ? $bannerMap[$shortSlug] : '';
$bannerPath = $bannerImage ? 'assets/images/banners/' . $bannerImage : '';
?>
<section class="container">
    <div class="category-banner vab-reveal fade-up" style="<?php if ($bannerPath && file_exists($bannerPath)): ?>background-image: url('<?= $bannerPath ?>'); background-size: cover; background-position: center;<?php else: ?>background:linear-gradient(135deg,#0a1628,#1a4a6e);<?php endif; ?>">
        <div class="banner-content">
            <h1>THỜI TRANG <?= strtoupper($sportName) ?></h1>
            <p>Trang phục và phụ kiện chuyên dụng cho bộ môn <?= htmlspecialchars($sportName) ?>. Hỗ trợ vận động tối ưu.</p>
            <a href="index.php?page=products" class="btn btn-category">Xem tất cả</a>
        </div>
    </div>
</section>

<!-- 4. DANH SÁCH CÁC HÀNG SLIDER SẢN PHẨM PHÂN NHÓM -->
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
                                    <span class="product-badge badge-new"><?= htmlspecialchars($sportName) ?></span>
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
