<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tieuDeTrang ?? 'VAB Admin' ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="assets/css/admin.css?v=<?= time() ?>">
</head>

<body>
    <div class="admin-wrapper">
        <!-- Mobile Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <a href="index.php?page=admin-dashboard" class="sidebar-logo">
                    <img src="assets/images/logo/logo1.png" alt="VAB Logo">
                </a>
                <button class="sidebar-close" id="sidebarClose"><i class="fas fa-times"></i></button>
            </div>

            <?php $currentRole = strtolower($_SESSION['vaitro_nhanvien'] ?? 'staff'); ?>
            <nav class="sidebar-menu">
                <div class="menu-label">Tổng quan</div>
                <a href="index.php?page=admin-dashboard" class="menu-item <?= $trangHienTai == 'admin-dashboard' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-chart-pie"></i></span>
                    <span class="menu-text">Bảng điều khiển</span>
                </a>

                <div class="menu-label">Quản lý</div>
                <a href="index.php?page=admin-products" class="menu-item <?= strpos($trangHienTai, 'admin-product') !== false ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-box"></i></span>
                    <span class="menu-text">Sản phẩm</span>
                </a>
                <?php if (in_array($currentRole, ['admin', 'manager'])): ?>
                    <a href="index.php?page=admin-categories" class="menu-item <?= strpos($trangHienTai, 'admin-categor') !== false ? 'active' : '' ?>">
                        <span class="menu-icon"><i class="fas fa-folder-open"></i></span>
                        <span class="menu-text">Danh mục</span>
                    </a>
                <?php endif; ?>
                <a href="index.php?page=admin-orders" class="menu-item <?= strpos($trangHienTai, 'admin-order') !== false ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-shopping-cart"></i></span>
                    <span class="menu-text">Đơn hàng</span>
                </a>
                <?php if (in_array($currentRole, ['admin', 'manager'])): ?>
                    <a href="index.php?page=admin-users" class="menu-item <?= $trangHienTai == 'admin-users' ? 'active' : '' ?>">
                        <span class="menu-icon"><i class="fas fa-users"></i></span>
                        <span class="menu-text">Khách hàng</span>
                    </a>
                <?php endif; ?>
                <?php if (in_array($currentRole, ['admin'])): ?>
                    <a href="index.php?page=admin-employees" class="menu-item <?= $trangHienTai == 'admin-employees' ? 'active' : '' ?>">
                        <span class="menu-icon"><i class="fas fa-user-tie"></i></span>
                        <span class="menu-text">Nhân viên</span>
                    </a>
                <?php endif; ?>

                <div class="menu-label">Tiếp thị</div>
                <?php if (in_array($currentRole, ['admin', 'manager'])): ?>
                    <a href="index.php?page=admin-vouchers" class="menu-item <?= $trangHienTai == 'admin-vouchers' ? 'active' : '' ?>">
                        <span class="menu-icon"><i class="fas fa-ticket-alt"></i></span>
                        <span class="menu-text">Mã giảm giá</span>
                    </a>
                <?php endif; ?>
                <a href="index.php?page=admin-reviews" class="menu-item <?= $trangHienTai == 'admin-reviews' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-star"></i></span>
                    <span class="menu-text">Đánh giá</span>
                </a>
                <a href="index.php?page=admin-mail" class="menu-item <?= $trangHienTai == 'admin-mail' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-envelope"></i></span>
                    <span class="menu-text">Email / Thông báo</span>
                </a>

                <?php if (in_array($currentRole, ['admin', 'manager'])): ?>
                    <div class="menu-label">Phân tích</div>
                    <a href="index.php?page=admin-revenue" class="menu-item <?= $trangHienTai == 'admin-revenue' ? 'active' : '' ?>">
                        <span class="menu-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="menu-text">Doanh thu</span>
                    </a>
                <?php endif; ?>

                <div class="menu-label">Hệ thống</div>
                <a href="index.php?page=admin-settings" class="menu-item <?= $trangHienTai == 'admin-settings' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-cog"></i></span>
                    <span class="menu-text">Cài đặt</span>
                </a>
                <a href="index.php?page=home" class="menu-item" style="color:rgba(242,92,25,.85)!important;">
                    <span class="menu-icon"><i class="fas fa-external-link-alt" style="color:rgba(242,92,25,.85)!important;"></i></span>
                    <span class="menu-text">Xem trang chủ</span>
                </a>
            </nav>

        </aside>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <span class="page-title"><?= $tieuDeTrang ?? 'Dashboard' ?></span>
                </div>
                <div class="header-right">

                    <?php
                    $tenAdmin = $_SESSION['ten_nhanvien'] ?? 'Admin VAB';
                    $vaiTroRaw = $_SESSION['vaitro_nhanvien'] ?? 'admin';
                    $roleLabelMap = [
                        'admin'   => 'Quản trị viên',
                        'manager' => 'Quản lý',
                        'staff'   => 'Nhân viên',
                    ];
                    $vaiTroLabel = $roleLabelMap[strtolower($vaiTroRaw)] ?? 'Quản trị viên';
                    $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($tenAdmin) . "&background=f25c19&color=fff&size=64";
                    ?>
                    <div class="admin-profile">
                        <img src="<?= $avatarUrl ?>" alt="Admin">
                        <div>
                            <span class="admin-name"><?= htmlspecialchars($tenAdmin) ?></span>
                            <span class="admin-role"><?= htmlspecialchars($vaiTroLabel) ?></span>
                        </div>
                        <span class="dropdown-arrow"><i class="fas fa-chevron-down"></i></span>
                        <div class="profile-dropdown">
                            <a href="index.php?page=home"><i class="fas fa-home"></i>Xem trang chủ</a>
                            <a href="index.php?page=admin-settings"><i class="fas fa-cog"></i>Cài đặt</a>
                            <div class="profile-dropdown-divider"></div>
                            <a href="index.php?page=admin-logout" style="color:#ef4444!important;"><i class="fas fa-sign-out-alt" style="color:#ef4444!important;"></i>Đăng xuất</a>
                        </div>
                    </div>
                </div>
            </header>
            <div class="admin-content">