<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Quản lý đánh giá - VAB Admin';
    $trangHienTai = 'admin-reviews';
    include 'admin-header.php';
}
$msg = $_GET['msg'] ?? '';
if ($msg): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= $msg == 'approved' ? '✅ Đã duyệt đánh giá!' : ($msg == 'rejected' ? '⛔ Đã cập nhật Không duyệt!' : ($msg == 'deleted' ? '🗑️ Đã xóa đánh giá!' : '')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<div class="admin-table-wrapper">
<div class="table-header">
<div class="table-actions review-filter-group">
    <button class="review-filter-btn active" data-status="">Tất cả</button>
    <button class="review-filter-btn pending-btn" data-status="pending">Chờ duyệt</button>
    <button class="review-filter-btn approved-btn" data-status="approved">Đã duyệt</button>
    <button class="review-filter-btn rejected-btn" data-status="rejected">Không duyệt</button>
</div>
</div>
<table class="admin-table" id="reviewsTable"><thead><tr><th>Mã SP</th><th>Sản phẩm</th><th>Khách hàng</th><th>Đánh giá</th><th>Nội dung</th><th>Trạng thái</th><th>Ngày</th><th>Hành động</th></tr></thead>
<tbody>
<?php if (empty($reviews)): ?>
<tr><td colspan="8" class="text-center py-4">Chưa có đánh giá nào</td></tr>
<?php else: ?>
<?php foreach($reviews as $r): ?>
<tr data-id="<?= $r['MaDG'] ?>" data-status="<?= $r['TrangThai'] ?>">
<td data-label="Mã SP"><small>SP<?= $r['MaSP'] ?></small></td>
<td data-label="Sản phẩm"><?= htmlspecialchars($r['TenSP'] ?? 'N/A') ?></td>
<td data-label="Khách hàng"><?= htmlspecialchars($r['HoTenKH']) ?></td>
<td data-label="Đánh giá">
    <div class="rating-stars">
        <?php for($i=1;$i<=5;$i++): ?>
            <i class="fas fa-star <?= $i <= $r['SoSao'] ? 'text-warning' : 'text-muted' ?>" style="font-size:12px;"></i>
        <?php endfor; ?>
    </div>
</td>
<td data-label="Nội dung"><small><?= htmlspecialchars(mb_substr($r['NoiDung']??'',0,80)) ?><?= mb_strlen($r['NoiDung']??'')>80?'...':'' ?></small></td>
<td data-label="Trạng thái">
    <span class="status-badge <?= $r['TrangThai'] ?>">
        <?= $r['TrangThai']=='pending'?'Chờ duyệt':($r['TrangThai']=='approved'?'Đã duyệt':'Không duyệt') ?>
    </span>
    <?php if (!empty($r['NguoiDuyet'])): ?>
        <br><small class="text-muted" style="font-size: 11px;"><i class="fas fa-user-check me-1"></i>Bởi: <?= htmlspecialchars($r['NguoiDuyet']) ?></small>
    <?php endif; ?>
</td>
<td data-label="Ngày"><small><?= date('d/m/Y', strtotime($r['NgayDG'])) ?></small></td>
<td data-label="Hành động"><div class="action-btns">
    <a href="index.php?page=admin-review-approve&id=<?= $r['MaDG'] ?>" class="btn-action btn-edit" title="Duyệt"><i class="fas fa-check"></i></a>
    <a href="index.php?page=admin-review-reject&id=<?= $r['MaDG'] ?>" class="btn-action btn-delete" title="Không duyệt"><i class="fas fa-times"></i></a>
</div></td></tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody></table>
</div>
<style>
.rating-stars { display:inline-flex; gap:1px; }
.review-filter-group { display:flex; gap:8px; flex-wrap:wrap; }
.review-filter-btn {
    padding: 6px 16px;
    border-radius: 20px;
    border: 1.5px solid var(--admin-border);
    background: transparent;
    color: var(--admin-text);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}
.review-filter-btn:hover { background: var(--admin-hover); }
.review-filter-btn.active { background: var(--admin-secondary); color:#fff; border-color: var(--admin-secondary); }
.review-filter-btn.pending-btn.active { background:#f59e0b; border-color:#f59e0b; }
.review-filter-btn.approved-btn.active { background:#10b981; border-color:#10b981; }
.review-filter-btn.rejected-btn.active { background:#ef4444; border-color:#ef4444; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btns = document.querySelectorAll('.review-filter-btn');
    btns.forEach(btn => {
        btn.addEventListener('click', function() {
            btns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const status = this.dataset.status;
            const rows = document.querySelectorAll('#reviewsTable tbody tr[data-status]');
            rows.forEach(row => {
                if (!status || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
</script>
<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>