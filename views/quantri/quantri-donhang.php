<?php if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Quản lý đơn hàng - VAB Admin';
    $trangHienTai = 'admin-orders';
    include 'admin-header.php';
}
?>
<?php $msg = $_GET['msg'] ?? ''; ?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div>
        <div class="page-header-title">Quản lý đơn hàng</div>
        <div class="page-header-subtitle">Theo dõi và cập nhật trạng thái các đơn hàng</div>
    </div>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg == 'updated' ? 'success' : 'danger' ?> alert-dismissible fade show mb-4">
    <i class="fas fa-<?= $msg == 'updated' ? 'check-circle' : 'exclamation-triangle' ?> me-1"></i>
    <?= $msg == 'updated' ? 'Đã cập nhật trạng thái đơn hàng thành công!' : ($msg == 'error_invalid_transition' ? 'Trạng thái đơn hàng không hợp lệ hoặc không được phép lùi trạng thái!' : '') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<div class="admin-table-wrapper" id="ordersTableWrapper">
<div class="table-header">
<div class="table-actions order-toolbar">
    <div class="order-search-box">
        <i class="fas fa-search order-search-icon"></i>
        <input type="search" id="orderSearchInput" class="form-control" placeholder="Tìm mã ĐH, khách hàng, SĐT...">
    </div>
    <div class="order-filter-pills">
        <button class="order-pill active" data-status="">Tất cả</button>
        <button class="order-pill pending-pill"   data-status="pending">Chờ xác nhận</button>
        <button class="order-pill confirmed-pill" data-status="confirmed">Đã thanh toán</button>
        <button class="order-pill shipping-pill"  data-status="shipping">Đang giao</button>
        <button class="order-pill delivered-pill" data-status="delivered">Đã giao</button>
        <button class="order-pill completed-pill" data-status="completed">Thành công</button>
        <button class="order-pill returning-pill" data-status="returning">Yêu cầu trả</button>
        <button class="order-pill returned-pill"  data-status="returned">Đã trả</button>
        <button class="order-pill cancelled-pill" data-status="cancelled">Đã hủy</button>
        <button class="order-pill refused-pill"   data-status="refused">Từ chối nhận</button>
    </div>
</div>
</div>
<table class="admin-table" id="ordersTable">
<thead><tr><th>Mã ĐH</th><th>Khách hàng</th><th>SĐT</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Thanh toán</th><th>Trạng thái</th><th>Hành động</th></tr></thead>
<tbody id="ordersTableBody">
<?php if (empty($orders)): ?>
<tr><td colspan="8" class="text-center py-4">Chưa có đơn hàng nào</td></tr>
<?php else: ?>
<?php
$statusMap = [
    'pending'   => 'Chờ xác nhận',
    'confirmed' => 'Đã thanh toán',
    'shipping'  => 'Đang giao',
    'delivered' => 'Đã giao',
    'completed' => 'Giao thành công',
    'returning' => 'Yêu cầu trả hàng',
    'returned'  => 'Đã trả hàng',
    'cancelled' => 'Đã hủy',
    'refused'   => 'Từ chối nhận'
];
foreach ($orders as $o):
    $pm      = strtolower($o['PhuongThucTT'] ?? 'cod');
    $pmLabel = strtoupper($o['PhuongThucTT'] ?? 'COD');
    $isCash  = ($pm === 'cod');
    $isPaid  = in_array($o['TrangThaiDH'], ['confirmed', 'shipping', 'delivered', 'completed']);

    if ($isCash) {
        $payBadge = '<span class="pay-badge pay-cod"><i class="fas fa-truck"></i> COD</span>';
    } elseif ($isPaid) {
        $payBadge = '<span class="pay-badge pay-paid"><i class="fas fa-check-circle"></i> Đã thanh toán</span>';
    } else {
        $payBadge = '<span class="pay-badge pay-unpaid"><i class="fas fa-clock"></i> Chưa thanh toán</span>';
    }
