<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['ma_nguoidung'])) {
    $pdo = getConnection();
    
    // 1. Đồng bộ vaitro_nguoidung nếu chưa có trong session
    if (!isset($_SESSION['vaitro_nguoidung'])) {
        $stmt = $pdo->prepare("SELECT VaiTro FROM NHANVIEN WHERE email = ?");
        $stmt->execute([$_SESSION['email_nguoidung']]);
        $role = $stmt->fetchColumn();
        $_SESSION['vaitro_nguoidung'] = $role ?: 'customer';
    }
    
    // 2. Đồng bộ phiên đăng nhập Admin/Employee nếu là admin/manager nhưng chưa có ma_nhanvien
    if (in_array($_SESSION['vaitro_nguoidung'], ['admin', 'manager']) && !isset($_SESSION['ma_nhanvien'])) {
        $stmt = $pdo->prepare("SELECT * FROM NHANVIEN WHERE email = ?");
        $stmt->execute([$_SESSION['email_nguoidung']]);
        $nhanVien = $stmt->fetch();
        
        if ($nhanVien) {
            $_SESSION['ma_nhanvien'] = $nhanVien['MaNV'];
            $_SESSION['ten_nhanvien'] = $nhanVien['HoTenNV'];
            $_SESSION['vaitro_nhanvien'] = $nhanVien['VaiTro'];
        }
    }
    
    // 3. Lấy hạng thành viên của khách hàng để hiển thị
    $stmtTier = $pdo->prepare("SELECT HangKH FROM KHACHHANG WHERE MaKH = ?");
    $stmtTier->execute([$_SESSION['ma_nguoidung']]);
    $userTier = $stmtTier->fetchColumn() ?: 'silver';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tieuDeTrang ?? 'VAB - Thời Trang Thể Thao' ?></title>
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Inter font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
</head>
<body>
 
<!-- ===== SITE HEADER (sticky) ===== -->
<div class="site-header">
<header class="main-header">
    <div class="container">
        <div class="header-inner">
            <div class="header-search">
                <form class="search-form" action="index.php" method="GET">
                    <input type="hidden" name="page" value="search">
                    <input id="search-input" type="text" name="keyword" placeholder="Tìm kiếm sản phẩm..." aria-label="Search" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>" autocomplete="off" required>
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
                <div id="search-suggestions" class="search-suggestions-dropdown" style="display: none;"></div>
            </div>
            <div class="header-logo">
                <a href="index.php?page=home" class="logo-link">
                    <img src="assets/images/logo/logo1.png" alt="VAB Logo" class="logo-img">
                </a>
            </div>
            <div class="header-actions">
                <?php if (isset($_SESSION['ma_nguoidung'])): ?>
                <a href="index.php?page=orders" class="action-item">
                    <span class="icon"><i class="fas fa-box"></i></span>
                    <span class="action-text">Đơn hàng</span>
                </a>
                <div class="dropdown action-item" style="display:inline-block;">
                    <?php
                    $tierBadgeMap = [
                        'silver' => '<span class="tier-badge tier-silver"><i class="fas fa-medal me-1"></i>Bạc</span>',
                        'gold' => '<span class="tier-badge tier-gold"><i class="fas fa-crown me-1"></i>Vàng</span>',
                        'diamond' => '<span class="tier-badge tier-diamond"><i class="fas fa-gem me-1"></i>Kim Cương</span>',
                    ];
                    $badgeHtml = $tierBadgeMap[$userTier ?? 'silver'] ?? $tierBadgeMap['silver'];
                    ?>
                    <a href="#" class="dropdown-toggle text-decoration-none d-inline-flex align-items-center gap-1" data-bs-toggle="dropdown" aria-expanded="false" style="color:var(--color-primary);">
                        <span class="icon"><i class="far fa-user"></i></span>
                        <span class="action-text"><?= htmlspecialchars($_SESSION['ten_nguoidung']) ?></span>
                        <?= $badgeHtml ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" style="border-radius:10px;border:none;min-width:160px;">
                        <?php if (isset($_SESSION['vaitro_nguoidung']) && in_array($_SESSION['vaitro_nguoidung'], ['admin', 'manager'])): ?>
                            <li><a class="dropdown-item py-2 fw-bold" href="index.php?page=admin-dashboard" style="color: #f37021 !important;"><i class="fas fa-user-shield me-2"></i>Trang quản trị</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item py-2" href="index.php?page=orders"><i class="fas fa-box me-2 text-muted"></i>Đơn hàng của tôi</a></li>
                        <li><a class="dropdown-item py-2" href="index.php?page=inbox"><i class="fas fa-envelope me-2 text-muted"></i>Hộp thư của tôi</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item py-2" href="index.php?page=logout"><i class="fas fa-sign-out-alt me-2 text-danger"></i>Đăng xuất</a></li>
                    </ul>
                </div>
                <?php else: ?>
                <a href="index.php?page=login" class="action-item">
                    <span class="icon"><i class="far fa-user"></i></span>
                    <span class="action-text">Tài khoản</span>
                </a>
                <?php endif; ?>
                <?php
                $cartCount = 0;
                if (!empty($_SESSION['giohang'])) {
                    foreach ($_SESSION['giohang'] as $cartItem) {
                        $cartCount += (int)($cartItem['SoLuongSP'] ?? 0);
                    }
                }
                ?>
                <a href="index.php?page=cart" class="action-item">
                    <span class="icon"><i class="fas fa-shopping-bag"></i></span>
                    <span class="action-text">Giỏ hàng</span>
                    <span class="cart-badge" style="<?= $cartCount > 0 ? '' : 'display: none;' ?>"><?= $cartCount ?></span>
                </a>
            </div>
        </div>
    </div>
</header>
 
<!-- ===== NAVIGATION ===== -->
<nav class="navbar-main">
    <div class="container">
        <button class="navbar-toggler" type="button" id="navbarToggler">
            <i class="fas fa-bars"></i> Menu
        </button>
<ul class="nav-menu" id="navMenu">
    <li><a href="index.php?page=home" class="<?= ($trangHienTai ?? '') == 'home' ? 'active' : '' ?>">Trang chủ</a></li>
            <li><a href="index.php?page=products" class="<?= ($trangHienTai ?? '') == 'products' ? 'active' : '' ?>">Sản phẩm</a></li>
            <?php
            $header_pdo = getConnection();
            $activeBrands = $header_pdo->query("SELECT * FROM THUONGHIEU WHERE TrangThai = 1 ORDER BY TenThuongHieu ASC")->fetchAll();
            $activeSports = $header_pdo->query("SELECT * FROM THETHAO WHERE TrangThai = 1 ORDER BY TenSport ASC")->fetchAll();
            
            $brandSlugs = array_map(function($b) { return 'brand-' . $b['DuongDan']; }, $activeBrands);
            $sportSlugs = array_map(function($s) { return $s['DuongDan']; }, $activeSports);
            ?>
            <?php if (!empty($activeBrands)): ?>
            <li class="has-dropdown">
                <a href="#" class="<?= in_array($trangHienTai ?? '', $brandSlugs) ? 'active' : '' ?>">Thương hiệu <i class="fas fa-chevron-down" style="font-size:10px;margin-left:4px;"></i></a>
                <ul class="dropdown-menu-custom">
                    <?php foreach ($activeBrands as $b): ?>
                    <li><a href="index.php?page=brand-<?= htmlspecialchars($b['DuongDan']) ?>" class="<?= ($trangHienTai ?? '') == 'brand-' . $b['DuongDan'] ? 'active' : '' ?>"><?= htmlspecialchars($b['TenThuongHieu']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>
            <?php if (!empty($activeSports)): ?>
            <li class="has-dropdown">
                <?php 
                $firstSportPage = !empty($activeSports) ? 'index.php?page=' . htmlspecialchars($activeSports[0]['DuongDan']) : '#'; 
                ?>
                <a href="<?= $firstSportPage ?>" class="<?= in_array($trangHienTai ?? '', $sportSlugs) ? 'active' : '' ?>">Thể thao <i class="fas fa-chevron-down" style="font-size:10px;margin-left:4px;"></i></a>
                <ul class="dropdown-menu-custom">
                    <?php foreach ($activeSports as $s): ?>
                    <li><a href="index.php?page=<?= htmlspecialchars($s['DuongDan']) ?>" class="<?= ($trangHienTai ?? '') == $s['DuongDan'] ? 'active' : '' ?>"><?= htmlspecialchars($s['TenSport']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>
            <li><a href="index.php?page=men" class="<?= ($trangHienTai ?? '') == 'men' ? 'active' : '' ?>">Nam</a></li>
            <li><a href="index.php?page=women" class="<?= ($trangHienTai ?? '') == 'women' ? 'active' : '' ?>">Nữ</a></li>
            <li><a href="index.php?page=sale" class="<?= ($trangHienTai ?? '') == 'sale' ? 'active' : '' ?>" style="color: #f25c19; font-weight: bold;"><i class="fas fa-tags me-1"></i>Khuyến mãi</a></li>
        </ul>
    </div>
</nav>
</div>