<?php

/**
 * UserModel - Tuong tac voi bang KHACHHANG
 */

if (!class_exists('xl_database')) {
    require './models/xl_database.php';
}

class UserModel extends xl_database
{

    public function getAll(): array
    {
        $sql = "SELECT * FROM KHACHHANG ORDER BY NgayTao DESC";
        return $this->layTatCa($sql);
    }

    public function getById(int $maKH): ?array
    {
        $sql = "SELECT * FROM KHACHHANG WHERE MaKH = ?";
        return $this->layMot($sql, [$maKH]);
    }

    public function getByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM KHACHHANG WHERE email = ?";
        return $this->layMot($sql, [$email]);
    }

    public function create(array $duLieu): int
    {
        $matKhau = password_hash($duLieu['MatKhauKH'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO KHACHHANG (HoTenKH, email, MatKhauKH, SDT, NgayTao) VALUES (?, ?, ?, ?, NOW())";
        return (int) $this->themVaLayId($sql, [
            $duLieu['HoTenKH'],
            $duLieu['email'],
            $matKhau,
            $duLieu['SDT'] ?? '',
        ]);
    }

    public function authenticate(string $email, string $matKhau): ?array
    {
        $nguoiDung = $this->getByEmail($email);
        if ($nguoiDung && password_verify($matKhau, $nguoiDung['MatKhauKH'])) {
            return $nguoiDung;
        }
        return null;
    }

    public function update(int $maKH, array $duLieu): bool
    {
        $sql = "UPDATE KHACHHANG SET HoTenKH = ?, email = ?, SDT = ?, DiaChi = ? WHERE MaKH = ?";
        return $this->thucThi($sql, [
            $duLieu['HoTenKH'],
            $duLieu['email'],
            $duLieu['SDT'] ?? '',
            $duLieu['DiaChi'] ?? null,
            $maKH,
        ]);
    }

    /**
     * Cập nhật hạng thành viên của khách hàng dựa trên tổng chi tiêu tích lũy từ các đơn hàng hoàn thành (completed)
     */
    public function updateMemberTier(int $maKH): string
    {
        $sql = "SELECT COALESCE(SUM(TongTien), 0) AS TongChiTieu FROM DONHANG WHERE MaKH = ? AND TrangThaiDH = 'completed'";
        $result = $this->layMot($sql, [$maKH]);
        $tongChiTieu = (int)($result['TongChiTieu'] ?? 0);

        // Lấy hạng hiện tại của người dùng trước khi tính toán
        $sqlCurrent = "SELECT HangKH FROM KHACHHANG WHERE MaKH = ?";
        $currentTierRow = $this->layMot($sqlCurrent, [$maKH]);
        $oldTier = $currentTierRow['HangKH'] ?? 'silver';

        $newTier = 'silver';
        if ($tongChiTieu >= 5000000) {
            $newTier = 'diamond';
        } elseif ($tongChiTieu >= 2000000) {
            $newTier = 'gold';
        }

        if ($newTier !== $oldTier) {
            $sqlUpd = "UPDATE KHACHHANG SET HangKH = ? WHERE MaKH = ?";
            $this->thucThi($sqlUpd, [$newTier, $maKH]);

            // Gửi thông báo thăng hạng tự động (lưu vào bảng THONGBAO)
            if (!class_exists('MailModel')) {
                require_once './models/MailModel.php';
            }
            $mailModel = new MailModel();

            if ($newTier === 'gold') {
                $tieuDe = "Chúc mừng! Bạn đã thăng hạng thành viên VÀNG";
                $noiDung = "Chào bạn,\n\nChúc mừng tài khoản của bạn đã được nâng cấp lên hạng thành viên VÀNG nhờ tích lũy chi tiêu đạt mốc trên 2.000.000đ.\n\nBạn được hưởng đặc quyền ưu đãi dành riêng cho hạng VÀNG: Sử dụng mã giảm giá VANG20 để nhận ưu đãi giảm giá 20% cho đơn hàng tiếp theo (áp dụng 1 lần sử dụng).\n\nCảm ơn bạn đã luôn tin tưởng và đồng hành cùng VAB SHOP!";
                $mailModel->createMail($maKH, $tieuDe, $noiDung);
            } elseif ($newTier === 'diamond') {
                $tieuDe = "Chúc mừng! Bạn đã thăng hạng thành viên KIM CƯƠNG";
                $noiDung = "Chào bạn,\n\nChúc mừng tài khoản của bạn đã được nâng cấp lên hạng thành viên KIM CƯƠNG nhờ tích lũy chi tiêu đạt mốc trên 5.000.000đ.\n\nBạn được hưởng đặc quyền ưu đãi dành riêng cho hạng KIM CƯƠNG: Sử dụng mã giảm giá KIMCUONG20 để nhận ưu đãi giảm giá 20% cho đơn hàng tiếp theo (áp dụng 1 lần sử dụng).\n\nCảm ơn bạn đã luôn tin tưởng và đồng hành cùng VAB SHOP!";
                $mailModel->createMail($maKH, $tieuDe, $noiDung);
            }
        }

        return $newTier;
    }

    public function delete(int $maKH): bool
    {
        $sql = "DELETE FROM KHACHHANG WHERE MaKH = ?";
        return $this->thucThi($sql, [$maKH]);
    }
}
