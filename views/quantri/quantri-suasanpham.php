<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Sửa sản phẩm - VAB Admin';
    $trangHienTai = 'admin-product-edit';
    include 'admin-header.php';
}
$totalStock = 0;
if (!empty($productSizes)) {
    foreach ($productSizes as $ps) {
        $totalStock += (int)$ps['SoLuong'];
    }
}
?>
<div class="admin-form-wrapper">
<form method="POST" enctype="multipart/form-data" data-form-type="edit">
<input type="hidden" name="id" value="<?= $product['MaSP'] ?>">
<input type="hidden" name="existing_image" value="<?= htmlspecialchars($product['AnhChinh'] ?? '') ?>">
<div class="form-section">
<div class="section-title"><i class="fas fa-info-circle"></i> Thông tin cơ bản</div>
<div class="row">
<div class="col-md-6"><div class="form-group"><label>Tên sản phẩm</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['TenSP']) ?>" required></div></div>
<div class="col-md-3"><div class="form-group"><label>Danh mục</label><select name="category" class="form-select">
<?php foreach($categories as $cat): ?>
    <?php if ((int)$cat['TrangThai'] === 1 || $cat['MaDanhMuc'] == $product['MaDanhMuc']): ?>
        <option value="<?= $cat['MaDanhMuc'] ?>" <?= $cat['MaDanhMuc'] == $product['MaDanhMuc'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['TenDM']) ?><?= (int)$cat['TrangThai'] === 0 ? ' (Đang ẩn)' : '' ?>
        </option>
    <?php endif; ?>
