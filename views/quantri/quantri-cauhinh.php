<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Cài đặt - VAB Admin';
    $trangHienTai = 'admin-settings';
    include 'admin-header.php';
}
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
        <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
        <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="admin-form-wrapper">
            <h5 class="mb-4"><i class="fas fa-user-cog me-2"></i>Thông tin tài khoản</h5>
            <form method="POST" action="index.php?page=admin-settings">
                <div class="form-group">
                    <label>Họ tên</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($employee['HoTenNV'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($employee['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($employee['SDT'] ?? '') ?>">
                </div>
                <div class="form-actions">
                    <button type="submit" name="update_info" class="btn-submit"><i class="fas fa-save me-2"></i>Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="admin-form-wrapper">
            <h5 class="mb-4"><i class="fas fa-lock me-2"></i>Đổi mật khẩu</h5>
            <form method="POST" action="index.php?page=admin-settings">
                <div class="form-group">
                    <label>Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Mật khẩu mới</label>
                    <input type="password" name="new_password" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Xác nhận mật khẩu</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                </div>
                <div class="form-actions">
                    <button type="submit" name="change_password" class="btn-submit" style="background:#f25c19;"><i class="fas fa-key me-2"></i>Đổi mật khẩu</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>