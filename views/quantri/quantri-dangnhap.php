<?php
$tieuDeTrang = 'Admin Login - VAB';
$trangHienTai = 'admin-login';
$nhungTuController = true;
?>
<div class="admin-login-body">
<div class="admin-login-wrapper">
    <div class="admin-login-card">
        <div class="login-logo">
            <h2>VAB<span>.</span></h2>
            <p>Quản trị hệ thống</p>
        </div>
        <?php if (isset($error)): ?>
        <div class="alert alert-danger py-2" style="font-size:13px;border-radius:8px;">
            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" placeholder="admin@vab.com" required></div>
            <div class="form-group"><label>Mật khẩu</label><input type="password" name="password" class="form-control" placeholder="••••••••" required></div>
            <button type="submit" class="btn-login">Đăng nhập</button>
        </form>
        <div class="text-center mt-3"><a href="index.php" style="color:#999;font-size:13px;"><i class="fas fa-arrow-left me-1"></i>Về trang chủ</a></div>
        <div class="text-center mt-2"><small style="color:#bbb;">Tài khoản mẫu: admin@vab.com / password</small></div>
    </div>
</div>
<?php if (!isset($nhungTuController)): ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php endif; ?>
</div>