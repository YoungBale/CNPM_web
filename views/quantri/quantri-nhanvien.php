<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Quản lý nhân viên - VAB Admin';
    $trangHienTai = 'admin-employees';
    include 'admin-header.php';
}
$msg = $_GET['msg'] ?? '';
if ($msg): ?>
<div class="alert alert-success alert-dismissible fade show mb-4">
    <i class="fas fa-check-circle me-1"></i>
    <?= $msg == 'added' ? 'Thêm nhân viên thành công!' : ($msg == 'toggled' ? 'Đã cập nhật trạng thái!' : '') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div>
        <div class="page-header-title">Quản lý nhân viên</div>
        <div class="page-header-subtitle">Quản lý tài khoản và phân quyền nhân viên</div>
    </div>
    <button class="btn-primary-admin" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
        <i class="fas fa-plus"></i> Thêm nhân viên
    </button>
</div>

<div class="admin-table-wrapper">
<div class="table-header">
    <h5><i class="fas fa-user-tie"></i> Danh sách nhân viên</h5>
</div>
<table class="admin-table"><thead><tr><th>Mã NV</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Vai trò</th><th>Trạng thái</th><th>Ngày tạo</th><th>Hành động</th></tr></thead>
<tbody>
<?php if (empty($employees)): ?>
<tr><td colspan="8"><div class="empty-state"><i class="fas fa-user-tie"></i><p>Chưa có nhân viên nào</p></div></td></tr>
<?php else: ?>
<?php
$roleMap = ['admin' => 'Quản trị viên', 'manager' => 'Quản lý', 'staff' => 'Nhân viên'];
foreach($employees as $e): ?>
<tr>
<td data-label="Mã NV"><small style="font-weight:700;color:var(--admin-secondary);">NV<?= $e['MaNV'] ?></small></td>
<td data-label="Họ tên"><strong><?= htmlspecialchars($e['HoTenNV']) ?></strong></td>
<td data-label="Email"><small style="color:var(--admin-text-soft);"><?= htmlspecialchars($e['email']) ?></small></td>
<td data-label="SĐT"><?= htmlspecialchars($e['SDT'] ?? '') ?></td>
<td data-label="Vai trò"><span class="status-badge <?= $e['VaiTro']=='admin'?'completed':($e['VaiTro']=='manager'?'active':'confirmed') ?>"><?= $roleMap[$e['VaiTro']] ?? ucfirst($e['VaiTro']) ?></span></td>
<td data-label="Trạng thái"><span class="status-badge <?= $e['TrangThai']=='active'?'active':'inactive' ?>"><?= $e['TrangThai']=='active'?'Hoạt động':'Khóa' ?></span></td>
<td data-label="Ngày tạo"><small style="color:var(--admin-text-soft);"><?= date('d/m/Y', strtotime($e['NgayLapTK'])) ?></small></td>
<td data-label="Hành động"><div class="action-btns">
<a href="index.php?page=admin-employees&toggle=<?= $e['MaNV'] ?>" class="btn-action <?= $e['TrangThai']=='active'?'btn-delete':'btn-edit' ?>" onclick="return confirm('<?= $e['TrangThai']=='active'?'Khóa':'Mở khóa' ?> nhân viên <?= addslashes($e['HoTenNV']) ?>?')"><i class="fas <?= $e['TrangThai']=='active'?'fa-lock':'fa-unlock' ?>"></i></a>
</div></td></tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody></table>
</div>

<!-- Modal Thêm nhân viên -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1">
<div class="modal-dialog">
    <div class="modal-content border-0" style="border-radius:var(--admin-radius);box-shadow:var(--admin-shadow-xl);">
        <div class="modal-header border-0" style="padding:24px 28px 0;">
            <h5 class="modal-title fw-bold" style="color:var(--admin-text);">
                <i class="fas fa-user-tie me-2" style="color:var(--admin-secondary);"></i>Thêm nhân viên mới
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST">
        <div class="modal-body" style="padding:20px 28px;">
            <div class="form-group mb-3"><label>Họ tên</label><input type="text" name="name" class="form-control" required></div>
            <div class="form-group mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
            <div class="form-group mb-3"><label>Số điện thoại</label><input type="text" name="phone" class="form-control"></div>
            <div class="form-group mb-3"><label>Mật khẩu</label><input type="password" name="password" class="form-control" placeholder="Mặc định: 123456"></div>
            <div class="form-group mb-0"><label>Vai trò</label>
                <select name="role" class="form-select">
                    <option value="staff">Nhân viên</option>
                    <option value="manager">Quản lý</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>
        <div class="modal-footer border-0" style="padding:0 28px 24px;">
            <button type="button" class="btn-secondary-admin" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" name="add_employee" class="btn-primary-admin"><i class="fas fa-plus me-1"></i>Thêm nhân viên</button>
        </div>
        </form>
    </div>
</div>
</div>

<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>