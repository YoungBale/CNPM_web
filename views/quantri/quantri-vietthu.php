<?php
if (!isset($nhungTuController)) {
    header('Location: index.php?page=admin-mail');
    exit;
}
?>

<div class="container-fluid py-4">
    <!-- TIÊU ĐỀ & QUAY LẠI -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-dark fw-bold">Gửi Email & Thông báo mới</h3>
        <a href="index.php?page=admin-mail" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    <!-- THÔNG BÁO LỖI -->
    <?php if (isset($_SESSION['mail_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius: 10px;">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= $_SESSION['mail_error']; unset($_SESSION['mail_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- FORM SOẠN THƯ -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4 p-md-5">
            <form action="index.php?page=admin-mail-store" method="POST">
                
                <!-- CHỌN KHÁCH HÀNG -->
                <div class="mb-4">
                    <label for="user_id" class="form-label fw-bold text-dark"><i class="fas fa-user me-2 text-muted"></i>Người nhận</label>
                    <select class="form-select" id="user_id" name="user_id" required style="border-radius: 8px; padding: 10px 15px;">
                        <option value="" disabled selected>-- Chọn khách hàng nhận thư --</option>
                        <option value="all" style="font-weight: bold; color: #f37021;">Gửi cho TẤT CẢ khách hàng (Gửi hàng loạt)</option>
                        <?php if (!empty($customers)): foreach ($customers as $c): ?>
                            <option value="<?= $c['MaKH'] ?>"><?= htmlspecialchars($c['HoTenKH']) ?> (<?= htmlspecialchars($c['email']) ?>)</option>
                        <?php endforeach; endif; ?>
                    </select>
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>Chọn gửi hàng loạt cho toàn bộ thành viên hoặc chọn một khách hàng cụ thể.
                    </div>
                </div>

                <!-- TIÊU ĐỀ -->
                <div class="mb-4">
                    <label for="subject" class="form-label fw-bold text-dark"><i class="fas fa-heading me-2 text-muted"></i>Tiêu đề thư</label>
                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Nhập tiêu đề email / thông báo..." required style="border-radius: 8px; padding: 10px 15px;">
                </div>

                <!-- NỘI DUNG -->
                <div class="mb-4">
                    <label for="content" class="form-label fw-bold text-dark"><i class="fas fa-align-left me-2 text-muted"></i>Nội dung thư</label>
                    <textarea class="form-control" id="content" name="content" rows="8" placeholder="Nhập nội dung thông báo gửi khách hàng tại đây..." required style="border-radius: 8px; padding: 15px;"></textarea>
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>Hệ thống sẽ đồng thời gửi một email thật tới Gmail của khách hàng và lưu một bản sao vào Hộp thư tài khoản của họ trên website VAB.
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="d-flex justify-content-end gap-3 mt-5">
                    <button type="reset" class="btn btn-light rounded-pill px-4 py-2 text-secondary fw-semibold">Xóa soạn thảo</button>
                    <button type="submit" class="btn btn-warning text-white rounded-pill px-5 py-2.5 fw-bold" style="background-color: #f37021; border-color: #f37021; font-size: 16px; box-shadow: 0 4px 12px rgba(243, 112, 33, 0.25);">
                        <i class="fas fa-paper-plane me-2"></i>Gửi thông báo
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
