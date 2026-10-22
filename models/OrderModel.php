<?php
/**
 * OrderModel - Tuong tac voi bang DONHANG va CHITIETDONHANG
 */

if (!class_exists('xl_database')) {
    require './models/xl_database.php';
}

class OrderModel extends xl_database {

    public function getAll(): array {
        $sql = "SELECT * FROM DONHANG ORDER BY NgayTao DESC";
        return $this->layTatCa($sql);
    }

    public function getById(int $maDonHang): ?array {
        $sql = "SELECT * FROM DONHANG WHERE MaDonHang = ?";
        return $this->layMot($sql, [$maDonHang]);
    }

    public function getByUserId(int $maKH): array {
        $sql = "SELECT * FROM DONHANG WHERE MaKH = ? ORDER BY NgayTao DESC";
        return $this->layTatCa($sql, [$maKH]);
    }

    public function create(array $duLieu): int {
        $tongTienVal = $duLieu['TongTien'] ?? 0;
        $trangThaiVal = $duLieu['TrangThaiDH'] ?? 'pending';
        if ($trangThaiVal === 'Cho xac nhan' || $trangThaiVal === 'cho xac nhan') {
            $trangThaiVal = 'pending';
        }

        $sql = "INSERT INTO DONHANG
                    (MaKH, HoTenKH, email, SDT, DiaChi, PhuongThucTT, GhiChu, TongTien, TrangThaiDH, MaGiamGia, SoTienGiam, NgayTao)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        return (int) $this->themVaLayId($sql, [
            $duLieu['MaKH']          ?? null,
            $duLieu['HoTenKH'],
            $duLieu['email']         ?? '',
            $duLieu['SDT'],
            $duLieu['DiaChi'],
            $duLieu['PhuongThucTT']  ?? 'COD',
            $duLieu['GhiChu']        ?? '',
            $tongTienVal,
            $trangThaiVal,
            $duLieu['MaGiamGia']    ?? null,
            $duLieu['SoTienGiam']    ?? 0,
        ]);
    }

    public function addItems(int $maDonHang, array $dsChiTiet): bool {
        $sql = "INSERT INTO CHITIETDONHANG (MaDonHang, MaSP, KichCo, TenSP, SoLuongSP, DonGia) VALUES (?, ?, ?, ?, ?, ?)";
        foreach ($dsChiTiet as $chiTiet) {
            $this->thucThi($sql, [
                $maDonHang,
                $chiTiet['MaSP'] > 0 ? $chiTiet['MaSP'] : null,
                $chiTiet['KichCo']     ?? '',
                $chiTiet['TenSP'],
                $chiTiet['SoLuongSP'],
                $chiTiet['DonGia'],
            ]);
        }
        return true;
    }

    public function updateStatus(int $maDonHang, string $trangThai): bool {
        $sql = "UPDATE DONHANG SET TrangThaiDH = ? WHERE MaDonHang = ?";
        return $this->thucThi($sql, [$trangThai, $maDonHang]);
    }

    public function delete(int $maDonHang): bool {
        $sql = "DELETE FROM DONHANG WHERE MaDonHang = ?";
        return $this->thucThi($sql, [$maDonHang]);
    }

    // ==========================================
    // CAC HAM THUC THE (NON-STATIC)
    // ==========================================

    /** Lay danh sach don hang cua nguoi dung dang dang nhap */
    public function getOrdersByUserId($maKH) {
        $sql    = "SELECT * FROM DONHANG WHERE MaKH = ? ORDER BY NgayTao DESC";
        return $this->layTatCa($sql, [(int)$maKH]);
    }

    /** Lay chi tiet don hang (co kiem tra MaKH de tranh xem trom) */
    public function getOrderByIdAndUserId($maDonHang, $maKH) {
        $sql   = "SELECT * FROM DONHANG WHERE MaDonHang = ? AND MaKH = ?";
        return $this->layMot($sql, [(int)$maDonHang, (int)$maKH]);
    }

    /** Lay danh sach san pham cua don hang */
    public function getOrderItems($maDonHang) {
        $sql = "
            SELECT ct.*, sp.AnhChinh as AnhChinh
            FROM CHITIETDONHANG ct
            LEFT JOIN SANPHAM sp ON ct.MaSP = sp.MaSP
            WHERE ct.MaDonHang = ?
        ";
        return $this->layTatCa($sql, [(int)$maDonHang]);
    }

    /** Tim don hang pending co cung so tien (dung cho Webhook khi khong co ma VABORD) */
    public function findPendingByAmount(int $soTien): ?array {
        $minAmount = $soTien - 1000;
        $maxAmount = $soTien + 1000;
        $sql = "SELECT * FROM DONHANG
                WHERE TrangThaiDH = 'pending'
                  AND TongTien BETWEEN ? AND ?
                ORDER BY NgayTao DESC
                LIMIT 1";
        return $this->layMot($sql, [$minAmount, $maxAmount]);
    }
}