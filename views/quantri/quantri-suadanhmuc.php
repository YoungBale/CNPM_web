<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Sửa danh mục - VAB Admin';
    $trangHienTai = 'admin-categories';
    include 'admin-header.php';
}
?>

<?php if (!empty($error)): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="admin-table-wrapper p-4">
            <h5 class="mb-4"><i class="fas fa-edit me-1"></i>Chỉnh sửa danh mục</h5>
            <form method="POST" action="index.php?page=admin-category-edit&id=<?= $category['MaDanhMuc'] ?>">
                <input type="hidden" name="id" value="<?= $category['MaDanhMuc'] ?>">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên danh mục *</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['TenDM']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Đường dẫn (Slug)</label>
                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($category['DuongDan']) ?>" placeholder="Để trống để tự động sinh theo tên">
                    <small class="text-muted">Slug quyết định đường dẫn URL (ví dụ: <code>ao-the-thao</code>). Chỉ dùng ký tự thường không dấu và dấu gạch ngang.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Trạng thái hiển thị *</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= $category['TrangThai'] == 1 ? 'selected' : '' ?>>Đang hoạt động (Hiển thị)</option>
                        <option value="0" <?= $category['TrangThai'] == 0 ? 'selected' : '' ?>>Đang ẩn</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: var(--admin-secondary); border: none;">
                        <i class="fas fa-save me-1"></i> Lưu thay đổi
                    </button>
                    <a href="index.php?page=admin-categories" class="btn btn-secondary rounded-pill px-4" style="background: var(--admin-muted); border: none;">
                        Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>
