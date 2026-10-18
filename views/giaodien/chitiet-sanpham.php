<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Chi tiết sản phẩm - VAB';
    $trangHienTai = 'product-detail';
    include 'dautrang.php';
}
$displayPrice = ProductModel::getDisplayPrice($product);
$mainImage = ProductModel::getImageUrl($product, 600, 700);
$totalStock = 0;
if (!empty($productSizes)) {
    foreach ($productSizes as $ps) {
        $totalStock += (int)$ps['SoLuong'];
    }
}
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section vab-reveal fade-in"><div class="container"><h1>CHI TIẾT SẢN PHẨM</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item"><a href="index.php?page=products">Sản phẩm</a></li><li class="breadcrumb-item active"><?= htmlspecialchars($product['TenSP']) ?></li></ol></nav>
</div></section>

<!-- PRODUCT DETAIL -->
<section class="pb-5"><div class="container"><div class="row">
    <div class="col-lg-6 mb-4 vab-reveal fade-right">
        <div class="product-detail-gallery">
            <img src="<?= $mainImage ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>" class="main-image" id="mainImage">
            <div class="thumbnail-list">
                <?php 
                $allImages = [];
                if (!empty($product['AnhChinh'])) {
                    $allImages[] = $product['AnhChinh'];
                }
                if (!empty($product['AnhPhu'])) {
                    $subImgs = array_filter(array_map('trim', explode(',', $product['AnhPhu'])));
                    $allImages = array_merge($allImages, $subImgs);
                }
                if (empty($allImages)) {
                    $allImages[] = '';
                }
                
                // Giới hạn hiển thị tối đa đúng 4 ảnh
                $allImages = array_slice($allImages, 0, 4);
                
                $t = 0;
                foreach ($allImages as $img): 
                    $t++;
                    $thumbUrl = '';
                    if (!empty($img)) {
                        if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0 || strpos($img, 'assets/') === 0) {
                            $thumbUrl = $img;
                        } else {
                            $thumbUrl = 'assets/images/products/' . $img;
                        }
                    } else {
                        $thumbUrl = "https://placehold.co/600x700/0a1628/fff?text=SP+" . (int)$product['MaSP'];
                    }
                ?>
                <img src="<?= $thumbUrl ?>" alt="Thumb <?= $t ?>" class="thumb-item <?= $t===1?'active':'' ?>" onclick="document.getElementById('mainImage').src=this.src;document.querySelectorAll('.thumb-item').forEach(el=>el.classList.remove('active'));this.classList.add('active');">
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6 vab-reveal fade-up">
        <div class="product-detail-info">
            <h1 class="product-name"><?= htmlspecialchars($product['TenSP']) ?></h1>
            <div class="product-meta">
                <span><strong>Thương hiệu:</strong> <span class="brand-name"><?= htmlspecialchars($product['brand']) ?></span></span>
                <span><strong>Danh mục:</strong> <?= htmlspecialchars($product['category']) ?></span>
                <span><strong>Bộ môn:</strong> <?= htmlspecialchars($product['TheThao']) ?></span>
            </div>
            <div class="product-price">
                <?= number_format($displayPrice,0,',',',') ?>đ
                <?php if (!empty($product['GiaKhuyenMai']) && $product['GiaKhuyenMai'] > 0): ?>
                <small class="text-muted text-decoration-line-through ms-2"><?= number_format($product['Gia'],0,',',',') ?>đ</small>
                <?php endif; ?>
            </div>
            <div class="product-desc">
                <p><?= htmlspecialchars($product['MoTaSP'] ?? 'Sản phẩm thể thao chính hãng tại VAB.') ?></p>
            </div>
            <form action="index.php" method="GET" class="add-to-cart-form">
                <input type="hidden" name="page" value="add-to-cart">
                <input type="hidden" name="id" value="<?= (int)$product['MaSP'] ?>">
                
                <div class="size-selector">
                    <label>Kích thước (Size):</label>
                    <div class="size-options">
                        <?php 
                        if (!empty($productSizes)):
                            $firstActive = false;
                            $defaultSize = '';
                            foreach ($productSizes as $ps):
                                $outOfStock = (int)$ps['SoLuong'] <= 0;
                                $activeClass = '';
                                if (!$outOfStock && !$firstActive) {
                                    $activeClass = 'active';
                                    $firstActive = true;
                                    $defaultSize = $ps['KichCo'];
                                }
                            ?>
                                <button type="button" class="size-btn <?= $activeClass ?> <?= $outOfStock ? 'disabled' : '' ?>" 
                                        data-size="<?= htmlspecialchars($ps['KichCo']) ?>"
                                        data-stock="<?= (int)$ps['SoLuong'] ?>"
                                        <?= $outOfStock ? 'disabled title="Hết hàng"' : '' ?>>
                                    <?= htmlspecialchars($ps['KichCo']) ?>
                                </button>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted">Không có kích thước cấu hình</span>
                        <?php endif; ?>
                    </div>
                    <!-- Hidden input to store selected size -->
                    <input type="hidden" name="size" id="selected-size" value="<?= htmlspecialchars($defaultSize) ?>">
                    <div class="size-stock-info mt-2" style="font-size: 13.5px; font-weight: 600;">
                        <!-- Stock status will show here via JS -->
                    </div>
                </div>
                
                <div class="quantity-selector">
                    <label>Số lượng:</label>
                    <div class="qty-control">
                        <button type="button" class="qty-minus">-</button>
                        <input type="text" name="qty" value="1" data-max-stock="<?= $totalStock ?>">
                        <button type="button" class="qty-plus">+</button>
                    </div>
                </div>
                
                <?php if ($totalStock <= 0): ?>
                    <button type="button" class="btn btn-add-cart disabled" disabled style="background:#dee2e6; border-color:#dee2e6; color:#6c757d; box-shadow:none; cursor:not-allowed;"><i class="fas fa-exclamation-circle me-2"></i>Hết hàng</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-add-cart"><i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div></div></section>

