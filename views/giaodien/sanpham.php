<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Sản phẩm - VAB Thời Trang Thể Thao';
    $trangHienTai = 'products';
    include 'dautrang.php';
}
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section vab-reveal fade-in"><div class="container"><h1>TẤT CẢ SẢN PHẨM</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item active">Sản phẩm</li></ol></nav>
</div></section>

<!-- PRODUCTS -->
<section class="pb-5"><div class="container"><div class="row">
    <?php 
    $pdo = getConnection();
    $activeCategories = $pdo->query("SELECT * FROM DANHMUCSP WHERE TrangThai = 1 ORDER BY MaDanhMuc ASC")->fetchAll();
    $activeBrands = $pdo->query("SELECT * FROM THUONGHIEU WHERE TrangThai = 1 ORDER BY TenThuongHieu ASC")->fetchAll();
    $activeSports = $pdo->query("SELECT * FROM THETHAO WHERE TrangThai = 1 ORDER BY TenSport ASC")->fetchAll();

    // 1. Phân tích các bộ lọc từ $_GET
    $selectedCategories = isset($_GET['categories']) && is_array($_GET['categories']) ? $_GET['categories'] : [];
    $selectedBrands = isset($_GET['brands']) && is_array($_GET['brands']) ? $_GET['brands'] : [];
    $selectedGender = isset($_GET['gender']) ? trim($_GET['gender']) : '';
    $selectedSports = isset($_GET['sports']) && is_array($_GET['sports']) ? $_GET['sports'] : [];
    $currentSort = isset($_GET['sort']) ? trim($_GET['sort']) : 'default';

    $filters = [
        'categories' => $selectedCategories,
        'brands'     => $selectedBrands,
        'gender'     => $selectedGender,
        'sports'     => $selectedSports
    ];

    $hasActiveFilters = !empty($selectedCategories) || !empty($selectedBrands) || !empty($selectedGender) || !empty($selectedSports);

    $productModel = new ProductModel();
    $limit = 12;
    $totalProducts = $productModel->getFilteredCount($filters);
    
    // Lấy số trang từ URL (?p=x), mặc định là trang 1
    $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    if ($currentPage < 1) {
        $currentPage = 1;
    }
    
    $totalPages = ceil($totalProducts / $limit);
    if ($totalPages < 1) {
        $totalPages = 1;
    }
    if ($currentPage > $totalPages) {
        $currentPage = $totalPages;
    }
    
    $offset = ($currentPage - 1) * $limit;
    $productsList = $productModel->getFiltered($filters, $offset, $limit, $currentSort);
    $loadedCount = count($productsList);
    
    // Chuỗi truy vấn để bảo toàn trạng thái lọc khi phân trang
    $queryStringParams = $_GET;
    unset($queryStringParams['p']); // bỏ trang để gán động
    $baseQueryString = http_build_query($queryStringParams);
    ?>
    <!-- Desktop Sidebar -->
    <div class="col-md-4 col-lg-3 d-none d-md-block vab-reveal fade-right">
        <form id="filter-form-desktop" method="GET" action="index.php">
            <input type="hidden" name="page" value="products">
            <input type="hidden" name="sort" id="sort-input-desktop" value="<?= htmlspecialchars($currentSort) ?>">
            <div class="filter-sidebar">
                <div class="filter-section"><h4 class="filter-title">Danh mục</h4><div class="filter-options">
                    <?php foreach($activeCategories as $ac): ?>
                    <?php
                    $catMap = ['ao' => 'Áo', 'quan' => 'Quần', 'giay' => 'Giày', 'phu kien' => 'Phụ kiện'];
                    $nameLower = strtolower(trim($ac['TenDM']));
                    $displayName = isset($catMap[$nameLower]) ? $catMap[$nameLower] : $ac['TenDM'];
                    ?>
                    <label><input type="checkbox" name="categories[]" value="<?= $ac['MaDanhMuc'] ?>" <?= in_array($ac['MaDanhMuc'], $selectedCategories) ? 'checked' : '' ?>> <?= htmlspecialchars($displayName) ?></label>
                    <?php endforeach; ?>
                </div></div>
                <div class="filter-section"><h4 class="filter-title">Thương hiệu</h4><div class="filter-options">
                    <?php foreach($activeBrands as $ab): ?>
                    <label><input type="checkbox" name="brands[]" value="<?= $ab['MaThuongHieu'] ?>" <?= in_array($ab['MaThuongHieu'], $selectedBrands) ? 'checked' : '' ?>> <?= htmlspecialchars($ab['TenThuongHieu']) ?></label>
                    <?php endforeach; ?>
                </div></div>
                <div class="filter-section"><h4 class="filter-title">Giới tính</h4><div class="filter-options">
                    <label><input type="radio" name="gender" value="" <?= empty($selectedGender) ? 'checked' : '' ?>> Tất cả</label>
                    <label><input type="radio" name="gender" value="Nam" <?= $selectedGender === 'Nam' ? 'checked' : '' ?>> Nam</label>
                    <label><input type="radio" name="gender" value="Nữ" <?= $selectedGender === 'Nữ' ? 'checked' : '' ?>> Nữ</label>
                    <label><input type="radio" name="gender" value="Unisex" <?= $selectedGender === 'Unisex' ? 'checked' : '' ?>> Unisex</label>
                </div></div>
                <div class="filter-section"><h4 class="filter-title">Bộ môn</h4><div class="filter-options">
                    <?php foreach($activeSports as $as): ?>
                    <label><input type="checkbox" name="sports[]" value="<?= htmlspecialchars($as['TenSport']) ?>" <?= in_array($as['TenSport'], $selectedSports) ? 'checked' : '' ?>> <?= htmlspecialchars($as['TenSport']) ?></label>
                    <?php endforeach; ?>
                </div></div>
                <button type="submit" class="btn btn-secondary w-100 mt-2" style="border-radius:50px;">Áp dụng bộ lọc</button>
                <?php if ($hasActiveFilters): ?>
                    <a href="index.php?page=products" class="btn btn-outline-danger w-100 mt-2" style="border-radius:50px; text-decoration:none !important; display:flex; align-items:center; justify-content:center;">Xóa bộ lọc</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <!-- Products Column -->
    <div class="col-12 col-md-8 col-lg-9">
        <!-- Mobile Sticky Filter Bar -->
        <div class="mobile-filter-bar d-md-none vab-reveal fade-in">
            <button class="filter-bar-btn" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                <i class="fas fa-sliders-h me-2"></i> Bộ lọc
            </button>
            <div class="filter-bar-divider"></div>
            <div class="filter-bar-btn sort-btn-wrapper">
                <span>Sắp xếp theo</span> <i class="fas fa-chevron-down ms-2"></i>
                <select class="mobile-sort-select">
                    <option value="default" <?= $currentSort === 'default' ? 'selected' : '' ?>>Mặc định</option>
                    <option value="price_asc" <?= $currentSort === 'price_asc' ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                    <option value="price_desc" <?= $currentSort === 'price_desc' ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
                </select>
            </div>
        </div>
        <style>
        .pagination .page-item .page-link {
            transition: all 0.25s ease;
            text-decoration: none !important;
        }
        .pagination .page-item:not(.active):not(.disabled) .page-link:hover {
            background-color: var(--color-secondary) !important;
            border-color: var(--color-secondary) !important;
            color: #fff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(242, 92, 25, 0.2);
        }
        </style>
        <div class="products-header d-none d-md-flex vab-reveal fade-in">
            <span class="products-count" id="products-count">Hiển thị <span id="shown-count"><?= $loadedCount ?></span> sản phẩm (Trang <?= $currentPage ?>/<?= $totalPages ?>)</span>
            <div><label class="me-2" style="font-size:14px;">Sắp xếp:</label><select class="sort-select"><option value="default" <?= $currentSort === 'default' ? 'selected' : '' ?>>Mặc định</option><option value="price_asc" <?= $currentSort === 'price_asc' ? 'selected' : '' ?>>Giá: Thấp đến Cao</option><option value="price_desc" <?= $currentSort === 'price_desc' ? 'selected' : '' ?>>Giá: Cao đến Thấp</option></select></div>
        </div>
        <div class="row g-3" id="product-list">
            <?php if (!empty($productsList)): 
                $productDelay = 1;
                foreach($productsList as $product): 
                    // Stagger delay from 1 to 4
                    $delayAttr = $productDelay <= 4 ? ' data-delay="' . $productDelay . '"' : ' data-delay="4"';
                    $productDelay++;
                    if ($productDelay > 4) $productDelay = 1;
                ?>
            <div class="col-lg-3 col-md-6 col-6 product-item vab-reveal fade-up"<?= $delayAttr ?> data-id="<?= (int)$product['MaSP'] ?>">
                <div class="product-card">
                    <div class="product-image" style="position: relative;">
                        <img src="<?= ProductModel::getImageUrl($product) ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>">
                        <?php 
                        $isOos = !ProductModel::hasStock((int)$product['MaSP']);
                        if ($isOos): 
                        ?>
                            <span class="oos-badge">Hết hàng</span>
                        <?php else: ?>
                            <?php if (!empty($product['GiaKhuyenMai']) && $product['GiaKhuyenMai'] > 0): 
                                $discountPercent = round((($product['Gia'] - $product['GiaKhuyenMai']) / $product['Gia']) * 100);
                                if ($discountPercent > 0):
                            ?>
                                <span class="product-badge badge-sale">-<?= $discountPercent ?>%</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php $productId = $product['MaSP']; include __DIR__ . '/partials/product-actions.php'; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?= htmlspecialchars($product['category'] ?? 'Sản phẩm') ?></div>
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
            <div class="col-12 text-center py-5"><p class="text-muted">Chưa có sản phẩm.</p></div>
            <?php endif; ?>
        </div>
        <?php if ($totalPages > 1): ?>
        <div class="pagination-wrapper d-flex justify-content-center mt-4 mb-4 vab-reveal fade-in">
            <nav aria-label="Page navigation">
                <ul class="pagination align-items-center" style="gap: 8px; list-style: none; display: flex; padding: 0;">
                    <!-- Nút Quay lại -->
                    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                        <?php if ($currentPage <= 1): ?>
                            <span class="page-link" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid #dee2e6; color: #ccc; cursor: not-allowed; background: #f8f9fa;">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        <?php else: ?>
                            <a class="page-link" href="index.php?<?= !empty($baseQueryString) ? $baseQueryString . '&' : '' ?>p=<?= $currentPage - 1 ?>" aria-label="Previous" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid #dee2e6; color: var(--color-primary); background: #fff;">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                    </li>
                    
                    <!-- Các trang số -->
                    <?php 
                    $pagesToShow = [];
                    if ($totalPages <= 7) {
                        for ($i = 1; $i <= $totalPages; $i++) {
                            $pagesToShow[] = $i;
                        }
                    } else {
                        $pagesToShow[] = 1;
                        $start = max(2, $currentPage - 2);
                        $end = min($totalPages - 1, $currentPage + 2);
                        
                        if ($currentPage <= 4) {
                            $end = 5;
                        }
                        if ($currentPage >= $totalPages - 3) {
                            $start = $totalPages - 4;
                        }
                        
                        if ($start > 2) {
                            $pagesToShow[] = '...';
                        }
                        for ($i = $start; $i <= $end; $i++) {
                            $pagesToShow[] = $i;
                        }
                        if ($end < $totalPages - 1) {
                            $pagesToShow[] = '...';
                        }
                        $pagesToShow[] = $totalPages;
                    }

                    foreach ($pagesToShow as $pageItem):
                        if ($pageItem === '...'):
                    ?>
                            <li class="page-item disabled">
                                <span class="page-link" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: none; background: transparent; color: #6c757d;">...</span>
                            </li>
                        <?php else: ?>
                            <li class="page-item <?= ($pageItem === $currentPage) ? 'active' : '' ?>">
                                <?php if ($pageItem === $currentPage): ?>
                                    <span class="page-link" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: var(--color-secondary); border-color: var(--color-secondary); color: #fff; font-weight: bold; box-shadow: var(--shadow-btn); text-decoration: none !important;"><?= $pageItem ?></span>
                                <?php else: ?>
                                    <a class="page-link" href="index.php?<?= !empty($baseQueryString) ? $baseQueryString . '&' : '' ?>p=<?= $pageItem ?>" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid #dee2e6; color: var(--color-primary); background: #fff; text-decoration: none !important;"><?= $pageItem ?></a>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <!-- Nút Tiếp theo -->
                    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                        <?php if ($currentPage >= $totalPages): ?>
                            <span class="page-link" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid #dee2e6; color: #ccc; cursor: not-allowed; background: #f8f9fa;">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        <?php else: ?>
                            <a class="page-link" href="index.php?<?= !empty($baseQueryString) ? $baseQueryString . '&' : '' ?>p=<?= $currentPage + 1 ?>" aria-label="Next" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid #dee2e6; color: var(--color-primary); background: #fff;">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div></div></section>