?>
<tr data-id="<?= $o['MaDonHang'] ?>" data-status="<?= $o['TrangThaiDH'] ?>">
    <td data-label="Mã ĐH"><small class="fw-bold">#ORD<?= str_pad($o['MaDonHang'], 3, '0', STR_PAD_LEFT) ?></small></td>
    <td data-label="Khách hàng"><?= htmlspecialchars($o['customer_name'] ?? $o['HoTenKH']) ?></td>
    <td data-label="SĐT"><?= htmlspecialchars($o['SDT']) ?></td>
    <td data-label="Ngày đặt"><?= date('d/m/Y', strtotime($o['NgayTao'])) ?></td>
    <td data-label="Tổng tiền" class="fw-bold"><?= number_format($o['TongTien'], 0, ',', ',') ?>đ</td>
    <td data-label="Thanh toán">
        <div style="display:flex;flex-direction:column;gap:4px;">
            <small style="color:var(--admin-muted);font-size:11px;"><?= htmlspecialchars($pmLabel) ?></small>
            <?= $payBadge ?>
        </div>
    </td>
    <td data-label="Trạng thái">
        <span class="status-badge <?= $o['TrangThaiDH'] ?>"><?= $statusMap[$o['TrangThaiDH']] ?? $o['TrangThaiDH'] ?></span>
    </td>
    <td data-label="Hành động"><div class="action-btns">
        <a href="index.php?page=admin-order-detail&id=<?= $o['MaDonHang'] ?>" class="btn-action btn-view" title="Chi tiết"><i class="fas fa-eye"></i></a>
        <?php
        $allowedTransitions = [
            'pending'   => ['confirmed', 'shipping', 'delivered', 'completed', 'cancelled'],
            'confirmed' => ['shipping', 'delivered', 'completed', 'cancelled'],
            'shipping'  => ['delivered', 'completed', 'cancelled', 'refused'],
            'delivered' => ['completed', 'cancelled', 'returning', 'refused'],
            'completed' => ['cancelled', 'returning'],
            'cancelled' => [],
            'returning' => ['returned'],
            'returned'  => [],
            'refused'   => []
        ];
        $currentStatus = $o['TrangThaiDH'];
        $nextStates = $allowedTransitions[$currentStatus] ?? [];
        ?>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="order_id" value="<?= $o['MaDonHang'] ?>">
            <select name="status" class="form-select form-select-sm d-inline-block" style="width:130px;font-size:12px;" onchange="this.form.submit()" <?= empty($nextStates) ? 'disabled' : '' ?>>
                <option value="pending"   <?= $currentStatus=='pending'   ?'selected':'' ?><?= ($currentStatus !== 'pending'   && !in_array('pending',   $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Chờ xác nhận</option>
                <option value="confirmed" <?= $currentStatus=='confirmed' ?'selected':'' ?><?= ($currentStatus !== 'confirmed' && !in_array('confirmed', $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Đã thanh toán</option>
                <option value="shipping"  <?= $currentStatus=='shipping'  ?'selected':'' ?><?= ($currentStatus !== 'shipping'  && !in_array('shipping',  $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Đang giao</option>
                <option value="delivered" <?= $currentStatus=='delivered' ?'selected':'' ?><?= ($currentStatus !== 'delivered' && !in_array('delivered', $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Đã giao</option>
                <option value="completed" <?= $currentStatus=='completed' ?'selected':'' ?><?= ($currentStatus !== 'completed' && !in_array('completed', $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Giao thành công</option>
                <option value="returning" <?= $currentStatus=='returning' ?'selected':'' ?><?= ($currentStatus !== 'returning' && !in_array('returning', $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Yêu cầu trả hàng</option>
                <option value="returned"  <?= $currentStatus=='returned'  ?'selected':'' ?><?= ($currentStatus !== 'returned'  && !in_array('returned',  $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Đã trả hàng</option>
                <option value="cancelled" <?= $currentStatus=='cancelled' ?'selected':'' ?><?= ($currentStatus !== 'cancelled' && !in_array('cancelled', $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Đã hủy</option>
                <option value="refused"   <?= $currentStatus=='refused'   ?'selected':'' ?><?= ($currentStatus !== 'refused'   && !in_array('refused',   $nextStates)) ? ' style="display:none;" disabled' : '' ?>>Từ chối nhận</option>
            </select>
            <input type="hidden" name="update_status" value="1">
        </form>
    </div></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div id="noOrderResult" class="text-center py-4" style="display:none;">Không tìm thấy đơn hàng nào</div>
</div>

<style>
.order-toolbar { display:flex; flex-wrap:wrap; gap:12px; align-items:center; }
.order-search-box { position:relative; }
.order-search-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--admin-muted); pointer-events:none; font-size:13px; }
.order-search-box .form-control { padding-left:34px; width:240px; border-radius:8px; }
.order-filter-pills { display:flex; flex-wrap:wrap; gap:6px; }
.order-pill {
    padding:5px 13px; border-radius:20px; border:1.5px solid var(--admin-border);
    background:transparent; color:var(--admin-text); font-size:12px; font-weight:500;
    cursor:pointer; transition:all 0.18s; white-space:nowrap;
}
.order-pill:hover { background:var(--admin-hover); }
.order-pill.active                { background:var(--admin-secondary); color:#fff; border-color:var(--admin-secondary); }
.order-pill.pending-pill.active   { background:#f59e0b; border-color:#f59e0b; color:#fff; }
.order-pill.confirmed-pill.active { background:#3b82f6; border-color:#3b82f6; color:#fff; }
.order-pill.shipping-pill.active  { background:#8b5cf6; border-color:#8b5cf6; color:#fff; }
.order-pill.delivered-pill.active { background:#06b6d4; border-color:#06b6d4; color:#fff; }
.order-pill.completed-pill.active { background:#10b981; border-color:#10b981; color:#fff; }
.order-pill.returning-pill.active { background:#f59e0b; border-color:#f59e0b; color:#fff; }
.order-pill.returned-pill.active  { background:#64748b; border-color:#64748b; color:#fff; }
.order-pill.cancelled-pill.active { background:#ef4444; border-color:#ef4444; color:#fff; }
.order-pill.refused-pill.active   { background:#7c3aed; border-color:#7c3aed; color:#fff; }

/* Payment status badges */
.pay-badge {
    display:inline-flex; align-items:center; gap:4px;
    padding:3px 8px; border-radius:20px; font-size:11px; font-weight:600; white-space:nowrap;
}
.pay-cod    { background:rgba(107,114,128,0.15); color:#9ca3af; border:1px solid rgba(107,114,128,0.3); }
.pay-paid   { background:rgba(16,185,129,0.15);  color:#10b981; border:1px solid rgba(16,185,129,0.3); }
.pay-unpaid { background:rgba(245,158,11,0.15);  color:#f59e0b; border:1px solid rgba(245,158,11,0.3); }

/* Thêm badge style cho 'confirmed' và 'refused' */
.status-badge.confirmed { background:rgba(59,130,246,0.15); color:#3b82f6; border:1px solid rgba(59,130,246,0.3); }
.status-badge.refused   { background:rgba(124,58,237,0.15); color:#7c3aed; border:1px solid rgba(124,58,237,0.3); font-weight:700; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('orderSearchInput');
    const pills = document.querySelectorAll('.order-pill');
    const noResult = document.getElementById('noOrderResult');
    let activeStatus = '';

    function filterTable() {
        const q = searchInput.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#ordersTableBody tr[data-id]');
        let found = 0;
        rows.forEach(row => {
            const statusMatch = !activeStatus || row.dataset.status === activeStatus;
            const textMatch   = !q || row.innerText.toLowerCase().includes(q);
            if (statusMatch && textMatch) { row.style.display = ''; found++; }
            else                          { row.style.display = 'none'; }
        });
        noResult.style.display = found === 0 ? '' : 'none';
    }

    searchInput.addEventListener('input', filterTable);
    pills.forEach(pill => {
        pill.addEventListener('click', function() {
            pills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            activeStatus = this.dataset.status;
            filterTable();
        });
    });
});
</script>
<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>