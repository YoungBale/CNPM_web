<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Quản lý danh mục - VAB Admin';
    $trangHienTai = 'admin-categories';
    include 'admin-header.php';
}

$msg = $_GET['msg'] ?? $msg ?? '';
$tab = $_GET['tab'] ?? 'category';
if (!in_array($tab, ['category', 'brand', 'sport'])) {
    $tab = 'category';
}
?>

<?php if ($msg): ?>
<div class="alert alert-<?= ($msg == 'deleted' || $msg == 'error_has_products') ? 'danger' : 'success' ?> alert-dismissible fade show">
    <?php
    if ($msg == 'added') echo '✅ Thêm mới thành công!';
    elseif ($msg == 'updated') echo '✅ Cập nhật trạng thái thành công!';
    elseif ($msg == 'deleted') echo '🗑️ Đã xóa thành công!';
    elseif ($msg == 'error_has_products') echo '⚠️ Bản ghi này đang chứa sản phẩm liên kết, không thể xóa!';
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div>
        <div class="page-header-title">Quản lý danh mục</div>
        <div class="page-header-subtitle">Danh mục sản phẩm, thương hiệu và bộ môn thể thao</div>
    </div>
</div>

<style>
.category-tab-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 24px;
    border-radius: var(--admin-radius);
    box-shadow: var(--admin-shadow);
    transition: var(--admin-transition);
    border: 1px solid var(--admin-border);
    text-decoration: none;
    cursor: pointer;
}
.category-tab-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--admin-shadow-lg);
}
.category-tab-card .tab-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--admin-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.category-tab-card .tab-label {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -0.3px;
}

/* Variant: Category (Orange) */
.category-tab-card-orange.active {
    background: linear-gradient(135deg, #f25c19 0%, #ff7c3b 100%);
    color: #fff;
    border-color: #f25c19;
}
.category-tab-card-orange.active .tab-icon {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
}
.category-tab-card-orange:not(.active) {
    background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 100%);
    color: #7c2d12;
    border-color: #ffedd5;
}
.category-tab-card-orange:not(.active) .tab-icon {
    background: #ffdbb5;
    color: #ea580c;
}

