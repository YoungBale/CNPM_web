<?php
/**
 * =================================================================================
 * CONTROLLER XÁC THỰC NGƯỜI DÙNG (AuthController.php)
 * =================================================================================
 * CONTROLLER NÀY XỬ LÝ TOÀN BỘ CÁC NGHIỆP VỤ LIÊN QUAN ĐẾN TÀI KHOẢN KHÁCH HÀNG:
 * Đăng nhập truyền thống, đăng ký tài khoản mới, tích hợp đăng nhập qua Google OAuth2
 * và đăng xuất khỏi hệ thống.
 * 
 * CÁC ĐIỂM CHÚ Ý KHI BẢO VỆ ĐỒ ÁN (Giảng viên hay hỏi):
 * 1. Cơ chế bảo mật mật khẩu:
 *    - Khi đăng ký, mật khẩu KHÔNG ĐƯỢC lưu dưới dạng văn bản thô. Hệ thống sử dụng hàm
 *      `password_hash($matKhau, PASSWORD_DEFAULT)` để mã hóa mật khẩu thành chuỗi hash bảo mật.
 *    - Khi đăng nhập, sử dụng hàm `password_verify($matKhauNhap, $matKhauHash)` để so khớp.
 *      Điều này đảm bảo dù database có bị rò rỉ, hacker cũng không thể đọc được mật khẩu thật của khách.
 * 2. Tích hợp Google OAuth2:
 *    - Sử dụng giải pháp đăng nhập một chạm của Google (Google One Tap/Google Sign-In).
 *    - Client gửi ID Token bảo mật (`$_POST['credential']`) lên Server.
 *    - Server dùng cURL gửi token này tới Google API (`oauth2.googleapis.com/tokeninfo`) để xác thực tính hợp lệ.
 *    - Nếu Google xác nhận thành công, Server tự động tạo tài khoản (nếu chưa có) và thiết lập Session đăng nhập.
 */

class AuthController {

    /**
     * CHỨC NĂNG: ĐĂNG NHẬP TRUYỀN THỐNG (dangNhap)
     * CƠ CHẾ HOẠT ĐỘNG:
     * 1. Nhận thông tin Email và Password gửi lên từ Form.
     * 2. Gọi hàm xác thực trong UserModel.
     * 3. Cơ chế đặc biệt: Nếu đăng nhập sai nhưng thông tin khớp với bảng nhân viên (NHANVIEN),
     *    hệ thống sẽ tự động đồng bộ tài khoản nhân viên đó sang bảng KHACHHANG và đăng nhập.
     * 4. Thiết lập các thông tin Session (`ma_nguoidung`, `ten_nguoidung`, `vaitro_nguoidung`...) để duy trì trạng thái đăng nhập.
     */
    public function dangNhap() {
        $tieuDeTrang = 'Đăng nhập - VAB';
        $trangHienTai = 'login';
        $nhungTuController = true;
        if (!class_exists('UserModel')) {
            require './models/UserModel.php';
        }
        $userModel = new UserModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $matKhau = $_POST['password'] ?? '';
            
            // Bước 1: Xác thực tài khoản khách hàng thông thường
            $nguoiDung = $userModel->authenticate($email, $matKhau);
            
            if (!$nguoiDung) {
                // ĐỒNG BỘ HÓA TÀI KHOẢN NHÂN VIÊN: 
                // Nếu khách hàng chưa có tài khoản nhưng lại là Nhân viên có sẵn trong bảng NHANVIEN,
                // hệ thống sẽ tự tạo mới tài khoản khách hàng tương ứng để họ có thể đăng nhập mua hàng.
                $pdo = getConnection();
                $stmt = $pdo->prepare("SELECT * FROM NHANVIEN WHERE email = ?");
                $stmt->execute([$email]);
                $nhanVien = $stmt->fetch();
                
                if ($nhanVien && password_verify($matKhau, $nhanVien['MatKhauNV'])) {
                    $nguoiDungDaCo = $userModel->getByEmail($email);
                    if (!$nguoiDungDaCo) {
                        // Chưa có -> Tạo mới
                        $stmtIns = $pdo->prepare("INSERT INTO KHACHHANG (HoTenKH, email, MatKhauKH, SDT, NgayTao) VALUES (?, ?, ?, ?, NOW())");
                        $stmtIns->execute([$nhanVien['HoTenNV'], $nhanVien['email'], $nhanVien['MatKhauNV'], $nhanVien['SDT'] ?? '']);
                    } else {
                        // Đã có -> Đồng bộ mật khẩu mới nhất từ nhân viên sang
                        $stmtUpd = $pdo->prepare("UPDATE KHACHHANG SET HoTenKH = ?, MatKhauKH = ?, SDT = ? WHERE MaKH = ?");
                        $stmtUpd->execute([$nhanVien['HoTenNV'], $nhanVien['MatKhauNV'], $nhanVien['SDT'] ?? '', $nguoiDungDaCo['MaKH']]);
                    }
                    
                    // Thực hiện xác thực lại sau khi đồng bộ thành công
                    $nguoiDung = $userModel->authenticate($email, $matKhau);
                }
            }

            // Đăng nhập thành công
            if ($nguoiDung) {
                // Kiểm tra tài khoản có bị khóa không
                if (($nguoiDung['TrangThai'] ?? 'active') === 'locked') {
                    $error = 'Tài khoản của bạn đã bị khóa!';
                } else {
                    // Thiết lập các biến Session đăng nhập của Khách hàng
                    $_SESSION['ma_nguoidung']    = $nguoiDung['MaKH'];
                    $_SESSION['ten_nguoidung']  = $nguoiDung['HoTenKH'];
                    $_SESSION['email_nguoidung'] = $nguoiDung['email'];
                    $_SESSION['vaitro_nguoidung']  = 'customer';

                    // ĐỒNG BỘ PHIÊN ADMIN: Kiểm tra xem tài khoản này có quyền Admin/Nhân viên không
                    $pdo  = getConnection();
                    $stmt = $pdo->prepare("SELECT * FROM NHANVIEN WHERE email = ?");
                    $stmt->execute([$nguoiDung['email']]);
                    $nhanVien = $stmt->fetch();
                    if ($nhanVien) {
                        $_SESSION['ma_nhanvien']   = $nhanVien['MaNV'];
                        $_SESSION['ten_nhanvien'] = $nhanVien['HoTenNV'];
                        $_SESSION['vaitro_nhanvien'] = $nhanVien['VaiTro'];
                        $_SESSION['vaitro_nguoidung']     = $nhanVien['VaiTro']; // Thiết lập quyền cao hơn
                    }

                    header('Location: index.php?page=home');
                    exit;
                }
            } else {
                $error = 'Email hoặc mật khẩu không đúng!';
            }
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/dangnhap.php';
        include './views/giaodien/chantrang.php';
    }

