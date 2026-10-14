<?php

/**
 * =================================================================================
 * CẤU HÌNH & KẾT NỐI CƠ SỞ DỮ LIỆU (database.php)
 * =================================================================================
 * FILE NÀY CHỊU TRÁCH NHIỆM KẾT NỐI VỚI CƠ SỞ DỮ LIỆU MYSQL THÔNG QUA THƯ VIỆN PDO.
 * 
 * TẠI SAO DÙNG PDO (PHP Data Objects)? (Câu hỏi vấn đáp cực kỳ phổ biến):
 * 1. PDO hỗ trợ cơ chế Prepared Statement cực kỳ mạnh mẽ giúp bảo mật tối đa, chống SQL Injection 100%.
 * 2. PDO là hướng đối tượng (Object-Oriented), dễ bắt lỗi bằng cấu trúc try-catch.
 * 3. Hỗ trợ nhiều loại hệ quản trị CSDL khác nhau (MySQL, PostgreSQL, SQLite...), nếu sau này đổi CSDL chỉ cần sửa DSN.
 * 
 * PHƯƠNG PHÁP SINGLETON DESIGN PATTERN (Biến static $pdo):
 * - Giúp tối ưu hiệu năng: Kết nối CSDL là tác vụ tốn tài nguyên nhất của một request.
 * - Bằng cách dùng biến tĩnh `static $pdo`, kết nối CSDL chỉ được tạo một lần duy nhất trong suốt vòng đời của request.
 * - Những lần gọi getConnection() tiếp theo sẽ dùng lại kết nối đã mở trước đó, thay vì mở thêm kết nối mới gây quá tải MySQL.
 */

// 1. KHAI BÁO CÁC BIẾN CẤU HÌNH KẾT NỐI

// Cấu hình chạy trên máy cá nhân cục bộ (Localhost - XAMPP)
$DB_HOST    = 'localhost';
$DB_PORT    = 3306;
$DB_NAME    = 'vab2026'; // Tên cơ sở dữ liệu MySQL của dự án
$DB_USER    = 'root';           // Username mặc định của XAMPP
$DB_PASS    = '';               // Mật khẩu mặc định của XAMPP là rỗng
$DB_CHARSET = 'utf8mb4';        // Bảng mã hỗ trợ tiếng Việt đầy đủ và các icon Emoji


// Cấu hình chạy thực tế trên Hosting Online (Alwaysdata)
// $DB_HOST    = 'mysql-vabshop2026.alwaysdata.net';
// $DB_PORT    = 3306;
// $DB_NAME    = 'vabshop2026_db';
// $DB_USER    = 'vabshop2026_user';
// $DB_PASS    = '@Vygo1022';
// $DB_CHARSET = 'utf8mb4';



// Cấu hình chạy trên Hosting miễn phí (InfinityFree)
// $DB_HOST    = 'sql309.infinityfree.com';
// $DB_PORT    = 3306;
// $DB_NAME    = 'if0_42254885_vabshop2026';
// $DB_USER    = 'if0_42254885';
// $DB_PASS    = 'lKJD0o3ILA9Li';
// $DB_CHARSET = 'utf8mb4';


/**
 * Hàm khởi tạo hoặc lấy ra kết nối PDO hiện tại (Singleton kết nối)
 * ĐẦU RA: Trả về đối tượng PDO đang hoạt động để thực hiện truy vấn.
 */
function getConnection(): PDO
{
    // Sử dụng biến cấu hình toàn cục bên ngoài hàm
    global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS, $DB_CHARSET;

    // Biến tĩnh tĩnh dùng để giữ trạng thái kết nối duy nhất
    static $pdo = null;

    // Nếu chưa có kết nối nào, tiến hành tạo mới
    if ($pdo === null) {
        try {
            // Chuỗi cấu hình DSN (Data Source Name) chỉ định driver, host, tên DB và charset
            $dsn = "mysql:host=" . $DB_HOST . ";port=" . $DB_PORT . ";dbname=" . $DB_NAME . ";charset=" . $DB_CHARSET;

            // Khởi tạo đối tượng PDO với các thiết lập bảo mật và hiệu năng
            $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
                // 1. Chế độ báo lỗi: Quăng ngoại lệ (Exception) khi có lỗi SQL để dễ dàng bắt lỗi bằng try-catch
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

                // 2. Chế độ fetch dữ liệu mặc định: Trả về mảng liên hợp (Associative Array) với khóa là tên cột
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                // 3. Tắt chế độ giả lập Prepared Statements (Emulate Prepares): Buộc MySQL thực hiện phân tích
                // và biên dịch thực sự cấu trúc câu lệnh SQL. Đây là chìa khóa để chặn hoàn toàn lỗi SQL Injection.
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            // Thiết lập đồng bộ múi giờ GMT+7 Việt Nam cho các phiên làm việc với MySQL
            // để các hàm NOW(), CURDATE() của SQL lưu trữ thời gian đồng nhất với PHP
            $pdo->exec("SET time_zone = '+07:00';");
        } catch (PDOException $e) {
            // Nếu kết nối thất bại, in ra thông báo lỗi và dừng toàn bộ chương trình ngay lập tức
            die('Database connection failed (Kết nối CSDL thất bại): ' . $e->getMessage());
        }
    }

    // Trả về kết nối hiện có
    return $pdo;
}
