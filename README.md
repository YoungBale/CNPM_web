# ĐỒ ÁN MÔN HỌC: CÔNG NGHỆ PHẦN MỀM

## ĐỀ TÀI: XÂY DỰNG WEBSITE BÁN THỜI TRANG THỂ THAO

---

### 📌 Giới Thiệu Đề Tài
Dự án được thực hiện trong khuôn khổ môn học **Công nghệ phần mềm**, nhằm áp dụng quy trình phân tích, thiết kế hệ thống và xây dựng một website thương mại điện tử chuyên kinh doanh các mặt hàng thời trang thể thao (quần áo, giày dép, phụ kiện tập luyện theo từng bộ môn như gym, chạy bộ, bóng đá...).

Hệ thống được thiết kế theo kiến trúc chuẩn **MVC (Model - View - Controller)** bằng ngôn ngữ **PHP thuần** và hệ quản trị cơ sở dữ liệu **MySQL**, tích hợp quy trình thanh toán tự động quét mã **VietQR** qua cổng **SePay Webhook** cùng hệ thống gửi hóa đơn tự động qua **Gmail SMTP (PHPMailer)**.

---

### 👥 Nhóm Sinh Viên Thực Hiện & Phân Công Nhiệm Vụ
- **Trương Chí Vỹ** (Trưởng nhóm / Backend Developer):
  - Khởi tạo kiến trúc MVC, thiết kế Front Controller (`index.php`) và bộ cấu hình hệ thống.
  - Xây dựng các Controller xử lý nghiệp vụ: Đăng nhập/Đăng ký, Giỏ hàng, Đặt hàng và toàn bộ bộ điều khiển phân hệ Quản trị viên (Admin Dashboard).
- **Nguyễn Trọng Hiếu** (Frontend Developer / UI-UX Designer):
  - Thiết kế và xây dựng toàn bộ giao diện phía Khách hàng và Quản trị bằng HTML5, CSS3 và JavaScript.
  - Xây dựng trải nghiệm người dùng (UI/UX) cho các trang: Trang chủ, Danh mục sản phẩm, Chi tiết sản phẩm, Giỏ hàng, Đặt hàng và Báo cáo doanh thu.
  - Thu thập, chuẩn hóa và tối ưu toàn bộ kho hình ảnh sản phẩm.
- **Nguyễn Thành Ngữ** (Database Administrator / API Integrator):
  - Khảo sát yêu cầu, thiết kế sơ đồ quan hệ thực thể (ERD) và xây dựng cơ sở dữ liệu MySQL (`vab2026`).
  - Xây dựng các lớp mô hình dữ liệu (Models) kế thừa bộ điều hợp PDO Prepared Statements an toàn.
  - Tích hợp cổng thanh toán trực tuyến VietQR tự động qua SePay Webhook và dịch vụ gửi mail thông báo PHPMailer.

---

### 🛠️ Công Nghệ & Kỹ Thuật Sử Dụng
- **Mô hình kiến trúc:** MVC (Model - View - Controller) thuần PHP.
- **Ngôn ngữ lập trình:** PHP 8.x, JavaScript (ES6), HTML5, CSS3.
- **Hệ quản trị cơ sở dữ liệu:** MySQL / MariaDB (Kết nối bảo mật qua PDO Singleton, chuẩn hóa chống SQL Injection bằng Prepared Statements).
- **Cơ chế bảo mật:** Mã hóa mật khẩu một chiều bằng thuật toán Bcrypt (`password_hash`), lọc đầu vào chống tấn công XSS (`htmlspecialchars`).
- **Thư viện & Dịch vụ tích hợp:**
  - **PHPMailer:** Gửi email hóa đơn xác nhận đơn hàng tự động thông qua giao thức Google SMTP.
  - **SePay API (VietQR Webhook):** Tự động nhận diện biến động số dư ngân hàng và cập nhật trạng thái đơn hàng theo thời gian thực.
- **Công cụ quản lý & Triển khai:** XAMPP (Apache, MySQL), Visual Studio Code, Git & GitHub.

---

### 🚀 Hướng Dẫn Cài Đặt Và Chạy Thử Nghiệm (Localhost)

#### 1. Chuẩn bị môi trường
- Cài đặt phần mềm **XAMPP** (hỗ trợ PHP 8.0 trở lên).
- Mở **XAMPP Control Panel** và nhấn **Start** cả 2 dịch vụ: **Apache** và **MySQL**.

#### 2. Cấu hình Cơ sở dữ liệu (Database)
- Mở trình duyệt web và truy cập địa chỉ: `http://localhost/phpmyadmin`
- Tạo một cơ sở dữ liệu mới với tên: `vab2026` (chọn bảng mã Collation: `utf8mb4_unicode_ci`).
- Chọn cơ sở dữ liệu `vab2026` vừa tạo, chuyển sang tab **Import (Nhập)**, chọn tệp tin `sql/vab2026.sql` trong thư mục dự án và nhấn **Import**.

#### 3. Cấu hình mã nguồn
- Đặt toàn bộ thư mục dự án vào thư mục gốc của máy chủ web:
  - Đường dẫn mặc định: `C:/xampp/htdocs/CNPM_web` (hoặc thư mục htdocs tương ứng trên máy tính của bạn).
- Kiểm tra thông số kết nối cơ sở dữ liệu tại tệp `models/database.php` (mặc định Host: `localhost`, User: `root`, Mật khẩu: rỗng, Tên DB: `vab2026`).

#### 4. Khởi chạy ứng dụng trên trình duyệt
- **Giao diện Khách hàng (Mua hàng & Thanh toán):**
  - Truy cập: `http://localhost/CNPM_web/`
- **Giao diện Quản trị viên (Admin Dashboard):**
  - Truy cập: `http://localhost/CNPM_web/index.php?act=quantri-dangnhap`
