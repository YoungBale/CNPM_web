<!-- ===== NEWSLETTER + MAP ===== -->
<section style="background:linear-gradient(135deg,#0a1628,#1a2744);padding:60px 0;color:#fff;border-bottom:3px solid rgba(242,92,25,0.3);overflow:hidden;">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6 text-center text-lg-start vab-reveal fade-right">
                <h3 style="font-size:28px;font-weight:800;margin-bottom:8px;letter-spacing:1px;">ĐĂNG KÝ NHẬN TIN</h3>
                <p style="font-size:15px;opacity:0.75;margin-bottom:22px;max-width:420px;">Nhận thông tin khuyến mãi và sản phẩm mới nhất từ VAB</p>
                <form style="display:flex;max-width:460px;border-radius:50px;overflow:hidden;box-shadow:0 6px 25px rgba(0,0,0,0.35);border:2px solid rgba(242,92,25,0.2);">
                    <input type="email" placeholder="Nhập email của bạn..." required style="flex:1;padding:14px 20px;border:none;outline:none;font-size:14px;color:#1a1a2e;">
                    <button type="submit" style="padding:14px 32px;border:none;background:linear-gradient(135deg,#f25c19,#ff6b35);color:#fff;font-weight:700;cursor:pointer;font-size:14px;white-space:nowrap;transition:all 0.3s;letter-spacing:0.5px;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">Đăng ký</button>
                </form>
            </div>
            <div class="col-lg-6 vab-reveal fade-left">
                <div style="border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.4);border:2px solid rgba(255,255,255,0.08);">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5610m!2d106.6357711!3d10.7760389!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ea3f9e915a3:0xe34b930cd78ff25!2s299%20Tr%E1%BB%8Bnh%20%C4%90%C3%ACnh%20Tr%E1%BB%8Dng%2C%20T%C3%A2n%20Ph%C3%BA%2C%20H%E1%BB%93%20Ch%C3%AD%20Minh!5e0!3m2!1svi!2s!4v1" width="100%" height="260" style="border:0;display:block;" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer" style="overflow:hidden;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 text-center text-lg-start vab-reveal fade-up" data-delay="1">
                <div class="footer-logo">
                    <img src="assets/images/logo/logo1.png" alt="VAB Logo" style="height:40px;width:auto;filter:brightness(0) invert(1);">
                </div>
                <p style="color:rgba(255,255,255,0.6);font-size:14px;margin-top:15px;line-height:1.8;max-width:320px;">
                    VAB là cửa hàng thời trang thể thao, cung cấp các sản phẩm chính hãng từ những thương hiệu thể thao nổi tiếng thế giới.
                </p>
            </div>
            <?php
            $footer_pdo = getConnection();
            $footerSports = $footer_pdo->query("SELECT * FROM THETHAO WHERE TrangThai = 1 ORDER BY TenSport ASC")->fetchAll();
            $footerBrands = $footer_pdo->query("SELECT * FROM THUONGHIEU WHERE TrangThai = 1 ORDER BY TenThuongHieu ASC")->fetchAll();
            ?>
            <div class="col-lg-2 col-md-6 col-6 text-center vab-reveal fade-up" data-delay="2">
                <h5>Danh mục</h5>
                <ul>
                    <?php foreach ($footerSports as $fs): ?>
                    <li><a href="index.php?page=<?= htmlspecialchars($fs['DuongDan']) ?>"><?= htmlspecialchars($fs['TenSport']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 col-6 text-center vab-reveal fade-up" data-delay="3">
                <h5>Thương hiệu</h5>
                <ul>
                    <?php foreach ($footerBrands as $fb): ?>
                    <li><a href="index.php?page=brand-<?= htmlspecialchars($fb['DuongDan']) ?>"><?= htmlspecialchars($fb['TenThuongHieu']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 col-6 text-center vab-reveal fade-up" data-delay="4">
                <h5>Hỗ trợ</h5>
                <ul>
                    <li><a href="index.php?page=login">Đăng nhập</a></li>
                    <li><a href="index.php?page=register">Đăng ký</a></li>
                    <li><a href="index.php?page=sale">Sản phẩm Sale</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 col-6 text-center">
                <h5>Liên hệ</h5>
                <ul class="contact-info" style="display:inline-block;text-align:left;">
                    <li><i class="fas fa-map-marker-alt"></i> 299 Trịnh Đình Trọng, Tân Phú, TP HCM</li>
                    <li><i class="fas fa-phone-alt"></i> 0359180209</li>
                    <li><i class="fas fa-envelope"></i> vabshop2026@gmail.com</li>
                    <li><i class="fas fa-clock"></i> 8:00 - 21:00 (T2 - CN)</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; 2026 <a href="index.php?page=home">VAB</a> - Thời Trang Thể Thao. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</footer>

<!-- Scroll to Top -->
<button id="scroll-to-top" class="scroll-to-top"><i class="fas fa-chevron-up"></i></button>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/script.js?v=<?= time() ?>"></script>
</body>
</html>