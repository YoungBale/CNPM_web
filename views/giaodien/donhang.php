<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Đơn hàng của tôi - VAB';
    $trangHienTai = 'orders';
    include 'dautrang.php';
}
?>

<!-- BREADCRUMB -->
<section class="breadcrumb-section">
    <div class="container">
        <h1>ĐƠN HÀNG CỦA TÔI</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li>
                <li class="breadcrumb-item active">Đơn hàng của tôi</li>
            </ol>
        </nav>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <?php if (isset($_SESSION['order_msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
                <i class="fas fa-check-circle me-2"></i><?= $_SESSION['order_msg'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['order_msg']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['order_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background-color: #f8d7da; color: #842029;">
                <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['order_error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['order_error']); ?>
        <?php endif; ?>
        <div class="row">
            <div class="col-lg-12">
                <?php if (!empty($orders)): ?>
                    <div class="admin-table-wrapper shadow-sm border rounded-3 overflow-hidden bg-white p-4">
                        <h5 class="fw-bold mb-4 text-primary-color"><i class="fas fa-box-open me-2 text-secondary-color"></i>Danh sách đơn hàng của bạn</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle cart-table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="py-3 px-4" style="width: 15%">Mã đơn hàng</th>
                                        <th scope="col" class="py-3">Ngày đặt</th>
                                        <th scope="col" class="py-3">Người nhận</th>
                                        <th scope="col" class="py-3">Tổng tiền</th>
                                        <th scope="col" class="py-3">Trạng thái</th>
                                        <th scope="col" class="py-3 text-center" style="width: 15%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): 
                                        $statusInfo = OrderController::getStatusInfo($order['TrangThaiDH']);
                                        $orderCode = '#ORD' . str_pad($order['MaDonHang'], 5, '0', STR_PAD_LEFT);
                                        $formattedDate = date('d/m/Y H:i', strtotime($order['NgayTao']));
                                        $totalPrice = number_format($order['TongTien'], 0, ',', '.') . 'đ';
                                    ?>
                                        <tr>
                                            <td data-label="Mã đơn" class="px-4"><strong class="text-primary-color"><?= htmlspecialchars($orderCode) ?></strong></td>
                                            <td data-label="Ngày đặt"><span class="text-muted"><i class="far fa-calendar-alt me-1"></i><?= htmlspecialchars($formattedDate) ?></span></td>
                                            <td data-label="Người nhận">
                                                <div class="fw-semibold"><?= htmlspecialchars($order['HoTenKH']) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($order['SDT']) ?></small>
                                            </td>
                                            <td data-label="Tổng tiền"><span class="fw-bold text-danger"><?= htmlspecialchars($totalPrice) ?></span></td>
                                            <td data-label="Trạng thái">
                                                <span class="status-badge <?= htmlspecialchars($statusInfo['class']) ?>">
                                                    <?= htmlspecialchars($statusInfo['label']) ?>
                                                </span>
                                            </td>
                                            <td data-label="Thao tác" class="text-center">
                                                <a href="index.php?page=order-detail&id=<?= (int)$order['MaDonHang'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 text-decoration-none">
                                                    <i class="fas fa-eye me-1"></i>Chi tiết
                                                </a>
                                                <?php if ($order['TrangThaiDH'] === 'pending'): ?>
                                                    <a href="index.php?page=order-cancel&id=<?= (int)$order['MaDonHang'] ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 text-decoration-none ms-1" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                                        <i class="fas fa-times me-1"></i>Hủy đơn
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 shadow-sm border rounded-3 bg-white">
                        <div class="mb-4">
                            <span class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 80px; height: 80px;">
                                <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                            </span>
                        </div>
                        <h4 class="fw-bold text-dark">Bạn chưa có đơn hàng nào</h4>
                        <p class="text-muted mb-4">Hãy khám phá bộ sưu tập thời trang thể thao VAB mới nhất và chọn cho mình sản phẩm phù hợp nhé!</p>
                        <a href="index.php?page=products" class="btn btn-secondary rounded-pill px-5 py-2.5 fw-bold" style="background:var(--color-secondary); border:none;">
                            Mua sắm ngay <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>
