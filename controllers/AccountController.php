<?php
/**
 * AccountController - Quản lý các hoạt động tài khoản khách hàng ở Frontend
 */

if (!class_exists('MailModel')) {
    require './models/MailModel.php';
}

class AccountController {
    /**
     * Kiểm tra đăng nhập khách hàng
     */
    private function checkUserAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['ma_nguoidung'])) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    /**
     * Hiển thị hộp thư cá nhân của khách hàng
     */
    public function hopThu() {
        $this->checkUserAuth();
        $maKH = $_SESSION['ma_nguoidung'];

        $mailModel = new MailModel();

        // Xử lý xem chi tiết và đánh dấu đã đọc
        $thuDaChon = null;
        $maThu = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($maThu > 0) {
            // Lấy danh sách mail để tìm thư cần xem (hoặc viết hàm riêng, nhưng getInboxByUserId đủ bảo mật)
            $dsThu = $mailModel->getInboxByUserId($maKH);
            foreach ($dsThu as $mail) {
                if ((int)$mail['MaThongBao'] === $maThu) {
                    $thuDaChon = $mail;
                    break;
                }
            }

            if ($thuDaChon) {
                // Đánh dấu đã đọc
                $mailModel->markAsRead($maThu, $maKH);
            }
        }

        // Lấy lại danh sách hộp thư cập nhật trạng thái đã đọc
        $dsHopThu = $mailModel->getInboxByUserId($maKH);

        // Chuẩn hóa dữ liệu tương thích với View
        $inboxMails = [];
        foreach ($dsHopThu as $m) {
            $inboxMails[] = [
                'id'         => $m['MaThongBao'],
                'MaKH'       => $m['MaKH'],
                'subject'    => $m['TieuDe'],
                'content'    => $m['NoiDung'],
                'is_read'    => $m['TrangThai'],
                'created_at' => $m['NgayTao'],
            ];
        }

        $selectedMail = null;
        if ($thuDaChon) {
            $selectedMail = [
                'id'         => $thuDaChon['MaThongBao'],
                'MaKH'       => $thuDaChon['MaKH'],
                'subject'    => $thuDaChon['TieuDe'],
                'content'    => $thuDaChon['NoiDung'],
                'is_read'    => 1, // Đã đánh dấu đọc ở trên
                'created_at' => $thuDaChon['NgayTao'],
            ];
        }

        $tieuDeTrang = 'Hộp thư của tôi - VAB';
        $trangHienTai = 'inbox';
        $nhungTuController = true;

        include './views/giaodien/dautrang.php';
        include './views/giaodien/hopthu.php';
        include './views/giaodien/chantrang.php';
    }
}