    /**
     * CHỨC NĂNG: ĐĂNG KÝ TÀI KHOẢN KHÁCH HÀNG MỚI (dangKy)
     * CƠ CHẾ HOẠT ĐỘNG:
     * 1. Nhận thông tin đăng ký (Họ tên, Email, SĐT, Mật khẩu).
     * 2. Kiểm tra trùng lặp email. Nếu email đã được đăng ký thì báo lỗi.
     * 3. Ngược lại, gọi hàm create trong UserModel để mã hóa mật khẩu và chèn dữ liệu vào bảng KHACHHANG.
     * 4. Đăng nhập tự động ngay sau khi đăng ký thành công và đưa về trang chủ.
     */
    public function dangKy() {
        $tieuDeTrang = 'Đăng ký - VAB';
        $trangHienTai = 'register';
        $nhungTuController = true;
        if (!class_exists('UserModel')) {
            require './models/UserModel.php';
        }
        $userModel = new UserModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            
            // Đảm bảo email chưa được ai khác sử dụng
            $nguoiDungDaCo = $userModel->getByEmail($email);
            if ($nguoiDungDaCo) {
                $error = 'Email này đã được đăng ký sử dụng!';
            } else {
                // Tạo tài khoản mới (Mật khẩu được mã hóa an toàn ở Model)
                $userId = $userModel->create([
                    'HoTenKH'   => $name,
                    'email'    => $email,
                    'SDT'    => $phone,
                    'MatKhauKH' => $password,
                ]);
                
                if ($userId) {
                    // Tự động đăng nhập cho khách hàng mới
                    $_SESSION['ma_nguoidung']    = $userId;
                    $_SESSION['ten_nguoidung']  = $name;
                    $_SESSION['email_nguoidung'] = $email;
                    $_SESSION['vaitro_nguoidung']  = 'customer';
                    header('Location: index.php?page=home');
                    exit;
                } else {
                    $error = 'Có lỗi xảy ra trong quá trình đăng ký!';
                }
            }
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/dangky.php';
        include './views/giaodien/chantrang.php';
    }

    /**
     * CHỨC NĂNG: ĐĂNG NHẬP THÔNG QUA TÀI KHOẢN GOOGLE (dangNhapGoogle)
     * CƠ CHẾ HOẠT ĐỘNG (Câu hỏi bảo vệ đồ án hay):
     * 1. Client sử dụng thư viện Javascript của Google hiển thị hộp thoại đăng nhập.
     * 2. Sau khi xác thực, Google trả về một chuỗi mã hóa an toàn gọi là 'Credential' (ID Token).
     * 3. Trình duyệt POST Credential này lên server PHP của chúng ta.
     * 4. Server PHP dùng thư viện cURL để gửi yêu cầu HTTPS lên Google API Server để giải mã và kiểm tra ID Token đó.
     * 5. Khi nhận được phản hồi JSON từ Google chứa email, họ tên của người dùng:
     *    - Nếu email này đã có tài khoản: Tiến hành đăng nhập luôn.
     *    - Nếu chưa có tài khoản: Hệ thống tự động chèn một bản ghi khách hàng mới với email đó, 
     *      và mật khẩu ngẫu nhiên tự tạo (đảm bảo tính toàn vẹn của CSDL) rồi cho phép đăng nhập.
     */
    public function dangNhapGoogle() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Nhận chuỗi ID Token gửi lên từ thư viện Javascript Google ở giao diện
        $idToken = $_POST['credential'] ?? null;
        if (!$idToken) {
            header('Location: index.php?page=login');
            exit;
        }

