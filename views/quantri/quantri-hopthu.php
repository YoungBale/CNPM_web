<?php
if (!isset($nhungTuController)) {
    header('Location: index.php?page=admin-mail');
    exit;
}
?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div>
        <div class="page-header-title">Email & Thông báo</div>
        <div class="page-header-subtitle">Quản lý lịch sử gửi email cho khách hàng</div>
    </div>
    <a href="index.php?page=admin-mail-create" class="btn-primary-admin">
        <i class="fas fa-paper-plane"></i> Gửi thư mới
    </a>
</div>

<!-- Alert messages -->
<?php if (isset($_SESSION['mail_success'])): ?>
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-check-circle"></i>
    <?= $_SESSION['mail_success']; unset($_SESSION['mail_success']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['mail_smtp_errors'])): ?>
<div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
    <div>
        <strong><i class="fas fa-exclamation-triangle me-1"></i> Một số email gửi lỗi:</strong>
        <ul class="mb-0 mt-2 ps-3">
            <?php foreach ($_SESSION['mail_smtp_errors'] as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <?php unset($_SESSION['mail_smtp_errors']); ?>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Mail History Table -->
<div class="admin-table-wrapper">
    <div class="table-header">
        <h5><i class="fas fa-history"></i> Lịch sử gửi thư</h5>
        <span style="font-size:12px;color:var(--admin-muted);">
            <i class="fas fa-info-circle me-1"></i>Nhấn "Xem" để đọc nội dung thư đã gửi
        </span>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Mã KH</th>
                <th>Khách nhận</th>
                <th>Email</th>
                <th>Tiêu đề</th>
                <th>Trạng thái đọc</th>
                <th>Ngày gửi</th>
                <th style="text-align:right;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($mails)): foreach ($mails as $m): ?>
            <tr>
                <td data-label="Mã KH">
                    <span style="font-weight:700;color:var(--admin-secondary);font-size:12px;">KH<?= $m['MaKH'] ?></span>
                </td>
                <td data-label="Khách nhận">
                    <strong><?= htmlspecialchars($m['HoTenKH'] ?? 'Không rõ') ?></strong>
                </td>
                <td data-label="Email">
                    <small style="color:var(--admin-text-soft);"><?= htmlspecialchars($m['email'] ?? 'Không rõ') ?></small>
                </td>
                <td data-label="Tiêu đề" style="max-width:220px;">
                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block;font-weight:500;">
                        <?= htmlspecialchars($m['TieuDe']) ?>
                    </span>
                </td>
                <td data-label="Trạng thái đọc">
                    <?php if ($m['TrangThai'] == 1): ?>
                        <span class="status-badge active">Đã đọc</span>
                    <?php else: ?>
                        <span class="status-badge pending">Chưa đọc</span>
                    <?php endif; ?>
                </td>
                <td data-label="Ngày gửi">
                    <small style="color:var(--admin-text-soft);"><?= date('d/m/Y H:i', strtotime($m['NgayTao'])) ?></small>
                </td>
                <td data-label="Hành động" style="text-align:right;">
                    <button type="button" class="btn-action btn-view" data-bs-toggle="modal" data-bs-target="#mailModal<?= $m['MaThongBao'] ?>" title="Xem nội dung">
                        <i class="far fa-eye"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i class="far fa-envelope-open" style="font-size:56px;"></i>
                        <p style="font-size:15px;">Chưa có email hay thông báo nào được gửi</p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (!empty($mails)): foreach ($mails as $m): ?>
<!-- Mail Detail Modal -->
<div class="modal fade" id="mailModal<?= $m['MaThongBao'] ?>" tabindex="-1" aria-labelledby="mailModalLabel<?= $m['MaThongBao'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:var(--admin-radius);box-shadow:var(--admin-shadow-xl);">
            <div class="modal-header border-0 pb-0" style="padding:24px 28px 0;">
                <div>
                    <h5 class="modal-title fw-bold" id="mailModalLabel<?= $m['MaThongBao'] ?>" style="color:var(--admin-text);">
                        <i class="fas fa-envelope me-2" style="color:var(--admin-secondary);"></i>Chi tiết thư đã gửi
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:20px 28px 24px;">
                <div style="display:grid;gap:14px;">
                    <div style="background:var(--admin-bg);border-radius:var(--admin-radius-sm);padding:14px 16px;">
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--admin-muted);margin-bottom:4px;">Người nhận</div>
                        <strong style="color:var(--admin-text);"><?= htmlspecialchars($m['HoTenKH'] ?? 'Không rõ') ?></strong>
                        <span style="color:var(--admin-text-soft);font-size:13px;"> — <?= htmlspecialchars($m['email'] ?? 'Không rõ') ?></span>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div style="background:var(--admin-bg);border-radius:var(--admin-radius-sm);padding:12px 16px;">
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--admin-muted);margin-bottom:4px;">Ngày gửi</div>
                            <span style="font-weight:600;font-size:13px;"><?= date('d/m/Y H:i:s', strtotime($m['NgayTao'])) ?></span>
                        </div>
                        <div style="background:var(--admin-bg);border-radius:var(--admin-radius-sm);padding:12px 16px;">
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--admin-muted);margin-bottom:4px;">Trạng thái</div>
                            <?php if ($m['TrangThai'] == 1): ?>
                                <span class="status-badge active">Đã đọc</span>
                            <?php else: ?>
                                <span class="status-badge pending">Chưa đọc</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--admin-muted);margin-bottom:6px;">Tiêu đề</div>
                        <strong style="font-size:15px;color:var(--admin-text);"><?= htmlspecialchars($m['TieuDe']) ?></strong>
                    </div>
                    <div>
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--admin-muted);margin-bottom:8px;">Nội dung thư</div>
                        <div style="background:var(--admin-bg);border-radius:var(--admin-radius-sm);padding:16px;font-size:14px;line-height:1.7;color:var(--admin-text);white-space:pre-line;border:1px solid var(--admin-border);">
                            <?= nl2br(htmlspecialchars($m['NoiDung'])) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0" style="padding:0 28px 24px;">
                <button type="button" class="btn-secondary-admin" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Đóng
                </button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; endif; ?>
