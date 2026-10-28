<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Bảng điều khiển - VAB Admin';
    $trangHienTai = 'admin-dashboard';
}
?>

<?php if (isset($_SESSION['admin_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0" role="alert" style="background-color: #f8d7da; color: #842029;">
        <i class="fas fa-exclamation-triangle me-2"></i><?= $_SESSION['admin_error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['admin_error']); ?>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div>
        <div class="page-header-title">Tổng quan hệ thống</div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <a href="index.php?page=admin-revenue" class="stat-card stat-card-blue">
            <div class="stat-icon" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#1d4ed8;">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Doanh thu</span>
                <span class="stat-value"><?= number_format($totalRevenue ?? 0, 0, ',', '.') ?>đ</span>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> Tổng cộng</span>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="index.php?page=admin-orders" class="stat-card stat-card-green">
            <div class="stat-icon" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#065f46;">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Đơn hàng</span>
                <span class="stat-value"><?= $totalOrders ?? 0 ?></span>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> Tất cả đơn</span>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="index.php?page=admin-users" class="stat-card stat-card-orange">
            <div class="stat-icon" style="background:linear-gradient(135deg,#ffedd5,#fed7aa);color:#c2410c;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Người dùng</span>
                <span class="stat-value"><?= $totalUsers ?? 0 ?></span>
                <span class="stat-change up"><i class="fas fa-user-plus"></i> Đã đăng ký</span>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="index.php?page=admin-products" class="stat-card stat-card-purple">
            <div class="stat-icon" style="background:linear-gradient(135deg,#ede9fe,#ddd6fe);color:#5b21b6;">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Sản phẩm</span>
                <span class="stat-value"><?= $totalProducts ?? 0 ?></span>
                <span class="stat-change up"><i class="fas fa-boxes"></i> Trong kho</span>
            </div>
        </a>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row g-3">
    <!-- Recent Orders - 8 cols -->
    <div class="col-xl-8">
        <div class="admin-table-wrapper" style="height:100%;">
            <div class="table-header">
                <h5><i class="fas fa-receipt"></i> Đơn hàng gần đây</h5>
                <a href="index.php?page=admin-orders" class="btn-secondary-admin" style="font-size:12px;padding:6px 14px;">
                    Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentOrders)): ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Chưa có đơn hàng nào</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php
                    $statusMap = [
                        'pending'   => 'Chờ xác nhận',
                        'confirmed' => 'Đã thanh toán',
                        'shipping'  => 'Đang giao',
                        'delivered' => 'Đã giao',
                        'completed' => 'Giao thành công',
                        'returning' => 'Yêu cầu trả hàng',
                        'returned'  => 'Đã trả hàng',
                        'cancelled' => 'Đã hủy',
                        'refused'   => 'Từ chối nhận'
                    ];
                    foreach($recentOrders as $o): ?>
                    <tr>
                        <td data-label="Mã ĐH">
                            <span style="font-weight:700;color:var(--admin-secondary);font-size:12px;">
                                #ORD<?= str_pad($o['MaDonHang'], 3, '0', STR_PAD_LEFT) ?>
                            </span>
                        </td>
                        <td data-label="Khách hàng">
                            <span style="font-weight:600;"><?= htmlspecialchars($o['customer_name'] ?? $o['HoTenKH']) ?></span>
                        </td>
                        <td data-label="Tổng tiền" style="font-weight:700;color:var(--admin-text);">
                            <?= number_format($o['TongTien'], 0, ',', '.') ?>đ
                        </td>
                        <td data-label="Trạng thái">
                            <span class="status-badge <?= $o['TrangThaiDH'] ?>">
                                <?= $statusMap[$o['TrangThaiDH']] ?? $o['TrangThaiDH'] ?>
                            </span>
                        </td>
                        <td data-label="Ngày đặt" style="color:var(--admin-text-soft);font-size:12.5px;">
                            <?= date('d/m/Y', strtotime($o['NgayTao'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column (Low Stock & Bom Warning) - 4 cols -->
    <div class="col-xl-4">
        <div class="d-flex flex-column gap-3">
            <!-- Tồn kho thấp -->
            <div class="widget-card">
                <div class="widget-title">
                    <i class="fas fa-exclamation-triangle"></i> Tồn kho thấp
                </div>
                <?php if (empty($lowStock)): ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle" style="color:var(--clr-success);"></i>
                    <p>Không có sản phẩm tồn kho thấp</p>
                </div>
                <?php else: ?>
                <?php foreach($lowStock as $s): ?>
                <div class="stock-item">
                    <span><?= htmlspecialchars($s['TenSP']) ?></span>
                    <span class="stock-qty">
                        <i class="fas fa-box-open me-1" style="font-size:10px;"></i>
                        Còn <?= $s['tong_kho'] ?>
                    </span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Cảnh báo Bom hàng -->
            <div class="widget-card" style="border-left: 3px solid #ef4444;">
                <div class="widget-title" style="color:#ef4444;">
                    <i class="fas fa-bomb"></i> Cảnh báo Bom hàng
                    <a href="index.php?page=admin-users&filter_bomb=bomb" style="font-size:11px; font-weight:500; color:#ef4444; text-decoration:none; margin-left:auto; opacity:0.8;">Xem tất cả →</a>
                </div>
                <?php if (empty($bombers)): ?>
                <div class="empty-state">
                    <i class="fas fa-shield-alt" style="color:var(--clr-success);"></i>
                    <p>Không có khách hàng bom hàng trong 30 ngày qua</p>
                </div>
                <?php else: ?>
                <?php foreach($bombers as $b): ?>
                <div class="stock-item" style="padding: 10px 0; border-bottom: 1px solid var(--admin-border);">
                    <div>
                        <span style="font-weight:600; font-size:13px;"><?= htmlspecialchars($b['HoTenKH']) ?></span>
                        <span class="d-block" style="font-size:11px; color:var(--admin-muted);"><?= htmlspecialchars($b['SDT'] ?? '') ?></span>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="badge bg-danger text-white" style="font-size:11px; padding:4px 9px; border-radius:20px;"><?= (int)$b['refused_orders'] ?> lần bom</span>
                        <a href="index.php?page=admin-users&toggle=<?= $b['MaKH'] ?>" 
                           class="btn btn-sm" 
                           style="background:#ef4444; color:#fff; border:none; border-radius:20px; font-size:11px; padding:3px 10px;"
                           onclick="return confirm('Khóa tài khoản <?= addslashes($b['HoTenKH']) ?>?')"
                           <?= $b['TrangThai'] === 'locked' ? 'disabled title=\'Đã khóa\'' : '' ?>>
                            <?= $b['TrangThai'] === 'locked' ? '🔒 Đã khóa' : '🔒 Khóa' ?>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>