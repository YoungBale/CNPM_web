<?php
/**
 * =================================================================================
 * VIEW TRANG CHỦ CHÍNH (views/giaodien/index.php)
 * =================================================================================
 * TRANG NÀY HIỂN THỊ GIAO DIỆN TRANG CHỦ CỦA WEBSITE VỚI BAO GỒM:
 * 1. Banner Slider Premium (Hero Carousel) quảng bá các dòng sản phẩm.
 * 2. Hàng sản phẩm mới nhất dạng Slider (vuốt trượt ngang mượt mà).
 * 3. Hàng sản phẩm thể thao ngẫu nhiên dạng Slider.
 * 4. Khu vực thương hiệu nổi bật (Nike, Adidas, Puma) với hiệu ứng Glassmorphism và Slogan tiếng Việt.
 * 
 * ĐIỂM CẦN TRẢ LỜI VẤN ĐÁP:
 * - Cách nhúng Header/Footer: Kiểm tra biến `$nhungTuController`. Nếu trang được chạy gián tiếp qua
 *   Front Controller index.php thì biến này đã được định nghĩa, ngược lại nếu mở trực tiếp file view
 *   thì hệ thống tự động gọi require/include dautrang.php và chantrang.php để giữ nguyên giao diện khung.
 * - Hiệu năng tải trang: Sử dụng thuộc tính `loading="lazy"` cho toàn bộ thẻ <img> giúp trình duyệt trì hoãn
 *   tải ảnh sản phẩm cho tới khi người dùng trượt màn hình tới đó, làm giảm đáng kể thời gian tải trang ban đầu.
 */

// Tự động include header nếu mở trực tiếp view (không đi qua Controller)
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'VAB - Thời Trang Thể Thao';
    $trangHienTai = 'home';
    include 'dautrang.php';
}
?>
<!-- ===== 1. HERO BANNER CAROUSEL — PREMIUM ===== -->
<section class="hero-carousel">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5500">

        <!-- Các nút chuyển slide bên dưới góc trái -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
        </div>

        <div class="carousel-inner">
            <!-- Slide 1 — Nike / Adidas / Puma -->
            <div class="carousel-item active">
                <div class="slide-accent"></div>
                <picture>
                    <img src="assets/images/banners/banner1.png" alt="Thương hiệu thể thao Nike Adidas Puma" class="banner-img-crop-1">
                </picture>
                <div class="carousel-caption">
                    <h2 class="banner-title">
                        PHONG CÁCH
                        <span>ĐỈNH CAO</span>
                    </h2>
                    <p class="banner-desc">Bộ sưu tập thể thao chính hãng từ Nike, Adidas, Puma — nơi hiệu suất gặp phong cách</p>
                    <div class="banner-actions">
                        <a href="index.php?page=products" class="btn btn-banner btn-banner-primary">
                            <i class="fas fa-shopping-bag"></i> Khám Phá Ngay
                        </a>
                    </div>
                </div>
            </div>

            <!-- Slide 2 — Nam giới -->
            <div class="carousel-item">
                <div class="slide-accent"></div>
                <picture>
                    <img src="assets/images/banners/banner2.png" alt="Thể thao nam" class="banner-img-crop-2">
                </picture>
                <div class="carousel-caption">
                    <h2 class="banner-title">
                        THỜI TRANG
                        <span>NAM GIỚI</span>
                    </h2>
                    <p class="banner-desc">Trang phục thể thao nam phong cách, chất lượng cao cho mọi hoạt động — từ gym đến đường phố</p>
                    <div class="banner-actions">
                        <a href="index.php?page=men" class="btn btn-banner btn-banner-primary">
                            <i class="fas fa-tshirt"></i> Khám Phá Ngay
                        </a>
                    </div>
                </div>
            </div>

            <!-- Slide 3 — Nữ giới -->
            <div class="carousel-item">
                <div class="slide-accent"></div>
                <picture>
                    <img src="assets/images/banners/banner3.png" alt="Thể thao nữ" class="banner-img-crop-3">
                </picture>
                <div class="carousel-caption">
                    <h2 class="banner-title">
                        PHONG CÁCH
                        <span>NỮ TỰ TIN</span>
                    </h2>
                    <p class="banner-desc">Năng động — quyến rũ — thời thượng. Bộ sưu tập thể thao nữ cho người phụ nữ hiện đại</p>
                    <div class="banner-actions">
                        <a href="index.php?page=women" class="btn btn-banner btn-banner-primary">
                            <i class="fas fa-heart"></i> Khám Phá Ngay
                        </a>
                    </div>
                </div>
            </div>

            <!-- Slide 4 — GYM Fitness -->
            <div class="carousel-item">
                <div class="slide-accent"></div>
                <picture>
                    <img src="assets/images/banners/banner4.png" alt="Thể thao GYM Fitness" class="banner-img-crop-4">
                </picture>
                <div class="carousel-caption">
                    <h2 class="banner-title">
                        THỂ THAO
                        <span>GYM FITNESS</span>
                    </h2>
                    <p class="banner-desc">Trang phục và phụ kiện gym cao cấp — hỗ trợ tối đa hiệu suất luyện tập và tôn vinh vóc dáng của bạn</p>
                    <div class="banner-actions">
                        <a href="index.php?page=products" class="btn btn-banner btn-banner-primary">
                            <i class="fas fa-running"></i> Khám Phá Ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Các mũi tên điều khiển banner qua lại -->
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Trước</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Tiếp</span>
        </button>
    </div>