/* Variant: Brand (Blue) */
.category-tab-card-blue.active {
    background: linear-gradient(135deg, #2563eb 0%, #60a5fa 100%);
    color: #fff;
    border-color: #2563eb;
}
.category-tab-card-blue.active .tab-icon {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
}
.category-tab-card-blue:not(.active) {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    color: #0369a1;
    border-color: #e0f2fe;
}
.category-tab-card-blue:not(.active) .tab-icon {
    background: #bae6fd;
    color: #0284c7;
}

/* Variant: Sport (Green) */
.category-tab-card-green.active {
    background: linear-gradient(135deg, #059669 0%, #34d399 100%);
    color: #fff;
    border-color: #059669;
}
.category-tab-card-green.active .tab-icon {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
}
.category-tab-card-green:not(.active) {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #047857;
    border-color: #dcfce7;
}
.category-tab-card-green:not(.active) .tab-icon {
    background: #bbf7d0;
    color: #059669;
}
</style>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="index.php?page=admin-categories&tab=category" class="category-tab-card category-tab-card-orange <?= $tab === 'category' ? 'active' : '' ?>">
            <div class="tab-icon"><i class="fas fa-folder"></i></div>
            <div class="tab-label">Danh mục sản phẩm</div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="index.php?page=admin-categories&tab=brand" class="category-tab-card category-tab-card-blue <?= $tab === 'brand' ? 'active' : '' ?>">
            <div class="tab-icon"><i class="fas fa-tags"></i></div>
            <div class="tab-label">Danh mục thương hiệu</div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="index.php?page=admin-categories&tab=sport" class="category-tab-card category-tab-card-green <?= $tab === 'sport' ? 'active' : '' ?>">
            <div class="tab-icon"><i class="fas fa-running"></i></div>
            <div class="tab-label">Danh mục thể thao</div>
        </a>
    </div>
</div>

<div class="row g-3">
    <?php if ($tab === 'category'): ?>
    <!-- TAB 1: DANH MỤC SẢN PHẨM -->
    <div class="col-lg-8">
        <div class="admin-table-wrapper">
            <h5 class="mb-3">Danh sách danh mục sản phẩm</h5>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Mã danh mục</th>
                        <th>Tên danh mục</th>
                        <th style="width: 25%;">Trạng thái</th>
                        <th style="width: 25%; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="4" class="text-center py-4">Chưa có danh mục nào</td></tr>
                    <?php else: ?>
                        <?php foreach($categories as $cat): ?>
                            <tr>
                                <td data-label="Mã danh mục"><small>#<?= $cat['MaDanhMuc'] ?></small></td>
                                <td data-label="Tên danh mục"><strong><?= htmlspecialchars($cat['TenDM']) ?></strong></td>
                                <td data-label="Trạng thái">
                                    <a href="index.php?page=admin-categories&tab=category&toggle_status=<?= $cat['MaDanhMuc'] ?>" 
                                       class="status-badge <?= $cat['TrangThai'] ? 'active' : 'inactive' ?>" 
                                       style="text-decoration: none;" 
                                       title="Nhấp để thay đổi">
                                        <?= $cat['TrangThai'] ? 'Hoạt động' : 'Đang ẩn' ?>
                                    </a>
                                </td>
                                <td data-label="Hành động" class="text-center">
                                    <div class="action-btns justify-content-center">
                                        <a href="index.php?page=admin-category-delete&tab=category&id=<?= $cat['MaDanhMuc'] ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('Xóa danh mục <?= addslashes($cat['TenDM']) ?>?')" 
                                           title="Xóa"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-table-wrapper p-4">
            <h5 class="mb-4">Thêm danh mục mới</h5>
            <form method="POST" action="index.php?page=admin-categories&tab=category">
                <input type="hidden" name="add_category" value="1">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên danh mục *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Áo thun, Giày bóng rổ" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Trạng thái hiển thị *</label>
                    <select name="status" class="form-select">
                        <option value="1">Đang hoạt động (Hiển thị)</option>
                        <option value="0">Đang ẩn</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold" style="background: var(--admin-secondary); border: none;">
                    <i class="fas fa-plus me-1"></i> Tạo danh mục mới
                </button>
            </form>
        </div>
    </div>

    <?php elseif ($tab === 'brand'): ?>
    <!-- TAB 2: DANH MỤC THƯƠNG HIỆU -->
    <div class="col-lg-8">
        <div class="admin-table-wrapper">
            <h5 class="mb-3">Danh sách thương hiệu</h5>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Mã TH</th>
                        <th>Tên thương hiệu</th>
                        <th>Mô tả</th>
                        <th style="width: 25%;">Trạng thái</th>
                        <th style="width: 25%; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($brands)): ?>
                        <tr><td colspan="5" class="text-center py-4">Chưa có thương hiệu nào</td></tr>
                    <?php else: ?>
                        <?php foreach($brands as $b): ?>
                            <tr>
                                <td data-label="Mã TH"><small>#<?= $b['MaThuongHieu'] ?></small></td>
                                <td data-label="Tên thương hiệu"><strong><?= htmlspecialchars($b['TenThuongHieu']) ?></strong></td>
                                <td data-label="Mô tả"><small class="text-muted"><?= htmlspecialchars($b['MoTa'] ?? '') ?></small></td>
                                <td data-label="Trạng thái">
                                    <a href="index.php?page=admin-categories&tab=brand&toggle_status=<?= $b['MaThuongHieu'] ?>" 
                                       class="status-badge <?= ($b['TrangThai'] ?? 1) ? 'active' : 'inactive' ?>" 
                                       style="text-decoration: none;" 
                                       title="Nhấp để thay đổi">
                                        <?= ($b['TrangThai'] ?? 1) ? 'Hoạt động' : 'Đang ẩn' ?>
                                    </a>
                                </td>
                                <td data-label="Hành động" class="text-center">
                                    <div class="action-btns justify-content-center">
                                        <a href="index.php?page=admin-category-delete&tab=brand&id=<?= $b['MaThuongHieu'] ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('Xóa thương hiệu <?= addslashes($b['TenThuongHieu']) ?>?')" 
                                           title="Xóa"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-table-wrapper p-4">
            <h5 class="mb-4">Thêm thương hiệu mới</h5>
            <form method="POST" action="index.php?page=admin-categories&tab=brand">
                <input type="hidden" name="add_brand" value="1">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên thương hiệu *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Nike, Adidas" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả ngắn</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Nhập mô tả thương hiệu..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Trạng thái hiển thị *</label>
                    <select name="status" class="form-select">
                        <option value="1">Đang hoạt động (Hiển thị)</option>
                        <option value="0">Đang ẩn</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold" style="background: var(--admin-secondary); border: none;">
                    <i class="fas fa-plus me-1"></i> Tạo thương hiệu mới
                </button>
            </form>
        </div>
    </div>

    <?php elseif ($tab === 'sport'): ?>
    <!-- TAB 3: DANH MỤC THỂ THAO -->
    <div class="col-lg-8">
        <div class="admin-table-wrapper">
            <h5 class="mb-3">Danh sách bộ môn thể thao</h5>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Mã thể thao</th>
                        <th>Tên bộ môn</th>
                        <th style="width: 25%;">Trạng thái</th>
                        <th style="width: 25%; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sports)): ?>
                        <tr><td colspan="4" class="text-center py-4">Chưa có bộ môn nào</td></tr>
                    <?php else: ?>
                        <?php foreach($sports as $sp): ?>
                            <tr>
                                <td data-label="Mã thể thao"><small>#<?= $sp['MaTheThao'] ?></small></td>
                                <td data-label="Tên bộ môn"><strong><?= htmlspecialchars($sp['TenSport']) ?></strong></td>
                                <td data-label="Trạng thái">
                                    <a href="index.php?page=admin-categories&tab=sport&toggle_status=<?= $sp['MaTheThao'] ?>" 
                                       class="status-badge <?= $sp['TrangThai'] ? 'active' : 'inactive' ?>" 
                                       style="text-decoration: none;" 
                                       title="Nhấp để thay đổi">
                                        <?= $sp['TrangThai'] ? 'Hoạt động' : 'Đang ẩn' ?>
                                    </a>
                                </td>
                                <td data-label="Hành động" class="text-center">
                                    <div class="action-btns justify-content-center">
                                        <a href="index.php?page=admin-category-delete&tab=sport&id=<?= $sp['MaTheThao'] ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('Xóa bộ môn <?= addslashes($sp['TenSport']) ?>?')" 
                                           title="Xóa"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-table-wrapper p-4">
            <h5 class="mb-4">Thêm bộ môn mới</h5>
            <form method="POST" action="index.php?page=admin-categories&tab=sport">
                <input type="hidden" name="add_sport" value="1">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên bộ môn thể thao *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Cầu lông, Bóng bàn" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Trạng thái hiển thị *</label>
                    <select name="status" class="form-select">
                        <option value="1">Đang hoạt động (Hiển thị)</option>
                        <option value="0">Đang ẩn</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold" style="background: var(--admin-secondary); border: none;">
                    <i class="fas fa-plus me-1"></i> Tạo bộ môn mới
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>
