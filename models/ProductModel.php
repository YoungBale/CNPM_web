<?php
/**
 * =================================================================================
 * MODEL SẢN PHẨM (ProductModel.php)
 * =================================================================================
 * LỚP NÀY CHỊU TRÁCH NHIỆM TƯƠNG TÁC VỚI BẢNG CSDL 'SANPHAM' VÀ 'QUANLYSP' (TỒN KHO).
 * Kế thừa lớp xl_database để sử dụng các hàm truy vấn PDO an toàn.
 * 
 * CÁC KỸ THUẬT TỐI ƯU TRUY VẤN SQL ĐƯỢC ÁP DỤNG:
 * 1. LEFT JOIN: Dùng để kết nối bảng SANPHAM với DANHMUCSP, THUONGHIEU, THETHAO nhằm lấy thêm tên
 *    danh mục, tên thương hiệu, tên môn thể thao tương ứng trong 1 câu lệnh SELECT duy nhất.
 * 2. CASE WHEN: Dịch chuẩn hóa tên danh mục từ dữ liệu thô (ao -> Áo, quan -> Quần, giay -> Giày)
 *    trực tiếp từ câu truy vấn SQL để hiển thị đẹp mắt ở giao diện.
 * 3. ĐIỀU KIỆN TRẠNG THÁI (TrangThai = 1): Chỉ hiển thị các sản phẩm, danh mục, thương hiệu đang mở (active).
 *    Nếu Admin ẩn một thương hiệu hoặc danh mục, sản phẩm thuộc nhóm đó sẽ tự động biến mất ở trang khách hàng.
 */

if (!class_exists('xl_database')) {
    require './models/xl_database.php';
}

class ProductModel extends xl_database {

