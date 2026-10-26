<?php
/**
 * MailModel - Tuong tac voi bang THONGBAO
 */

if (!class_exists('xl_database')) {
    require './models/xl_database.php';
}

class MailModel extends xl_database {

    /** Lay tat ca thu da gui (Admin) */
    public function getAllSentMail(): array {
        $sql = "SELECT tb.*, kh.HoTenKH AS HoTenKH, kh.email AS email
                FROM THONGBAO tb
                LEFT JOIN KHACHHANG kh ON tb.MaKH = kh.MaKH
                ORDER BY tb.NgayTao DESC";
        return $this->layTatCa($sql);
    }

    /** Tao mot thong bao moi (luu vao CSDL) */
    public function createMail(int $maKH, string $tieuDe, string $noiDung): int {
        $sql = "INSERT INTO THONGBAO (MaKH, TieuDe, NoiDung, TrangThai, NgayTao) VALUES (?, ?, ?, 0, NOW())";
        return (int) $this->themVaLayId($sql, [$maKH, $tieuDe, $noiDung]);
    }

    /** Lay hop thu cua mot nguoi dung cu the */
    public function getInboxByUserId(int $maKH): array {
        $sql  = "SELECT * FROM THONGBAO WHERE MaKH = ? ORDER BY NgayTao DESC";
        return $this->layTatCa($sql, [$maKH]);
    }

    /** Danh dau thu la da doc */
    public function markAsRead(int $mailId, int $maKH): bool {
        $sql = "UPDATE THONGBAO SET TrangThai = 1 WHERE MaThongBao = ? AND MaKH = ?";
        return $this->thucThi($sql, [$mailId, $maKH]);
    }
}
