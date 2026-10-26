<?php
if (!isset($nhungTuController)) {
    header('Location: index.php?page=inbox');
    exit;
}
?>

<!-- BREADCRUMB -->
<section class="breadcrumb-section" style="background: var(--color-bg-light); padding: 30px 0; border-bottom: 1px solid #eee;">
    <div class="container">
        <h1 class="fw-bold text-dark mb-1" style="font-size: 28px;">Hộp thư của tôi</h1>
        <nav>
            <ol class="breadcrumb mb-0" style="background: none; padding: 0;">
                <li class="breadcrumb-item"><a href="index.php?page=home" class="text-decoration-none" style="color: var(--color-primary);">Trang chủ</a></li>
                <li class="breadcrumb-item active text-secondary" aria-current="page">Hộp thư</li>
            </ol>
        </nav>
    </div>
</section>

<!-- HỘP THƯ CHIA ĐÔI BỐ CỤC (2 CỘT) -->
<section class="py-5" style="background-color: #f8f9fa; min-height: 500px;">
    <div class="container">
        <div class="row g-4">
            
            <!-- CỘT TRÁI: DANH SÁCH THƯ -->
            <div class="col-lg-5 col-md-12">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-inbox me-2 text-muted"></i>Hộp thư đến</h5>
                        <?php 
                        $unreadCount = 0;
                        if (!empty($inboxMails)) {
                            foreach ($inboxMails as $m) {
                                if ($m['is_read'] == 0) $unreadCount++;
                            }
                        }
                        ?>
                        <?php if ($unreadCount > 0): ?>
                            <span class="badge bg-danger rounded-pill px-2.5 py-1.5" style="font-size: 11px;"><?= $unreadCount ?> chưa đọc</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="list-group list-group-flush" style="max-height: 480px; overflow-y: auto;">
                        <?php if (!empty($inboxMails)): foreach ($inboxMails as $m): ?>
                            <?php 
                            $isCurrentMail = ($selectedMail && $selectedMail['id'] === $m['id']);
                            $bgClass = $isCurrentMail ? 'bg-warning-subtle' : ($m['is_read'] == 0 ? 'bg-white font-weight-bold' : 'bg-white');
                            ?>
                            <a href="index.php?page=inbox&id=<?= $m['id'] ?>" class="list-group-item list-group-item-action py-3 border-bottom <?= $bgClass ?>" style="transition: all 0.2s;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark" style="font-size: 14px;">
                                        <?php if ($m['is_read'] == 0): ?>
                                            <i class="fas fa-envelope text-warning me-2"></i>
                                        <?php else: ?>
                                            <i class="fas fa-envelope-open text-muted me-2"></i>
                                        <?php endif; ?>
                                        Admin VAB
                                    </span>
                                    <small class="text-muted" style="font-size: 11px;"><?= date('d/m H:i', strtotime($m['created_at'])) ?></small>
                                </div>
                                <div class="text-truncate fw-semibold text-dark" style="font-size: 13.5px; padding-left: 22px;">
                                    <?= htmlspecialchars($m['subject']) ?>
                                </div>
                                <div class="text-muted text-truncate" style="font-size: 12px; padding-left: 22px;">
                                    <?= htmlspecialchars(mb_substr($m['content'], 0, 60)) ?>...
                                </div>
                            </a>
                        <?php endforeach; else: ?>
                            <div class="text-center py-5 text-muted">
                                <i class="far fa-envelope-open fa-3x mb-3 text-secondary"></i>
                                <p class="mb-0">Hộp thư của bạn đang trống.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: CHI TIẾT NỘI DUNG THƯ -->
            <div class="col-lg-7 col-md-12">
                <?php if ($selectedMail): ?>
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                                <div>
                                    <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($selectedMail['subject']) ?></h4>
                                    <span class="text-muted" style="font-size: 13px;">
                                        <i class="far fa-user me-1"></i>Người gửi: <strong>Quản trị viên VAB</strong>
                                    </span>
                                </div>
                                <span class="text-secondary" style="font-size: 13px;">
                                    <i class="far fa-clock me-1"></i><?= date('d/m/Y H:i:s', strtotime($selectedMail['created_at'])) ?>
                                </span>
                            </div>
                            
                            <!-- NỘI DUNG THƯ CHÍNH -->
                            <div class="mail-content p-4 bg-light rounded" style="font-size: 15px; line-height: 1.8; color: #333; white-space: pre-line;">
                                <?= nl2br(htmlspecialchars($selectedMail['content'])) ?>
                            </div>

                            <div class="mt-5 border-top pt-4 text-end">
                                <a href="index.php?page=inbox" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="fas fa-times me-1"></i>Đóng nội dung
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; min-height: 350px;">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-5 text-muted">
                            <div class="icon-wrap p-4 bg-light rounded-circle mb-4">
                                <i class="far fa-envelope fa-4x text-secondary"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Hãy chọn một thư ở danh sách bên trái</h5>
                            <p class="mb-0" style="max-width: 320px;">Bấm chọn bất kỳ tiêu đề thông báo nào để xem chi tiết nội dung và cập nhật trạng thái.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