    /**
     * HÀM 1: LẤY TẤT CẢ SẢN PHẨM ĐANG BÁN (getAll)
     * Thường dùng để kết xuất toàn bộ kho sản phẩm đang hoạt động.
     * ĐẦU RA: Mảng chứa thông tin chi tiết của tất cả sản phẩm kèm tên danh mục và thương hiệu.
     */
    public function getAll(): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1) 
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 ORDER BY sp.NgayTao DESC";
        return $this->layTatCa($sql);
    }

    /**
     * HÀM 2: LẤY CHI TIẾT SẢN PHẨM THEO MÃ (getById)
     * Dùng cho trang chi tiết sản phẩm.
     * ĐẦU VÀO: $id (Mã sản phẩm - MaSP).
     * ĐẦU RA: Mảng thông tin sản phẩm duy nhất hoặc null nếu không tồn tại.
     */
    public function getById(int $id): ?array {
        $sql = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                FROM SANPHAM sp
                LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                WHERE sp.MaSP = ? 
                  AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                  AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                  AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)";
        return $this->layMot($sql, [$id]);
    }

    /**
     * HÀM 3: LẤY SẢN PHẨM THEO DANH MỤC (getByCategory)
     * ĐẦU VÀO: $category (Tên danh mục hoặc Đường dẫn - DuongDan).
     */
    public function getByCategory(string $category): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE (dm.TenDM = ? OR dm.DuongDan = ?) 
                   AND sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 ORDER BY sp.NgayTao DESC";
        // Truyền 2 lần tham số $category cho 2 dấu chấm hỏi ở điều kiện (TenDM = ? OR DuongDan = ?)
        return $this->layTatCa($sql, [$category, $category]);
    }

    /**
     * HÀM 4: LẤY SẢN PHẨM THEO GIỚI TÍNH (getByGender)
     * Hỗ trợ lấy đúng giới tính yêu cầu (Nam hoặc Nữ) và bao gồm cả các sản phẩm 'Unisex' (Nam nữ đều dùng được).
     */
    public function getByGender(string $gender): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE (sp.GioiTinh = ? OR sp.GioiTinh = 'Unisex') 
                   AND sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 ORDER BY sp.NgayTao DESC";
        return $this->layTatCa($sql, [$gender]);
    }

    /**
     * HÀM 5: LẤY SẢN PHẨM THEO THƯƠNG HIỆU (getByBrand)
     * ĐẦU VÀO: $brand (Tên thương hiệu hoặc Đường dẫn - ví dụ: nike, adidas).
     */
    public function getByBrand(string $brand): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE (th.TenThuongHieu = ? OR th.DuongDan = ?) 
                   AND sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 ORDER BY sp.NgayTao DESC";
        return $this->layTatCa($sql, [$brand, $brand]);
    }

    /**
     * HÀM 6: LẤY SẢN PHẨM THEO BỘ MÔN THỂ THAO (getBySport)
     */
    public function getBySport(string $sport): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE sp.TheThao = ? 
                   AND sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 ORDER BY sp.NgayTao DESC";
        return $this->layTatCa($sql, [$sport]);
    }

    /**
     * HÀM 7: LẤY SẢN PHẨM ĐANG GIẢM GIÁ (getSaleItems)
     * Lọc sản phẩm có GiaKhuyenMai hợp lệ (lớn hơn 0 và không rỗng), giới hạn lấy 8 sản phẩm.
     */
    public function getSaleItems(): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE sp.GiaKhuyenMai IS NOT NULL AND sp.GiaKhuyenMai > 0 
                   AND sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 LIMIT 8";
        return $this->layTatCa($sql);
    }

    /**
     * HÀM 8: LẤY SẢN PHẨM MỚI NHẤT (getNewProducts)
     * Sắp xếp theo ngày tạo giảm dần (NgayTao DESC) để hiển thị sản phẩm mới lên trước.
     */
    public function getNewProducts(int $limit = 4): array {
        $sql = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                FROM SANPHAM sp
                LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                WHERE sp.TrangThai = 1 
                  AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                  AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                  AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                ORDER BY sp.NgayTao DESC";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }
        return $this->layTatCa($sql);
    }

    /**
     * HÀM 9: LẤY SẢN PHẨM NGẪU NHIÊN (getRandomProducts)
     * Dùng ORDER BY RAND() của MySQL để lấy sản phẩm ngẫu nhiên cho mục đề xuất/gợi ý.
     */
    public function getRandomProducts(int $limit = 10): array {
        $sql = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                FROM SANPHAM sp
                LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                WHERE sp.TrangThai = 1 
                  AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                  AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                  AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                ORDER BY RAND()";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }
        return $this->layTatCa($sql);
    }

    /**
     * HÀM 10: LẤY ĐƯỜNG DẪN ẢNH SẢN PHẨM (getImageUrl) - Hàm Tĩnh (static)
     * Giải quyết trường hợp sản phẩm không có ảnh chính hoặc ảnh lưu dạng URL bên ngoài.
     * ĐẦU RA: Đường dẫn ảnh hoàn chỉnh để in ra thẻ <img>.
     */
    public static function getImageUrl(array $product, int $w = 400, int $h = 500): string {
        $img = $product['AnhChinh'] ?? '';
        if (!empty($img)) {
            // Nếu ảnh bắt đầu bằng http/https hoặc thư mục assets/, trả về nguyên gốc
            if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0 || strpos($img, 'assets/') === 0) {
                return $img;
            }
            // Ngược lại, trả về đường dẫn tới thư mục ảnh sản phẩm cục bộ
            return 'assets/images/products/' . $img;
        }
        // Ảnh giả lập dự phòng (placeholder) khi sản phẩm chưa có ảnh
        $id = (int)($product['MaSP'] ?? 0);
        return "https://placehold.co/{$w}x{$h}/0a1628/fff?text=SP+{$id}";
    }

    /**
     * HÀM 11: LẤY GIÁ THỰC TẾ HIỂN THỊ (getDisplayPrice) - Hàm Tĩnh (static)
     * Tính toán giá bán cuối cùng: Nếu có giá khuyến mãi thì lấy giá khuyến mãi, ngược lại lấy giá gốc.
     */
    public static function getDisplayPrice(array $product): int {
        $giaKM = $product['GiaKhuyenMai'] ?? null;
        if (!empty($giaKM) && $giaKM > 0) {
            return (int)$giaKM;
        }
        return (int)($product['Gia'] ?? 0);
    }

    /**
     * HÀM 12: LẤY SẢN PHẨM PHÂN TRANG (getWithOffset)
     * Thường dùng cho phân trang danh sách sản phẩm.
     */
    public function getWithOffset(int $offset, int $limit): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 ORDER BY sp.NgayTao DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        return $this->layTatCa($sql);
    }

    /**
     * HÀM 13: ĐẾM TỔNG SỐ SẢN PHẨM ĐANG BÁN (getCount)
     */
    public function getCount(): int {
        $sql = "SELECT sp.* FROM SANPHAM sp
                LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                WHERE sp.TrangThai = 1 
                  AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                  AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                  AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)";
        return $this->demDong($sql);
    }

    /**
     * HÀM 14: ĐẾM SỐ SẢN PHẨM THEO BỘ LỌC ĐỘNG (getFilteredCount)
     * Hỗ trợ chức năng lọc sản phẩm động (Filter Sidebar) bằng cách tự tạo câu SQL nối thêm điều kiện WHERE.
     */
    public function getFilteredCount(array $filters): int {
        $sql = "SELECT sp.* FROM SANPHAM sp
                LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                WHERE sp.TrangThai = 1 
                  AND (dm.TrangThai IS NULL OR dm.TrangThai = 1) 
                  AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                  AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)";
        
        $params = [];
        
        // Nối thêm điều kiện lọc theo danh mục
        if (!empty($filters['categories'])) {
            $ids = array_map('intval', $filters['categories']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql .= " AND sp.MaDanhMuc IN ($placeholders)";
            $params = array_merge($params, $ids);
        }
        
        // Nối thêm điều kiện lọc theo thương hiệu
        if (!empty($filters['brands'])) {
            $ids = array_map('intval', $filters['brands']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql .= " AND sp.MaThuongHieu IN ($placeholders)";
            $params = array_merge($params, $ids);
        }
        
        // Nối thêm điều kiện lọc theo giới tính
        if (!empty($filters['gender'])) {
            $sql .= " AND (sp.GioiTinh = ? OR sp.GioiTinh = 'Unisex')";
            $params[] = $filters['gender'];
        }
        
        // Nối thêm điều kiện lọc theo môn thể thao
        if (!empty($filters['sports'])) {
            $placeholders = implode(',', array_fill(0, count($filters['sports']), '?'));
            $sql .= " AND sp.TheThao IN ($placeholders)";
            $params = array_merge($params, $filters['sports']);
        }
        
        return $this->demDong($sql, $params);
    }

    /**
     * HÀM 15: LẤY SẢN PHẨM THEO BỘ LỌC ĐỘNG (getFiltered)
     * Thao tác lọc sản phẩm kèm theo sắp xếp (giá tăng, giá giảm, mới nhất) và phân trang.
     */
    public function getFiltered(array $filters, int $offset, int $limit, string $sort = 'default'): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1) 
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)";
        
        $params = [];
        
        if (!empty($filters['categories'])) {
            $ids = array_map('intval', $filters['categories']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql .= " AND sp.MaDanhMuc IN ($placeholders)";
            $params = array_merge($params, $ids);
        }
        
        if (!empty($filters['brands'])) {
            $ids = array_map('intval', $filters['brands']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql .= " AND sp.MaThuongHieu IN ($placeholders)";
            $params = array_merge($params, $ids);
        }
        
        if (!empty($filters['gender'])) {
            $sql .= " AND (sp.GioiTinh = ? OR sp.GioiTinh = 'Unisex')";
            $params[] = $filters['gender'];
        }
        
        if (!empty($filters['sports'])) {
            $placeholders = implode(',', array_fill(0, count($filters['sports']), '?'));
            $sql .= " AND sp.TheThao IN ($placeholders)";
            $params = array_merge($params, $filters['sports']);
        }
        
        // Sắp xếp dữ liệu (Sử dụng hàm COALESCE để lấy giá hiển thị thực tế khi sắp xếp theo giá)
        if ($sort === 'price_asc') {
            $sql .= " ORDER BY COALESCE(NULLIF(sp.GiaKhuyenMai, 0), sp.Gia) ASC";
        } elseif ($sort === 'price_desc') {
            $sql .= " ORDER BY COALESCE(NULLIF(sp.GiaKhuyenMai, 0), sp.Gia) DESC";
        } else {
            $sql .= " ORDER BY sp.NgayTao DESC";
        }
        
        // Nối phân trang LIMIT và OFFSET vào cuối câu lệnh SQL
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        return $this->layTatCa($sql, $params);
    }

    /**
     * HÀM 16: TÌM KIẾM SẢN PHẨM THEO TỪ KHÓA (search)
     * Thường dùng cho thanh tìm kiếm đầu trang. Sử dụng toán tử LIKE.
     */
    public function search(string $keyword): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE (
                     LOWER(sp.TenSP) COLLATE utf8mb4_bin LIKE ? 
                     OR LOWER(dm.TenDM) COLLATE utf8mb4_bin LIKE ?
                     OR LOWER(th.TenThuongHieu) COLLATE utf8mb4_bin LIKE ?
                 ) 
                   AND sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)";
        $like = '%' . mb_strtolower($keyword, 'UTF-8') . '%';
        return $this->layTatCa($sql, [$like, $like, $like]);
    }

    /**
     * HÀM 17: THÊM MỚI SẢN PHẨM (create) - Dành cho trang quản trị Admin
     */
    public function create(array $data): int {
        $sql = "INSERT INTO SANPHAM
                    (TenSP, DuongDan, MaDanhMuc, Gia, GiaKhuyenMai, AnhChinh, MoTaSP, MaThuongHieu, TheThao, GioiTinh, KichCo, TrangThai, NgayTao)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        return (int) $this->themVaLayId($sql, [
            $data['TenSP'],
            $data['DuongDan'] ?? '',
            $data['MaDanhMuc'] ?? null,
            $data['Gia'],
            $data['GiaKhuyenMai'] ?? null,
            $data['AnhChinh'] ?? '',
            $data['MoTaSP'] ?? '',
            $data['MaThuongHieu'] ?? null,
            $data['TheThao'] ?? '',
            $data['GioiTinh'] ?? '',
            $data['KichCo'] ?? '',
            $data['TrangThai'] ?? 1,
        ]);
    }

    /**
     * HÀM 18: CẬP NHẬT THÔNG TIN SẢN PHẨM (update) - Dành cho trang quản trị Admin
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE SANPHAM
                SET TenSP=?, DuongDan=?, MaDanhMuc=?, Gia=?, GiaKhuyenMai=?, AnhChinh=?, MoTaSP=?, MaThuongHieu=?, TheThao=?, GioiTinh=?, KichCo=?, TrangThai=?
                WHERE MaSP=?";
        return $this->thucThi($sql, [
            $data['TenSP'],
            $data['DuongDan'] ?? '',
            $data['MaDanhMuc'] ?? null,
            $data['Gia'],
            $data['GiaKhuyenMai'] ?? null,
            $data['AnhChinh'] ?? '',
            $data['MoTaSP'] ?? '',
            $data['MaThuongHieu'] ?? null,
            $data['TheThao'] ?? '',
            $data['GioiTinh'] ?? '',
            $data['KichCo'] ?? '',
            $data['TrangThai'] ?? 1,
            $id, // Truyền MaSP vào điều kiện WHERE để sửa đúng sản phẩm
        ]);
    }

    /**
     * HÀM 19: XÓA SẢN PHẨM (delete) - Dành cho trang quản trị Admin
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM SANPHAM WHERE MaSP = ?";
        return $this->thucThi($sql, [$id]);
    }

    /**
     * HÀM 20: LẤY DANH SÁCH SIZE & SỐ LƯỢNG TỒN (getSizes)
     * Kết nối tới bảng QUANLYSP để xem sản phẩm này còn size nào và số lượng tồn bao nhiêu.
     */
    public function getSizes(int $productId): array {
        $sql  = "SELECT * FROM QUANLYSP WHERE MaSP = ? ORDER BY KichCo ASC";
        return $this->layTatCa($sql, [$productId]);
    }

    /**
     * HÀM 21: TRỪ TỒN KHO KHI MUA HÀNG THÀNH CÔNG (reduceStock)
     * ĐẦU VÀO: $id (Mã SP), $quantity (Số lượng mua), $size (Kích cỡ chọn).
     * CƠ CHẾ: Sử dụng hàm GREATEST(0, SoLuong - ?) để đảm bảo số lượng tồn kho không bao giờ bị âm (< 0).
     */
    public function reduceStock(int $id, int $quantity, string $size = ''): bool {
        if (!empty($size)) {
            $sqlSize = "UPDATE QUANLYSP SET SoLuong = GREATEST(0, SoLuong - ?) WHERE MaSP = ? AND KichCo = ?";
            $this->thucThi($sqlSize, [$quantity, $id, $size]);
            return true;
        } else {
            // Nếu không chọn size (dự phòng), giảm tồn kho ở size đầu tiên tìm thấy
            $sqlFirst = "SELECT KichCo, SoLuong FROM QUANLYSP WHERE MaSP = ? ORDER BY KichCo ASC LIMIT 1";
            $first    = $this->layMot($sqlFirst, [$id]);
            if ($first) {
                $sqlSize = "UPDATE QUANLYSP SET SoLuong = GREATEST(0, SoLuong - ?) WHERE MaSP = ? AND KichCo = ?";
                $this->thucThi($sqlSize, [$quantity, $id, $first['KichCo']]);
            }
            return true;
        }
    }

    /**
     * HÀM 22: KIỂM TRA SẢN PHẨM CÒN HÀNG TRÊN KHO KHÔNG (hasStock) - Hàm Tĩnh (static)
     * Cộng tổng số lượng của tất cả các size. Nếu tổng lớn hơn 0 thì trả về true (còn hàng).
     */
    public static function hasStock(int $productId): bool {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(SoLuong), 0) FROM QUANLYSP WHERE MaSP = ?");
        $stmt->execute([$productId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * HÀM 23: GỢI Ý TÌM KIẾM ĐỘNG QUA AJAX (suggest)
     * Trả về danh sách gợi ý nhanh khi người dùng gõ từ khóa vào ô tìm kiếm (Live Search).
     */
    public function suggest(string $keyword, int $limit = 6): array {
        $sql  = "SELECT sp.MaSP, sp.TenSP, sp.Gia, sp.GiaKhuyenMai, sp.AnhChinh 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE LOWER(sp.TenSP) COLLATE utf8mb4_bin LIKE ? 
                   AND sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 LIMIT " . (int)$limit;
        $like = '%' . mb_strtolower($keyword, 'UTF-8') . '%';
        return $this->layTatCa($sql, [$like]);
    }

    /**
     * HÀM 24: HOÀN TRẢ LẠI TỒN KHO KHI HỦY ĐƠN HÀNG (restoreStock)
     */
    public function restoreStock(int $id, int $quantity, string $size = ''): bool {
        if (!empty($size)) {
            $sqlSize = "UPDATE QUANLYSP SET SoLuong = SoLuong + ? WHERE MaSP = ? AND KichCo = ?";
            return $this->thucThi($sqlSize, [$quantity, $id, $size]);
        }
        return false;
    }

    /**
     * HÀM ĐẾM SỐ SẢN PHẨM KHUYẾN MÃI (getSaleItemsCount)
     */
    public function getSaleItemsCount(): int {
        $sql = "SELECT COUNT(*) FROM SANPHAM sp
                LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                WHERE sp.GiaKhuyenMai IS NOT NULL AND sp.GiaKhuyenMai > 0 
                  AND sp.TrangThai = 1 
                  AND (dm.TrangThai IS NULL OR dm.TrangThai = 1) 
                  AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                  AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)";
        $pdo = getConnection();
        return (int)$pdo->query($sql)->fetchColumn();
    }

    /**
     * HÀM LẤY SẢN PHẨM KHUYẾN MÃI CÓ PHÂN TRANG VÀ SẮP XẾP (getSaleItemsFiltered)
     */
    public function getSaleItemsFiltered(int $offset, int $limit, string $sort = 'default'): array {
        $sql = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                FROM SANPHAM sp
                LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                WHERE sp.GiaKhuyenMai IS NOT NULL AND sp.GiaKhuyenMai > 0 
                  AND sp.TrangThai = 1 
                  AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                  AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                  AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)";
        
        if ($sort === 'price_asc') {
            $sql .= " ORDER BY sp.GiaKhuyenMai ASC";
        } elseif ($sort === 'price_desc') {
            $sql .= " ORDER BY sp.GiaKhuyenMai DESC";
        } else {
            $sql .= " ORDER BY sp.NgayTao DESC";
        }
        
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        return $this->layTatCa($sql);
    }
}