<!-- Offcanvas Filter Sidebar for Mobile -->
<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="filterOffcanvasLabel" style="font-weight: 800; color: var(--color-primary);">
            <i class="fas fa-sliders-h me-2" style="color: var(--color-secondary);"></i> BỘ LỌC
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" style="background-color: #f4f6f9;">
        <form id="filter-form-mobile" method="GET" action="index.php">
            <input type="hidden" name="page" value="products">
            <input type="hidden" name="sort" id="sort-input-mobile" value="<?= htmlspecialchars($currentSort) ?>">
            <div class="filter-sidebar" style="box-shadow: none; padding: 0; background: none;">
                <div class="filter-section"><h4 class="filter-title">Danh mục</h4><div class="filter-options">
                    <?php foreach($activeCategories as $ac): ?>
                    <?php
                    $catMap = ['ao' => 'Áo', 'quan' => 'Quần', 'giay' => 'Giày', 'phu kien' => 'Phụ kiện'];
                    $nameLower = strtolower(trim($ac['TenDM']));
                    $displayName = isset($catMap[$nameLower]) ? $catMap[$nameLower] : $ac['TenDM'];
                    ?>
                    <label><input type="checkbox" name="categories[]" value="<?= $ac['MaDanhMuc'] ?>" <?= in_array($ac['MaDanhMuc'], $selectedCategories) ? 'checked' : '' ?>> <?= htmlspecialchars($displayName) ?></label>
                    <?php endforeach; ?>
                </div></div>
                <div class="filter-section"><h4 class="filter-title">Thương hiệu</h4><div class="filter-options">
                    <?php foreach($activeBrands as $ab): ?>
                    <label><input type="checkbox" name="brands[]" value="<?= $ab['MaThuongHieu'] ?>" <?= in_array($ab['MaThuongHieu'], $selectedBrands) ? 'checked' : '' ?>> <?= htmlspecialchars($ab['TenThuongHieu']) ?></label>
                    <?php endforeach; ?>
                </div></div>
                <div class="filter-section"><h4 class="filter-title">Giới tính</h4><div class="filter-options">
                    <label><input type="radio" name="gender" value="" <?= empty($selectedGender) ? 'checked' : '' ?>> Tất cả</label>
                    <label><input type="radio" name="gender" value="Nam" <?= $selectedGender === 'Nam' ? 'checked' : '' ?>> Nam</label>
                    <label><input type="radio" name="gender" value="Nữ" <?= $selectedGender === 'Nữ' ? 'checked' : '' ?>> Nữ</label>
                    <label><input type="radio" name="gender" value="Unisex" <?= $selectedGender === 'Unisex' ? 'checked' : '' ?>> Unisex</label>
                </div></div>
                <div class="filter-section"><h4 class="filter-title">Bộ môn</h4><div class="filter-options">
                    <?php foreach($activeSports as $as): ?>
                    <label><input type="checkbox" name="sports[]" value="<?= htmlspecialchars($as['TenSport']) ?>" <?= in_array($as['TenSport'], $selectedSports) ? 'checked' : '' ?>> <?= htmlspecialchars($as['TenSport']) ?></label>
                    <?php endforeach; ?>
                </div></div>
                <button type="submit" class="btn btn-secondary w-100 mt-2" style="border-radius:50px;">Áp dụng bộ lọc</button>
                <?php if ($hasActiveFilters): ?>
                    <a href="index.php?page=products" class="btn btn-outline-danger w-100 mt-2" style="border-radius:50px; text-decoration:none !important; display:flex; align-items:center; justify-content:center;">Xóa bộ lọc</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function handleSortChange(selectEl) {
        if (!selectEl) return;
        selectEl.addEventListener('change', function() {
            const val = this.value;
            const desktopSidebar = document.querySelector('.d-none.d-md-block');
            const isDesktop = desktopSidebar && window.getComputedStyle(desktopSidebar).display !== 'none';
            
            if (isDesktop) {
                const sortInput = document.getElementById('sort-input-desktop');
                const form = document.getElementById('filter-form-desktop');
                if (sortInput && form) {
                    sortInput.value = val;
                    form.submit();
                }
            } else {
                const sortInput = document.getElementById('sort-input-mobile');
                const form = document.getElementById('filter-form-mobile');
                if (sortInput && form) {
                    sortInput.value = val;
                    form.submit();
                }
            }
        });
    }

    handleSortChange(document.querySelector('.sort-select'));
    handleSortChange(document.querySelector('.mobile-sort-select'));
});
</script>

<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>