</section>

<!-- ===== 2. KHU VỰC SẢN PHẨM MỚI NHẤT ===== -->
<section style="padding:72px 0;background:transparent;">
    <div class="container">
        <div class="section-title vab-reveal fade-right">
            <a href="index.php?page=products" class="section-title-link">
                <h2>SẢN PHẨM MỚI NHẤT</h2>
            </a>
            <p>Những mẫu mới nhất vừa cập bến — khám phá trước khi hết hàng</p>
            <a href="index.php?page=products" class="btn-view-all">
                Xem tất cả sản phẩm &nbsp;<i class="fas fa-arrow-right" style="font-size:11px;"></i>
            </a>
        </div>
        
        <!-- BỘ KHUNG SLIDER SẢN PHẨM -->
        <div class="product-slider-wrap vab-reveal fade-up">
            <!-- Nút di chuyển lùi (Trái) -->
            <button type="button" class="product-slider-arrow product-slider-prev" aria-label="Lùi">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="product-slider-viewport">
                <div class="product-slider-track">
                    <?php 
                    // Duyệt qua danh sách sản phẩm mới nhận được từ Controller gửi sang
                    if (isset($sanPhamMoi) && !empty($sanPhamMoi)): 
                        $delayIndex = 1;
                        foreach ($sanPhamMoi as $product): 
                            // Kiểm tra trạng thái tồn kho của sản phẩm (hết hàng/còn hàng)
                            $isOos = !ProductModel::hasStock((int)$product['MaSP']);
                            ?>
                            <div class="product-slider-item">
                                <!-- Thêm class 'out-of-stock' nếu sản phẩm hết hàng để làm mờ ảnh thẻ sản phẩm -->
                                <div class="product-card<?= $isOos ? ' out-of-stock' : '' ?>">
                                    <div class="product-image">
                                        <?php if ($isOos): ?>
                                            <span class="oos-badge">Hết hàng</span>
                                        <?php else: ?>
                                            <span class="product-badge badge-new">Mới</span>
                                        <?php endif; ?>
                                        <img src="<?= ProductModel::getImageUrl($product) ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>" loading="lazy">
                                        <?php 
                                        $productId = $product['MaSP'];
                                        // Nhúng các nút chức năng nhanh (Thêm giỏ hàng, xem chi tiết)
                                        include __DIR__ . '/partials/product-actions.php'; 
                                        ?>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-category"><?= htmlspecialchars($product['category'] ?? 'Sản phẩm') ?></div>
                                        <a href="index.php?page=product-detail&id=<?= (int)$product['MaSP'] ?>" class="product-name"><?= htmlspecialchars($product['TenSP']) ?></a>
                                        <div class="product-price">
                                            <span class="current-price"><?= number_format($product['Gia'], 0, ',', ',') ?>đ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;
                    else: ?>
                        <!-- Giao diện hiển thị khi không tìm thấy dữ liệu sản phẩm nào -->
                        <div style="width:100%;text-align:center;padding:48px 0;color:var(--color-gray);">
                            <i class="fas fa-box-open" style="font-size:40px;opacity:.4;margin-bottom:12px;display:block;"></i>
                            Chưa có sản phẩm nào.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Nút di chuyển tiến (Phải) -->
            <button type="button" class="product-slider-arrow product-slider-next" aria-label="Tiến">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

