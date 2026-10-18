<?php
$productId = (int)($productId ?? ($product['MaSP'] ?? 0));
$hasStock = ProductModel::hasStock($productId);
?>
<div class="product-actions">
    <a href="index.php?page=product-detail&id=<?= $productId ?>" class="btn-action btn-view-detail" title="Xem chi tiết">
        <i class="fas fa-eye"></i>
    </a>
    <?php if ($hasStock): ?>
        <a href="index.php?page=add-to-cart&id=<?= $productId ?>" class="btn-action btn-add-cart" title="Thêm vào giỏ hàng">
            <i class="fas fa-shopping-cart"></i>
        </a>
    <?php else: ?>
        <a href="javascript:void(0)" class="btn-action btn-add-cart disabled" title="Hết hàng" style="opacity:0.5; cursor:not-allowed;">
            <i class="fas fa-shopping-cart"></i>
        </a>
        <script>
            (function() {
                var scriptTag = document.currentScript;
                if (scriptTag) {
                    var card = scriptTag.closest('.product-card');
                    if (card) {
                        card.classList.add('out-of-stock');
                        if (!card.querySelector('.oos-badge')) {
                            var imgDiv = card.querySelector('.product-image');
                            if (imgDiv) {
                                var badge = document.createElement('span');
                                badge.className = 'oos-badge';
                                badge.textContent = 'Hết hàng';
                                imgDiv.appendChild(badge);
                            }
                        }
                    }
                }
            })();
        </script>
    <?php endif; ?>
</div>
