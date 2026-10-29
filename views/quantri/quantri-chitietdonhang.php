<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Chi tiết đơn hàng - VAB Admin';
    $trangHienTai = 'admin-orders';
    include 'admin-header.php';
}
$msg = $_GET['msg'] ?? '';
?>
<?php if ($msg): ?>
<div class="alert alert-<?= $msg == 'updated' ? 'success' : 'danger' ?> alert-dismissible fade show">
    <?= $msg == 'updated' ? '✅ Đã cập nhật trạng thái đơn hàng!' : ($msg == 'error_invalid_transition' ? '⚠️ Trạng thái đơn hàng không hợp lệ hoặc không được phép lùi trạng thái!' : '') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<div class="row g-3">
<div class="col-lg-6">
<div class="admin-table-wrapper">
<h5 class="mb-3">Thông tin đơn hàng</h5>
<table class="admin-table">
    <tr><th style="width:150px;">Mã đơn hàng</th><td>#ORD<?= str_pad($order['MaDonHang'],3,'0',STR_PAD_LEFT) ?></td></tr>
    <tr><th>Khách hàng</th><td><?= htmlspecialchars($order['customer_name'] ?? $order['HoTenKH']) ?></td></tr>
    <tr><th>Email</th><td><?= htmlspecialchars($order['email']) ?></td></tr>
    <tr><th>Số điện thoại</th><td><?= htmlspecialchars($order['SDT']) ?></td></tr>
    <tr><th>Địa chỉ</th><td><?= htmlspecialchars($order['DiaChi']) ?></td></tr>
    <tr><th>Phương thức thanh toán</th><td><?= htmlspecialchars($order['PhuongThucTT']) ?></td></tr>
    <tr><th>Ghi chú</th><td><?= htmlspecialchars($order['GhiChu'] ?? 'Không có') ?></td></tr>
    <?php if (!empty($order['LyDoTraHang'])): ?>
        <tr><th>Lý do trả hàng</th><td class="text-danger fw-bold"><?= htmlspecialchars($order['LyDoTraHang']) ?></td></tr>
    <?php endif; ?>
    <tr><th>Ngày đặt</th><td><?= date('d/m/Y H:i', strtotime($order['NgayTao'])) ?></td></tr>
    <tr><th>Nhân viên xử lý</th><td><?= htmlspecialchars($order['NguoiXuLy'] ?? 'Chưa xác định') ?></td></tr>
    <tr><th>Trạng thái</th>
        <td>
            <span class="status-badge <?= $order['TrangThaiDH'] ?>">
                <?php 
                $sm = ['pending'=>'Chờ xác nhận','confirmed'=>'Đã thanh toán','shipping'=>'Đang giao','delivered'=>'Đã giao','completed'=>'Giao thành công','returning'=>'Yêu cầu trả hàng','returned'=>'Đã trả hàng','cancelled'=>'Đã hủy','refused'=>'Từ chối nhận'];
                echo $sm[$order['TrangThaiDH']] ?? $order['TrangThaiDH'];
                ?>
            </span>
        </td>
    </tr>
    <tr><th>Cập nhật trạng thái</th>
        <td>
            <?php
            $allowedTransitions = [
                'pending'   => ['confirmed', 'shipping', 'delivered', 'completed', 'cancelled'],
                'confirmed' => ['shipping', 'delivered', 'completed', 'cancelled'],
                'shipping'  => ['delivered', 'completed', 'cancelled', 'refused'],
                'delivered' => ['completed', 'cancelled', 'returning', 'refused'],
                'completed' => ['cancelled', 'returning'],
                'cancelled' => [],
                'returning' => ['returned'],
                'returned'  => [],
                'refused'   => []
            ];
            $currentStatus = $order['TrangThaiDH'];
            $nextStates = $allowedTransitions[$currentStatus] ?? [];
            ?>
            <form method="POST" class="d-flex gap-2 align-items-center">
                <select name="status" class="form-select form-select-sm" style="width:180px;" <?= empty($nextStates) ? 'disabled' : '' ?>>
                    <option value="pending"   <?= $currentStatus=='pending'   ?'selected':'' ?><?= ($currentStatus !== 'pending'   && !in_array('pending',   $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Chờ xác nhận</option>
                    <option value="confirmed" <?= $currentStatus=='confirmed' ?'selected':'' ?><?= ($currentStatus !== 'confirmed' && !in_array('confirmed', $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Đã thanh toán</option>
                    <option value="shipping"  <?= $currentStatus=='shipping'  ?'selected':'' ?><?= ($currentStatus !== 'shipping'  && !in_array('shipping',  $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Đang giao</option>
                    <option value="delivered" <?= $currentStatus=='delivered' ?'selected':'' ?><?= ($currentStatus !== 'delivered' && !in_array('delivered', $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Đã giao</option>
                    <option value="completed" <?= $currentStatus=='completed' ?'selected':'' ?><?= ($currentStatus !== 'completed' && !in_array('completed', $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Giao thành công</option>
                    <option value="returning" <?= $currentStatus=='returning' ?'selected':'' ?><?= ($currentStatus !== 'returning' && !in_array('returning', $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Yêu cầu trả hàng</option>
                    <option value="returned"  <?= $currentStatus=='returned'  ?'selected':'' ?><?= ($currentStatus !== 'returned'  && !in_array('returned',  $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Đã trả hàng</option>
                    <option value="cancelled" <?= $currentStatus=='cancelled' ?'selected':'' ?><?= ($currentStatus !== 'cancelled' && !in_array('cancelled', $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Đã hủy</option>
                    <option value="refused"   <?= $currentStatus=='refused'   ?'selected':'' ?><?= ($currentStatus !== 'refused'   && !in_array('refused',   $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Từ chối nhận</option>
                </select>
                <button type="submit" name="update_status" class="btn btn-primary btn-sm" style="background:var(--admin-secondary);border:none;" <?= empty($nextStates) ? 'disabled' : '' ?>>Cập nhật</button>
            </form>
        </td>
    </tr>
</table>
</div>
</div>
<div class="col-lg-6">
<div class="admin-table-wrapper">
<h5 class="mb-3">Sản phẩm trong đơn hàng</h5>
<table class="admin-table">
<thead><tr><th>Sản phẩm</th><th>SL</th><th>Giá</th><th>Thành tiền</th></tr></thead>
<tbody>
<?php 
if (!class_exists('ProductModel')) {
    require_once './models/ProductModel.php';
}
$subtotal = 0;
foreach($items as $item): 
$lineTotal = $item['SoLuongSP'] * $item['DonGia'];
$subtotal += $lineTotal;
?>
<tr>
<td data-label="Sản phẩm">
    <div class="d-flex align-items-center gap-3">
        <img src="<?= ProductModel::getImageUrl($item, 80, 80) ?>" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #ddd;" alt="">
        <div>
            <small class="fw-semibold" style="display:block; font-size:13px; line-height:1.4;"><?= htmlspecialchars($item['TenSP']) ?></small>
            <?php if (!empty($item['KichCo'])): ?>
                <span class="badge bg-secondary text-white rounded-pill px-2 py-0.5" style="font-size:10px;display:inline-block;margin-top:4px;">Size: <?= htmlspecialchars($item['KichCo']) ?></span>
            <?php endif; ?>
        </div>
    </div>
</td>
<td data-label="Số lượng" class="text-center"><?= $item['SoLuongSP'] ?></td>
<td data-label="Đơn giá"><?= number_format($item['DonGia'],0,',',',') ?>đ</td>
<td data-label="Thành tiền"><?= number_format($lineTotal,0,',',',') ?>đ</td>
</tr>
<?php endforeach; ?>
</tbody>
<tfoot>
<tr><th colspan="3" class="text-end">Tạm tính:</th><th><?= number_format($subtotal,0,',',',') ?>đ</th></tr>
<?php if (!empty($order['MaGiamGia'])): ?>
<tr><th colspan="3" class="text-end">Mã giảm giá (<?= htmlspecialchars($order['MaGiamGia']) ?>):</th><th class="text-danger">-<?= number_format($order['SoTienGiam'],0,',',',') ?>đ</th></tr>
<?php endif; ?>
<tr><th colspan="3" class="text-end">Phí vận chuyển:</th><th>Miễn phí</th></tr>
<tr><th colspan="3" class="text-end">Tổng cộng:</th><th class="fw-bold text-danger"><?= number_format($order['TongTien'],0,',',',') ?>đ</th></tr>
</tfoot>
</table>
</div>
</div>
</div>
<div class="mt-3">
<a href="index.php?page=admin-orders" class="btn btn-secondary" style="background:var(--admin-muted);border:none;"><i class="fas fa-arrow-left me-2"></i>Quay lại</a>
</div>
<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>