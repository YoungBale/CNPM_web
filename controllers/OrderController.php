<?php
/**
 * OrderController - Handles user order list and detail viewing
 * Quản lý danh sách đơn hàng và chi tiết đơn hàng của người dùng
 */
class OrderController {

    /**
     * Kiểm tra người dùng đã đăng nhập chưa
     */
    private function checkLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['ma_nguoidung'])) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    /**
     * Hiển thị danh sách đơn hàng
     */
    public function danhSach() {
        $this->checkLogin();

        if (!class_exists('OrderModel')) {
            require './models/OrderModel.php';
        }
        $donHangModel = new OrderModel();
        $maKH = $_SESSION['ma_nguoidung'];

        $orders = $donHangModel->getOrdersByUserId($maKH);

        $tieuDeTrang = 'Đơn hàng của tôi - VAB';
        $trangHienTai = 'orders';
        $nhungTuController = true;

        include './views/giaodien/dautrang.php';
        include './views/giaodien/donhang.php';
        include './views/giaodien/chantrang.php';
    }

    /**
     * Hiển thị chi tiết một đơn hàng
     */
    public function chiTiet() {
        $this->checkLogin();

        $maDonHang = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($maDonHang <= 0) {
            header('Location: index.php?page=orders');
            exit;
        }

        if (!class_exists('OrderModel')) {
            require './models/OrderModel.php';
        }
        $donHangModel = new OrderModel();
        $maKH = $_SESSION['ma_nguoidung'];

        // Bảo mật: Người dùng chỉ xem được đơn hàng của chính họ
        $order = $donHangModel->getOrderByIdAndUserId($maDonHang, $maKH);

        if (!$order) {
            // Đơn hàng không tồn tại hoặc không thuộc về người dùng đang đăng nhập
            header('Location: index.php?page=orders');
            exit;
        }

        $orderItems = $donHangModel->getOrderItems($maDonHang);

        if ($order['TrangThaiDH'] === 'completed' || $order['TrangThaiDH'] === 'delivered') {
            if (!class_exists('ReviewModel')) {
                require_once './models/ReviewModel.php';
            }
            $reviewModel = new ReviewModel();
            foreach ($orderItems as &$item) {
                $item['da_danh_gia'] = $reviewModel->hasUserReviewed($maKH, $item['MaSP']);
            }
            unset($item);
        }

        $tieuDeTrang = 'Chi tiết đơn hàng #' . str_pad($maDonHang, 5, '0', STR_PAD_LEFT) . ' - VAB';
        $trangHienTai = 'orders';
        $nhungTuController = true;

        include './views/giaodien/dautrang.php';
        include './views/giaodien/chitiet-donhang.php';
        include './views/giaodien/chantrang.php';
    }

    /**
     * Khách hàng tự hủy đơn hàng (khi đang Chờ xác nhận)
     */
    public function huy() {
        $this->checkLogin();
        $maDonHang = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($maDonHang <= 0) {
            header('Location: index.php?page=orders');
            exit;
        }

        if (!class_exists('OrderModel')) {
            require './models/OrderModel.php';
        }
        $donHangModel = new OrderModel();
        $maKH = $_SESSION['ma_nguoidung'];

        $order = $donHangModel->getOrderByIdAndUserId($maDonHang, $maKH);
        if (!$order) {
            header('Location: index.php?page=orders');
            exit;
        }

        if ($order['TrangThaiDH'] !== 'pending') {
            $_SESSION['order_error'] = 'Chỉ có thể hủy đơn hàng ở trạng thái Chờ xác nhận!';
            header('Location: index.php?page=orders');
            exit;
        }

        $pdo = getConnection();
        $stmt = $pdo->prepare("UPDATE DONHANG SET TrangThaiDH = 'cancelled' WHERE MaDonHang = ?");
        $success = $stmt->execute([$maDonHang]);

        if ($success) {
            // Khôi phục hàng tồn kho
            $stmtItems = $pdo->prepare("SELECT MaSP, SoLuongSP, KichCo FROM CHITIETDONHANG WHERE MaDonHang = ?");
            $stmtItems->execute([$maDonHang]);
            $items = $stmtItems->fetchAll();

            $stmtRestore = $pdo->prepare("UPDATE QUANLYSP SET SoLuong = SoLuong + ? WHERE MaSP = ? AND KichCo = ?");
            foreach ($items as $item) {
                if (!empty($item['MaSP'])) {
                    $stmtRestore->execute([$item['SoLuongSP'], $item['MaSP'], $item['KichCo']]);
                }
            }

            // Khôi phục lượt sử dụng của mã giảm giá nếu có
            $stmtVoucher = $pdo->prepare("SELECT MaGiamGia FROM DONHANG WHERE MaDonHang = ?");
            $stmtVoucher->execute([$maDonHang]);
            $voucherCode = $stmtVoucher->fetchColumn();
            if (!empty($voucherCode)) {
                $stmtVoucherRestore = $pdo->prepare("UPDATE MAGIAMGIA SET SoLuot = SoLuot + 1, DaDung = GREATEST(0, DaDung - 1) WHERE MaGiamGia = ?");
                $stmtVoucherRestore->execute([$voucherCode]);
            }

            $_SESSION['order_msg'] = 'Đã hủy đơn hàng thành công!';
        } else {
            $_SESSION['order_error'] = 'Đã xảy ra lỗi khi hủy đơn hàng!';
        }

        header('Location: index.php?page=orders');
        exit;
    }

    /**
     * Khách hàng gửi yêu cầu Trả hàng / Hoàn tiền
     */
    public function traHang() {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=orders');
            exit;
        }

        $maDonHang = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

        if ($maDonHang <= 0 || empty($reason)) {
            $_SESSION['order_error'] = 'Thông tin yêu cầu trả hàng không hợp lệ!';
            header('Location: index.php?page=orders');
            exit;
        }

        if (!class_exists('OrderModel')) {
            require './models/OrderModel.php';
        }
        $donHangModel = new OrderModel();
        $maKH = $_SESSION['ma_nguoidung'];

        $order = $donHangModel->getOrderByIdAndUserId($maDonHang, $maKH);
        if (!$order) {
            header('Location: index.php?page=orders');
            exit;
        }

        if ($order['TrangThaiDH'] !== 'delivered' && $order['TrangThaiDH'] !== 'completed') {
            $_SESSION['order_error'] = 'Chỉ có thể yêu cầu trả hàng với đơn hàng đã giao hoặc giao thành công!';
            header('Location: index.php?page=order-detail&id=' . $maDonHang);
            exit;
        }

        $pdo = getConnection();
        $stmt = $pdo->prepare("UPDATE DONHANG SET TrangThaiDH = 'returning', LyDoTraHang = ? WHERE MaDonHang = ?");
        $success = $stmt->execute([$reason, $maDonHang]);

        if ($success) {
            $_SESSION['order_msg'] = 'Gửi yêu cầu trả hàng thành công! Đang chờ Quản trị viên duyệt.';
        } else {
            $_SESSION['order_error'] = 'Đã xảy ra lỗi khi gửi yêu cầu trả hàng!';
        }

        header('Location: index.php?page=order-detail&id=' . $maDonHang);
        exit;
    }

    /**
     * Helper chuyển đổi trạng thái đơn hàng sang Badge CSS class và nhãn tiếng Việt
     */
    public static function getStatusInfo($status) {
        $statusClean = strtolower(trim($status));
        switch ($statusClean) {
            case 'pending':
            case 'chờ xác nhận':
            case 'cho xac nhan':
                return [
                    'class' => 'status-pending',
                    'label' => 'Chờ xác nhận'
                ];
            case 'confirmed':
            case 'đã xác nhận':
            case 'da xac nhan':
                return [
                    'class' => 'status-confirmed',
                    'label' => 'Đã thanh toán'
                ];
            case 'shipping':
            case 'đang giao hàng':
            case 'dang giao hang':
                return [
                    'class' => 'status-shipping',
                    'label' => 'Đang giao hàng'
                ];
            case 'delivered':
            case 'completed':
            case 'đã giao hàng':
            case 'da giao hang':
                return [
                    'class' => 'status-delivered',
                    'label' => 'Đã giao hàng'
                ];
            case 'cancelled':
            case 'đã hủy':
            case 'da huy':
                return [
                    'class' => 'status-cancelled',
                    'label' => 'Đã hủy'
                ];
            case 'returning':
            case 'yêu cầu trả hàng':
            case 'yeu cau tra hang':
                return [
                    'class' => 'status-returning',
                    'label' => 'Yêu cầu trả hàng'
                ];
            case 'returned':
            case 'đã trả hàng':
            case 'da tra hang':
                return [
                    'class' => 'status-returned',
                    'label' => 'Đã trả hàng'
                ];
            case 'refused':
            case 'từ chối nhận':
            case 'tu choi nhan':
                return [
                    'class' => 'status-refused',
                    'label' => 'Từ chối nhận'
                ];
            default:
                return [
                    'class' => 'status-pending',
                    'label' => $status
                ];
        }
    }
}
