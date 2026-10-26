<?php

/**
 * ProductController - Handles product-related pages, Cart and Search features
 * Đợt 3: Bổ sung xử lý Giỏ hàng (Cart) và Tìm kiếm sản phẩm (Search)
 * Include model trước, sau đó include header -> view content -> footer
 */
class ProductController
{

    public function danhSach()
    {
        $tieuDeTrang = 'Sản phẩm - VAB Thời Trang Thể Thao';
        $trangHienTai = 'products';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        include './views/giaodien/dautrang.php';
        include './views/giaodien/sanpham.php';
        include './views/giaodien/chantrang.php';
    }

    public function chiTiet()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: index.php?page=products');
            exit;
        }

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();
        $product = $productModel->getById((int) $id);

        if (!$product || (int)$product['TrangThai'] !== 1) {
            $tieuDeTrang = 'Không tìm thấy sản phẩm - VAB';
            $trangHienTai = 'products';
            $nhungTuController = true;

            include './views/giaodien/dautrang.php';
            echo '<section class="py-5"><div class="container text-center"><h2>Không tìm thấy sản phẩm</h2><p class="text-muted mt-2">Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.</p><a href="index.php?page=products" class="btn btn-secondary rounded-pill mt-3 px-4" style="background:var(--color-secondary);border:none;">Quay lại danh sách</a></div></section>';
            include './views/giaodien/chantrang.php';
            return;
        }

        $tieuDeTrang = $product['TenSP'] . ' - VAB';
        $trangHienTai = 'products';
        $nhungTuController = true;
        $relatedProducts = $productModel->getNewProducts(4);

        // Nạp Model Đánh giá và lấy dữ liệu
        if (!class_exists('ReviewModel')) {
            require './models/ReviewModel.php';
        }
        $reviewModel = new ReviewModel();
        $approvedReviews = $reviewModel->getApprovedReviewsByProduct((int)$id);
        $avgRating = $reviewModel->getAverageRating((int)$id);
        $reviewCount = $reviewModel->getReviewCount((int)$id);

        $userId = $_SESSION['ma_nguoidung'] ?? null;
        $canReview = false;
        $hasReviewed = false;
        if ($userId) {
            $canReview = $reviewModel->canUserReview($userId, (int)$id);
            $hasReviewed = $reviewModel->hasUserReviewed($userId, (int)$id);
        }

        $productSizes = $productModel->getSizes((int)$id);

        include './views/giaodien/dautrang.php';
        include './views/giaodien/chitiet-sanpham.php';
        include './views/giaodien/chantrang.php';
    }

    /**
     * CHỨC NĂNG: THÊM SẢN PHẨM VÀO GIỎ HÀNG (themGioHang)
     * ĐẦU VÀO: Lấy MaSP (?id=), số lượng (?qty=, mặc định là 1), và kích cỡ (?size=) từ URL.
     * CƠ CHẾ XỬ LÝ:
     * 1. Kiểm tra tính hợp lệ của sản phẩm trong Cơ sở dữ liệu (phải tồn tại và đang kích hoạt TrangThai = 1).
     * 2. Kiểm tra tồn kho của Size được chọn trong bảng QUANLYSP. Nếu không chọn size hoặc hết hàng thì báo lỗi.
     * 3. Kiểm tra xem số lượng trong giỏ hàng hiện tại cộng thêm số lượng mới có vượt quá tồn kho thực tế của size đó không.
     * 4. Giỏ hàng được lưu trong phiên làm việc (`$_SESSION['giohang']`).
     *    Khóa (Key) của mỗi mục trong giỏ hàng có cấu trúc: [MaSP]_[KichCo] (ví dụ: '102_L' hoặc '102_M').
     *    Điều này giúp tách biệt cùng một sản phẩm nhưng khác size thành các hàng riêng biệt trong giỏ hàng.
     * 5. Trả về kết quả dạng JSON nếu request gửi bằng AJAX để cập nhật số lượng badge giỏ hàng trên giao diện mà không cần load lại trang.
     */
    public function themGioHang()
    {
        $id = $_GET['id'] ?? null;
        $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
        if ($qty < 1) $qty = 1;
        $size = isset($_GET['size']) ? trim($_GET['size']) : '';

        // Nếu không có mã sản phẩm, trả về lỗi
        if (!$id) {
            if (isset($_GET['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
                exit;
            }
            header('Location: index.php?page=products');
            exit;
        }

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();
        $product = $productModel->getById((int) $id);

        // Đảm bảo sản phẩm có thật và đang được bán
        if (!$product || (int)$product['TrangThai'] !== 1) {
            if (isset($_GET['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại hoặc đã bị ẩn.']);
                exit;
            }
            header('Location: index.php?page=products');
            exit;
        }

        // Lấy thông tin kích cỡ và số lượng tồn kho thực tế của sản phẩm
        $productSizes = $productModel->getSizes((int)$id);
        $availableStock = 0;
        $sizeExists = false;

        if (!empty($productSizes)) {
            foreach ($productSizes as $ps) {
                if ($ps['KichCo'] === $size) {
                    $availableStock = (int)$ps['SoLuong'];
                    $sizeExists = true;
                    break;
                }
            }
            // Nếu khách hàng truyền size không tồn tại trong danh sách size của sản phẩm
            if (!$sizeExists && !empty($size)) {
                if (isset($_GET['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Kích thước không hợp lệ.']);
                    exit;
                }
                header('Location: index.php?page=product-detail&id=' . (int)$id);
                exit;
            }
            // Bắt buộc phải chọn kích cỡ sản phẩm mới cho thêm vào giỏ hàng
            if (empty($size)) {
                if (isset($_GET['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Vui lòng chọn kích thước.']);
                    exit;
                }
                header('Location: index.php?page=product-detail&id=' . (int)$id);
                exit;
            }
        } else {
            $availableStock = 0; // Không có kích cỡ nào nghĩa là hết hàng hoàn toàn
        }

        // Tạo mảng giỏ hàng trong session nếu chưa tồn tại
        if (!isset($_SESSION['giohang'])) {
            $_SESSION['giohang'] = [];
        }

        // Ưu tiên lấy giá khuyến mãi nếu có
        $price = ProductModel::getDisplayPrice($product);
        $pid = (int) $product['MaSP'];

        // Khóa định danh của sản phẩm trong giỏ hàng (kết hợp Mã sản phẩm và Size)
        $cartKey = !empty($size) ? $pid . '_' . $size : $pid;

        // Kiểm tra xem số lượng sản phẩm cộng dồn trong giỏ hàng có vượt quá tồn kho tối đa của size đó không
        $currentCartQty = isset($_SESSION['giohang'][$cartKey]) ? (int)$_SESSION['giohang'][$cartKey]['SoLuongSP'] : 0;
        if ($currentCartQty + $qty > $availableStock) {
            $remainingAllowed = $availableStock - $currentCartQty;
            if ($remainingAllowed <= 0) {
                $msg = 'Bạn đã thêm tối đa số lượng sản phẩm này trong giỏ hàng (' . $availableStock . ' sản phẩm).';
            } else {
                $msg = 'Rất tiếc, bạn chỉ có thể thêm tối đa ' . $remainingAllowed . ' sản phẩm cho kích thước này vào giỏ hàng.';
            }
            if (isset($_GET['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            header('Location: index.php?page=product-detail&id=' . $pid);
            exit;
        }

        // Nếu sản phẩm đã tồn tại trong giỏ hàng với cùng size, cộng dồn số lượng
        if (isset($_SESSION['giohang'][$cartKey])) {
            $_SESSION['giohang'][$cartKey]['SoLuongSP'] += $qty;
        } else {
            // Ngược lại, thêm mới thông tin sản phẩm vào giỏ hàng
            $_SESSION['giohang'][$cartKey] = [
                'MaSP'      => $pid,
                'TenSP'     => $product['TenSP'],
                'KichCo'    => $size,
                'DonGia'    => $price,
                'AnhChinh'  => ProductModel::getImageUrl($product, 80, 80),
                'SoLuongSP' => $qty,
            ];
        }

        // Tính tổng số lượng sản phẩm trong giỏ hàng hiện tại để cập nhật số hiển thị (Badge) trên Header
        $cartCount = 0;
        if (!empty($_SESSION['giohang'])) {
            foreach ($_SESSION['giohang'] as $item) {
                $cartCount += (int)($item['SoLuongSP'] ?? 0);
            }
        }

        // Trả về dữ liệu JSON nếu gọi qua AJAX (giúp trang web không bị tải lại)
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng thành công!',
                'cartCount' => $cartCount
            ]);
            exit;
        }

        // Nếu là request thông thường, tự động chuyển hướng qua trang xem giỏ hàng
        header('Location: index.php?page=cart');
        exit;
    }

    public function nam()
    {
        $tieuDeTrang = 'Thời trang nam - VAB';
        $trangHienTai = 'men';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/thoitrangnam.php';
        include './views/giaodien/chantrang.php';
    }

    public function nu()
    {
        $tieuDeTrang = 'Thời trang nữ - VAB';
        $trangHienTai = 'women';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/thoitrangnu.php';
        include './views/giaodien/chantrang.php';
    }

    public function giamGia()
    {
        $tieuDeTrang = 'Sale - VAB';
        $trangHienTai = 'sale';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        
        $productModel = new ProductModel();
        
        // Cấu hình phân trang
        $limit = 12;
        $totalProducts = $productModel->getSaleItemsCount();
        
        $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        
        $totalPages = ceil($totalProducts / $limit);
        if ($totalPages < 1) {
            $totalPages = 1;
        }
        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }
        
        $offset = ($currentPage - 1) * $limit;
        $currentSort = isset($_GET['sort']) ? trim($_GET['sort']) : 'default';
        
        $products = $productModel->getSaleItemsFiltered($offset, $limit, $currentSort);

        include './views/giaodien/dautrang.php';
        include './views/giaodien/giamgia.php';
        include './views/giaodien/chantrang.php';
    }

    public function timKiem()
    {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        if (empty($keyword)) {
            header('Location: index.php?page=products');
            exit;
        }
        $tieuDeTrang = 'Tìm kiếm: ' . htmlspecialchars($keyword) . ' - VAB';
        $trangHienTai = 'search';
        $nhungTuController = true;
        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();
        $products = $productModel->search($keyword);
        $totalResults = count($products);
        include './views/giaodien/dautrang.php';
        include './views/giaodien/ketqua-timkiem.php';
        include './views/giaodien/chantrang.php';
    }

    public function goiY()
    {
        header('Content-Type: application/json');
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        if (empty($keyword)) {
            echo json_encode([]);
            exit;
        }

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();
        $products = $productModel->suggest($keyword, 6);

        $results = [];
        foreach ($products as $p) {
            $imageUrl = ProductModel::getImageUrl($p, 100, 100);
            $results[] = [
                'id' => (int)$p['MaSP'],
                'name' => $p['TenSP'],
                'price' => (int)$p['Gia'],
                'promo_price' => !empty($p['GiaKhuyenMai']) ? (int)$p['GiaKhuyenMai'] : null,
                'image' => $imageUrl
            ];
        }

        echo json_encode($results);
        exit;
    }

    public function taiThem()
    {
        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
        $limit = 12;
        $products = $productModel->getWithOffset($offset, $limit);
        $total = $productModel->getCount();
        $loaded = $offset + count($products);
        $hasMore = $loaded < $total;
        include './views/giaodien/partials/product-items.php';
    }

    public function gioHang()
    {
        $tieuDeTrang = 'Giỏ hàng - VAB';
        $trangHienTai = 'cart';
        $nhungTuController = true;
        $cartItems = $_SESSION['giohang'] ?? [];
        include './views/giaodien/dautrang.php';
        include './views/giaodien/giohang.php';
        include './views/giaodien/chantrang.php';
    }

    /**
     * CHỨC NĂNG: CẬP NHẬT SỐ LƯỢNG SẢN PHẨM TRONG GIỎ HÀNG (capNhatGioHang)
     * ĐẦU VÀO: Lấy mã Khóa giỏ hàng (?id= dạng [MaSP]_[Size] hoặc [MaSP]) và Số lượng mới (?qty=) từ URL.
     * CƠ CHẾ XỬ LÝ:
     * 1. Kiểm tra xem sản phẩm có trong giỏ hàng không. Nếu số lượng nhập vào <= 0, tự động xóa sản phẩm ra khỏi giỏ hàng.
     * 2. Phân tách Khóa giỏ hàng bằng hàm explode() để lấy ra Mã sản phẩm và Size của nó.
     * 3. Kiểm tra lại tồn kho hiện thời của Size đó trong CSDL.
     * 4. Nếu người dùng chỉnh số lượng lớn hơn tồn kho thực tế, hệ thống sẽ tự động hạ số lượng đó xuống bằng mức tồn kho tối đa có thể bán.
     * 5. Lưu lại số lượng mới vào `$_SESSION['giohang']` và chuyển hướng quay lại trang giỏ hàng.
     */
    public function capNhatGioHang()
    {
        $key = isset($_GET['id']) ? trim($_GET['id']) : '';
        $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
        if (!empty($key) && isset($_SESSION['giohang'][$key])) {
            if ($qty > 0) {
                // Phân tích key để lấy product_id và size (Ví dụ: '102_M' tách thành [102, 'M'])
                $parts = explode('_', $key);
                $pid = (int)$parts[0];
                $size = isset($parts[1]) ? trim($parts[1]) : '';

                if (!class_exists('ProductModel')) {
                    require './models/ProductModel.php';
                }
                $productModel = new ProductModel();
                $product = $productModel->getById($pid);

                if ($product) {
                    $productSizes = $productModel->getSizes($pid);
                    $availableStock = 0;
                    $sizeExists = false;

                    // Tìm số lượng tồn kho của size sản phẩm hiện tại
                    if (!empty($productSizes)) {
                        foreach ($productSizes as $ps) {
                            if ($ps['KichCo'] === $size) {
                                $availableStock = (int)$ps['SoLuong'];
                                $sizeExists = true;
                                break;
                            }
                        }
                    } else {
                        $availableStock = 0;
                    }

                    // BẢO VỆ TỒN KHO: Tự động khóa giới hạn nếu số lượng mua vượt quá số lượng tồn
                    if ($qty > $availableStock) {
                        $qty = $availableStock;
                    }
                }

                // Cập nhật lại số lượng mới được duyệt
                $_SESSION['giohang'][$key]['SoLuongSP'] = $qty;
            } else {
                // Nếu số lượng <= 0, xóa hẳn mục này khỏi giỏ hàng
                unset($_SESSION['giohang'][$key]);
            }
        }
        header('Location: index.php?page=cart');
        exit;
    }

    /**
     * CHỨC NĂNG: XÓA SẢN PHẨM KHỎI GIỎ HÀNG (xoaGioHang)
     * ĐẦU VÀO: Nhận mã Khóa giỏ hàng (?id=) và xóa bỏ mục đó khỏi Session giỏ hàng.
     */
    public function xoaGioHang()
    {
        $key = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!empty($key) && isset($_SESSION['giohang'][$key])) {
            unset($_SESSION['giohang'][$key]);
        }
        header('Location: index.php?page=cart');
        exit;
    }

    /**
     * CHỨC NĂNG: ĐẶT HÀNG & THANH TOÁN (thanhToan)
     * ĐẦU VÀO: Các thông tin giao hàng POST lên từ Form thanh toán (Họ tên, SĐT, Địa chỉ, Email, Ghi chú, Phương thức thanh toán).
     * CƠ CHẾ XỬ LÝ ĐẶT HÀNG & THANH TOÁN (ORDER WORKFLOW):
     * 1. Kiểm tra đăng nhập: Khách hàng bắt buộc phải đăng nhập thì mới được thanh toán (chống đơn ảo).
     * 2. Lấy thông tin giỏ hàng từ `$_SESSION['giohang']`. Nếu trống, chuyển về giỏ hàng.
     * 3. Tính tổng tiền giỏ hàng (tổng giá sản phẩm * số lượng).
     * 4. Xử lý và kiểm tra mã giảm giá (Voucher) áp dụng:
     *    - Đọc mã giảm giá lưu tạm trong Session `$_SESSION['applied_coupon']`.
     *    - Đối soát Cơ sở dữ liệu xem mã còn hoạt động (TrangThai = 'active'), còn trong thời hạn dùng, đạt giá trị đơn tối thiểu không.
     *    - Nghiệp vụ VIP: Các mã đặc quyền `VANG30` (cho thành viên Vàng trở lên) hoặc `KIMCUONG30` (cho Kim Cương) được đếm số lần sử dụng thực tế (bỏ các đơn đã hủy) trong bảng DONHANG. Chỉ cho phép dùng tối đa 3 lần.
     * 5. Lưu thông tin đơn hàng vào bảng `DONHANG` thông qua `OrderModel->create()`. Trạng thái ban đầu mặc định là 'pending' (Chờ xác nhận).
     * 6. Lưu chi tiết các sản phẩm trong đơn hàng vào bảng `CHITIETDONHANG` bằng `OrderModel->addItems()`.
     * 7. Tự động đồng bộ cập nhật lại thông tin cá nhân khách hàng (SĐT, Địa chỉ, Họ tên) vào bảng `KHACHHANG`.
     * 8. Trừ số lượng tồn kho của các sản phẩm tương ứng trong bảng `QUANLYSP` thông qua `ProductModel->reduceStock()`.
     * 9. Gửi Email thông báo xác nhận đơn hàng tự động thông qua hàm `sendMail()` trong helper (dùng PHPMailer kết nối SMTP Gmail).
     *    (Chỉ gửi email ngay đối với đơn hàng thanh toán bằng COD. Đơn hàng chuyển khoản qua SePay sẽ được gửi email sau khi Webhook xác nhận thanh toán thành công).
     * 10. Giải phóng giỏ hàng khỏi Session (`unset($_SESSION['giohang'])`).
     * 11. Chuyển hướng: Nếu chọn thanh toán qua cổng trực tuyến `sepay`, chuyển sang trang quét mã QR động.
     *     Ngược lại (COD), chuyển sang trang danh sách đơn hàng kèm thông báo đặt hàng thành công.
     */
    public function thanhToan()
    {
        // Đảm bảo khách hàng đã đăng nhập trước khi tiến hành thanh toán
        if (!isset($_SESSION['ma_nguoidung'])) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!class_exists('OrderModel')) {
                require './models/OrderModel.php';
            }
            $orderModel = new OrderModel();
            $cartItems = $_SESSION['giohang'] ?? [];
            if (empty($cartItems)) {
                header('Location: index.php?page=cart');
                exit;
            }

            // Tính tổng giá trị đơn hàng thô
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['DonGia'] * $item['SoLuongSP'];
            }

            // Xử lý kiểm duyệt và tính toán số tiền được giảm giá từ Coupon
            $couponCode = null;
            $discount = 0;
            if (isset($_SESSION['applied_coupon'])) {
                $sessionCoupon = $_SESSION['applied_coupon'];
                $code = $sessionCoupon['code'];

                $pdo = getConnection();
                $stmt = $pdo->prepare("SELECT * FROM MAGIAMGIA WHERE MaGiamGia = ? LIMIT 1");
                $stmt->execute([$code]);
                $coupon = $stmt->fetch();

                if ($coupon) {
                    $now = date('Y-m-d');
                    // Kiểm tra các điều kiện hiệu lực của mã giảm giá
                    if (
                        $coupon['NgayBD'] <= $now &&
                        $coupon['NgayKT'] >= $now &&
                        $coupon['TrangThai'] === 'active' &&
                        $coupon['SoLuot'] > 0 &&
                        $total >= $coupon['DonHangToiThieu']
                    ) {

                        // Kiểm tra hạng thành viên và lượt sử dụng của khách hàng cho mã đặc quyền VANG30 hoặc KIMCUONG30
                        $stmtUser = $pdo->prepare("SELECT HangKH FROM KHACHHANG WHERE MaKH = ? LIMIT 1");
                        $stmtUser->execute([$_SESSION['ma_nguoidung']]);
                        $userTier = $stmtUser->fetchColumn() ?: 'silver';

                        $isAllowed = true;

                        $stmtUsed = $pdo->prepare("SELECT COUNT(*) FROM DONHANG WHERE MaKH = ? AND MaGiamGia = ? AND TrangThaiDH != 'cancelled'");
                        $stmtUsed->execute([$_SESSION['ma_nguoidung'], $code]);
                        if ((int)$stmtUsed->fetchColumn() > 0) {
                            $isAllowed = false;
                        }

                        if ($code === 'VANG30') {
                            if ($userTier !== 'gold' && $userTier !== 'diamond') {
                                $isAllowed = false;
                            } else {
                                $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM DONHANG WHERE MaKH = ? AND MaGiamGia = 'VANG30' AND TrangThaiDH != 'cancelled'");
                                $stmtCount->execute([$_SESSION['ma_nguoidung']]);
                                if ((int)$stmtCount->fetchColumn() >= 3) {
                                    $isAllowed = false; // Vượt quá giới hạn 3 lần sử dụng
                                }
                            }
                        } elseif ($code === 'KIMCUONG30') {
                            if ($userTier !== 'diamond') {
                                $isAllowed = false;
                            } else {
                                $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM DONHANG WHERE MaKH = ? AND MaGiamGia = 'KIMCUONG30' AND TrangThaiDH != 'cancelled'");
                                $stmtCount->execute([$_SESSION['ma_nguoidung']]);
                                if ((int)$stmtCount->fetchColumn() >= 3) {
                                    $isAllowed = false;
                                }
                            }
                        }

                        if ($isAllowed) {
                            $couponCode = $coupon['MaGiamGia'];
                            // Tính số tiền giảm theo phần trăm hoặc theo số tiền cố định
                            if ($coupon['Loai'] === 'percent') {
                                $discount = round($total * ($coupon['GiaTri'] / 100));
                            } else {
                                $discount = (int)$coupon['GiaTri'];
                            }
                            // Số tiền giảm tối đa bằng tổng tiền đơn hàng
                            if ($discount > $total) {
                                $discount = $total;
                            }
                        }
                    }
                }
            }

            // Tính số tiền cuối cùng khách hàng cần thanh toán
            $finalTotal = $total - $discount;
            $paymentMethod = $_POST['payment_method'] ?? 'COD';
            $initialStatus = 'pending'; // Trạng thái mặc định ban đầu là 'Chờ xác nhận'

            // 5. Tạo đơn hàng mới trong CSDL
            $orderId = $orderModel->create([
                'MaKH'         => $_SESSION['ma_nguoidung'],
                'HoTenKH'       => $_POST['fullname'] ?? '',
                'email'          => $_POST['email'] ?? '',
                'SDT'          => $_POST['phone'] ?? '',
                'DiaChi'        => $_POST['address'] ?? '',
                'PhuongThucTT' => $paymentMethod,
                'GhiChu'           => $_POST['note'] ?? '',
                'TongTien'          => $finalTotal,
                'TrangThaiDH'         => $initialStatus,
                'MaGiamGia'        => $couponCode,
                'SoTienGiam'       => $discount
            ]);

            // 6. Thêm các sản phẩm trong giỏ hàng vào chi tiết đơn hàng
            $orderModel->addItems($orderId, $cartItems);

            if ($orderId > 0) {
                // Tăng lượt sử dụng của Mã giảm giá trong CSDL
                if ($couponCode !== null) {
                    $pdo = getConnection();
                    $stmt = $pdo->prepare("UPDATE MAGIAMGIA SET SoLuot = SoLuot - 1, DaDung = DaDung + 1 WHERE MaGiamGia = ?");
                    $stmt->execute([$couponCode]);
                }
                unset($_SESSION['applied_coupon']); // Xóa mã giảm giá đã dùng khỏi phiên

                // 7. Đồng bộ thông tin giao hàng mới nhất vào hồ sơ khách hàng
                if (!class_exists('UserModel')) {
                    require './models/UserModel.php';
                }
                $userModel = new UserModel();
                $userModel->update((int)$_SESSION['ma_nguoidung'], [
                    'HoTenKH' => $_POST['fullname'] ?? '',
                    'email'   => $_SESSION['email_nguoidung'],
                    'SDT'     => $_POST['phone'] ?? '',
                    'DiaChi'  => $_POST['address'] ?? ''
                ]);

                $_SESSION['ten_nguoidung'] = $_POST['fullname'] ?? '';
            }

            // 8. Trừ số lượng tồn kho sản phẩm trong CSDL
            if (!class_exists('ProductModel')) {
                require './models/ProductModel.php';
            }
            $productModel = new ProductModel();
            foreach ($cartItems as $item) {
                if (!empty($item['MaSP'])) {
                    $productModel->reduceStock((int)$item['MaSP'], (int)$item['SoLuongSP'], $item['KichCo'] ?? '');
                }
            }

            // 9. Gửi Email thông báo xác nhận đơn hàng tự động (Chỉ áp dụng với thanh toán COD)
            if (!empty($_POST['email']) && $paymentMethod !== 'sepay') {
                if (!function_exists('sendMail')) {
                    require './helpers/mail_helper.php';
                }

                $productItemsRows = '';
                foreach ($cartItems as $item) {
                    $img = $item['AnhChinh'] ?? '';
                    $imgUrl = '';
                    if (!empty($img)) {
                        if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
                            $imgUrl = $img;
                        } elseif (strpos($img, 'assets/') === 0) {
                            $imgUrl = $absoluteBaseUrl . $img;
                        } else {
                            $imgUrl = $absoluteBaseUrl . 'assets/images/products/' . $img;
                        }
                    } else {
                        $imgUrl = "https://placehold.co/80x80/0a1628/fff?text=SP+" . ($item['MaSP'] ?? 0);
                    }

                    $productItemsRows .= '
                    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 12px; margin-bottom: 16px; border-collapse: separate;">
                        <tr>
                            <td width="70" style="padding: 16px; vertical-align: middle;">
                                <img src="' . htmlspecialchars($imgUrl) . '" width="60" height="60" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #d1d5db;" alt="' . htmlspecialchars($item['TenSP']) . '">
                            </td>
                            <td style="padding: 16px 8px; vertical-align: middle;">
                                <div style="font-size: 15px; font-weight: 700; color: #1f2937; margin-bottom: 4px;">' . htmlspecialchars($item['TenSP']) . '</div>';
                    if (!empty($item['KichCo'])) {
                        $productItemsRows .= '
                                <div style="font-size: 13px; color: #6b7280;">Size: ' . htmlspecialchars($item['KichCo']) . '</div>';
                    }
                    $productItemsRows .= '
                            </td>
                            <td align="right" style="padding: 16px; vertical-align: middle; font-size: 15px; font-weight: 700; color: #1f2937; white-space: nowrap;">
                                ' . number_format($item['DonGia'], 0, ',', '.') . 'đ<br>
                                <small style="color: #6b7280; font-size: 13px; font-weight: 500;">x ' . $item['SoLuongSP'] . '</small>
                            </td>
                        </tr>
                    </table>';
                }

                $orderDate = date('d/m/Y H:i');
                $subtotalFormatted = number_format($total, 0, ',', '.');
                $discountRowEmail = '';
                if ($discount > 0) {
                    $discountRowEmail = '
                            <tr>
                                <td style="padding-bottom: 6px; color: #6b7280;">Mã giảm giá (' . htmlspecialchars($couponCode) . ')</td>
                                <td align="right" style="color: #f25c19; font-weight: 600; padding-bottom: 6px;">-' . number_format($discount, 0, ',', '.') . 'đ</td>
                            </tr>';
                }
                $totalFormatted = number_format($finalTotal, 0, ',', '.');
                $orderDetailUrl = $absoluteBaseUrl . 'index.php?page=order-detail&id=' . $orderId;
                $paymentMethodText = ($paymentMethod === 'COD') ? 'Thanh toán COD' : 'Chuyển khoản ngân hàng';

                $emailBody = '
