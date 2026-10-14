<?php
/**
 * =================================================================================
 * LỚP CƠ SỞ XỬ LÝ TRUY VẤN CƠ SỞ DỮ LIỆU (xl_database.php)
 * =================================================================================
 * LỚP NÀY ĐÓNG VAI TRÒ LÀ MỘT "WRAPPER" (BỘ BỌC TIỆN ÍCH CSDL).
 * Mọi Model khác trong hệ thống (như ProductModel, UserModel, OrderModel...) đều kế thừa 
 * (extends) từ lớp này để tái sử dụng các phương thức truy vấn nhanh gọn.
 * 
 * TẠI SAO PHẢI DÙNG CƠ CHẾ PREPARED STATEMENTS Ở ĐÂY? (Câu hỏi vấn đáp then chốt):
 * 1. Khi gọi $this->conn->prepare($sql), câu lệnh SQL (chứa các dấu chấm hỏi đại diện '?' hoặc tham số hóa ':id')
 *    sẽ được gửi trước lên MySQL Server để biên dịch cấu trúc (phác thảo khung sườn của câu lệnh).
 * 2. Khi gọi $stmt->execute($params), các dữ liệu từ biến đầu vào mới được gửi lên sau và lấp đầy vào các vị trí '?'.
 * 3. MySQL chỉ coi các giá trị trong $params là tham số thuần túy (dữ liệu thô), chứ không biên dịch chúng thành mã lệnh.
 *    Vì thế, dù hacker có cố tình nhập chuỗi độc hại dạng `' OR '1'='1` thì MySQL cũng chỉ hiểu đó là một chuỗi tìm kiếm thông thường.
 *    Đây là cơ chế ngăn chặn SQL Injection triệt để nhất!
 */

class xl_database
{
    // Thuộc tính lưu trữ kết nối cơ sở dữ liệu PDO
    protected $conn;

    /**
     * Hàm khởi tạo (Constructor) của lớp.
     * Khi bất kỳ Model nào kế thừa lớp này được new, nó sẽ tự động lấy kết nối PDO hiện tại.
     */
    public function __construct()
    {
        $this->conn = getConnection();
    }

    /**
     * PHƯƠNG THỨC 1: LẤY TẤT CẢ DÒNG DỮ LIỆU (layTatCa)
     * Thường dùng cho câu lệnh: SELECT hiển thị danh sách sản phẩm, danh sách hóa đơn, danh sách người dùng...
     * ĐẦU VÀO:
     *   - $sql: Chuỗi câu lệnh SQL chứa các tham số ẩn '?' (Ví dụ: "SELECT * FROM SANPHAM WHERE MaDanhMuc = ?")
     *   - $params: Mảng chứa các giá trị thực tế truyền vào thay thế các '?' (Ví dụ: [5])
     * ĐẦU RA: Trả về một mảng chứa nhiều dòng kết quả (mỗi dòng là một mảng liên hợp tên cột => giá trị).
     */
    public function layTatCa($sql, $params = [])
    {
        try {
            // Bước 1: Chuẩn bị câu lệnh (Biên dịch khung câu lệnh trước)
            $stmt = $this->conn->prepare($sql);
            
            // Bước 2: Nạp tham số và thực thi truy vấn
            $stmt->execute($params);
            
            // Bước 3: Thu hồi toàn bộ dữ liệu kết quả dưới dạng mảng liên hợp
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Nếu có lỗi truy vấn CSDL, in lỗi ra màn hình để lập trình viên sửa và dừng chương trình
            die('Lỗi truy vấn layTatCa: ' . $e->getMessage());
        }
    }