<?php endforeach; ?>
</select></div></div>
<div class="col-md-3"><div class="form-group"><label>Thương hiệu</label><select name="brand" class="form-select">
<?php foreach($brands as $b): ?>
    <?php if ((int)($b['TrangThai'] ?? 1) === 1 || $b['MaThuongHieu'] == $product['MaThuongHieu']): ?>
        <option value="<?= $b['MaThuongHieu'] ?>" <?= $b['MaThuongHieu'] == $product['MaThuongHieu'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($b['TenThuongHieu']) ?><?= (int)($b['TrangThai'] ?? 1) === 0 ? ' (Đang ẩn)' : '' ?>
        </option>
    <?php endif; ?>
<?php endforeach; ?>
</select></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="form-group"><label>Giới tính</label><select name="gender" class="form-select">
    <option value="Nam" <?= $product['GioiTinh']=='Nam'?'selected':'' ?>>Nam</option>
    <option value="Nữ" <?= $product['GioiTinh']=='Nữ'?'selected':'' ?>>Nữ</option>
    <option value="Unisex" <?= $product['GioiTinh']=='Unisex'?'selected':'' ?>>Unisex</option>
</select></div></div>
<div class="col-md-4"><div class="form-group"><label>Bộ môn thể thao</label><select name="sport" class="form-select">
<?php foreach($sports as $sp): ?>
    <?php if ((int)$sp['TrangThai'] === 1 || $sp['TenSport'] == $product['TheThao']): ?>
        <option value="<?= htmlspecialchars($sp['TenSport']) ?>" <?= $sp['TenSport'] == $product['TheThao'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($sp['TenSport']) ?><?= (int)$sp['TrangThai'] === 0 ? ' (Đang ẩn)' : '' ?>
        </option>
    <?php endif; ?>
<?php endforeach; ?>
</select></div></div>
<div class="col-md-4"><div class="form-group"><label>Trạng thái</label><select name="status" class="form-select">
    <option value="1" <?= $product['TrangThai']==1?'selected':'' ?>>Hiển thị</option>
    <option value="0" <?= $product['TrangThai']==0?'selected':'' ?>>Ẩn</option>
</select></div></div>
</div></div>
<div class="form-section">
<div class="section-title"><i class="fas fa-dollar-sign"></i> Giá & Tồn kho</div>
<div class="row">
<div class="col-md-6"><div class="form-group"><label>Giá gốc</label><input type="number" name="price" class="form-control" value="<?= $product['Gia'] ?>" required></div></div>
<div class="col-md-6"><div class="form-group"><label>Giá khuyến mãi</label><input type="number" name="sale_price" class="form-control" value="<?= $product['GiaKhuyenMai'] ?? '' ?>"></div></div>
</div>

<div class="form-group">
    <label class="d-flex justify-content-between align-items-center fw-bold text-dark mb-2">
        <span>Quản lý Kích thước & Tồn kho từng Size</span>
        <button type="button" class="btn btn-sm btn-secondary btn-add-size-row" style="background:var(--admin-secondary);border:none;padding:4px 12px;font-size:12px;border-radius:4px;color:#fff;"><i class="fas fa-plus me-1"></i>Thêm Size</button>
    </label>
    <div class="table-responsive">
        <table class="table table-bordered align-middle" id="size-quantity-table" style="background:#fff;border-radius:8px;overflow:hidden;">
            <thead class="table-light">
                <tr>
                    <th style="width:50%;">Kích thước (Size)</th>
                    <th style="width:40%;">Số lượng tồn kho (soluong)</th>
                    <th style="width:10%;" class="text-center">Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($productSizes)): ?>
                    <?php foreach ($productSizes as $ps): ?>
                        <tr>
                            <td>
                                <input type="text" name="sizes[]" class="form-control form-control-sm" value="<?= htmlspecialchars($ps['KichCo']) ?>" placeholder="VD: S, M, XL, 40..." required>
                            </td>
                            <td>
                                <input type="number" name="quantities[]" class="form-control form-control-sm" value="<?= (int)$ps['SoLuong'] ?>" placeholder="Số lượng" min="0" required>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm btn-delete-size-row" style="padding:2px 8px;"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback nếu sản phẩm cũ chưa có bảng chi tiết -->
                    <?php 
                    $legacySizes = array_filter(array_map('trim', explode(',', $product['KichCo'] ?? '')));
                    if (!empty($legacySizes)):
                        $distributedQty = floor($totalStock / count($legacySizes));
                        foreach ($legacySizes as $ls): ?>
                            <tr>
                                <td>
                                    <input type="text" name="sizes[]" class="form-control form-control-sm" value="<?= htmlspecialchars($ls) ?>" placeholder="VD: S, M, XL, 40..." required>
                                </td>
                                <td>
                                    <input type="number" name="quantities[]" class="form-control form-control-sm" value="<?= (int)$distributedQty ?>" placeholder="Số lượng" min="0" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm btn-delete-size-row" style="padding:2px 8px;"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td>
                                <input type="text" name="sizes[]" class="form-control form-control-sm" value="OS" placeholder="VD: S, M, XL, 40..." required>
                            </td>
                            <td>
                                <input type="number" name="quantities[]" class="form-control form-control-sm" value="<?= $totalStock ?>" placeholder="Số lượng" min="0" required>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm btn-delete-size-row" style="padding:2px 8px;"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#size-quantity-table tbody');
    const addBtn = document.querySelector('.btn-add-size-row');
    
    function addSizeRow(size = '', quantity = '0') {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="text" name="sizes[]" class="form-control form-control-sm" value="${size}" placeholder="VD: S, M, XL, 40..." required>
            </td>
            <td>
                <input type="number" name="quantities[]" class="form-control form-control-sm" value="${quantity}" placeholder="Số lượng" min="0" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-delete-size-row" style="padding:2px 8px;"><i class="fas fa-trash"></i></button>
            </td>
        `;
        tableBody.appendChild(row);
        
        row.querySelector('.btn-delete-size-row').addEventListener('click', function() {
            row.remove();
        });
    }
    
    // Bind click event to existing delete buttons
    document.querySelectorAll('.btn-delete-size-row').forEach(function(btn) {
        btn.addEventListener('click', function() {
            btn.closest('tr').remove();
        });
    });
    
    addBtn.addEventListener('click', function() {
        addSizeRow('', '0');
    });
});
</script>
<div class="form-section">
<div class="section-title"><i class="fas fa-image"></i> Hình ảnh</div>
<div class="row">
    <div class="col-md-6">
        <?php if (!empty($product['AnhChinh'])): ?>
        <div class="form-group"><label>Ảnh chính hiện tại</label><br><img src="<?= ProductModel::getImageUrl($product, 120, 120) ?>" class="current-image" alt="" style="max-width:120px;border-radius:8px;"></div>
        <?php endif; ?>
        <div class="form-group"><label>Thay đổi ảnh chính</label><input type="file" name="image" class="form-control" data-preview="preview1"></div>
        <div class="image-preview" id="preview1"><i class="fas fa-camera me-2"></i>Chọn ảnh chính mới</div>
    </div>
    <div class="col-md-6">
        <input type="hidden" name="existing_images" value="<?= htmlspecialchars($product['AnhPhu'] ?? '') ?>">
        <div class="form-group">
            <label>Ảnh chi tiết hiện tại</label><br>
            <div class="d-flex gap-2 flex-wrap mb-2">
                <?php 
                if (!empty($product['AnhPhu'])) {
                    $subImgs = explode(',', $product['AnhPhu']);
                    foreach ($subImgs as $subImg) {
                        $imgUrl = 'assets/images/products/' . trim($subImg);
                        echo '<img src="' . $imgUrl . '" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;" alt="Sub">';
                    }
                } else {
                    echo '<span class="text-muted"><small>Chưa có ảnh chi tiết</small></span>';
                }
                ?>
            </div>
        </div>
        <div class="form-group">
            <label>Thay đổi ảnh chi tiết (tối đa 3 ảnh)</label>
            <input type="file" name="sub_images[]" class="form-control" multiple accept="image/*" id="subImagesInput">
        </div>
        <div id="subImagesPreview" class="d-flex gap-2 flex-wrap mt-2"></div>
    </div>
</div>
</div>
<div class="form-section">
<div class="section-title"><i class="fas fa-align-left"></i> Mô tả</div>
<div class="form-group"><label>Mô tả</label><textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['MoTaSP'] ?? '') ?></textarea></div>
</div>
<div class="form-actions">
<button type="submit" class="btn-submit"><i class="fas fa-save me-2"></i>Cập nhật sản phẩm</button>
<a href="index.php?page=admin-products" class="btn-cancel"><i class="fas fa-times me-2"></i>Hủy</a>
</div>
</form></div>
<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>