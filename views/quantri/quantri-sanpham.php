<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Quản lý sản phẩm - VAB Admin';
    $trangHienTai = 'admin-products';
    include 'admin-header.php';
}
$msg = $_GET['msg'] ?? $msg ?? '';
if ($msg): ?>
<div class="alert alert-<?= ($msg == 'deleted' || $msg == 'error_purchased') ? 'danger' : 'success' ?> alert-dismissible fade show">
    <?= $msg == 'added' ? '✅ Thêm sản phẩm thành công!' : ($msg == 'updated' ? '✅ Cập nhật sản phẩm thành công!' : ($msg == 'deleted' ? '🗑️ Đã xóa sản phẩm!' : ($msg == 'error_purchased' ? '⚠️ Sản phẩm này đã được mua và có trong đơn hàng, không được phép xóa!' : ''))) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div>
        <div class="page-header-title">Quản lý sản phẩm</div>
        <div class="page-header-subtitle">Thêm, sửa, xóa và quản lý trạng thái sản phẩm</div>
    </div>
</div>

<div class="admin-table-wrapper">

<div class="table-header flex-wrap gap-3" style="display: flex; justify-content: space-between; align-items: center;">
    <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: center; margin: 0; flex-wrap: wrap;">
        <input type="hidden" name="page" value="admin-products">
        <input type="search" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..." style="width:220px;" id="searchProduct" value="<?= htmlspecialchars($search ?? '') ?>" onsearch="this.form.submit()" onchange="this.form.submit()">
        <select name="category" class="form-select" style="min-width: 200px; width: auto;" id="filterCategory" onchange="this.form.submit()">
            <option value="">Tất cả danh mục</option>
            <?php foreach ($categoriesList as $cat): ?>
                <option value="<?= $cat['MaDanhMuc'] ?>" <?= ((string)($category ?? '') === (string)$cat['MaDanhMuc'] || ($category ?? '') === $cat['TenDM']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['TenDM']) ?><?= !$cat['TrangThai'] ? ' (Đang ẩn)' : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($search) || !empty($category)): ?>
            <a href="index.php?page=admin-products" class="btn btn-outline-secondary" style="border-radius:8px; border-color:var(--admin-border);"><i class="fas fa-undo"></i> Reset</a>
        <?php endif; ?>
    </form>
    <div class="table-actions" style="margin: 0;">
        <a href="index.php?page=admin-product-add" class="btn btn-secondary" style="background:var(--admin-secondary);border:none;border-radius:8px;"><i class="fas fa-plus me-1"></i>Thêm sản phẩm</a>
    </div>
</div>
<table class="admin-table"><thead><tr><th>Mã SP</th><th>Ảnh</th><th>Tên sản phẩm</th><th>Danh mục</th><th>Thương hiệu</th><th>Giá</th><th>Tồn kho</th><th>Trạng thái</th><th>Hành động</th></tr></thead>
<tbody>
<?php if (empty($products)): ?>
<tr><td colspan="9" class="text-center py-4">Chưa có sản phẩm nào</td></tr>
<?php else: ?>
<?php foreach($products as $p): ?>
<tr>
<td data-label="Mã SP"><small>SP<?= $p['MaSP'] ?></small></td>
<td data-label="Ảnh">
    <img src="<?= ProductModel::getImageUrl($p, 40, 40) ?>" class="product-img-cell" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
</td>
<td data-label="Tên">
    <strong><?= htmlspecialchars($p['TenSP']) ?></strong>
    <?php if (!empty($p['NguoiTao'])): ?>
        <br><small class="text-muted" style="font-size: 11px;"><i class="fas fa-user me-1"></i>Tạo bởi: <?= htmlspecialchars($p['NguoiTao']) ?></small>
    <?php endif; ?>
</td>
<td data-label="Danh mục"><?= htmlspecialchars($p['category']) ?></td>
<td data-label="Thương hiệu"><?= htmlspecialchars($p['brand']) ?></td>
<td data-label="Giá">
    <?php if (!empty($p['GiaKhuyenMai']) && $p['GiaKhuyenMai'] > 0): ?>
        <span class="text-decoration-line-through text-muted small"><?= number_format($p['Gia'],0,',',',') ?>đ</span>
        <strong class="text-danger"><?= number_format($p['GiaKhuyenMai'],0,',',',') ?>đ</strong>
    <?php else: ?>
        <strong><?= number_format($p['Gia'],0,',',',') ?>đ</strong>
    <?php endif; ?>
</td>
<td data-label="Tồn kho">
    <?php
    $stock = (int)($p['SoLuong'] ?? 0);
    if ($stock == 0) {
        echo '<span class="badge bg-danger text-white fw-bold" style="font-size: 11.5px; padding: 6px 10px; border-radius: 30px; letter-spacing: 0.2px;">Hết hàng</span>';
    } elseif ($stock < 5) {
        echo '<span class="badge bg-warning text-dark fw-bold" style="font-size: 11.5px; padding: 6px 10px; border-radius: 30px; letter-spacing: 0.2px;">Sắp hết (' . $stock . ')</span>';
    } else {
        echo '<span class="badge bg-success text-white fw-bold" style="font-size: 11.5px; padding: 6px 10px; border-radius: 30px; letter-spacing: 0.2px;">Còn hàng (' . $stock . ')</span>';
    }
    ?>
</td>
<td data-label="Trạng thái"><a href="index.php?page=admin-products&toggle_status=<?= $p['MaSP'] ?>" class="status-badge <?= $p['TrangThai']?'active':'inactive' ?>" style="text-decoration: none;" title="Nhấp để thay đổi"><?= $p['TrangThai']?'Hiển thị':'Ẩn' ?></a></td>
<td data-label="Hành động"><div class="action-btns">
<a href="index.php?page=admin-product-edit&id=<?= $p['MaSP'] ?>" class="btn-action btn-edit" title="Sửa"><i class="fas fa-edit"></i></a>
<a href="index.php?page=admin-products&delete=<?= $p['MaSP'] ?>" class="btn-action btn-delete" onclick="return confirm('Xóa sản phẩm <?= addslashes($p['TenSP']) ?>?')" title="Xóa"><i class="fas fa-trash"></i></a>
</div></td></tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody></table>
</div>
<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>