<!-- ===== 3. KHU VỰC KHÁM PHÁ THỂ THAO ĐỘNG ===== -->
<section style="padding:72px 0;background:transparent;">
    <div class="container">
        <div class="section-title vab-reveal fade-right">
            <h2>KHÁM PHÁ THỂ THAO</h2>
            <p>Trang phục chuyên dụng cho từng bộ môn</p>
        </div>
        
        <!-- Bộ lọc nhanh các bộ môn thể thao được lấy động từ CSDL -->
        <div class="filter-buttons vab-reveal fade-up">
            <a href="index.php?page=products" class="btn-filter active">Tất cả</a>
            <?php
            $home_pdo = getConnection();
            // Lấy toàn bộ các bộ môn thể thao đang kích hoạt
            $homeSports = $home_pdo->query("SELECT * FROM THETHAO WHERE TrangThai = 1 ORDER BY TenSport ASC")->fetchAll();
            foreach ($homeSports as $hs):
            ?>
                <a href="index.php?page=<?= htmlspecialchars($hs['DuongDan']) ?>" class="btn-filter"><?= htmlspecialchars($hs['TenSport']) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="product-slider-wrap vab-reveal fade-up">
            <button type="button" class="product-slider-arrow product-slider-prev" aria-label="Lùi">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="product-slider-viewport">
                <div class="product-slider-track">
                    <?php
                    if (!class_exists('ProductModel')) {
                        require './models/ProductModel.php';
                    }
                    $productModel = new ProductModel();
                    // Lấy ngẫu nhiên 10 sản phẩm bất kỳ thuộc các bộ môn thể thao
                    $sportProducts = $productModel->getRandomProducts(10);
                    foreach ($sportProducts as $product):
                        $productId = $product['MaSP'];
                        $isOos = !ProductModel::hasStock((int)$product['MaSP']);
                    ?>
                        <div class="product-slider-item">
                            <div class="product-card<?= $isOos ? ' out-of-stock' : '' ?>">
                                <div class="product-image">
                                    <?php if ($isOos): ?>
                                        <span class="oos-badge">Hết hàng</span>
                                    <?php endif; ?>
                                    <img src="<?= ProductModel::getImageUrl($product) ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>" loading="lazy">
                                    <?php include __DIR__ . '/partials/product-actions.php'; ?>
                                </div>
                                <div class="product-info">
                                    <div class="product-category"><?= htmlspecialchars($product['category'] ?? 'Sản phẩm') ?></div>
                                    <a href="index.php?page=product-detail&id=<?= $productId ?>" class="product-name"><?= htmlspecialchars($product['TenSP']) ?></a>
                                    <div class="product-price">
                                        <?php if (!empty($product['GiaKhuyenMai']) && $product['GiaKhuyenMai'] > 0): ?>
                                            <span class="current-price"><?= number_format($product['GiaKhuyenMai'], 0, ',', ',') ?>đ</span>
                                        <?php else: ?>
                                            <span class="current-price"><?= number_format($product['Gia'], 0, ',', ',') ?>đ</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="button" class="product-slider-arrow product-slider-next" aria-label="Tiến">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

<!-- ===== 4. KHU VỰC THƯƠNG HIỆU NỔI BẬT ===== -->
<section style="padding:72px 0;background:transparent;">
    <div class="container">
        <div class="section-title vab-reveal fade-right">
            <a href="index.php?page=thuonghieu" class="section-title-link">
                <h2>THƯƠNG HIỆU</h2>
            </a>
            <p>Các thương hiệu thể thao hàng đầu thế giới</p>
        </div>
        
        <!-- Hiển thị các khối thương hiệu đẹp mắt -->
        <div class="row g-3 g-md-4">
            <?php
            // Lấy danh sách các thương hiệu trong database đang mở (TrangThai = 1)
            $homeBrands = $home_pdo->query("SELECT * FROM THUONGHIEU WHERE TrangThai = 1 ORDER BY TenThuongHieu ASC")->fetchAll();
            $brandDelay = 1;
            foreach ($homeBrands as $b):
                $brandNameUpper = strtoupper($b['TenThuongHieu']);
                // Kiểm tra sự tồn tại của tệp logo thương hiệu tương ứng trong thư mục assets
                $logoFile = 'assets/images/logo/logo-' . strtolower($b['DuongDan']) . '.png';
                if (!file_exists($logoFile)) {
                    $logoFileJpg = 'assets/images/logo/logo-' . strtolower($b['DuongDan']) . '.jpg';
                    if (file_exists($logoFileJpg)) {
                        $logoFile = $logoFileJpg;
                    } else {
                        // Ảnh giả lập dự phòng nếu không tìm thấy tệp ảnh logo thương hiệu
                        $logoFile = "https://placehold.co/200x200/f5f0e8/0a1628?text=" . urlencode($brandNameUpper);
                    }
                }
                
                // Giới hạn delay tối đa 4
                $delayAttr = $brandDelay <= 4 ? ' data-delay="' . $brandDelay . '"' : ' data-delay="4"';
                $brandDelay++;
            ?>
                <div class="col-4 col-md-4">
                    <!-- Brand-card thiết kế Glassmorphism, bóng đổ sâu và đường viền dày nổi bật -->
                    <div class="brand-card vab-reveal scale-up"<?= $delayAttr ?>>
                        <a href="index.php?page=brand-<?= htmlspecialchars($b['DuongDan']) ?>" class="brand-logo-link">
                            <img src="<?= $logoFile ?>" alt="<?= htmlspecialchars($b['TenThuongHieu']) ?>" class="brand-logo" loading="lazy">
                        </a>
                        <!-- Hiển thị câu Slogan tiếng Việt có dấu đầy đủ từ cột MoTa trong bảng THUONGHIEU -->
                        <p class="brand-slogan" style="margin-top: 12px;">"<?= htmlspecialchars($b['MoTa'] ?? '') ?>"</p>
                        <a href="index.php?page=brand-<?= htmlspecialchars($b['DuongDan']) ?>" class="btn btn-brand">
                            <span class="d-none d-sm-inline">Xem </span><?= htmlspecialchars($b['TenThuongHieu']) ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php 
// Tự động include footer nếu chạy độc lập
if (!isset($nhungTuController)) {
    include 'chantrang.php';
} 
?>