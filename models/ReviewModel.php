<?php
/**
 * ReviewModel - Tuong tac voi bang DANHGIASP
 */
class ReviewModel {

    /**
     * Kiem tra nguoi dung da mua san pham va don hang da giao thanh cong chua
     */
    public function canUserReview($maKH, $maSP) {
        $pdo  = getConnection();
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM DONHANG dh
            JOIN CHITIETDONHANG ct ON dh.MaDonHang = ct.MaDonHang
            WHERE dh.MaKH = ?
              AND ct.MaSP = ?
              AND dh.TrangThaiDH IN ('completed', 'delivered')
        ");
        $stmt->execute([(int)$maKH, (int)$maSP]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Kiem tra nguoi dung da danh gia san pham nay chua
     */
    public function hasUserReviewed($maKH, $maSP) {
        $pdo  = getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM DANHGIASP WHERE MaKH = ? AND MaSP = ?");
        $stmt->execute([(int)$maKH, (int)$maSP]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Luu danh gia moi (mac dinh TrangThai la pending)
     */
    public function createReview($maSP, $maKH, $soSao, $noiDung) {
        $pdo    = getConnection();
        $soSao = (int)$soSao;
        if ($soSao < 1 || $soSao > 5) return false;
        $noiDung = trim($noiDung);
        if (empty($noiDung)) return false;

        $stmtUser = $pdo->prepare("SELECT HoTenKH, email FROM KHACHHANG WHERE MaKH = ?");
        $stmtUser->execute([(int)$maKH]);
        $nguoiDung = $stmtUser->fetch();
        $tenKH  = $nguoiDung['HoTenKH'] ?? 'Khach hang';
        $email = $nguoiDung['email']   ?? '';

        $stmt = $pdo->prepare("INSERT INTO DANHGIASP (MaSP, MaKH, TenKH, email, SoSao, NoiDung, TrangThai, NgayDG)
                               VALUES (?, ?, ?, ?, ?, ?, 'approved', NOW())");
        return $stmt->execute([(int)$maSP, (int)$maKH, $tenKH, $email, $soSao, $noiDung]);
    }

    /**
     * Lay danh sach danh gia da duoc duyet cua mot san pham
     */
    public function getApprovedReviewsByProduct($maSP) {
        $pdo  = getConnection();
        $stmt = $pdo->prepare("
            SELECT dg.*,
                   COALESCE(kh.HoTenKH, dg.TenKH) AS HoTenKH
            FROM DANHGIASP dg
            LEFT JOIN KHACHHANG kh ON dg.MaKH = kh.MaKH
            WHERE dg.MaSP = ? AND dg.TrangThai = 'approved'
            ORDER BY dg.NgayDG DESC
        ");
        $stmt->execute([(int)$maSP]);
        return $stmt->fetchAll();
    }

    /**
     * Tinh diem danh gia trung binh (chi lay danh gia da duyet)
     */
    public function getAverageRating($maSP) {
        $pdo  = getConnection();
        $stmt = $pdo->prepare("SELECT AVG(SoSao) FROM DANHGIASP WHERE MaSP = ? AND TrangThai = 'approved'");
        $stmt->execute([(int)$maSP]);
        $avg = $stmt->fetchColumn();
        return $avg !== null ? round((float)$avg, 1) : 0;
    }

    /**
     * Dem tong so danh gia da duyet
     */
    public function getReviewCount($maSP) {
        $pdo  = getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM DANHGIASP WHERE MaSP = ? AND TrangThai = 'approved'");
        $stmt->execute([(int)$maSP]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Admin: Lay tat ca danh gia
     */
    public function getAllReviews() {
        $pdo  = getConnection();
        $stmt = $pdo->query("
            SELECT dg.*,
                   sp.TenSP AS TenSP,
                   COALESCE(kh.HoTenKH, dg.TenKH) AS HoTenKH,
                   nv.HoTenNV AS NguoiDuyet
            FROM DANHGIASP dg
            LEFT JOIN SANPHAM sp ON dg.MaSP = sp.MaSP
            LEFT JOIN KHACHHANG kh ON dg.MaKH = kh.MaKH
            LEFT JOIN NHANVIEN nv ON dg.MaNV = nv.MaNV
            ORDER BY dg.NgayDG DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Admin: Duyet hoac tu choi danh gia
     */
    public function updateReviewStatus($reviewId, $trangThai, $maNV = null) {
        $pdo  = getConnection();
        $stmt = $pdo->prepare("UPDATE DANHGIASP SET TrangThai = ?, MaNV = ? WHERE MaDG = ?");
        return $stmt->execute([$trangThai, $maNV, (int)$reviewId]);
    }

    /**
     * Admin: Xoa danh gia
     */
    public function deleteReview($reviewId) {
        $pdo  = getConnection();
        $stmt = $pdo->prepare("DELETE FROM DANHGIASP WHERE MaDG = ?");
        return $stmt->execute([(int)$reviewId]);
    }
}