        if (!class_exists('UserModel')) {
            require './models/UserModel.php';
        }
        $userModel = new UserModel();

        // API Endpoint kiểm tra tính xác thực ID Token của Google
        $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($idToken);
        
        $response = null;
        // Sử dụng cURL để thực hiện gọi API từ xa (HTTP Request)
        if (function_exists('curl_version')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Tắt kiểm tra SSL certificate trên máy local tránh lỗi
            $response = curl_exec($ch);
            curl_close($ch);
        } else {
            // Giải pháp dự phòng nếu Server PHP không bật cURL extension
            $response = @file_get_contents($url);
        }

        if (!$response) {
            $tieuDeTrang = 'Lỗi đăng nhập - VAB';
            $trangHienTai = 'login';
            $error = 'Không thể kết nối đến máy chủ Google để xác thực!';
            include './views/giaodien/dautrang.php';
            include './views/giaodien/dangnhap.php';
            include './views/giaodien/chantrang.php';
            return;
        }

        $payload = json_decode($response, true);
        if (!isset($payload['email'])) {
            $tieuDeTrang = 'Lỗi đăng nhập - VAB';
            $trangHienTai = 'login';
            $error = 'Thông tin Google gửi về không hợp lệ hoặc thiếu Email!';
            include './views/giaodien/dautrang.php';
            include './views/giaodien/dangnhap.php';
            include './views/giaodien/chantrang.php';
            return;
        }

        $email = $payload['email'];
        $name = $payload['name'] ?? $email;

        // Tìm tài khoản khách hàng trong CSDL theo email nhận từ Google
        $nguoiDung = $userModel->getByEmail($email);

        if ($nguoiDung) {
            // Nếu tài khoản đang bị quản trị viên khóa, từ chối đăng nhập
            if (($nguoiDung['TrangThai'] ?? 'active') === 'locked') {
                $tieuDeTrang = 'Tài khoản bị khóa - VAB';
                $trangHienTai = 'login';
                $error = 'Tài khoản của bạn đã bị khóa!';
                include './views/giaodien/dautrang.php';
                include './views/giaodien/dangnhap.php';
                include './views/giaodien/chantrang.php';
                return;
            }
        } else {
            // TỰ ĐỘNG ĐĂNG KÝ: Nếu chưa có tài khoản, tự sinh mật khẩu ngẫu nhiên và đăng ký tài khoản khách hàng mới
            $randomPassword = bin2hex(random_bytes(8)); // Tạo mật khẩu ngẫu nhiên 16 ký tự hexa
            $userId = $userModel->create([
                'HoTenKH'   => $name,
                'email'    => $email,
                'MatKhauKH' => $randomPassword,
                'SDT'    => '',
            ]);

            $nguoiDung = $userModel->getById($userId);
        }

        // Tạo phiên đăng nhập thành công cho phiên làm việc hiện tại
        if ($nguoiDung) {
            $_SESSION['ma_nguoidung']    = $nguoiDung['MaKH'];
            $_SESSION['ten_nguoidung']  = $nguoiDung['HoTenKH'];
            $_SESSION['email_nguoidung'] = $nguoiDung['email'];
            $_SESSION['vaitro_nguoidung']  = 'customer';

            // Đồng bộ kiểm tra phân quyền Admin
            $pdo  = getConnection();
            $stmt = $pdo->prepare("SELECT * FROM NHANVIEN WHERE email = ?");
            $stmt->execute([$nguoiDung['email']]);
            $nhanVien = $stmt->fetch();
            if ($nhanVien) {
                $_SESSION['ma_nhanvien']   = $nhanVien['MaNV'];
                $_SESSION['ten_nhanvien'] = $nhanVien['HoTenNV'];
                $_SESSION['vaitro_nhanvien'] = $nhanVien['VaiTro'];
                $_SESSION['vaitro_nguoidung']     = $nhanVien['VaiTro'];
            }

            header('Location: index.php?page=home');
            exit;
        } else {
            $tieuDeTrang = 'Lỗi đăng nhập - VAB';
            $trangHienTai = 'login';
            $error = 'Không thể khởi tạo phiên đăng nhập từ tài khoản Google!';
            include './views/giaodien/dautrang.php';
            include './views/giaodien/dangnhap.php';
            include './views/giaodien/chantrang.php';
        }
    }

    /**
     * CHỨC NĂNG: ĐĂNG XUẤT HỆ THỐNG (dangXuat)
     * Giải phóng toàn bộ biến session hiện tại và chuyển hướng khách hàng về trang chủ.
     */
    public function dangXuat() {
        session_destroy();
        header('Location: index.php?page=home');
        exit;
    }
}