<!-- PRODUCT REVIEWS & RATING FORM -->
<section class="py-5 border-top vab-reveal fade-up"><div class="container">
    <div class="row">
        <!-- Review Summary & List -->
        <div class="col-lg-7 mb-4">
            <div class="reviews-section p-4 bg-white rounded-3 shadow-sm border">
                <h4 class="fw-bold mb-4 text-primary-color"><i class="fas fa-comments me-2 text-secondary-color"></i>Đánh giá từ khách hàng</h4>
                
                <!-- Average Rating Stats -->
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div class="text-center me-4 pe-4 border-end">
                        <h1 class="display-4 fw-extrabold text-dark mb-0"><?= number_format($avgRating, 1) ?></h1>
                        <div class="rating-stars mb-1">
                            <?php 
                            $floorRating = floor($avgRating);
                            $hasHalf = ($avgRating - $floorRating) >= 0.5;
                            for($i = 1; $i <= 5; $i++) {
                                if ($i <= $floorRating) {
                                    echo '<i class="fas fa-star text-warning" style="font-size:16px;"></i>';
                                } elseif ($i == $floorRating + 1 && $hasHalf) {
                                    echo '<i class="fas fa-star-half-alt text-warning" style="font-size:16px;"></i>';
                                } else {
                                    echo '<i class="far fa-star text-warning" style="font-size:16px;"></i>';
                                }
                            }
                            ?>
                        </div>
                        <small class="text-muted d-block">Đánh giá trung bình</small>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Tổng số đánh giá: <?= $reviewCount ?></h5>
                        <p class="text-muted mb-0" style="font-size: 13.5px;">Chỉ các đánh giá đã được kiểm duyệt mới hiển thị ở đây.</p>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="reviews-list" style="max-height: 500px; overflow-y: auto; padding-right: 10px;">
                    <?php if (empty($approvedReviews)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="far fa-star fa-2x mb-2 text-muted"></i>
                            <p class="mb-0">Chưa có đánh giá nào cho sản phẩm này.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($approvedReviews as $review): 
                            $rStars = (int)$review['SoSao'];
                            $rStarsText = str_repeat('★', $rStars) . str_repeat('☆', 5 - $rStars);
                            $rDate = date('d/m/Y H:i', strtotime($review['NgayDG']));
                        ?>
                            <div class="review-item mb-4 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <strong class="text-dark d-block"><?= htmlspecialchars($review['HoTenKH']) ?></strong>
                                        <span class="text-warning font-monospace" style="font-size: 15px; letter-spacing: 1.5px;"><?= $rStarsText ?></span>
                                    </div>
                                    <small class="text-muted"><i class="far fa-clock me-1"></i><?= htmlspecialchars($rDate) ?></small>
                                </div>
                                <p class="text-dark mb-0" style="line-height:1.5; font-size:14.5px;"><?= nl2br(htmlspecialchars($review['NoiDung'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Rating & Comment Form -->
        <div class="col-lg-5">
            <div class="review-form-container p-4 bg-white rounded-3 shadow-sm border">
                <h4 class="fw-bold mb-3 text-primary-color"><i class="fas fa-pen-fancy me-2 text-secondary-color"></i>Viết đánh giá của bạn</h4>
                
                <!-- Display Success/Error Messages from Session -->
                <?php if (isset($_SESSION['review_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                        <i class="fas fa-check-circle me-1"></i><?= $_SESSION['review_success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['review_success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['review_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i><?= $_SESSION['review_error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['review_error']); ?>
                <?php endif; ?>

                <?php if (!isset($_SESSION['ma_nguoidung'])): ?>
                    <div class="alert alert-warning border border-warning-subtle bg-warning-subtle text-warning-emphasis p-3 rounded mb-0">
                        <i class="fas fa-lock me-2"></i>Vui lòng <a href="index.php?page=login" class="fw-bold text-decoration-underline text-warning-emphasis">đăng nhập</a> để đánh giá sản phẩm.
                    </div>
                <?php elseif (!$canReview): ?>
                    <div class="alert alert-warning border border-warning-subtle bg-warning-subtle text-warning-emphasis p-3 rounded mb-0">
                        <i class="fas fa-info-circle me-2"></i>Bạn cần mua sản phẩm này và đơn hàng đã giao thành công mới có thể đánh giá.
                    </div>
                <?php elseif ($hasReviewed): ?>
                    <div class="alert alert-info border border-info-subtle bg-info-subtle text-info-emphasis p-3 rounded mb-0">
                        <i class="fas fa-check-double me-2"></i>Bạn đã đánh giá sản phẩm này.
                    </div>
                <?php else: ?>
                    <form method="POST" action="index.php?page=review-store" class="review-form">
                        <input type="hidden" name="product_id" value="<?= (int)$product['MaSP'] ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn số sao đánh giá</label>
                            <div class="star-rating-selector d-flex flex-row-reverse justify-content-end gap-2">
                                <input type="radio" id="star5" name="rating" value="5" class="d-none" required><label for="star5" class="fas fa-star" title="5 sao" style="font-size:24px; cursor:pointer; color:#ccc; transition:color 0.2s;"></label>
                                <input type="radio" id="star4" name="rating" value="4" class="d-none"><label for="star4" class="fas fa-star" title="4 sao" style="font-size:24px; cursor:pointer; color:#ccc; transition:color 0.2s;"></label>
                                <input type="radio" id="star3" name="rating" value="3" class="d-none"><label for="star3" class="fas fa-star" title="3 sao" style="font-size:24px; cursor:pointer; color:#ccc; transition:color 0.2s;"></label>
                                <input type="radio" id="star2" name="rating" value="2" class="d-none"><label for="star2" class="fas fa-star" title="2 sao" style="font-size:24px; cursor:pointer; color:#ccc; transition:color 0.2s;"></label>
                                <input type="radio" id="star1" name="rating" value="1" class="d-none"><label for="star1" class="fas fa-star" title="1 sao" style="font-size:24px; cursor:pointer; color:#ccc; transition:color 0.2s;"></label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label fw-bold">Nội dung bình luận</label>
                            <textarea id="comment" name="comment" class="form-control" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold" style="background:var(--color-secondary); border:none;">Gửi đánh giá</button>
                    </form>

                    <style>
                        /* CSS Star Rating Selector */
                        .star-rating-selector input:checked ~ label,
                        .star-rating-selector label:hover,
                        .star-rating-selector label:hover ~ label {
                            color: #ffc107 !important;
                        }
                    </style>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div></section>

<!-- RELATED PRODUCTS -->
<?php if (!empty($relatedProducts)): ?>
<section class="py-5 bg-light"><div class="container">
    <div class="section-title"><h2>SẢN PHẨM LIÊN QUAN</h2></div>
    <div class="row">
        <?php foreach($relatedProducts as $rel): ?>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="product-card">
                <div class="product-image">
                    <img src="<?= ProductModel::getImageUrl($rel) ?>" alt="<?= htmlspecialchars($rel['TenSP']) ?>">
                    <?php $productId = $rel['MaSP']; include __DIR__ . '/partials/product-actions.php'; ?>
                </div>
                <div class="product-info">
                    <div class="product-category"><?= htmlspecialchars($rel['category']) ?></div>
                    <a href="index.php?page=product-detail&id=<?= (int)$rel['MaSP'] ?>" class="product-name"><?= htmlspecialchars($rel['TenSP']) ?></a>
                    <div class="product-price"><span class="current-price"><?= number_format($rel['Gia'],0,',',',') ?>đ</span></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div></section>
<?php endif; ?>
<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>
