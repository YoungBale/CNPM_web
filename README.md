# 🏀 VAB SHOP - Website Thương Mại Điện Tử Đồ Thể Thao

Website bán đồ thể thao chuyên nghiệp được phát triển theo mô hình MVC thuần PHP và MySQL, tích hợp thanh toán tự động qua SePay (VietQR) và hệ thống gửi email thông báo qua Gmail SMTP.

---

## 👥 Nhóm Thực Hiện
- **Vỹ** (Team Lead / Backend Developer)
- **Hiếu** (Frontend Developer / UI-UX Designer)
- **Ngữ** (Database Administrator / API Integrator)

---

## 🛠️ Công Nghệ Sử Dụng
- **Ngôn ngữ:** PHP 8.x, JavaScript (ES6), HTML5, CSS3
- **Kiến trúc:** MVC (Model - View - Controller)
- **Cơ sở dữ liệu:** MySQL / MariaDB (Kết nối bảo mật qua PDO Singleton)
- **Thư viện tích hợp:** 
  - PHPMailer (Gmail SMTP)
  - SePay Webhook (Thanh toán quét mã QR tự động)

---

## 🚀 Hướng Dẫn Cài Đặt (Localhost)

1. **Khởi động môi trường:**
   - Cài đặt và mở **XAMPP**.
   - Bật dịch vụ **Apache** và **MySQL**.

2. **Cấu hình Cơ sở dữ liệu:**
   - Mở trình duyệt truy cập: `http://localhost/phpmyadmin`
   - Tạo CSDL mới tên: `vab2026` với collation `utf8mb4_unicode_ci`.
   - Import file `sql/vab2026.sql` vào CSDL `vab2026`.

3. **Cấu hình dự án:**
   - Kiểm tra thông tin kết nối trong `models/database.php`.
   - Đặt thư mục dự án vào `C:/xampp/htdocs/vab-frontend` hoặc chạy trực tiếp qua PHP Built-in Server.

4. **Chạy ứng dụng:**
   - Truy cập trang khách hàng: `http://localhost/vab-frontend/`
   - Truy cập trang quản trị: `http://localhost/vab-frontend/index.php?act=quantri-dangnhap`
