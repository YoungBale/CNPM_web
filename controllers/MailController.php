<?php
/**
 * MailController - Quản lý chức năng gửi email và thông báo của Admin
 */

if (!class_exists('MailModel')) {
    require './models/MailModel.php';
}
if (!class_exists('UserModel')) {
    require './models/UserModel.php';
}
require './helpers/mail_helper.php';

class MailController {
    /**
     * Kiểm tra quyền quản trị viên (Admin/Nhân viên) và phân quyền
     */
    private function checkAdminAuth($allowedRoles = []) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['ma_nhanvien'])) {
            header('Location: index.php?page=admin-login');
            exit;
        }
        if (!empty($allowedRoles)) {
            $vaiTro = strtolower($_SESSION['vaitro_nhanvien'] ?? 'staff');
            if (!in_array($vaiTro, $allowedRoles)) {
                $_SESSION['admin_error'] = 'Bạn không có quyền thực hiện chức năng này!';
                header('Location: index.php?page=admin-dashboard');
                exit;
            }
        }
    }

    /**
     * Hiển thị danh sách email/thông báo đã gửi
     */
    public function hopThu() {
        $this->checkAdminAuth();

        $mailModel = new MailModel();
        $mails = $mailModel->getAllSentMail();

        $tieuDeTrang = 'Quản lý Email & Thông báo - VAB Admin';
        $trangHienTai = 'admin-mail';
        $nhungTuController = true;

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-hopthu.php';
        include './views/quantri/quantri-chantrang.php';
    }

    /**
     * Hiển thị form soạn email/thông báo
     */
    public function vietThu() {
        $this->checkAdminAuth(['admin', 'manager']);

        $userModel = new UserModel();
        $customers = $userModel->getAll();

        $tieuDeTrang = 'Gửi Email & Thông báo - VAB Admin';
        $trangHienTai = 'admin-mail';
        $nhungTuController = true;

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-vietthu.php';
        include './views/quantri/quantri-chantrang.php';
    }

    /**
     * Xử lý gửi email & lưu thông báo
     */
    public function guiThu() {
        $this->checkAdminAuth(['admin', 'manager']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin-mail');
            exit;
        }

        $targetUserId = $_POST['user_id'] ?? '';
        $subject      = trim($_POST['subject'] ?? '');
        $content      = trim($_POST['content'] ?? '');

        if (empty($subject) || empty($content)) {
            $_SESSION['mail_error'] = 'Tiêu đề và Nội dung không được để trống!';
            header('Location: index.php?page=admin-mail-create');
            exit;
        }

        $mailModel = new MailModel();
        $userModel = new UserModel();

        // Xác định danh sách khách hàng sẽ nhận thư
        $recipients = [];
        if ($targetUserId === 'all') {
            $recipients = $userModel->getAll();
        } else {
            $user = $userModel->getById((int)$targetUserId);
            if ($user) {
                $recipients[] = $user;
            }
        }

        if (empty($recipients)) {
            $_SESSION['mail_error'] = 'Không tìm thấy người nhận hợp lệ!';
            header('Location: index.php?page=admin-mail-create');
            exit;
        }

        $dbSuccessCount   = 0;
        $smtpSuccessCount = 0;
        $smtpFailCount    = 0;
        $smtpErrors       = [];

        foreach ($recipients as $recipient) {
            // 1. Lưu vào Database thông báo
            $mailId = $mailModel->createMail($recipient['MaKH'], $subject, $content);
            if ($mailId > 0) {
                $dbSuccessCount++;
            }

            // 2. Gửi Email thật qua SMTP
            $result = sendMail($recipient['email'], $recipient['HoTenKH'], $subject, $content);
            if ($result['success']) {
                $smtpSuccessCount++;
            } else {
                $smtpFailCount++;
                $smtpErrors[] = $recipient['email'] . ': ' . $result['message'];
            }
        }

        // Tạo thông báo kết quả
        $message = "Đã lưu vào Database {$dbSuccessCount} thông báo. ";
        $message .= "Gửi email thật thành công: {$smtpSuccessCount} mail.";
        if ($smtpFailCount > 0) {
            $message .= " Thất bại: {$smtpFailCount} mail.";
            $_SESSION['mail_smtp_errors'] = $smtpErrors; // Lưu các chi tiết lỗi SMTP để hiển thị
        }

        $_SESSION['mail_success'] = $message;
        header('Location: index.php?page=admin-mail');
        exit;
    }
}
