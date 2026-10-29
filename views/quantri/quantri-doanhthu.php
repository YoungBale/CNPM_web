<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Quản lý doanh thu - VAB Admin';
    $trangHienTai = 'admin-revenue';
    include 'admin-header.php';
}
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
    /* ===== REVENUE PAGE STYLES ===== */
    .rev-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    .rev-kpi-card {
        border-radius: 16px;
        padding: 22px 22px 18px;
        box-shadow: 0 2px 14px rgba(0, 0, 0, .07);
        display: flex;
        flex-direction: column;
        gap: 10px;
        border: 1px solid transparent;
        position: relative;
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }

    .rev-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 32px rgba(0, 0, 0, .12);
    }

    /* Pastel background + accent border */
    .rev-kpi-card.kpi-day {
        background: linear-gradient(135deg, #fff7f3 0%, #fff0ea 100%);
        border-color: #ffd4bc;
    }

    .rev-kpi-card.kpi-month {
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
        border-color: #c4b5fd;
    }

    .rev-kpi-card.kpi-year {
        background: linear-gradient(135deg, #f0fdf9 0%, #d1fae5 100%);
        border-color: #a7f3d0;
    }

    .rev-kpi-card.kpi-product {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border-color: #fde68a;
    }

    /* Top accent stripe */
    .rev-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-radius: 16px 16px 0 0;
    }

    .rev-kpi-card.kpi-day::before {
        background: linear-gradient(90deg, #f25c19, #ff8c42);
    }

    .rev-kpi-card.kpi-month::before {
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
    }

    .rev-kpi-card.kpi-year::before {
        background: linear-gradient(90deg, #10b981, #34d399);
    }

    .rev-kpi-card.kpi-product::before {
        background: linear-gradient(90deg, #f59e0b, #fcd34d);
    }

    /* Decorative blob in corner */
    .rev-kpi-card::after {
        content: '';
        position: absolute;
        bottom: -20px;
        right: -20px;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        opacity: .12;
    }

    .kpi-day::after {
        background: #f25c19;
    }

    .kpi-month::after {
        background: #6366f1;
    }

    .kpi-year::after {
        background: #10b981;
    }

    .kpi-product::after {
        background: #f59e0b;
    }

    .kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .kpi-day .kpi-icon {
        background: rgba(242, 92, 25, .15);
        color: #ea5b17;
    }

    .kpi-month .kpi-icon {
        background: rgba(99, 102, 241, .15);
        color: #5254c9;
    }

    .kpi-year .kpi-icon {
        background: rgba(16, 185, 129, .15);
        color: #0ea472;
    }

    .kpi-product .kpi-icon {
        background: rgba(245, 158, 11, .15);
        color: #c78c07;
    }

    .kpi-label {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .kpi-value {
        font-size: 21px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .kpi-sub {
        font-size: 12px;
        font-weight: 500;
    }

    .kpi-sub.up {
        color: #059669;
    }

    .kpi-sub.down {
        color: #dc2626;
    }

    .kpi-sub.neutral {
        color: #94a3b8;
    }

    /* ===== CHART SECTION ===== */
    .rev-chart-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
        border: 1px solid #f0f0f0;
        margin-bottom: 24px;
    }

    .rev-chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .rev-chart-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }

    .chart-period-btns {
        display: flex;
        gap: 6px;
    }

    .chart-period-btn {
        padding: 7px 16px;
        border-radius: 8px;
        border: 1.5px solid var(--admin-border);
        background: #fff;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: all .2s;
    }

    .chart-period-btn:hover {
        border-color: #f25c19;
        color: #f25c19;
    }

    .chart-period-btn.active {
        background: #f25c19;
        border-color: #f25c19;
        color: #fff;
    }

    .chart-wrap {
        position: relative;
        height: 300px;
    }

    /* ===== SEARCH & FILTER BAR ===== */
    .rev-filter-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .rev-search-wrap {
        position: relative;
        flex: 1;
        min-width: 200px;
        max-width: 360px;
    }

    .rev-search-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
    }

    .rev-search-input {
        width: 100%;
        padding: 10px 14px 10px 36px;
        border: 1.5px solid var(--admin-border);
        border-radius: 10px;
        font-size: 14px;
        outline: none;
        transition: border-color .2s;
        background: #fff;
    }

    .rev-search-input:focus {
        border-color: #f25c19;
        box-shadow: 0 0 0 3px rgba(242, 92, 25, .08);
    }

    .rev-select {
        padding: 10px 14px;
        border: 1.5px solid var(--admin-border);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        background: #fff;
        outline: none;
        cursor: pointer;
        transition: border-color .2s;
    }

    .rev-select:focus {
        border-color: #f25c19;
    }

    .rev-search-result-count {
        font-size: 13px;
        color: #94a3b8;
        margin-left: auto;
    }

    /* ===== TABLE STYLES ===== */
    .rev-table-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
        border: 1px solid #f0f0f0;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .rev-table-title {
        padding: 18px 22px;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rev-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .rev-table thead tr {
        background: #f8fafc;
    }

    .rev-table th {
        padding: 10px 14px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: #64748b;
        border-bottom: 2px solid var(--admin-border);
        white-space: nowrap;
    }

    .rev-table td {
        padding: 11px 14px;
        border-bottom: 1px solid var(--admin-border);
        vertical-align: middle;
    }

    .rev-table tbody tr {
        transition: background .15s;
    }

    .rev-table tbody tr:hover {
        background: #fafafa;
    }

    .rev-table tbody tr:last-child td {
        border-bottom: none;
    }

    .order-badge {
        display: inline-block;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
    }

    .rev-amount {
        font-weight: 800;
        color: #10b981;
        font-size: 15px;
    }

    .rev-amount.danger {
        color: #ef4444;
    }

    /* Progress bar */
    .prog-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .prog-bar-bg {
        width: 90px;
        flex-shrink: 0;
        height: 5px;
        background: #e9eff6;
        border-radius: 999px;
        overflow: hidden;
    }

    .prog-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #f25c19, #ff8c42);
        transition: width .6s ease;
    }

    /* ===== PRODUCT SECTION ===== */
    .rev-products-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .prod-filter-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 22px;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
        flex-wrap: wrap;
    }

    .prod-filter-label {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
    }

    .rank-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 12px;
    }

    .rank-best {
        background: linear-gradient(135deg, #ecfdf5, #a7f3d0);
        color: #065f46;
    }

    .rank-worst {
        background: linear-gradient(135deg, #fef2f2, #fecaca);
        color: #991b1b;
    }

    .empty-state-rev {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }

    .empty-state-rev i {
        font-size: 36px;
        display: block;
        margin-bottom: 10px;
    }

    .empty-state-rev p {
        font-size: 14px;
        margin: 0;
    }

    @media (max-width: 900px) {
        .rev-kpi-grid {
            grid-template-columns: 1fr 1fr;
        }

        .rev-products-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 560px) {
        .rev-kpi-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php
// Tính max revenue cho progress bar
$maxDailyRev   = !empty($dailyRevenue)   ? max(array_column($dailyRevenue,   'revenue')) : 1;
$maxMonthlyRev = !empty($monthlyRevenue) ? max(array_column($monthlyRevenue, 'revenue')) : 1;
$maxYearlyRev  = !empty($yearlyRevenue)  ? max(array_column($yearlyRevenue,  'revenue')) : 1;
$currentYear   = date('Y');
$currentMonth  = date('n');
?>

<!-- ===== 4 KPI CARDS ===== -->
<div class="rev-kpi-grid">

    <!-- Hôm nay -->
    <div class="rev-kpi-card kpi-day">
        <div class="kpi-icon"><i class="fas fa-calendar-day"></i></div>
        <div>
            <div class="kpi-label">Hôm nay</div>
            <div class="kpi-value"><?= number_format($revenueToday, 0, ',', '.') ?>đ</div>
            <div class="kpi-sub neutral"><i class="fas fa-clock me-1"></i><?= date('d/m/Y') ?></div>
        </div>
    </div>

    <!-- Tháng này -->
    <div class="rev-kpi-card kpi-month">
        <div class="kpi-icon"><i class="fas fa-calendar-alt"></i></div>
        <div>
            <div class="kpi-label">Tháng <?= date('n/Y') ?></div>
            <div class="kpi-value"><?= number_format($revenueThisMonth, 0, ',', '.') ?>đ</div>
            <?php if ($monthGrowth > 0): ?>
                <div class="kpi-sub up"><i class="fas fa-arrow-trend-up me-1"></i>+<?= $monthGrowth ?>% so tháng trước</div>
            <?php elseif ($monthGrowth < 0): ?>
                <div class="kpi-sub down"><i class="fas fa-arrow-trend-down me-1"></i><?= $monthGrowth ?>% so tháng trước</div>
            <?php else: ?>
                <div class="kpi-sub neutral"><i class="fas fa-minus me-1"></i>Chưa có dữ liệu trước</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Năm nay -->
    <div class="rev-kpi-card kpi-year">
        <div class="kpi-icon"><i class="fas fa-calendar"></i></div>
        <div>
            <div class="kpi-label">Năm <?= date('Y') ?></div>
            <div class="kpi-value"><?= number_format($revenueThisYear, 0, ',', '.') ?>đ</div>
            <div class="kpi-sub neutral"><i class="fas fa-chart-line me-1"></i>Tổng doanh thu năm nay</div>
        </div>
    </div>

    <!-- Sản phẩm đã bán -->
    <div class="rev-kpi-card kpi-product">
        <div class="kpi-icon"><i class="fas fa-box-open"></i></div>
        <div>
            <div class="kpi-label">Sản phẩm bán ra</div>
            <div class="kpi-value"><?= number_format($productsSoldThisMonth, 0, ',', '.') ?> sp</div>
            <div class="kpi-sub neutral"><i class="fas fa-tag me-1"></i>Trong tháng <?= date('n/Y') ?></div>
        </div>
    </div>
</div>

<!-- ===== BIỂU ĐỒ ===== -->
<div class="rev-chart-card">
    <div class="rev-chart-header">
        <div class="rev-chart-title"><i class="fas fa-chart-bar me-2" style="color:#f25c19;"></i>Biểu đồ doanh thu</div>
        <div class="chart-period-btns">
            <button class="chart-period-btn active" onclick="switchChart('day',this)"><i class="fas fa-calendar-day me-1"></i>Theo ngày</button>
            <button class="chart-period-btn" onclick="switchChart('month',this)"><i class="fas fa-calendar-alt me-1"></i>Theo tháng</button>
            <button class="chart-period-btn" onclick="switchChart('year',this)"><i class="fas fa-calendar me-1"></i>Theo năm</button>
        </div>
    </div>
    <div class="chart-wrap">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<!-- ===== BẢNG BÁO CÁO + TÌM KIẾM ===== -->

<!-- Thanh tìm kiếm & lọc -->
<div class="rev-filter-bar">
    <div class="rev-search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" id="revSearchInput" class="rev-search-input" placeholder="Tìm kiếm theo ngày, tháng, năm...">
    </div>
    <select id="revPeriodSelect" class="rev-select" onchange="changeReportPeriod(this.value)">
        <option value="day">Theo ngày</option>
        <option value="month">Theo tháng</option>
        <option value="year">Theo năm</option>
    </select>
    <span class="rev-search-result-count" id="resultCount"></span>
</div>

<!-- Bảng theo ngày -->
<div class="rev-table-card" id="tableDay">
    <div class="rev-table-title"><i class="fas fa-calendar-day" style="color:#f25c19;"></i> Doanh thu theo ngày</div>
    <table class="rev-table">
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Số đơn</th>
                <th>Doanh thu</th>
                <th style="width:130px;">Tỷ lệ</th>
            </tr>
        </thead>
        <tbody id="tbodyDay">
            <?php if (empty($dailyRevenue)): ?>
                <tr>
                    <td colspan="4">
                        <div class="empty-state-rev"><i class="fas fa-chart-line"></i>
                            <p>Chưa có dữ liệu</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($dailyRevenue as $d): ?>
                    <?php $pct = $maxDailyRev > 0 ? round($d['revenue'] / $maxDailyRev * 100) : 0; ?>
                    <tr data-search="<?= date('d/m/Y', strtotime($d['day'])) ?>">
                        <td><strong><?= date('d/m/Y', strtotime($d['day'])) ?></strong></td>
                        <td><span class="order-badge"><?= $d['order_count'] ?> đơn</span></td>
                        <td><span class="rev-amount"><?= number_format($d['revenue'], 0, ',', '.') ?>đ</span></td>
                        <td>
                            <div class="prog-wrap">
                                <div class="prog-bar-bg">
                                    <div class="prog-bar-fill" style="width:<?= $pct ?>%"></div>
                                </div>
                                <span style="font-size:12px;color:#94a3b8;width:32px;text-align:right;"><?= $pct ?>%</span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Bảng theo tháng -->
<div class="rev-table-card" id="tableMonth" style="display:none;">
    <div class="rev-table-title"><i class="fas fa-calendar-alt" style="color:#6366f1;"></i> Doanh thu theo tháng</div>
    <table class="rev-table">
        <thead>
            <tr>
                <th>Tháng</th>
                <th>Số đơn</th>
                <th>Doanh thu</th>
                <th style="width:130px;">Tỷ lệ</th>
            </tr>
        </thead>
        <tbody id="tbodyMonth">
            <?php if (empty($monthlyRevenue)): ?>
                <tr>
                    <td colspan="4">
                        <div class="empty-state-rev"><i class="fas fa-chart-bar"></i>
                            <p>Chưa có dữ liệu</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($monthlyRevenue as $m): ?>
                    <?php $pct = $maxMonthlyRev > 0 ? round($m['revenue'] / $maxMonthlyRev * 100) : 0; ?>
                    <tr data-search="<?= date('m/Y', strtotime($m['month'] . '-01')) ?>">
                        <td><strong><?= date('m/Y', strtotime($m['month'] . '-01')) ?></strong></td>
                        <td><span class="order-badge"><?= $m['order_count'] ?> đơn</span></td>
                        <td><span class="rev-amount"><?= number_format($m['revenue'], 0, ',', '.') ?>đ</span></td>
                        <td>
                            <div class="prog-wrap">
                                <div class="prog-bar-bg">
                                    <div class="prog-bar-fill" style="width:<?= $pct ?>%;background:linear-gradient(90deg,#6366f1,#8b5cf6);"></div>
                                </div>
                                <span style="font-size:12px;color:#94a3b8;width:32px;text-align:right;"><?= $pct ?>%</span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Bảng theo năm -->
<div class="rev-table-card" id="tableYear" style="display:none;">
    <div class="rev-table-title"><i class="fas fa-calendar" style="color:#10b981;"></i> Doanh thu theo năm</div>
    <table class="rev-table">
        <thead>
            <tr>
                <th>Năm</th>
                <th>Số đơn</th>
                <th>Doanh thu</th>
                <th style="width:130px;">Tỷ lệ</th>
            </tr>
        </thead>
        <tbody id="tbodyYear">
            <?php if (empty($yearlyRevenue)): ?>
                <tr>
                    <td colspan="4">
                        <div class="empty-state-rev"><i class="fas fa-chart-pie"></i>
                            <p>Chưa có dữ liệu</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($yearlyRevenue as $y): ?>
                    <?php $pct = $maxYearlyRev > 0 ? round($y['revenue'] / $maxYearlyRev * 100) : 0; ?>
                    <tr data-search="<?= $y['year'] ?>">
                        <td><strong><?= $y['year'] ?></strong></td>
                        <td><span class="order-badge"><?= $y['order_count'] ?> đơn</span></td>
                        <td><span class="rev-amount"><?= number_format($y['revenue'], 0, ',', '.') ?>đ</span></td>
                        <td>
                            <div class="prog-wrap">
                                <div class="prog-bar-bg">
                                    <div class="prog-bar-fill" style="width:<?= $pct ?>%;background:linear-gradient(90deg,#10b981,#34d399);"></div>
                                </div>
                                <span style="font-size:12px;color:#94a3b8;width:32px;text-align:right;"><?= $pct ?>%</span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ===== SẢN PHẨM BÁN CHẠY / BÁN ÍT ===== -->
<div class="rev-products-grid">

    <!-- Bán chạy nhất -->
    <div class="rev-table-card" style="margin-bottom:0;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:1px solid #f1f5f9;">
            <div class="rev-table-title" style="padding:0;border:none;"><i class="fas fa-arrow-trend-up" style="color:#10b981;"></i> Bán chạy nhất</div>
        </div>
        <div class="prod-filter-bar">
            <span class="prod-filter-label"><i class="fas fa-filter me-1"></i>Lọc:</span>
            <form method="get" action="" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;" id="spFilterForm">
                <input type="hidden" name="page" value="admin-revenue">
                <select name="sp_year" class="rev-select" style="padding:7px 10px;font-size:12px;" onchange="document.getElementById('spFilterForm').submit()">
                    <?php foreach ($availableYears as $yr): ?>
                        <option value="<?= $yr ?>" <?= $filterYear == $yr ? 'selected' : '' ?>>Năm <?= $yr ?></option>
                    <?php endforeach; ?>
                    <?php if (empty($availableYears)): ?>
                        <option value="<?= $currentYear ?>">Năm <?= $currentYear ?></option>
                    <?php endif; ?>
                </select>
                <select name="sp_month" class="rev-select" style="padding:7px 10px;font-size:12px;" onchange="document.getElementById('spFilterForm').submit()">
                    <option value="0" <?= $filterMonth == 0 ? 'selected' : '' ?>>Tất cả tháng</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= $filterMonth == $i ? 'selected' : '' ?>>Tháng <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>
        <table class="rev-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sản phẩm</th>
                    <th style="text-align:center;">Đã bán</th>
                    <th>Doanh thu</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bestSellers)): ?>
                    <tr>
                        <td colspan="4">
                            <div class="empty-state-rev"><i class="fas fa-box-open"></i>
                                <p>Chưa có dữ liệu</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $i = 1;
                    foreach ($bestSellers as $b): ?>
                        <tr>
                            <td><span class="rank-badge rank-best"><?= $i++ ?></span></td>
                            <td style="font-weight:600;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($b['TenSP']) ?>"><?= htmlspecialchars($b['TenSP']) ?></td>
                            <td style="text-align:center;font-weight:700;"><?= $b['total_sold'] ?></td>
                            <td><span class="rev-amount"><?= number_format($b['total_revenue'], 0, ',', '.') ?>đ</span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bán ít nhất -->
    <div class="rev-table-card" style="margin-bottom:0;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:1px solid #f1f5f9;">
            <div class="rev-table-title" style="padding:0;border:none;"><i class="fas fa-arrow-trend-down" style="color:#ef4444;"></i> Bán ít nhất</div>
        </div>
        <div class="prod-filter-bar">
            <span class="prod-filter-label" style="color:#94a3b8;">
                <?php if ($filterMonth > 0): ?>
                    Tháng <?= $filterMonth ?>/<?= $filterYear ?>
                <?php else: ?>
                    Năm <?= $filterYear ?>
                <?php endif; ?>
            </span>
        </div>
        <table class="rev-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sản phẩm</th>
                    <th style="text-align:center;">Đã bán</th>
                    <th>Doanh thu</th>
                    <th style="text-align:center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($worstSellers)): ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state-rev"><i class="fas fa-box-open"></i>
                                <p>Chưa có dữ liệu</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $i = 1;
                    foreach ($worstSellers as $w): ?>
                        <tr>
                            <td><span class="rank-badge rank-worst"><?= $i++ ?></span></td>
                            <td style="font-weight:600;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($w['TenSP']) ?>"><?= htmlspecialchars($w['TenSP']) ?></td>
                            <td style="text-align:center;font-weight:700;"><?= $w['total_sold'] ?></td>
                            <td><span class="rev-amount danger"><?= number_format($w['total_revenue'], 0, ',', '.') ?>đ</span></td>
                            <td style="text-align:center;">
                                <?php if (!empty($w['MaSP'])): ?>
                                    <a href="index.php?page=admin-product-edit&id=<?= (int)$w['MaSP'] ?>" class="btn btn-sm btn-outline-danger" style="border-radius:20px; font-size:11px; padding: 2px 10px; border-color: #ef4444; color: #ef4444; font-weight: 600; text-decoration: none;" onmouseover="this.style.background='#ef4444'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#ef4444';"><i class="fas fa-edit me-1"></i>Sửa giá</a>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size:11px;">K.định danh</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===== CHART.JS SCRIPT ===== -->
<script>
    // Dữ liệu từ PHP
    const chartData = {
        day: {
            labels: <?= json_encode($chartDayLabels) ?>,
            data: <?= json_encode($chartDayData) ?>
        },
        month: {
            labels: <?= json_encode($chartMonthLabels) ?>,
            data: <?= json_encode($chartMonthData) ?>
        },
        year: {
            labels: <?= json_encode($chartYearLabels) ?>,
            data: <?= json_encode($chartYearData) ?>
        }
    };

    let currentPeriod = 'day';
    let revenueChart;

    function buildGradient(ctx) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(242, 92, 25, 0.85)');
        gradient.addColorStop(1, 'rgba(242, 92, 25, 0.12)');
        return gradient;
    }

    function initChart(period) {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const d = chartData[period];
        const gradient = buildGradient(ctx);

        if (revenueChart) revenueChart.destroy();

        revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: d.labels,
                datasets: [{
                    label: 'Doanh thu',
                    data: d.data,
                    backgroundColor: gradient,
                    borderColor: '#f25c19',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 64,
                    barPercentage: 0.85,
                    categoryPercentage: 0.9,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 600,
                    easing: 'easeInOutQuart'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + new Intl.NumberFormat('vi-VN').format(ctx.parsed.y) + 'đ'
                        },
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#f25c19',
                        padding: 12,
                        borderRadius: 10,
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#f1f5f9',
                            lineWidth: 1
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 11
                            },
                            callback: v => new Intl.NumberFormat('vi-VN', {
                                notation: 'compact'
                            }).format(v) + 'đ'
                        }
                    }
                }
            }
        });
    }

    function switchChart(period, btn) {
        currentPeriod = period;
        document.querySelectorAll('.chart-period-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        initChart(period);
    }

    // Khởi tạo biểu đồ
    initChart('day');

    // ===== BẢNG BÁO CÁO TOGGLE =====
    function changeReportPeriod(val) {
        document.getElementById('tableDay').style.display = 'none';
        document.getElementById('tableMonth').style.display = 'none';
        document.getElementById('tableYear').style.display = 'none';
        document.getElementById('table' + val.charAt(0).toUpperCase() + val.slice(1)).style.display = 'block';
        updateCount();
        // Đồng bộ biểu đồ
        const btn = document.querySelector(`.chart-period-btn[onclick*="'${val}'"]`);
        if (btn) {
            document.querySelectorAll('.chart-period-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            initChart(val);
        }
    }

    // ===== TÌM KIẾM =====
    document.getElementById('revSearchInput').addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        const period = document.getElementById('revPeriodSelect').value;
        const tbodyId = 'tbody' + period.charAt(0).toUpperCase() + period.slice(1);
        const rows = document.querySelectorAll('#' + tbodyId + ' tr');
        let shown = 0;
        rows.forEach(row => {
            const txt = (row.getAttribute('data-search') || '').toLowerCase();
            const visible = !q || txt.includes(q);
            row.style.display = visible ? '' : 'none';
            if (visible) shown++;
        });
        document.getElementById('resultCount').textContent = q ? `${shown} kết quả` : '';
    });

    function updateCount() {
        document.getElementById('resultCount').textContent = '';
        document.getElementById('revSearchInput').value = '';
    }
</script>

<?php if (!isset($nhungTuController)) {
    include 'admin-footer.php';
} ?>