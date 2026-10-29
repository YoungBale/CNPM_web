<?php
/**
 * =================================================================================
 * FILE ĐỊNH TUYẾN CHÍNH - FRONT CONTROLLER (index.php)
 * =================================================================================
 * ĐÂY LÀ ĐIỂM VÀO DUY NHẤT (SINGLE ENTRY POINT) CỦA TOÀN BỘ HỆ THỐNG WEBSITE.
 * 
 * CƠ CHẾ HOẠT ĐỘNG:
 * 1. Mọi yêu cầu từ trình duyệt (request) đều được gửi về đây (ví dụ: index.php?page=home, index.php?page=products...).
 * 2. File này có nhiệm vụ:
 *    - Khởi tạo Session để lưu giỏ hàng, thông tin đăng nhập.
 *    - Nạp cấu hình kết nối Cơ sở dữ liệu (database.php).
 *    - Phân tích tham số trên URL (?page=... hoặc ?controller=...&action=...) để xác định Client muốn gì.
 *    - Dịch từ tên trang tiện lợi (alias) sang Controller và Action tương ứng (thông qua bảng ánh xạ $pageMap).
 *    - Tự động nạp file Controller thích hợp, khởi tạo đối tượng Controller và chạy hàm (Action) xử lý.
 * 
 * ƯU ĐIỂM (Cần trả lời khi thầy hỏi):
 * - Giúp quản lý tập trung: Cấu hình hệ thống, múi giờ, bảo mật đầu vào đều đặt ở đây, không cần lặp lại ở từng trang.
 * - Che giấu cấu trúc thư mục thật bên trong, giúp ứng dụng bảo mật hơn.
 * - Dễ dàng mở rộng route (đường dẫn) mới mà không làm ảnh hưởng các file khác.
 */

// 1. CẤU HÌNH HỆ THỐNG BAN ĐẦU
error_reporting(E_ALL); // Hiển thị tất cả các lỗi để lập trình viên dễ phát hiện và sửa chữa (Debug)
ini_set('display_errors', 1);

// Khởi động phiên làm việc (Session). Dùng để lưu trữ Giỏ hàng ($_SESSION['cart']) và thông tin tài khoản khi đăng nhập thành công.
session_start();

// Thiết lập múi giờ mặc định sang giờ Việt Nam để ghi nhận thời gian đặt hàng chính xác
date_default_timezone_set('Asia/Ho_Chi_Minh');

// 2. KẾT NỐI CƠ SỞ DỮ LIỆU
// Nạp file kết nối CSDL PDO. File này cung cấp hàm getConnection() dùng chung trên toàn hệ thống.
require './models/database.php';

// 3. PHÂN TÍCH YÊU CẦU ĐỊNH TUYẾN (ROUTING LOGIC)

// Lấy tên Controller từ URL. Nếu không truyền (?controller=), mặc định là 'HomeController' (Trang chủ)
$controllerName = isset($_GET['controller'])
    ? ucfirst(strtolower(trim($_GET['controller']))) . 'Controller'
    : 'HomeController';

// Lấy tên Action (hàm xử lý trong Controller) từ URL. Nếu không truyền (?action=), mặc định chạy hàm 'trangChu'
$actionName = isset($_GET['action'])
    ? strtolower(trim($_GET['action']))
    : 'trangChu';

// Lấy tham số 'page' (tên trang thân thiện) nếu có. Ví dụ: ?page=brand-nike, ?page=checkout
$page = isset($_GET['page']) ? trim($_GET['page']) : '';