<div style="background-color: #f3f4f6; padding: 30px 15px; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #1f2937;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 20px; overflow: hidden; border: 1px solid #e5e7eb; border-collapse: separate; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
        <tr>
            <td style="padding: 40px 30px 20px 30px; text-align: center;">
                <div style="font-size: 28px; font-weight: 900; color: #f25c19; letter-spacing: -0.5px; margin-bottom: 4px;">VAB SHOP</div>
                <div style="font-size: 11px; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: 3px;">Thời Trang Thể Thao Cao Cấp</div>
                <div style="height: 1px; background-color: #e5e7eb; margin-top: 20px; margin-bottom: 10px;"></div>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 30px 30px 30px;">
                <div style="margin-bottom: 20px;">
                    <h2 style="margin: 0 0 8px 0; font-size: 22px; font-weight: 800; color: #111827; text-align: left;">Cảm ơn bạn đã đặt hàng!</h2>
                    <p style="margin: 0; color: #4b5563; font-size: 15px; line-height: 1.5;">Đơn đặt hàng của bạn đã được tiếp nhận và đang được xử lý.</p>
                </div>
                <div style="margin-bottom: 24px;">
                    ' . $productItemsRows . '
                </div>
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 24px; font-size: 14px; color: #4b5563; line-height: 2;">
                    <tr>
                        <td style="color: #6b7280;">ID đơn hàng</td>
                        <td align="right" style="font-weight: bold; color: #111827;">#' . $orderId . '</td>
                    </tr>
                    <tr>
                        <td style="color: #6b7280;">Ngày đặt hàng</td>
                        <td align="right" style="color: #111827;">' . $orderDate . '</td>
                    </tr>
                    <tr>
                        <td style="color: #6b7280;">Phương thức thanh toán</td>
                        <td align="right" style="color: #111827; font-weight: 600;">' . $paymentMethodText . '</td>
                    </tr>
                </table>
                <div style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <h4 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; color: #111827;">Tổng quan đơn hàng</h4>
                    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; color: #4b5563; border-collapse: collapse; line-height: 1.8;">
                        <tr>
                            <td style="color: #6b7280; padding-bottom: 6px;">Tổng phụ</td>
                            <td align="right" style="color: #111827; font-weight: 600; padding-bottom: 6px;">' . $subtotalFormatted . 'đ</td>
                        </tr>
                        ' . $discountRowEmail . '
                        <tr>
                            <td style="color: #6b7280; padding-bottom: 6px;">Vận chuyển</td>
                            <td align="right" style="color: #10b981; font-weight: 600; padding-bottom: 6px;">Miễn phí</td>
                        </tr>
                        <tr>
                            <td style="padding-top: 12px; border-top: 1px solid #d1d5db; font-weight: 700; color: #111827; font-size: 15px;">Tổng cộng</td>
                            <td align="right" style="padding-top: 12px; border-top: 1px solid #d1d5db; font-weight: 800; color: #f25c19; font-size: 18px;">' . $totalFormatted . 'đ</td>
                        </tr>
                    </table>
                </div>
                <div style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 30px;">
                    <h4 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; color: #111827;">Thông tin giao hàng</h4>
                    <p style="margin: 0 0 4px 0; color: #111827; font-weight: 700; font-size: 14px;">' . htmlspecialchars($_POST['fullname']) . '</p>
                    <p style="margin: 0 0 4px 0; color: #4b5563; font-size: 13px;">📞 ' . htmlspecialchars($_POST['phone']) . '</p>
                    <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.4;">📍 ' . htmlspecialchars($_POST['address']) . '</p>
                </div>
                <div style="text-align: center; margin-bottom: 10px;">
                    <a href="' . htmlspecialchars($orderDetailUrl) . '" style="display: inline-block; background-color: #e25862; color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 50px; font-size: 14px; font-weight: 700; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(226, 88, 98, 0.25); transition: background-color 0.3s;">Xem chi tiết đơn hàng</a>
                </div>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f3f4f6; border-top: 1px solid #e5e7eb; padding: 25px 30px; text-align: center; color: #6b7280; font-size: 12px;">
                <p style="margin: 0 0 6px 0; font-weight: 700; color: #4b5563;">Cảm ơn bạn đã lựa chọn mua sắm tại VAB SHOP!</p>
                <p style="margin: 0 0 10px 0; color: #9ca3af; line-height: 1.5;">Email này được gửi tự động từ hệ thống cửa hàng VAB SHOP. Vui lòng không trả lời trực tiếp email này.</p>
                <p style="margin: 0; color: #9ca3af;">&copy; 2026 VAB SHOP. All rights reserved.</p>
            </td>
        </tr>
    </table>
