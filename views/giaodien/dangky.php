<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Đăng ký - VAB';
    $trangHienTai = 'register';
    include 'dautrang.php';
}
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo"><img src="assets/images/logo/logo1.png" alt="VAB Logo" style="height:50px;width:auto;"></div>
                <h3>Đăng ký tài khoản</h3>
                <p>Tham gia cùng VAB để nhận ưu đãi đặc biệt</p>
            </div>
            <?php if (isset($error)): ?>
            <div class="alert alert-danger py-2" style="font-size:13px;border-radius:8px;">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>
            <form method="POST" action="index.php?page=register">
                <div class="form-group"><label>Họ tên</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="form-group"><label>Số điện thoại</label><input type="tel" name="phone" class="form-control" required></div>
                <div class="form-group"><label>Mật khẩu</label><input type="password" name="password" id="register-password" class="form-control" required></div>
                <div class="form-group"><label>Nhập lại mật khẩu</label><input type="password" name="confirm-password" id="confirm-password" class="form-control" required></div>
                <button type="submit" class="btn-auth">Đăng ký</button>
            </form>
            <div class="auth-footer">
                Đã có tài khoản? <a href="index.php?page=login">Đăng nhập</a>
            </div>
        </div>
    </div>
</section>
<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>