<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Thêm sản phẩm - VAB Admin';
    $trangHienTai = 'admin-product-add';
    include 'admin-header.php';
}
?>
<div class="admin-form-wrapper">
<form method="POST" enctype="multipart/form-data" data-form-type="add">
<div class="form-section">
<div class="section-title"><i class="fas fa-info-circle"></i> Thông tin cơ bản</div>
<div class="row">
<div class="col-md-6"><div class="form-group"><label>Tên sản phẩm</label><input type="text" name="name" class="form-control" required></div></div>
<div class="col-md-3"><div class="form-group"><label>Danh mục</label><select name="category" class="form-select">
<?php foreach($categories as $cat): ?>
    <?php if ((int)$cat['TrangThai'] === 1): ?>
        <option value="<?= $cat['MaDanhMuc'] ?>"><?= htmlspecialchars($cat['TenDM']) ?></option>
    <?php endif; ?>
<?php endforeach; ?>
</select></div></div>
<div class="col-md-3"><div class="form-group"><label>Thương hiệu</label><select name="brand" class="form-select">
<?php foreach($brands as $b): ?>
    <?php if ((int)($b['TrangThai'] ?? 1) === 1): ?>
        <option value="<?= $b['MaThuongHieu'] ?>"><?= htmlspecialchars($b['TenThuongHieu']) ?></option>
    <?php endif; ?>
<?php endforeach; ?>
</select></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="form-group"><label>Giới tính</label><select name="gender" class="form-select"><option>Nam</option><option>Nữ</option><option>Unisex</option></select></div></div>
<div class="col-md-4"><div class="form-group"><label>Bộ môn thể thao</label><select name="sport" class="form-select">
<?php foreach($sports as $sp): ?>
    <?php if ((int)$sp['TrangThai'] === 1): ?>
        <option value="<?= htmlspecialchars($sp['TenSport']) ?>"><?= htmlspecialchars($sp['TenSport']) ?></option>
    <?php endif; ?>
<?php endforeach; ?>
</select></div></div>
<div class="col-md-4"><div class="form-group"><label>Trạng thái</label><select name="status" class="form-select"><option value="1">Hiển thị</option><option value="0">Ẩn</option></select></div></div>
</div></div>
<div class="form-section">
<div class="section-title"><i class="fas fa-dollar-sign"></i> Giá & Tồn kho</div>
<div class="row">
<div class="col-md-6"><div class="form-group"><label>Giá gốc</label><input type="number" name="price" class="form-control" required></div></div>
<div class="col-md-6"><div class="form-group"><label>Giá khuyến mãi</label><input type="number" name="sale_price" class="form-control"></div></div>
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
                <!-- Hàng size sẽ được thêm động bằng JS -->
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
    
    // Tạo sẵn các size mặc định
    addSizeRow('S', '10');
    addSizeRow('M', '10');
    addSizeRow('L', '10');
    addSizeRow('XL', '10');
    
    addBtn.addEventListener('click', function() {
        addSizeRow('', '0');
    });
});
</script>
<div class="form-section">
<div class="section-title"><i class="fas fa-image"></i> Hình ảnh</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group"><label>Ảnh chính sản phẩm</label><input type="file" name="image" class="form-control" data-preview="preview1"></div>
        <div class="image-preview" id="preview1"><i class="fas fa-camera me-2"></i>Chọn ảnh chính</div>
    </div>
    <div class="col-md-6">
        <div class="form-group"><label>Ảnh chi tiết (tối đa 3 ảnh)</label><input type="file" name="sub_images[]" class="form-control" multiple accept="image/*" id="subImagesInput"></div>
        <div id="subImagesPreview" class="d-flex gap-2 flex-wrap mt-2"></div>
    </div>
</div>
</div>
<div class="form-section">
<div class="section-title"><i class="fas fa-align-left"></i> Mô tả</div>
<div class="form-group"><label>Mô tả ngắn</label><textarea name="description" class="form-control" rows="2"></textarea></div>
</div>
<div class="form-actions">
<button type="submit" class="btn-submit"><i class="fas fa-save me-2"></i>Lưu sản phẩm</button>
<a href="index.php?page=admin-products" class="btn-cancel"><i class="fas fa-times me-2"></i>Hủy</a>
</div>
</form></div>
<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>