</div>';

                $subject = "Xác nhận đơn hàng #" . $orderId . " từ VAB SHOP";
                sendMail($_POST['email'], $_POST['fullname'], $subject, $emailBody);
            }

            // 10. Giải phóng giỏ hàng sau khi đặt thành công
            unset($_SESSION['giohang']);

            // 11. Chuyển hướng tùy thuộc phương thức thanh toán
            if ($paymentMethod === 'sepay') {
                header('Location: index.php?page=sepay-payment&order_id=' . $orderId);
            } else {
                header('Location: index.php?page=orders&msg=order_placed');
            }
            exit;
        }

        if (!class_exists('UserModel')) {
            require './models/UserModel.php';
        }
        $userModel = new UserModel();
        $currentUser = $userModel->getById((int)$_SESSION['ma_nguoidung']);

        $tieuDeTrang = 'Thanh toán - VAB';
        $trangHienTai = 'checkout';
        $nhungTuController = true;
        include './views/giaodien/dautrang.php';
        include './views/giaodien/thanhtoan.php';
        include './views/giaodien/chantrang.php';
    }

    /**
     * CHỨC NĂNG: HIỂN THỊ TRANG QUÉT MÃ QR THANH TOÁN (thanhtoanSepay)
     * ĐẦU VÀO: Lấy mã đơn hàng (?order_id=) từ URL.
     * CƠ CHẾ HOẠT ĐỘNG:
     * 1. Xác thực xem đơn hàng này có thuộc về khách hàng đang đăng nhập hay không (bảo mật dữ liệu).
     * 2. Nếu khớp, nạp view `thanhtoan-sepay.php` để hiển thị mã QR kèm hướng dẫn chuyển khoản.
     */
    public function thanhtoanSepay()
    {
        if (!isset($_SESSION['ma_nguoidung'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        if ($orderId <= 0) {
            header('Location: index.php?page=orders');
            exit;
        }

        if (!class_exists('OrderModel')) {
            require './models/OrderModel.php';
        }
        $orderModel = new OrderModel();
        $userId = $_SESSION['ma_nguoidung'];
        // Lấy đơn hàng và kiểm tra quyền sở hữu
        $order = $orderModel->getOrderByIdAndUserId($orderId, $userId);

        if (!$order) {
            header('Location: index.php?page=orders');
            exit;
        }

        $tieuDeTrang = 'Thanh toán chuyển khoản - VAB';
        $trangHienTai = 'orders';
        $nhungTuController = true;

        include './views/giaodien/dautrang.php';
        include './views/giaodien/thanhtoan-sepay.php';
        include './views/giaodien/chantrang.php';
    }

    /**
     * CHỨC NĂNG: AJAX KIỂM TRA TRẠNG THÁI ĐƠN HÀNG SEPAY (kiemtraTrangthaiSepay)
     * ĐẦU VÀO: Nhận mã đơn hàng qua AJAX (?order_id=).
     * CƠ CHẾ HOẠT ĐỘNG AJAX POLLING:
     * 1. Khi khách hàng ở trang Quét mã QR, Javascript ở client sẽ gửi request AJAX tới hàm này mỗi 2-3 giây (Polling).
     * 2. Hàm này truy vấn CSDL để kiểm tra trạng thái đơn hàng (`TrangThaiDH`).
     * 3. Khi khách chuyển khoản thành công, SePay Webhook nhận được tiền sẽ bắn tín hiệu về cập nhật CSDL thành 'đã xác nhận' hoặc 'hoàn thành'.
     * 4. Hàm này thấy trạng thái thay đổi sẽ lập tức trả về JSON thông báo cho trình duyệt tự động chuyển hướng khách hàng sang trang hoàn tất đơn hàng.
     */
    public function kiemtraTrangthaiSepay()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['ma_nguoidung'])) {
            echo json_encode(['status' => 'unauthorized']);
            exit;
        }

        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        if ($orderId <= 0) {
            echo json_encode(['status' => 'invalid']);
            exit;
        }

        if (!class_exists('OrderModel')) {
            require './models/OrderModel.php';
        }
        $orderModel = new OrderModel();
        $userId = $_SESSION['ma_nguoidung'];
        $order = $orderModel->getOrderByIdAndUserId($orderId, $userId);

        if (!$order) {
            echo json_encode(['status' => 'not_found']);
            exit;
        }

        // Chuẩn hóa chuỗi trạng thái tiếng Việt sang tiếng Anh không dấu để client xử lý đồng bộ
        $rawStatus = trim(strtolower($order['TrangThaiDH']));
        $status = $rawStatus;
        if ($rawStatus === 'chờ xác nhận' || $rawStatus === 'cho xac nhan') {
            $status = 'pending';
        } elseif ($rawStatus === 'đã xác nhận' || $rawStatus === 'da xac nhan') {
            $status = 'confirmed';
        } elseif ($rawStatus === 'đang giao hàng' || $rawStatus === 'dang giao hang') {
            $status = 'shipping';
        } elseif ($rawStatus === 'đã giao hàng' || $rawStatus === 'da giao hang' || $rawStatus === 'hoàn thành' || $rawStatus === 'hoan thanh' || $rawStatus === 'completed') {
            $status = 'completed';
        }

        echo json_encode(['status' => $status]);
        exit;
    }

    /**
     * CHỨC NĂNG: ÁP DỤNG MÃ GIẢM GIÁ VÀO GIỎ HÀNG (apDungMaGiamGia)
     * ĐẦU VÀO: Mã giảm giá (POST code).
     * CƠ CHẾ XỬ LÝ:
     * 1. Đối soát mã giảm giá trong bảng `MAGIAMGIA`: Ngày bắt đầu/kết thúc, lượt sử dụng còn lại, giá trị đơn tối thiểu.
     * 2. Nghiệp vụ hạng thành viên VIP (Gold/Diamond) có được áp dụng mã không, giới hạn tối đa 3 lần.
     * 3. Tính toán số tiền được giảm (giảm theo % hoặc giảm thẳng số tiền cố định).
     * 4. Lưu thông tin voucher vào session `$_SESSION['applied_coupon']`.
     * 5. Trả về JSON để hiển thị số tiền giảm giá và tổng tiền mới cập nhật ở client ngay lập tức mà không cần load lại trang.
     */
    public function apDungMaGiamGia()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['ma_nguoidung'])) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập để sử dụng mã giảm giá.']);
            exit;
        }

        $code = strtoupper(trim($_POST['code'] ?? $_GET['code'] ?? ''));
        if (empty($code)) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập mã giảm giá.']);
            exit;
        }

        $cartItems = $_SESSION['giohang'] ?? [];
        if (empty($cartItems)) {
            echo json_encode(['status' => 'error', 'message' => 'Giỏ hàng của bạn đang trống.']);
            exit;
        }

        $cartTotal = 0;
        foreach ($cartItems as $item) {
            $cartTotal += $item['DonGia'] * $item['SoLuongSP'];
        }

        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM MAGIAMGIA WHERE MaGiamGia = ? LIMIT 1");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch();

        if (!$coupon) {
            echo json_encode(['status' => 'error', 'message' => 'Mã giảm giá không tồn tại.']);
            exit;
        }

        $now = date('Y-m-d');
        if ($coupon['NgayBD'] > $now) {
            echo json_encode(['status' => 'error', 'message' => 'Mã giảm giá chưa đến thời gian áp dụng.']);
            exit;
        }

        if ($coupon['NgayKT'] < $now) {
            echo json_encode(['status' => 'error', 'message' => 'Mã giảm giá đã hết hạn sử dụng.']);
            exit;
        }

        if ($coupon['TrangThai'] !== 'active') {
            echo json_encode(['status' => 'error', 'message' => 'Mã giảm giá đã bị khóa hoặc không hoạt động.']);
            exit;
        }

        if ($coupon['SoLuot'] <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Mã giảm giá đã hết lượt sử dụng.']);
            exit;
        }

        if ($cartTotal < $coupon['DonHangToiThieu']) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($coupon['DonHangToiThieu'], 0, ',', '.') . 'đ để sử dụng mã này.'
            ]);
            exit;
        }

        // Mỗi mã giảm giá chỉ được dùng tối đa 1 lần trên mỗi tài khoản (trừ đơn hàng đã hủy)
        $stmtUsed = $pdo->prepare("SELECT COUNT(*) FROM DONHANG WHERE MaKH = ? AND MaGiamGia = ? AND TrangThaiDH != 'cancelled'");
        $stmtUsed->execute([$_SESSION['ma_nguoidung'], $code]);
        if ((int)$stmtUsed->fetchColumn() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Bạn đã sử dụng mã giảm giá này rồi. Mỗi tài khoản chỉ được dùng mã này 1 lần.']);
            exit;
        }

        // Kiểm tra phân quyền hạng khách hàng VIP cho các mã ưu đãi độc quyền
        $stmtUser = $pdo->prepare("SELECT HangKH FROM KHACHHANG WHERE MaKH = ? LIMIT 1");
        $stmtUser->execute([$_SESSION['ma_nguoidung']]);
        $userTier = $stmtUser->fetchColumn() ?: 'silver';

        if ($code === 'VANG30') {
            if ($userTier !== 'gold' && $userTier !== 'diamond') {
                echo json_encode(['status' => 'error', 'message' => 'Mã giảm giá này chỉ dành cho khách hàng hạng Vàng trở lên.']);
                exit;
            }

            $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM DONHANG WHERE MaKH = ? AND MaGiamGia = 'VANG30' AND TrangThaiDH != 'cancelled'");
            $stmtCount->execute([$_SESSION['ma_nguoidung']]);
            $usedCount = (int)$stmtCount->fetchColumn();

            if ($usedCount >= 3) {
                echo json_encode(['status' => 'error', 'message' => 'Bạn đã sử dụng mã giảm giá ưu đãi hạng Vàng tối đa 3 lần.']);
                exit;
            }
        } elseif ($code === 'KIMCUONG30') {
            if ($userTier !== 'diamond') {
                echo json_encode(['status' => 'error', 'message' => 'Mã giảm giá này chỉ dành cho khách hàng hạng Kim Cương.']);
                exit;
            }

            $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM DONHANG WHERE MaKH = ? AND MaGiamGia = 'KIMCUONG30' AND TrangThaiDH != 'cancelled'");
            $stmtCount->execute([$_SESSION['ma_nguoidung']]);
            $usedCount = (int)$stmtCount->fetchColumn();

            if ($usedCount >= 3) {
                echo json_encode(['status' => 'error', 'message' => 'Bạn đã sử dụng mã giảm giá ưu đãi hạng Kim Cương tối đa 3 lần.']);
                exit;
            }
        }

        // Tính toán số tiền được chiết khấu giảm giá
        $discount = 0;
        if ($coupon['Loai'] === 'percent') {
            $discount = round($cartTotal * ($coupon['GiaTri'] / 100));
        } else {
            $discount = (int)$coupon['GiaTri'];
        }

        if ($discount > $cartTotal) {
            $discount = $cartTotal;
        }

        // Lưu thông tin voucher vào phiên session
        $_SESSION['applied_coupon'] = [
            'code' => $coupon['MaGiamGia'],
            'discount' => $discount,
            'type' => $coupon['Loai'],
            'value' => $coupon['GiaTri']
        ];

        echo json_encode([
            'status' => 'success',
            'code' => $coupon['MaGiamGia'],
            'discount' => $discount,
            'discountFormatted' => number_format($discount, 0, ',', '.') . 'đ',
            'newTotal' => $cartTotal - $discount,
            'newTotalFormatted' => number_format($cartTotal - $discount, 0, ',', '.') . 'đ',
            'message' => 'Áp dụng mã giảm giá thành công!'
        ]);
        exit;
    }

    /**
     * CHỨC NĂNG: HỦY ÁP DỤNG MÃ GIẢM GIÁ (xoaMaGiamGia)
     * ĐẦU RA: Xóa voucher khỏi session, khôi phục giá trị tổng tiền gốc của giỏ hàng.
     */
    public function xoaMaGiamGia()
    {
        header('Content-Type: application/json');
        unset($_SESSION['applied_coupon']);

        $cartItems = $_SESSION['giohang'] ?? [];
        $cartTotal = 0;
        foreach ($cartItems as $item) {
            $cartTotal += $item['DonGia'] * $item['SoLuongSP'];
        }

        echo json_encode([
            'status' => 'success',
            'newTotal' => $cartTotal,
            'newTotalFormatted' => number_format($cartTotal, 0, ',', '.') . 'đ',
            'message' => 'Đã hủy áp dụng mã giảm giá!'
        ]);
        exit;
    }

    public function chiTietThuongHieuDynamic()
    {
        $tieuDeTrang = 'Thương hiệu - VAB';
        $trangHienTai = 'brand-' . ($_GET['brand_slug'] ?? '');
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();

        include './views/giaodien/dautrang.php';
        include './views/giaodien/thuonghieu.php';
        include './views/giaodien/chantrang.php';
    }

    public function chiTietTheThaoDynamic()
    {
        $tieuDeTrang = 'Thể thao - VAB';
        $trangHienTai = $_GET['sport_slug'] ?? '';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();

        include './views/giaodien/dautrang.php';
        include './views/giaodien/thethao.php';
        include './views/giaodien/chantrang.php';
    }
}
