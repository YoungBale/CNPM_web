<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Đăng nhập - VAB';
    $trangHienTai = 'login';
    include 'dautrang.php';
}
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo"><img src="assets/images/logo/logo1.png" alt="VAB Logo" style="height:50px;width:auto;"></div>
                <h3>Đăng nhập</h3>

            </div>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger py-2" style="font-size:13px;border-radius:8px;">
                    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="index.php?page=login">
                <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="form-group"><label>Mật khẩu</label><input type="password" name="password" class="form-control" required></div>
                <div class="form-group d-flex justify-content-between">
                    <label><input type="checkbox" checked> Ghi nhớ đăng nhập</label>
                    <a href="#" class="text-secondary" style="font-size:13px;">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="btn-auth">Đăng nhập</button>
            </form>

            <!-- Separator -->
            <div class="my-4 text-center text-muted position-relative">
                <hr class="my-0">
                <span class="position-absolute top-50 start-50 translate-middle px-3" style="font-size: 13px; background-color: var(--color-white); color: var(--color-gray);">Hoặc đăng nhập bằng</span>
            </div>

            <?php
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            $googleCallbackUrl = $protocol . $host . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/index.php?page=google-login';
            ?>
            <!-- Google Sign-in Button (Callback URL: <?= htmlspecialchars($googleCallbackUrl) ?>) -->
            <div class="d-flex justify-content-center mb-3">
                <script src="https://accounts.google.com/gsi/client" async defer></script>
                <!-- CHÚ Ý: Thay Client ID bằng mã thật từ Google Cloud Console của bạn -->
                <div id="g_id_onload"
                    data-client_id="830643746255-m01ihe0o6c8osqh3mccqof94t43hbchs.apps.googleusercontent.com"
                    data-context="signin"
                    data-ux_mode="redirect"
                    data-login_uri="<?= htmlspecialchars($googleCallbackUrl) ?>"
                    data-auto_prompt="false">
                </div>
                <div class="g_id_signin"
                    data-type="standard"
                    data-shape="pill"
                    data-theme="outline"
                    data-text="signin_with"
                    data-size="large"
                    data-logo_alignment="left"
                    data-width="320">
                </div>
            </div>

            <div class="auth-footer">
                Chưa có tài khoản? <a href="index.php?page=register">Đăng ký ngay</a>
            </div>
        </div>
    </div>
</section>
<?php if (!isset($nhungTuController)) {
    include 'chantrang.php';
} ?>