if ($page) {
    /**
     * BẢNG ÁNH XẠ ĐỊNH TUYẾN (ROUTING MAP / ALIAS)
     * Chuyển đổi tên trang thân thiện (chìa khóa) sang cặp Controller và Action tương ứng để xử lý nghiệp vụ.
     */
    $pageMap = [
        // Trang chủ & Trang sản phẩm
        'home'             => ['controller' => 'Home',    'action' => 'trangChu'],
        'products'         => ['controller' => 'Product', 'action' => 'danhSach'],
        'search'           => ['controller' => 'Product', 'action' => 'timKiem'],
        'suggest'          => ['controller' => 'Product', 'action' => 'goiY'],
        'product-detail'   => ['controller' => 'Product', 'action' => 'chiTiet'],
        'load-more'        => ['controller' => 'Product', 'action' => 'taiThem'],
        'add-to-cart'      => ['controller' => 'Product', 'action' => 'themGioHang'],
        
        // Phân loại sản phẩm theo giới tính & Khuyến mãi
        'men'              => ['controller' => 'Product', 'action' => 'nam'],
        'women'            => ['controller' => 'Product', 'action' => 'nu'],
        'sale'             => ['controller' => 'Product', 'action' => 'giamGia'],
        
        // Nghiệp vụ giỏ hàng & Thanh toán
        'cart'             => ['controller' => 'Product', 'action' => 'gioHang'],
        'cart-update'      => ['controller' => 'Product', 'action' => 'capNhatGioHang'],
        'cart-remove'      => ['controller' => 'Product', 'action' => 'xoaGioHang'],
        'checkout'         => ['controller' => 'Product', 'action' => 'thanhToan'],
        'apply-coupon'     => ['controller' => 'Product', 'action' => 'apDungMaGiamGia'],
        'remove-coupon'    => ['controller' => 'Product', 'action' => 'xoaMaGiamGia'],
        
        // Đăng nhập, đăng ký, đăng xuất tài khoản khách hàng
        'login'            => ['controller' => 'Auth',    'action' => 'dangNhap'],
        'register'         => ['controller' => 'Auth',    'action' => 'dangKy'],
        'logout'           => ['controller' => 'Auth',    'action' => 'dangXuat'],
        'google-login'      => ['controller' => 'Auth',    'action' => 'dangNhapGoogle'],
        
        // Quản lý đơn hàng của tài khoản khách hàng
        'orders'           => ['controller' => 'Order',   'action' => 'danhSach'],
        'order-detail'     => ['controller' => 'Order',   'action' => 'chiTiet'],
        'order-cancel'     => ['controller' => 'Order',   'action' => 'huy'],
        'order-return'     => ['controller' => 'Order',   'action' => 'traHang'],
        'don-hang'         => ['controller' => 'Order',   'action' => 'danhSach'],
        'chi-tiet-don-hang'=> ['controller' => 'Order',   'action' => 'chiTiet'],
        
        // Phân hệ quản trị (Admin Dashboard)
        'admin'             => ['controller' => 'Admin',   'action' => 'trangChu'],
        'admin-dashboard'   => ['controller' => 'Admin',   'action' => 'trangChu'],
        'admin-products'    => ['controller' => 'Admin',   'action' => 'sanPham'],
        'admin-product-add' => ['controller' => 'Admin',   'action' => 'themSanPham'],
        'admin-product-edit'=> ['controller' => 'Admin',   'action' => 'suaSanPham'],
        'admin-orders'      => ['controller' => 'Admin',   'action' => 'donHang'],
        'admin-order-detail'=> ['controller' => 'Admin',   'action' => 'chiTietDonHang'],
        'admin-categories'      => ['controller' => 'Admin',   'action' => 'danhMuc'],
        'admin-category-edit'   => ['controller' => 'Admin',   'action' => 'suaDanhMuc'],
        'admin-category-delete' => ['controller' => 'Admin',   'action' => 'xoaDanhMuc'],
        'admin-users'       => ['controller' => 'Admin',   'action' => 'nguoiDung'],
        'admin-employees'   => ['controller' => 'Admin',   'action' => 'nhanVien'],
        'admin-vouchers'    => ['controller' => 'Admin',   'action' => 'maGiamGia'],
        'admin-revenue'     => ['controller' => 'Admin',   'action' => 'doanhThu'],
        'admin-settings'    => ['controller' => 'Admin',   'action' => 'cauhinh'],
        'admin-login'       => ['controller' => 'Admin',   'action' => 'dangNhap'],
        'admin-logout'      => ['controller' => 'Admin',   'action' => 'dangXuat'],
        
        // Quản lý đánh giá sản phẩm (Review)
        'admin-reviews'        => ['controller' => 'Review',  'action' => 'quantriDanhSach'],
        'admin-review-approve' => ['controller' => 'Review',  'action' => 'duyet'],
        'admin-review-reject'  => ['controller' => 'Review',  'action' => 'tuChoi'],
        'admin-review-delete'  => ['controller' => 'Review',  'action' => 'xoa'],
        'review-store'         => ['controller' => 'Review',  'action' => 'luu'],
        
        // Hộp thư liên hệ & Mail hỗ trợ
        'admin-mail'        => ['controller' => 'Mail',    'action' => 'hopThu'],
        'admin-mail-create' => ['controller' => 'Mail',    'action' => 'vietThu'],
        'admin-mail-store'  => ['controller' => 'Mail',    'action' => 'guiThu'],
        'inbox'             => ['controller' => 'Account', 'action' => 'hopThu'],
        
        // Cổng thanh toán quét mã QR tự động qua SePay
        'sepay-payment'      => ['controller' => 'Product', 'action' => 'thanhtoanSepay'],
        'sepay-check-status' => ['controller' => 'Product', 'action' => 'kiemtraTrangthaiSepay'],
        'sepay-webhook'      => ['controller' => 'SePayWebhook', 'action' => 'xuly'],
    ];

    // Kiểm tra trang yêu cầu có khớp với bảng định nghĩa cố định hay không
    if (isset($pageMap[$page])) {
        $controllerName = $pageMap[$page]['controller'] . 'Controller';
        $actionName     = $pageMap[$page]['action'];
    } else {
        // CƠ CHẾ ĐỊNH TUYẾN ĐỘNG (DYNAMIC ROUTING)
        // Nếu trang bắt đầu bằng tiền tố 'brand-', chuyển hướng xử lý sang hàm dynamic hiển thị sản phẩm theo thương hiệu tương ứng
        if (strpos($page, 'brand-') === 0) {
            $controllerName = 'ProductController';
            $actionName     = 'chiTietThuongHieuDynamic';
            $_GET['brand_slug'] = substr($page, 6); // Cắt lấy slug đằng sau chữ 'brand-' (ví dụ: nike, adidas)
        
        // Nếu trang bắt đầu bằng tiền tố 'sport-', chuyển hướng xử lý sang hàm dynamic hiển thị sản phẩm theo môn thể thao tương ứng
        } elseif (strpos($page, 'sport-') === 0) {
            $controllerName = 'ProductController';
            $actionName     = 'chiTietTheThaoDynamic';
            $_GET['sport_slug'] = $page; // Gửi toàn bộ slug thể thao (ví dụ: sport-football) qua $_GET
        } else {
            // Nếu không tìm thấy trang phù hợp, tự động chuyển hướng người dùng về trang chủ để tránh lỗi 404
            header('Location: index.php?page=home');
            exit;
        }
    }
}

// 4. KÍCH HOẠT CONTROLLER & ACTION PHÙ HỢP

// Xác định đường dẫn file Controller tương ứng
$controllerFile = './controllers/' . $controllerName . '.php';

// Kiểm tra xem file Controller có tồn tại vật lý trên ổ đĩa hay không
if (!file_exists($controllerFile)) {
    header('Location: index.php?page=home');
    exit;
}

// Nhúng file Controller vào luồng chạy
include $controllerFile;

// Kiểm tra lớp (Class) Controller đã được định nghĩa chưa
if (!class_exists($controllerName)) {
    header('Location: index.php?page=home');
    exit;
}

// Khởi tạo đối tượng (Object) Controller tương ứng
$controller = new $controllerName();

// Kiểm tra hàm (Action) xử lý có tồn tại trong Controller đó hay không
if (!method_exists($controller, $actionName)) {
    header('Location: index.php?page=home');
    exit;
}

// Tiến hành gọi hàm (Action) xử lý. Hàm này sẽ lấy dữ liệu từ Model và render ra giao diện cho người dùng.
$controller->$actionName();