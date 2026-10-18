<?php

/**
 * Partial view for rendering product items (used for both initial load and AJAX load more)
 * Expects: $products array, $hasMore bool (optional for AJAX)
 */
$isAjax = !empty($hasMore) || isset($hasMore);
?>
<?php if (!empty($products)): foreach ($products as $product): 
        $isOos = !ProductModel::hasStock((int)$product['MaSP']);
        ?>
        <div class="col-lg-4 col-md-6 col-6 product-item" data-id="<?= (int)$product['MaSP'] ?>">
            <div class="product-card<?= $isOos ? ' out-of-stock' : '' ?>">
                <div class="product-image" style="position: relative;">
                    <img src="<?= ProductModel::getImageUrl($product) ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>">
                    <?php if ($isOos): ?>
                        <span class="oos-badge">Hết hàng</span>
                    <?php else: ?>
                        <?php if (!empty($product['GiaKhuyenMai']) && $product['GiaKhuyenMai'] > 0): 
                            $discountPercent = round((($product['Gia'] - $product['GiaKhuyenMai']) / $product['Gia']) * 100);
                            if ($discountPercent > 0):
                        ?>
                            <span class="product-badge badge-sale">-<?= $discountPercent ?>%</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php $productId = $product['MaSP'];
                    include __DIR__ . '/product-actions.php'; ?>
                </div>
                <div class="product-info">
                    <div class="product-category"><?= htmlspecialchars($product['category'] ?? 'Sản phẩm') ?></div>
                    <a href="index.php?page=product-detail&id=<?= (int)$product['MaSP'] ?>" class="product-name"><?= htmlspecialchars($product['TenSP']) ?></a>
                    <div class="product-price">
                        <?php if (!empty($product['GiaKhuyenMai']) && $product['GiaKhuyenMai'] > 0): ?>
                            <span class="current-price"><?= number_format($product['GiaKhuyenMai'], 0, ',', ',') ?>đ</span>
                            <span class="old-price"><?= number_format($product['Gia'], 0, ',', ',') ?>đ</span>
                        <?php else: ?>
                            <span class="current-price"><?= number_format($product['Gia'], 0, ',', ',') ?>đ</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;
else: ?>
    <div class="col-12 text-center py-5 load-more-empty">
        <p class="text-muted">Không còn sản phẩm nào.</p>
    </div>
<?php endif; ?>
<?php if ($isAjax && isset($loaded) && isset($total)): ?>
    <div class="col-12 load-more-data" data-loaded="<?= $loaded ?>" data-total="<?= $total ?>" data-hasmore="<?= $hasMore ? '1' : '0' ?>"></div>
<?php endif; ?>