<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Chi tiết đơn hàng - VAB';
    $trangHienTai = 'orders';
    include 'dautrang.php';
}

if (!class_exists('ProductModel')) {
    include './models/ProductModel.php';
}

$statusInfo = OrderController::getStatusInfo($order['TrangThaiDH']);
$orderCode = '#ORD' . str_pad($order['MaDonHang'], 5, '0', STR_PAD_LEFT);
$formattedDate = date('d/m/Y H:i', strtotime($order['NgayTao']));
$totalPrice = number_format($order['TongTien'], 0, ',', '.') . 'đ';
?>

<!-- BREADCRUMB -->
<section class="breadcrumb-section">
    <div class="container">
        <h1>CHI TIẾT ĐƠN HÀNG</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="index.php?page=orders">Đơn hàng của tôi</a></li>
                <li class="breadcrumb-item active">Chi tiết đơn hàng</li>
            </ol>
        </nav>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <!-- Display Success/Error Messages -->
        <?php if (isset($_SESSION['order_msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
                <i class="fas fa-check-circle me-2"></i><?= $_SESSION['order_msg'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['order_msg']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['order_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background-color: #f8d7da; color: #842029;">
                <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['order_error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['order_error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['review_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
                <i class="fas fa-star me-2"></i><?= $_SESSION['review_success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['review_success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['review_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="background-color: #f8d7da; color: #842029;">
                <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['review_error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['review_error']); ?>
        <?php endif; ?>

        <div class="row">
            <!-- Thông tin đơn hàng & Người nhận -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 rounded-3 mb-4 bg-white">
                    <div class="card-header bg-light py-3 border-0">
                        <h5 class="fw-bold mb-0 text-primary-color"><i class="fas fa-info-circle me-2 text-secondary-color"></i>Thông tin chung</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-muted">Mã đơn hàng:</span>
                            <strong class="text-dark"><?= htmlspecialchars($orderCode) ?></strong>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-muted">Ngày đặt:</span>
                            <span class="text-dark"><?= htmlspecialchars($formattedDate) ?></span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Trạng thái:</span>
                            <span class="status-badge <?= htmlspecialchars($statusInfo['class']) ?>">
                                <?= htmlspecialchars($statusInfo['label']) ?>
                            </span>
                        </div>
                        <div class="mb-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Tổng thanh toán:</span>
                            <strong class="text-danger fs-5"><?= htmlspecialchars($totalPrice) ?></strong>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-header bg-light py-3 border-0">
                        <h5 class="fw-bold mb-0 text-primary-color"><i class="fas fa-shipping-fast me-2 text-secondary-color"></i>Thông tin nhận hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted mb-1 d-block">Người nhận hàng:</label>
                            <strong class="text-dark"><?= htmlspecialchars($order['HoTenKH']) ?></strong>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted mb-1 d-block">Số điện thoại:</label>
                            <span class="text-dark"><?= htmlspecialchars($order['SDT']) ?></span>
                        </div>
                        <?php if (!empty($order['email'])): ?>
                            <div class="mb-3">
                                <label class="text-muted mb-1 d-block">Email:</label>
                                <span class="text-dark"><?= htmlspecialchars($order['email']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="text-muted mb-1 d-block">Địa chỉ giao hàng:</label>
                            <span class="text-dark d-block" style="line-height: 1.4;"><?= htmlspecialchars($order['DiaChi']) ?></span>
                        </div>
                        <?php if (!empty($order['PhuongThucTT'])): ?>
                            <div class="mb-3">
                                <label class="text-muted mb-1 d-block">Phương thức thanh toán:</label>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded">
                                    <?= htmlspecialchars($order['PhuongThucTT'] === 'COD' ? 'Thanh toán COD' : ($order['PhuongThucTT'] === 'Bank' ? 'Chuyển khoản ngân hàng' : 'Ví điện tử')) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($order['GhiChu'])): ?>
                            <div class="mb-0">
                                <label class="text-muted mb-1 d-block">Ghi chú đơn hàng:</label>
                                <em class="text-dark-gray text-muted" style="font-size: 13.5px;">"<?= htmlspecialchars($order['GhiChu']) ?>"</em>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Danh sách sản phẩm mua -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3 bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0 text-primary-color"><i class="fas fa-shopping-bag me-2 text-secondary-color"></i>Sản phẩm đã mua</h5>
                        <a href="index.php?page=orders" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                            <i class="fas fa-chevron-left me-1"></i>Danh sách đơn hàng
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle cart-table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="py-3 px-4">Sản phẩm</th>
                                    <th scope="col" class="py-3 text-end" style="width: 20%">Đơn giá</th>
                                    <th scope="col" class="py-3 text-center" style="width: 15%">Số lượng</th>
                                    <th scope="col" class="py-3 text-end" style="width: 20%">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $itemsSubtotalSum = 0;
                                foreach ($orderItems as $item): 
                                    $itemSubtotal = $item['SoLuongSP'] * $item['DonGia'];
                                    $itemsSubtotalSum += $itemSubtotal;
                                    $productDummy = [
                                        'MaSP' => $item['MaSP'],
                                        'AnhChinh' => $item['AnhChinh'] ?? ''
                                    ];
                                    $imageUrl = ProductModel::getImageUrl($productDummy, 80, 80);
                                ?>
                                    <tr>
                                        <td data-label="Sản phẩm" class="px-4">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($item['TenSP']) ?>" style="width: 65px; height: 65px; object-fit: cover; border-radius: 8px; margin-right: 15px;" class="border">
                                                <div>
                                                    <span class="fw-bold text-dark d-block" style="font-size: 14.5px; line-height: 1.3;"><?= htmlspecialchars($item['TenSP']) ?></span>
                                                    <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                                                        <small class="text-muted">Mã SP: #<?= (int)$item['MaSP'] ?></small>
                                                        <?php if (!empty($item['KichCo'])): ?>
                                                            <span class="badge bg-secondary text-white rounded-pill px-2 py-0.5" style="font-size: 11.5px;">Cỡ: <?= htmlspecialchars($item['KichCo']) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <!-- Đánh giá sản phẩm -->
                                                    <?php if ($order['TrangThaiDH'] === 'completed' || $order['TrangThaiDH'] === 'delivered'): ?>
                                                        <?php if (isset($item['da_danh_gia']) && $item['da_danh_gia']): ?>
                                                            <div class="mt-2">
                                                                 <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded" style="font-size: 11px;">
                                                                     <i class="fas fa-check-circle me-1"></i>Đã đánh giá
                                                                 </span>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Đơn giá" class="text-end text-dark font-monospace"><?= number_format($item['DonGia'], 0, ',', '.') ?>đ</td>
                                        <td data-label="Số lượng" class="text-center text-dark fw-semibold"><?= (int)$item['SoLuongSP'] ?></td>
                                        <td data-label="Thành tiền" class="text-end text-danger fw-bold font-monospace"><?= number_format($itemSubtotal, 0, ',', '.') ?>đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light border-top">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold py-3 text-primary-color">Tạm tính:</td>
                                    <td class="text-end fw-bold py-3 text-dark font-monospace"><?= number_format($itemsSubtotalSum, 0, ',', '.') ?>đ</td>
                                </tr>
                                <?php if (!empty($order['MaGiamGia'])): ?>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold py-3 text-primary-color border-0">Mã giảm giá (<?= htmlspecialchars($order['MaGiamGia']) ?>):</td>
                                        <td class="text-end fw-bold py-3 text-danger border-0 font-monospace">-<?= number_format($order['SoTienGiam'], 0, ',', '.') ?>đ</td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold py-3 text-primary-color border-0">Phí giao hàng:</td>
                                    <td class="text-end fw-bold py-3 text-success border-0">Miễn phí</td>
                                </tr>
                                <tr class="fs-5 border-top border-dark-subtle">
                                    <td colspan="3" class="text-end fw-extrabold py-3 text-primary-color">Tổng thanh toán:</td>
                                    <td class="text-end fw-extrabold py-3 text-danger font-monospace"><?= number_format($order['TongTien'], 0, ',', '.') ?>đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Thao tác đơn hàng (Đánh giá, Trả hàng, Hủy đơn) -->
                <?php if ($order['TrangThaiDH'] === 'completed' || $order['TrangThaiDH'] === 'delivered' || $order['TrangThaiDH'] === 'returning' || $order['TrangThaiDH'] === 'returned' || $order['TrangThaiDH'] === 'pending'): ?>
                    <div class="card shadow-sm border-0 rounded-3 bg-white p-4 mt-4">
                        <h5 class="fw-bold mb-3 text-primary-color"><i class="fas fa-tasks me-2 text-secondary-color"></i>Thao tác đơn hàng</h5>
                        
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <?php if ($order['TrangThaiDH'] === 'pending'): ?>
                                <a href="index.php?page=order-cancel&id=<?= (int)$order['MaDonHang'] ?>" 
                                   class="btn btn-danger rounded-pill px-4 py-2 text-white fw-bold text-decoration-none border-0" 
                                   onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')">
                                    <i class="fas fa-times-circle me-1"></i>Hủy đơn hàng
                                </a>
                                
                            <?php elseif ($order['TrangThaiDH'] === 'delivered' || $order['TrangThaiDH'] === 'completed'): ?>
                                <!-- Nút yêu cầu trả hàng -->
                                <button type="button" 
                                        class="btn btn-outline-danger rounded-pill px-4 py-2 fw-bold" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#returnOrderModal">
                                    <i class="fas fa-undo-alt me-1"></i>Yêu cầu trả hàng / Hoàn tiền
                                </button>
                                
                                <!-- Nút đánh giá sản phẩm -->
                                <?php 
                                $canReviewAny = false;
                                foreach ($orderItems as $item) {
                                    if (!isset($item['da_danh_gia']) || !$item['da_danh_gia']) {
                                        $canReviewAny = true;
                                        break;
                                    }
                                }
                                if ($canReviewAny): 
                                ?>
                                    <div class="w-100 mt-2">
                                        <h6 class="fw-bold text-muted mb-2 text-start" style="font-size: 13px;">ĐÁNH GIÁ SẢN PHẨM ĐÃ MUA:</h6>
                                        <div class="d-flex flex-column gap-2">
                                            <?php foreach ($orderItems as $item): 
                                                if (isset($item['da_danh_gia']) && $item['da_danh_gia']) continue;
                                                $productDummy = [
                                                    'MaSP' => $item['MaSP'],
                                                    'AnhChinh' => $item['AnhChinh'] ?? ''
                                                ];
                                                $imageUrl = ProductModel::getImageUrl($productDummy, 50, 50);
                                            ?>
                                                <div class="d-flex align-items-center justify-content-between p-2 border rounded-3 bg-light-subtle">
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($item['TenSP']) ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;" class="border me-2">
                                                        <span class="fw-semibold text-dark text-start" style="font-size: 13.5px;"><?= htmlspecialchars($item['TenSP']) ?></span>
                                                    </div>
                                                    <button type="button" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold text-dark border-0" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#reviewModal<?= (int)$item['MaSP'] ?>" 
                                                            style="background: #ffc107; font-size: 12px;">
                                                        <i class="fas fa-star me-1 text-dark"></i>Đánh giá
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="w-100 text-success fw-bold mt-2 text-start" style="font-size: 13.5px;">
                                        <i class="fas fa-check-circle me-1"></i>Bạn đã hoàn thành đánh giá tất cả sản phẩm trong đơn hàng này!
                                    </div>
                                <?php endif; ?>
                                
                            <?php elseif ($order['TrangThaiDH'] === 'returning'): ?>
                                <div class="w-100">
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle p-3 rounded w-100 d-block text-start fs-6 fw-bold">
                                        <i class="fas fa-clock me-2"></i>Đang yêu cầu trả hàng / hoàn tiền
                                    </span>
                                    <?php if (!empty($order['LyDoTraHang'])): ?>
                                        <div class="mt-2 p-2 border rounded-3 bg-light text-start">
                                            <strong style="font-size: 13px;">Lý do trả hàng:</strong>
                                            <span style="font-size: 13px; color: var(--color-dark);"><?= htmlspecialchars($order['LyDoTraHang']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                            <?php elseif ($order['TrangThaiDH'] === 'returned'): ?>
                                <div class="w-100">
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle p-3 rounded w-100 d-block text-start fs-6 fw-bold">
                                        <i class="fas fa-check-double me-2"></i>Đã trả hàng / Hoàn tiền thành công
                                    </span>
                                    <?php if (!empty($order['LyDoTraHang'])): ?>
                                        <div class="mt-2 p-2 border rounded-3 bg-light text-start">
                                            <strong style="font-size: 13px;">Lý do trả hàng:</strong>
                                            <span style="font-size: 13px; color: var(--color-dark);"><?= htmlspecialchars($order['LyDoTraHang']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Modal Yêu cầu Trả hàng -->
<div class="modal fade" id="returnOrderModal" tabindex="-1" aria-labelledby="returnOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h5 class="modal-title fw-bold" id="returnOrderModalLabel">Yêu cầu Trả hàng / Hoàn tiền</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="index.php?page=order-return">
                <div class="modal-body p-4 text-start">
                    <input type="hidden" name="order_id" value="<?= (int)$order['MaDonHang'] ?>">
                    
                    <div class="alert alert-info border-0 p-3 mb-3 text-info-emphasis bg-info-subtle" style="font-size: 13.5px;">
                        <i class="fas fa-info-circle me-2"></i>Vui lòng cho cửa hàng biết lý do bạn muốn trả hàng. Chúng tôi sẽ phản hồi yêu cầu của bạn sớm nhất có thể.
                    </div>
                    
                    <div class="mb-0">
                        <label for="return-reason" class="form-label fw-bold text-dark">Lý do trả hàng:</label>
                        <textarea name="reason" id="return-reason" rows="4" class="form-control border-secondary-subtle" placeholder="Nhập lý do chi tiết (ví dụ: sản phẩm bị lỗi, không vừa kích thước, giao sai mẫu...)" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 text-white fw-bold border-0">Gửi yêu cầu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modals Đánh giá sản phẩm -->
<?php if ($order['TrangThaiDH'] === 'completed' || $order['TrangThaiDH'] === 'delivered'): ?>
    <?php foreach ($orderItems as $item): 
        if (isset($item['da_danh_gia']) && $item['da_danh_gia']) continue;
        $productDummy = [
            'MaSP' => $item['MaSP'],
            'AnhChinh' => $item['AnhChinh'] ?? ''
        ];
        $imageUrl = ProductModel::getImageUrl($productDummy, 80, 80);
    ?>
        <div class="modal fade" id="reviewModal<?= (int)$item['MaSP'] ?>" tabindex="-1" aria-labelledby="reviewModalLabel<?= (int)$item['MaSP'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white border-0 py-3">
                        <h5 class="modal-title fw-bold" id="reviewModalLabel<?= (int)$item['MaSP'] ?>">Đánh giá sản phẩm</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="index.php?page=review-store">
                        <div class="modal-body p-4 text-start">
                            <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($item['TenSP']) ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" class="border me-3">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 14px; line-height: 1.3;"><?= htmlspecialchars($item['TenSP']) ?></h6>
                                    <?php if (!empty($item['KichCo'])): ?>
                                        <span class="badge bg-secondary text-white rounded-pill px-2 py-0.5" style="font-size: 10.5px;">Cỡ: <?= htmlspecialchars($item['KichCo']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <input type="hidden" name="product_id" value="<?= (int)$item['MaSP'] ?>">
                            <input type="hidden" name="redirect_to" value="index.php?page=order-detail&id=<?= (int)$order['MaDonHang'] ?>">

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark d-block">Chọn số sao đánh giá:</label>
                                <div class="star-rating-selector d-flex flex-row-reverse justify-content-end gap-2 my-2">
                                    <input type="radio" id="modal-star5-<?= (int)$item['MaSP'] ?>" name="rating" value="5" class="d-none" required>
                                    <label for="modal-star5-<?= (int)$item['MaSP'] ?>" class="fas fa-star" title="5 sao" style="font-size:26px; cursor:pointer; color:#ccc; transition:color 0.2s;"></label>
                                    
                                    <input type="radio" id="modal-star4-<?= (int)$item['MaSP'] ?>" name="rating" value="4" class="d-none">
                                    <label for="modal-star4-<?= (int)$item['MaSP'] ?>" class="fas fa-star" title="4 sao" style="font-size:26px; cursor:pointer; color:#ccc; transition:color 0.2s;"></label>
                                    
                                    <input type="radio" id="modal-star3-<?= (int)$item['MaSP'] ?>" name="rating" value="3" class="d-none">
                                    <label for="modal-star3-<?= (int)$item['MaSP'] ?>" class="fas fa-star" title="3 sao" style="font-size:26px; cursor:pointer; color:#ccc; transition:color 0.2s;"></label>
                                    
                                    <input type="radio" id="modal-star2-<?= (int)$item['MaSP'] ?>" name="rating" value="2" class="d-none">
                                    <label for="modal-star2-<?= (int)$item['MaSP'] ?>" class="fas fa-star" title="2 sao" style="font-size:26px; cursor:pointer; color:#ccc; transition:color 0.2s;"></label>
                                    
                                    <input type="radio" id="modal-star1-<?= (int)$item['MaSP'] ?>" name="rating" value="1" class="d-none">
                                    <label for="modal-star1-<?= (int)$item['MaSP'] ?>" class="fas fa-star" title="1 sao" style="font-size:26px; cursor:pointer; color:#ccc; transition:color 0.2s;"></label>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label for="comment-<?= (int)$item['MaSP'] ?>" class="form-label fw-bold text-dark">Nội dung bình luận:</label>
                                <textarea name="comment" id="comment-<?= (int)$item['MaSP'] ?>" rows="4" class="form-control border-secondary-subtle" placeholder="Nhập chia sẻ của bạn về sản phẩm này..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light p-3">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                            <button type="submit" class="btn btn-warning rounded-pill px-4 text-dark fw-bold border-0" style="background: #ffc107;">Gửi đánh giá</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<style>
    /* CSS Star Rating Selector in modal */
    .star-rating-selector input:checked ~ label,
    .star-rating-selector label:hover,
    .star-rating-selector label:hover ~ label {
        color: #ffc107 !important;
    }
</style>

<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>
