<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Quản lý khách hàng - VAB Admin';
    $trangHienTai = 'admin-users';
    include 'admin-header.php';
}
$msg = $_GET['msg'] ?? '';
if ($msg): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= $msg == 'toggled' ? '✅ Đã cập nhật trạng thái tài khoản!' : '' ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div>
        <div class="page-header-title">Quản lý khách hàng</div>
        <div class="page-header-subtitle">Xem và quản lý tài khoản khách hàng</div>
    </div>
</div>

<div class="admin-table-wrapper">
<div class="table-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
<div class="table-actions" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
    <div class="user-search-box">
        <i class="fas fa-search user-search-icon"></i>
        <input type="search" id="userSearchInput" class="form-control" placeholder="Tìm tên, email, SĐT...">
    </div>
    <form method="GET" action="index.php" style="display:flex; gap:8px; align-items:center; margin:0;">
        <input type="hidden" name="page" value="admin-users">
        <select name="filter_bomb" class="form-select" style="min-width:200px;" onchange="this.form.submit()">
            <option value="all" <?= ($filter_bomb ?? 'all') === 'all' ? 'selected' : '' ?>>Tất cả khách hàng</option>
            <option value="bomb" <?= ($filter_bomb ?? 'all') === 'bomb' ? 'selected' : '' ?>>&#128680; Khách bom hàng (≥ 1 lần bị trả)</option>
            <option value="normal" <?= ($filter_bomb ?? 'all') === 'normal' ? 'selected' : '' ?>>&#9989; Khách bình thường</option>
        </select>
        <?php if (($filter_bomb ?? 'all') !== 'all'): ?>
            <a href="index.php?page=admin-users" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;"><i class="fas fa-undo"></i> Reset</a>
        <?php endif; ?>
    </form>
</div>
</div>
<table class="admin-table" id="usersTable"><thead><tr><th>Mã KH</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Hạng</th><th>Hủy / Bom</th><th>Trạng thái</th><th>Ngày tạo</th><th>Hành động</th></tr></thead>
<tbody id="usersTableBody">
<?php if (empty($users)): ?>
<tr><td colspan="9" class="text-center py-4">Chưa có khách hàng nào</td></tr>
<?php else: ?>
<?php 
$tierMap = [
    'silver'  => 'Bạc',
    'gold'    => 'Vàng',
    'diamond' => 'Kim cương'
];
foreach($users as $u):
    $cancelledCount = (int)($u['cancelled_orders'] ?? 0);
    $refusedCount = (int)($u['refused_orders'] ?? 0);
    $isBomber = $refusedCount >= 1;
    $isDangerBomber = $refusedCount >= 2;
?>
<tr data-id="<?= $u['MaKH'] ?>" <?= $isDangerBomber ? 'style="background:rgba(239,68,68,0.05);"' : ($isBomber ? 'style="background:rgba(245,158,11,0.05);"' : '') ?>>
<td data-label="Mã KH"><small>KH<?= $u['MaKH'] ?></small></td>
<td data-label="Họ tên">
    <strong><?= htmlspecialchars($u['HoTenKH']) ?></strong>
    <?php if ($isDangerBomber): ?>
        <span class="d-block" style="font-size:10px; color:#ef4444; font-weight:700; margin-top:2px;">&#128680; Khách bom hàng (Nguy hiểm)</span>
    <?php elseif ($isBomber): ?>
        <span class="d-block" style="font-size:10px; color:#d97706; font-weight:700; margin-top:2px;">&#9888; Cảnh báo bom (1 lần)</span>
    <?php endif; ?>
</td>
<td data-label="Email"><small><?= htmlspecialchars($u['email']) ?></small></td>
<td data-label="SĐT"><?= htmlspecialchars($u['SDT'] ?? '') ?></td>
<td data-label="Hạng">
    <span class="tier-badge tier-<?= $u['HangKH'] ?? 'silver' ?>">
        <i class="fas fa-<?= ($u['HangKH']??'silver')=='diamond'?'gem':(($u['HangKH']??'silver')=='gold'?'crown':'medal') ?>"></i>
        <?= $tierMap[$u['HangKH'] ?? 'silver'] ?? ucfirst($u['HangKH'] ?? 'silver') ?>
    </span>
</td>
<td data-label="Hủy / Bom">
    <div style="display:flex; flex-direction:column; gap:4px;">
        <span class="badge bg-secondary text-white fw-bold" style="font-size:10.5px; padding:3px 8px; border-radius:10px; width:fit-content;" title="Hủy chủ động">Hủy: <?= $cancelledCount ?></span>
        <span class="badge <?= $isDangerBomber ? 'bg-danger text-white' : ($isBomber ? 'bg-warning text-dark' : 'bg-light text-muted') ?> fw-bold" style="font-size:10.5px; padding:3px 8px; border-radius:10px; width:fit-content;" title="Từ chối nhận (Bom hàng)">Bom: <?= $refusedCount ?></span>
    </div>
</td>
<td data-label="Trạng thái"><span class="status-badge <?= $u['TrangThai']=='active'?'active':'inactive' ?>"><?= $u['TrangThai']=='active'?'Hoạt động':'Khóa' ?></span></td>
<td data-label="Ngày tạo"><small><?= date('d/m/Y', strtotime($u['NgayTao'])) ?></small></td>
<td data-label="Hành động"><div class="action-btns">
<a href="index.php?page=admin-users&toggle=<?= $u['MaKH'] ?>" 
   class="btn-action <?= $u['TrangThai']=='active'?'btn-delete':'btn-edit' ?>" 
   onclick="return confirm('<?= $u['TrangThai']=='active'?'Khóa':'Mở khóa' ?> tài khoản <?= addslashes($u['HoTenKH']) ?>?')"
   title="<?= $isBomber && $u['TrangThai']=='active' ? '&#9888; Khóa tài khoản bom hàng' : ($u['TrangThai']=='active'?'Khóa':'Mở khóa') ?>"
   style="<?= $isDangerBomber && $u['TrangThai']=='active' ? 'box-shadow: 0 0 0 2px #ef4444;' : ($isBomber && $u['TrangThai']=='active' ? 'box-shadow: 0 0 0 2px #f59e0b;' : '') ?>">
    <i class="fas <?= $u['TrangThai']=='active'?'fa-lock':'fa-unlock' ?>"></i>
</a>
</div></td></tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody></table>
<div id="noUserResult" class="text-center py-4" style="display:none;">Không tìm thấy khách hàng nào</div>
</div>
<style>
.tier-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
.tier-diamond { background:linear-gradient(135deg,#e8f4fd,#b3e0ff); color:#0099ff; border:1px solid #66c2ff; }
.tier-gold { background:linear-gradient(135deg,#fff8e1,#ffe082); color:#f57f17; border:1px solid #ffb300; }
.tier-silver { background:linear-gradient(135deg,#f5f5f5,#e0e0e0); color:#616161; border:1px solid #bdbdbd; }
.user-search-box { position:relative; }
.user-search-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--admin-muted); pointer-events:none; font-size:13px; }
.user-search-box .form-control { padding-left:34px; width:260px; border-radius:8px; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('userSearchInput');
    if (!input) return;
    input.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#usersTableBody tr[data-id]');
        let found = 0;
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (!q || text.includes(q)) {
                row.style.display = '';
                found++;
            } else {
                row.style.display = 'none';
            }
        });
        document.getElementById('noUserResult').style.display = found === 0 ? '' : 'none';
    });
});
</script>
<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>