    /**
     * PHƯƠNG THỨC 2: LẤY DUY NHẤT MỘT DÒNG DỮ LIỆU (layMot)
     * Thường dùng cho câu lệnh: SELECT lấy chi tiết 1 sản phẩm theo mã, lấy thông tin 1 người dùng theo email...
     * ĐẦU VÀO: 
     *   - $sql: Câu truy vấn SQL (Ví dụ: "SELECT * FROM NGUOIDUNG WHERE Email = ?")
     *   - $params: Mảng tham số truyền vào (Ví dụ: ['khachhang@gmail.com'])
     * ĐẦU RA: Trả về mảng 1 chiều chứa các cột của dòng đó, hoặc trả về null nếu không tìm thấy.
     */
    public function layMot($sql, $params = [])
    {
        try {
            // Chuẩn bị câu lệnh
            $stmt = $this->conn->prepare($sql);
            
            // Thực thi truy vấn
            $stmt->execute($params);
            
            // Lấy dòng dữ liệu đầu tiên. Nếu fetch() không ra dòng nào, trả về null.
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            die('Lỗi truy vấn layMot: ' . $e->getMessage());
        }
    }

    /**
     * PHƯƠNG THỨC 3: THỰC THI THAY ĐỔI DỮ LIỆU (thucThi)
     * Dùng cho các câu lệnh thay đổi dữ liệu: INSERT (Thêm mới), UPDATE (Sửa đổi), DELETE (Xóa bỏ).
     * ĐẦU VÀO:
     *   - $sql: Câu lệnh INSERT/UPDATE/DELETE (Ví dụ: "UPDATE SANPHAM SET SoLuong = ? WHERE MaSP = ?")
     *   - $params: Giá trị thực tế điền vào (Ví dụ: [15, 102])
     * ĐẦU RA: Trả về true nếu thực thi thành công, false nếu thất bại.
     */
    public function thucThi($sql, $params = [])
    {
        try {
            // Chuẩn bị câu lệnh
            $stmt = $this->conn->prepare($sql);
            
            // Thực thi câu lệnh tác động trực tiếp vào CSDL
            return $stmt->execute($params);
        } catch (PDOException $e) {
            die('Lỗi truy vấn thucThi: ' . $e->getMessage());
        }
    }

    /**
     * PHƯƠNG THỨC 4: THÊM DỮ LIỆU VÀ LẤY MÃ ID VỪA TẠO (themVaLayId)
     * Dùng khi bạn INSERT một bản ghi vào bảng có ID tự động tăng (AUTO_INCREMENT) 
     * và cần ngay lập tức mã ID đó (Ví dụ: Thêm đơn hàng mới và lấy MaHD để thêm các sản phẩm vào chi tiết đơn hàng).
     * ĐẦU VÀO: Câu lệnh INSERT SQL và tham số.
     * ĐẦU RA: Trả về giá trị ID tự tăng vừa được tạo ra trong CSDL.
     */
    public function themVaLayId($sql, $params = [])
    {
        try {
            // Chuẩn bị câu lệnh
            $stmt = $this->conn->prepare($sql);
            
            // Thực thi chèn dữ liệu
            $stmt->execute($params);
            
            // Gọi lastInsertId() của PDO để lấy ID tự động sinh ra gần nhất của kết nối này
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            die('Lỗi truy vấn themVaLayId: ' . $e->getMessage());
        }
    }

    /**
     * PHƯƠNG THỨC 5: ĐẾM SỐ DÒNG BỊ ẢNH HƯỞNG HOẶC ĐẾM DÒNG KẾT QUẢ (demDong)
     * Dùng để kiểm tra xem một truy vấn SELECT có bao nhiêu dòng kết quả, 
     * hoặc câu lệnh UPDATE/DELETE đã tác động được bao nhiêu dòng dữ liệu.
     */
    public function demDong($sql, $params = [])
    {
        try {
            // Chuẩn bị câu lệnh
            $stmt = $this->conn->prepare($sql);
            
            // Thực thi
            $stmt->execute($params);
            
            // Trả về số dòng kết quả thông qua rowCount()
            return $stmt->rowCount();
        } catch (PDOException $e) {
            die('Lỗi truy vấn demDong: ' . $e->getMessage());
        }
    }
}
