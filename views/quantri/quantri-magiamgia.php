<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Quản lý mã giảm giá - VAB Admin';
    $trangHienTai = 'admin-vouchers';
    include 'admin-header.php';
}
$msg = $_GET['msg'] ?? '';
if ($msg): ?>
<div class="alert <?= strpos($msg, 'error') !== false ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show">
    <?= $msg == 'added' ? '✅ Thêm mã giảm giá thành công!' : 
       ($msg == 'deleted' ? '🗑️ Đã xóa mã giảm giá!' : 
       ($msg == 'error_duplicate' ? '❌ Mã giảm giá này đã tồn tại trong hệ thống!' : '')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div>
        <div class="page-header-title">Mã giảm giá</div>
        <div class="page-header-subtitle">Thêm, quản lý và kiểm soát các mã giảm giá</div>
    </div>
    <button class="btn-primary-admin" data-bs-toggle="modal" data-bs-target="#addVoucherModal">
        <i class="fas fa-plus"></i> Thêm mã giảm giá
    </button>
</div>

<div class="admin-table-wrapper">
<div class="table-header">
    <h5><i class="fas fa-ticket-alt"></i> Danh sách mã giảm giá</h5>
</div>

<table class="admin-table"><thead><tr><th>Mã</th><th>Tên</th><th>Loại</th><th>Giá trị</th><th>Đơn tối thiểu</th><th>Số lượt</th><th>Đã dùng</th><th>Thời hạn</th><th>Trạng thái</th><th>Hành động</th></tr></thead>

<tbody>
<?php if (empty($vouchers)): ?>
<tr><td colspan="10" class="text-center py-4">Chưa có mã giảm giá nào</td></tr>
<?php else: ?>
<?php foreach($vouchers as $v): ?>
<?php 
$now = date('Y-m-d');
$code = (string)($v['MaGiamGia'] ?? $v['MAGIAMGIA'] ?? '');
$ten = (string)($v['Ten'] ?? '');
$expired = ($v['NgayKT'] ?? '') < $now;
$status = $v['TrangThai'] ?? 'active';
if ($expired && $status == 'active') $status = 'expired';
?>
<tr data-id="<?= htmlspecialchars($code) ?>">
<td data-label="Mã"><strong class="text-uppercase"><?= htmlspecialchars($code) ?></strong></td>
<td data-label="Tên"><?= htmlspecialchars($ten) ?></td>
<td data-label="Loại"><?= ($v['Loai'] ?? 'percent')=='percent'?'%':'VNĐ' ?></td>
<td data-label="Giá trị"><?= ($v['Loai'] ?? 'percent')=='percent' ? ($v['GiaTri'] ?? 0).'%' : number_format((float)($v['GiaTri'] ?? 0),0,',',',') . 'đ' ?></td>
<td data-label="Đơn tối thiểu"><?= number_format((float)($v['DonHangToiThieu'] ?? 0),0,',',',') ?>đ</td>
<td data-label="Số lượt"><?= $v['SoLuot'] ?? 0 ?></td>
<td data-label="Đã dùng"><?= $v['DaDung'] ?? 0 ?></td>
<td data-label="Thời hạn"><small><?= !empty($v['NgayBD']) ? date('d/m/Y', strtotime($v['NgayBD'])) : '' ?> - <?= !empty($v['NgayKT']) ? date('d/m/Y', strtotime($v['NgayKT'])) : '' ?></small></td>
<td data-label="Trạng thái"><span class="status-badge <?= $status ?>"><?= $status=='active'?'Hoạt động':($status=='expired'?'Hết hạn':'Ẩn') ?></span></td>
<td data-label="Hành động"><div class="action-btns">
<a href="index.php?page=admin-vouchers&delete=<?= urlencode($code) ?>" class="btn-action btn-delete" onclick="return confirm('Xóa mã giảm giá <?= addslashes($code) ?>?')" title="Xóa"><i class="fas fa-trash"></i></a>
</div></td></tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody></table>
</div>

<div class="modal fade" id="addVoucherModal" tabindex="-1">
<div class="modal-dialog modal-lg">
    <div class="modal-content border-0" style="border-radius:var(--admin-radius);box-shadow:var(--admin-shadow-xl);">
        <div class="modal-header border-0" style="padding:24px 28px 0;">
            <h5 class="modal-title fw-bold" style="color:var(--admin-text);">
                <i class="fas fa-ticket-alt me-2" style="color:var(--admin-secondary);"></i>Thêm mã giảm giá mới
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST">
<div class="modal-body">
<div class="row">
<div class="col-md-6"><div class="form-group mb-3"><label>Mã giảm giá</label><input type="text" name="code" class="form-control" placeholder="VD: SALE50" required></div></div>
<div class="col-md-6"><div class="form-group mb-3"><label>Tên mã giảm giá</label><input type="text" name="name" class="form-control" required></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="form-group mb-3"><label>Loại</label><select name="type" class="form-select"><option value="percent">%</option><option value="fixed">VNĐ</option></select></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label>Giá trị</label><input type="number" name="value" class="form-control" required></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label>Đơn tối thiểu</label><input type="number" name="min_order" class="form-control" value="0"></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="form-group mb-3"><label>Số lượt tối đa</label><input type="number" name="max_use" class="form-control" value="100"></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label>Ngày bắt đầu</label><input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>"></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label>Ngày kết thúc</label><input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>"></div></div>
</div>
</div>
<div class="modal-footer border-0" style="padding:0 28px 24px;">
<button type="button" class="btn-secondary-admin" data-bs-dismiss="modal">Hủy</button>
<button type="submit" name="add_voucher" class="btn-primary-admin"><i class="fas fa-plus me-1"></i>Tạo mã giảm giá</button>
</div>
</form>
</div></div></div>

<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>