<?php

/**
 * =================================================================================
 * CONTROLLER QUẢN TRỊ VIÊN (AdminController.php)
 * =================================================================================
 * Quản lý tổng thể phân hệ Admin: Dashboard, Sản phẩm, Danh mục, Đơn hàng, Doanh thu.
 */
class AdminController
{

    private function checkAuth()
    {
        if (!isset($_SESSION['ma_nhanvien'])) {
            header('Location: index.php?page=admin-login');
            exit;
        }
    }

    private function checkRole($allowedRoles)
    {
        $this->checkAuth();
        $vaiTro = strtolower($_SESSION['vaitro_nhanvien'] ?? 'staff');
        if (!in_array($vaiTro, $allowedRoles)) {
            $_SESSION['admin_error'] = 'Bạn không có quyền truy cập vào chức năng này!';
            header('Location: index.php?page=admin-dashboard');
            exit;
        }
    }

    // ============ DASHBOARD ============
    public function trangChu()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Bảng điều khiển - VAB Admin';
        $trangHienTai = 'admin-dashboard';
        $nhungTuController = true;

        $pdo = getConnection();

        $stmt = $pdo->query("SELECT COUNT(*) FROM SANPHAM");
        $totalProducts = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) FROM DONHANG");
        $totalOrders = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) FROM KHACHHANG");
        $totalUsers = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COALESCE(SUM(TongTien), 0) FROM DONHANG WHERE TrangThaiDH='completed'");
        $totalRevenue = $stmt->fetchColumn();

        // Đơn hàng gần đây
        $stmt = $pdo->query("SELECT dh.*, kh.HoTenKH AS customer_name FROM DONHANG dh LEFT JOIN KHACHHANG kh ON dh.MaKH = kh.MaKH ORDER BY dh.NgayTao DESC LIMIT 5");
        $recentOrders = $stmt->fetchAll();

        // Sản phẩm tồn kho thấp (dưới 7 sản phẩm)
        $stmt = $pdo->query("SELECT sp.*, COALESCE(SUM(ql.SoLuong),0) AS tong_kho FROM SANPHAM sp LEFT JOIN QUANLYSP ql ON sp.MaSP = ql.MaSP GROUP BY sp.MaSP HAVING tong_kho > 0 AND tong_kho < 7 ORDER BY tong_kho ASC LIMIT 5");
        $lowStock = $stmt->fetchAll();

        // Khách hàng bom hàng (có >= 1 đơn từ chối nhận trong 30 ngày gần đây)
        $stmt = $pdo->query("
            SELECT kh.MaKH, kh.HoTenKH, kh.SDT, kh.TrangThai,
                COUNT(dh.MaDonHang) AS total_orders,
                SUM(CASE WHEN dh.TrangThaiDH = 'refused' THEN 1 ELSE 0 END) AS refused_orders
            FROM KHACHHANG kh
            JOIN DONHANG dh ON kh.MaKH = dh.MaKH
            WHERE dh.NgayTao >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY kh.MaKH
            HAVING refused_orders >= 1
            ORDER BY refused_orders DESC
            LIMIT 5
        ");
        $bombers = $stmt->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-trangchu.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ PRODUCTS ============
    public function sanPham()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Quản lý sản phẩm - VAB Admin';
        $trangHienTai = 'admin-products';
        $nhungTuController = true;

        $pdo = getConnection();

        // Xóa sản phẩm
        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $productId = (int)$_GET['delete'];

            // Kiểm tra xem sản phẩm đã được mua chưa
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM CHITIETDONHANG WHERE MaSP = ?");
            $stmtCheck->execute([$productId]);
            $hasPurchased = (int)$stmtCheck->fetchColumn() > 0;

            if ($hasPurchased) {
                header('Location: index.php?page=admin-products&msg=error_purchased');
                exit;
            } else {
                $stmt = $pdo->prepare("DELETE FROM SANPHAM WHERE MaSP = ?");
                $stmt->execute([$productId]);
                header('Location: index.php?page=admin-products&msg=deleted');
                exit;
            }
        }

        // Thay đổi trạng thái hiển thị
        if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
            $stmt = $pdo->prepare("UPDATE SANPHAM SET TrangThai = IF(TrangThai=1, 0, 1) WHERE MaSP = ?");
            $stmt->execute([$_GET['toggle_status']]);
            header('Location: index.php?page=admin-products&msg=updated');
            exit;
        }

        $search   = isset($_GET['search'])   ? trim($_GET['search'])   : '';
        $category = isset($_GET['category']) ? trim($_GET['category']) : '';
        $filter_sales = isset($_GET['filter_sales']) ? trim($_GET['filter_sales']) : 'all';

        $sql    = "SELECT sp.*, 
                          CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, 
                          th.TenThuongHieu AS brand, 
                          COALESCE(SUM(ql.SoLuong),0) AS SoLuong, 
                          nv.HoTenNV AS NguoiTao,
                          (SELECT COALESCE(SUM(ct.SoLuongSP), 0) FROM CHITIETDONHANG ct JOIN DONHANG dh ON ct.MaDonHang = dh.MaDonHang WHERE ct.MaSP = sp.MaSP AND dh.TrangThaiDH != 'cancelled') AS DaBan
                   FROM SANPHAM sp 
                   LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc 
                   LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                   LEFT JOIN QUANLYSP ql ON sp.MaSP = ql.MaSP 
                   LEFT JOIN NHANVIEN nv ON sp.MaNV = nv.MaNV
                   WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql     .= " AND sp.TenSP LIKE ?";
            $params[] = '%' . $search . '%';
        }
        if ($category !== '') {
            if (is_numeric($category)) {
                $sql     .= " AND sp.MaDanhMuc = ?";
                $params[] = (int)$category;
            } else {
                $catMap = [
                    'Áo' => 1,
                    'Quần' => 2,
                    'Giày' => 3,
                    'Phụ kiện' => 4,
                    'Ao' => 1,
                    'Quan' => 2,
                    'Giay' => 3,
                    'Phu kien' => 4
                ];
                $catId = $catMap[$category] ?? 0;
                $sql     .= " AND sp.MaDanhMuc = ?";
                $params[] = $catId;
            }
        }

        $sql .= " GROUP BY sp.MaSP";

        if ($filter_sales === 'slow') {
            $sql .= " HAVING DaBan < 3";
        } elseif ($filter_sales === 'fast') {
            $sql .= " HAVING DaBan >= 3";
        }

        $sql .= " ORDER BY sp.NgayTao DESC";

        $stmt     = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        $categoriesList = $pdo->query("SELECT * FROM DANHMUCSP ORDER BY MaDanhMuc ASC")->fetchAll();

        $msg = $_GET['msg'] ?? '';

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-sanpham.php';
        include './views/quantri/quantri-chantrang.php';
    }
    // thêm sản phẩm
    public function themSanPham()
    {
        $this->checkRole(['admin', 'manager']);
        $tieuDeTrang   = 'Thêm sản phẩm - VAB Admin';
        $trangHienTai = 'admin-product-add';
        $nhungTuController = true;

        $pdo = getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name      = $_POST['name'] ?? '';
            $slug      = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            $category  = $_POST['category'] ?? '';
            $brand     = $_POST['brand'] ?? '';
            $price     = (int)($_POST['price'] ?? 0);
            $sale_price = !empty($_POST['sale_price']) ? (int)$_POST['sale_price'] : null;
            $gender    = $_POST['gender'] ?? '';
            $sport     = $_POST['sport'] ?? '';
            $status    = (int)($_POST['status'] ?? 1);
            $description = $_POST['description'] ?? '';

            // Xử lý Size và Số lượng
            $inputSizes      = $_POST['sizes']      ?? [];
            $inputQuantities = $_POST['quantities']  ?? [];
            $sizeData  = [];
            $sizesCache = [];
            if (is_array($inputSizes) && is_array($inputQuantities)) {
                for ($i = 0; $i < count($inputSizes); $i++) {
                    $sz = trim($inputSizes[$i]);
                    if ($sz !== '') {
                        $qty = max(0, (int)($inputQuantities[$i] ?? 0));
                        $sizeData[$sz] = $qty;
                        $sizesCache[]  = $sz;
                    }
                }
            }
            $sizesStr = implode(',', $sizesCache);

            // Upload ảnh chính
            $image = '';
            if (!empty($_FILES['image']['name'])) {
                $target_dir = 'assets/images/products/';
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $image = time() . '_' . str_replace(',', '_', basename($_FILES['image']['name']));
                move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image);
            }

            // Upload ảnh phụ (sub_images)
            $sub_images_list = [];
            if (!empty($_FILES['sub_images']['name'][0])) {
                $target_dir = 'assets/images/products/';
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $file_count = count($_FILES['sub_images']['name']);
                $limit = min($file_count, 3);
                for ($i = 0; $i < $limit; $i++) {
                    if ($_FILES['sub_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $sub_img_name = time() . '_sub_' . $i . '_' . str_replace(',', '_', basename($_FILES['sub_images']['name'][$i]));
                        if (move_uploaded_file($_FILES['sub_images']['tmp_name'][$i], $target_dir . $sub_img_name)) {
                            $sub_images_list[] = $sub_img_name;
                        }
                    }
                }
            }
            $images_str = implode(',', $sub_images_list);

            $maNV = $_SESSION['ma_nhanvien'] ?? null;
            $stmt = $pdo->prepare("INSERT INTO SANPHAM (TenSP, DuongDan, MaDanhMuc, MaThuongHieu, Gia, GiaKhuyenMai, GioiTinh, TheThao, TrangThai, KichCo, AnhChinh, AnhPhu, MoTaSP, MaNV, NgayTao) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())");
            $stmt->execute([$name, $slug, $category, $brand, $price, $sale_price, $gender, $sport, $status, $sizesStr, $image, $images_str, $description, $maNV]);
            $productId = $pdo->lastInsertId();

            $stmtSize = $pdo->prepare("INSERT INTO QUANLYSP (MaSP, KichCo, SoLuong) VALUES (?, ?, ?)");
            foreach ($sizeData as $sz => $qty) {
                $stmtSize->execute([$productId, $sz, $qty]);
            }

            header('Location: index.php?page=admin-products&msg=added');
            exit;
        }

        $categories = $pdo->query("SELECT * FROM DANHMUCSP")->fetchAll();
        $brands     = $pdo->query("SELECT * FROM THUONGHIEU")->fetchAll();
        $sports     = $pdo->query("SELECT * FROM THETHAO")->fetchAll();

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-themsanpham.php';
        include './views/quantri/quantri-chantrang.php';
    }

    public function suaSanPham()
    {
        $this->checkRole(['admin', 'manager']);
        $tieuDeTrang   = 'Sửa sản phẩm - VAB Admin';
        $trangHienTai = 'admin-product-edit';
        $nhungTuController = true;

        $pdo = getConnection();
        $id  = (int)($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id          = (int)($_POST['id'] ?? 0);
            $name        = $_POST['name'] ?? '';
            $slug        = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            $category    = $_POST['category'] ?? '';
            $brand       = $_POST['brand'] ?? '';
            $price       = (int)($_POST['price'] ?? 0);
            $sale_price  = !empty($_POST['sale_price']) ? (int)$_POST['sale_price'] : null;
            $gender      = $_POST['gender'] ?? '';
            $sport       = $_POST['sport'] ?? '';
            $status      = (int)($_POST['status'] ?? 1);
            $description = $_POST['description'] ?? '';

            $inputSizes      = $_POST['sizes']     ?? [];
            $inputQuantities = $_POST['quantities'] ?? [];
            $sizeData   = [];
            $sizesCache = [];
            if (is_array($inputSizes) && is_array($inputQuantities)) {
                for ($i = 0; $i < count($inputSizes); $i++) {
                    $sz = trim($inputSizes[$i]);
                    if ($sz !== '') {
                        $qty = max(0, (int)($inputQuantities[$i] ?? 0));
                        $sizeData[$sz] = $qty;
                        $sizesCache[]  = $sz;
                    }
                }
            }
            $sizesStr = implode(',', $sizesCache);

            $image = $_POST['existing_image'] ?? '';
            if (!empty($_FILES['image']['name'])) {
                $target_dir = 'assets/images/products/';
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $image = time() . '_' . str_replace(',', '_', basename($_FILES['image']['name']));
                move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image);
            }

            $images_str = $_POST['existing_images'] ?? '';
            if (!empty($_FILES['sub_images']['name'][0])) {
                $target_dir    = 'assets/images/products/';
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $sub_images_list = [];
                $file_count = count($_FILES['sub_images']['name']);
                $limit = min($file_count, 3);
                for ($i = 0; $i < $limit; $i++) {
                    if ($_FILES['sub_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $sub_img_name = time() . '_sub_' . $i . '_' . str_replace(',', '_', basename($_FILES['sub_images']['name'][$i]));
                        if (move_uploaded_file($_FILES['sub_images']['tmp_name'][$i], $target_dir . $sub_img_name)) {
                            $sub_images_list[] = $sub_img_name;
                        }
                    }
                }
                if (!empty($sub_images_list)) {
                    $images_str = implode(',', $sub_images_list);
                }
            }

            $stmt = $pdo->prepare("UPDATE SANPHAM SET TenSP=?, DuongDan=?, MaDanhMuc=?, MaThuongHieu=?, Gia=?, GiaKhuyenMai=?, GioiTinh=?, TheThao=?, TrangThai=?, KichCo=?, AnhChinh=?, AnhPhu=?, MoTaSP=? WHERE MaSP=?");
            $stmt->execute([$name, $slug, $category, $brand, $price, $sale_price, $gender, $sport, $status, $sizesStr, $image, $images_str, $description, $id]);

            $stmtDel = $pdo->prepare("DELETE FROM QUANLYSP WHERE MaSP = ?");
            $stmtDel->execute([$id]);

            $stmtSize = $pdo->prepare("INSERT INTO QUANLYSP (MaSP, KichCo, SoLuong) VALUES (?, ?, ?)");
            foreach ($sizeData as $sz => $qty) {
                $stmtSize->execute([$id, $sz, $qty]);
            }

            header('Location: index.php?page=admin-products&msg=updated');
            exit;
        }

        if (!$id) {
            header('Location: index.php?page=admin-products');
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM SANPHAM WHERE MaSP = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            header('Location: index.php?page=admin-products');
            exit;
        }

        $categories = $pdo->query("SELECT * FROM DANHMUCSP")->fetchAll();
        $brands     = $pdo->query("SELECT * FROM THUONGHIEU")->fetchAll();
        $sports     = $pdo->query("SELECT * FROM THETHAO")->fetchAll();

        $stmtSizes = $pdo->prepare("SELECT * FROM QUANLYSP WHERE MaSP = ? ORDER BY KichCo ASC");
        $stmtSizes->execute([$id]);
        $productSizes = $stmtSizes->fetchAll();

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-suasanpham.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ EMPLOYEES ============
    public function nhanVien()
    {
        $this->checkRole(['admin']);
        $tieuDeTrang   = 'Quản lý nhân viên - VAB Admin';
        $trangHienTai = 'admin-employees';
        $nhungTuController = true;

        $pdo = getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_employee'])) {
            $name     = $_POST['name']     ?? '';
            $email    = $_POST['email']    ?? '';
            $phone    = $_POST['phone']    ?? '';
            $role     = $_POST['role']     ?? 'staff';
            $password = password_hash($_POST['password'] ?? '123456', PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO NHANVIEN (HoTenNV, email, MatKhauNV, SDT, VaiTro) VALUES (?,?,?,?,?)");
            $stmt->execute([$name, $email, $password, $phone, $role]);
            header('Location: index.php?page=admin-employees&msg=added');
            exit;
        }

        if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
            $stmt = $pdo->prepare("UPDATE NHANVIEN SET TrangThai = IF(TrangThai='active','locked','active') WHERE MaNV=?");
            $stmt->execute([$_GET['toggle']]);
            header('Location: index.php?page=admin-employees&msg=toggled');
            exit;
        }

        $stmt      = $pdo->query("SELECT * FROM NHANVIEN ORDER BY NgayLapTK DESC");
        $employees = $stmt->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-nhanvien.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ REVIEWS ============
    public function danhGia()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Quản lý đánh giá - VAB Admin';
        $trangHienTai = 'admin-reviews';
        $nhungTuController = true;

        $pdo = getConnection();

        if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
            $stmt = $pdo->prepare("UPDATE DANHGIASP SET TrangThai='approved' WHERE id=?");
            $stmt->execute([$_GET['approve']]);
            header('Location: index.php?page=admin-reviews&msg=approved');
            exit;
        }

        if (isset($_GET['reject']) && is_numeric($_GET['reject'])) {
            $stmt = $pdo->prepare("UPDATE DANHGIASP SET TrangThai='rejected' WHERE id=?");
            $stmt->execute([$_GET['reject']]);
            header('Location: index.php?page=admin-reviews&msg=rejected');
            exit;
        }

        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $stmt = $pdo->prepare("DELETE FROM DANHGIASP WHERE id=?");
            $stmt->execute([$_GET['delete']]);
            header('Location: index.php?page=admin-reviews&msg=deleted');
            exit;
        }

        $stmt = $pdo->query("
            SELECT dg.*, sp.TenSP AS TenSP,
                   COALESCE(kh.HoTenKH, dg.TenKH) AS HoTenKH
            FROM DANHGIASP dg
            LEFT JOIN SANPHAM sp ON dg.MaSP = sp.MaSP
            LEFT JOIN KHACHHANG kh ON dg.MaKH = kh.MaKH
            ORDER BY dg.NgayDG DESC
        ");
        $reviews = $stmt->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-danhgia.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ USERS (Khach hang) ============
    public function nguoiDung()
    {
        $this->checkRole(['admin', 'manager']);
        $tieuDeTrang   = 'Quản lý khách hàng - VAB Admin';
        $trangHienTai = 'admin-users';
        $nhungTuController = true;

        $pdo = getConnection();

        if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
            $stmt = $pdo->prepare("UPDATE KHACHHANG SET TrangThai = IF(TrangThai='active','locked','active') WHERE MaKH=?");
            $stmt->execute([$_GET['toggle']]);
            header('Location: index.php?page=admin-users&msg=toggled');
            exit;
        }

        $filter_bomb = isset($_GET['filter_bomb']) ? trim($_GET['filter_bomb']) : 'all';

        $userSql = "
            SELECT kh.*,
                COUNT(dh.MaDonHang) AS total_orders,
                SUM(CASE WHEN dh.TrangThaiDH = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_orders,
                SUM(CASE WHEN dh.TrangThaiDH = 'refused' THEN 1 ELSE 0 END) AS refused_orders
            FROM KHACHHANG kh
            LEFT JOIN DONHANG dh ON kh.MaKH = dh.MaKH
            GROUP BY kh.MaKH
        ";

        if ($filter_bomb === 'bomb') {
            $userSql .= " HAVING refused_orders >= 1";
        } elseif ($filter_bomb === 'normal') {
            $userSql .= " HAVING refused_orders < 1";
        }

        $userSql .= " ORDER BY refused_orders DESC, cancelled_orders DESC, kh.NgayTao DESC";

        $users = $pdo->query($userSql)->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-nguoidung.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ VOUCHERS ============
    public function maGiamGia()
    {
        $this->checkRole(['admin', 'manager']);
        $tieuDeTrang   = 'Quản lý mã giảm giá - VAB Admin';
        $trangHienTai = 'admin-vouchers';
        $nhungTuController = true;

        $pdo = getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_voucher'])) {
            $code      = strtoupper(trim($_POST['code'] ?? ''));
            $name      = $_POST['name']       ?? '';
            $type      = $_POST['type']       ?? 'percent';
            $value     = (int)($_POST['value']     ?? 0);
            $min_order = (int)($_POST['min_order']  ?? 0);
            $max_use   = (int)($_POST['max_use']    ?? 100);
            $start_date = $_POST['start_date'] ?? date('Y-m-d');
            $end_date   = $_POST['end_date']   ?? date('Y-m-d', strtotime('+30 days'));

            try {
                $stmt = $pdo->prepare("INSERT INTO MAGIAMGIA (MaGiamGia, Ten, Loai, GiaTri, DonHangToiThieu, SoLuot, NgayBD, NgayKT) VALUES (?,?,?,?,?,?,?,?)");
                $stmt->execute([$code, $name, $type, $value, $min_order, $max_use, $start_date, $end_date]);
                header('Location: index.php?page=admin-vouchers&msg=added');
                exit;
            } catch (PDOException $e) {
                header('Location: index.php?page=admin-vouchers&msg=error_duplicate');
                exit;
            }
        }

        if (isset($_GET['delete'])) {
            $voucherCode = trim($_GET['delete']);
            if ($voucherCode !== '') {
                $stmt = $pdo->prepare("DELETE FROM MAGIAMGIA WHERE MaGiamGia=?");
                $stmt->execute([$voucherCode]);
                header('Location: index.php?page=admin-vouchers&msg=deleted');
                exit;
            }
        }

        $vouchers = $pdo->query("SELECT * FROM MAGIAMGIA ORDER BY NgayBD DESC")->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-magiamgia.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ ORDERS ============
    public function donHang()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Quản lý đơn hàng - VAB Admin';
        $trangHienTai = 'admin-orders';
        $nhungTuController = true;

        $pdo = getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
            $order_id   = (int)($_POST['order_id'] ?? 0);
            $new_status = $_POST['status'] ?? '';

            // Lấy trạng thái hiện tại của đơn hàng
            $stmtStatus = $pdo->prepare("SELECT TrangThaiDH FROM DONHANG WHERE MaDonHang = ?");
            $stmtStatus->execute([$order_id]);
            $current_status = $stmtStatus->fetchColumn();

            $allowedTransitions = [
                'pending'   => ['confirmed', 'shipping', 'delivered', 'completed', 'cancelled'],
                'confirmed' => ['shipping', 'delivered', 'completed', 'cancelled'],
                'shipping'  => ['delivered', 'completed', 'cancelled', 'refused'],
                'delivered' => ['completed', 'cancelled', 'returning', 'refused'],
                'completed' => ['cancelled', 'returning'],
                'cancelled' => [],
                'returning' => ['returned'],
                'returned'  => [],
                'refused'   => []
            ];

            $allowed = $allowedTransitions[$current_status] ?? [];
            if (in_array($new_status, $allowed)) {
                // Lấy MaKH của đơn hàng trước khi cập nhật
                $stmtKH = $pdo->prepare("SELECT MaKH FROM DONHANG WHERE MaDonHang = ?");
                $stmtKH->execute([$order_id]);
                $maKH = $stmtKH->fetchColumn();

                $maNV = $_SESSION['ma_nhanvien'] ?? null;
                $stmt = $pdo->prepare("UPDATE DONHANG SET TrangThaiDH = ?, MaNV = ? WHERE MaDonHang = ?");
                $stmt->execute([$new_status, $maNV, $order_id]);

                // Khôi phục số lượng hàng tồn kho nếu hủy đơn hàng, từ chối nhận hoặc trả hàng
                if (($new_status === 'cancelled' && $current_status !== 'cancelled') || ($new_status === 'returned' && $current_status !== 'returned') || ($new_status === 'refused' && $current_status !== 'refused')) {
                    $stmtItems = $pdo->prepare("SELECT MaSP, SoLuongSP, KichCo FROM CHITIETDONHANG WHERE MaDonHang = ?");
                    $stmtItems->execute([$order_id]);
                    $items = $stmtItems->fetchAll();

                    $stmtRestore = $pdo->prepare("UPDATE QUANLYSP SET SoLuong = SoLuong + ? WHERE MaSP = ? AND KichCo = ?");
                    foreach ($items as $item) {
                        if (!empty($item['MaSP'])) {
                            $stmtRestore->execute([$item['SoLuongSP'], $item['MaSP'], $item['KichCo']]);
                        }
                    }

                    // Khôi phục lượt sử dụng của mã giảm giá nếu có
                    $stmtVoucher = $pdo->prepare("SELECT MaGiamGia FROM DONHANG WHERE MaDonHang = ?");
                    $stmtVoucher->execute([$order_id]);
                    $voucherCode = $stmtVoucher->fetchColumn();
                    if (!empty($voucherCode)) {
                        $stmtVoucherRestore = $pdo->prepare("UPDATE MAGIAMGIA SET SoLuot = SoLuot + 1, DaDung = GREATEST(0, DaDung - 1) WHERE MaGiamGia = ?");
                        $stmtVoucherRestore->execute([$voucherCode]);
                    }
                }

                // Tự động nâng hạng thành viên nếu hoàn thành đơn hàng
                if ($new_status === 'completed' && $maKH) {
                    if (!class_exists('UserModel')) {
                        require_once './models/UserModel.php';
                    }
                    $userModel = new UserModel();
                    $userModel->updateMemberTier((int)$maKH);
                }

                // Gửi email nếu trạng thái chuyển sang 'confirmed' và phương thức thanh toán là 'sepay'
                if ($new_status === 'confirmed') {
                    $stmtPM = $pdo->prepare("SELECT PhuongThucTT FROM DONHANG WHERE MaDonHang = ?");
                    $stmtPM->execute([$order_id]);
                    $pm = strtolower($stmtPM->fetchColumn());

                    if ($pm === 'sepay') {
                        if (!function_exists('sendPaymentSuccessEmail')) {
                            require_once './helpers/mail_helper.php';
                        }
                        sendPaymentSuccessEmail($order_id);
                    }
                }

                header('Location: index.php?page=admin-orders&msg=updated');
                exit;
            } else {
                header('Location: index.php?page=admin-orders&msg=error_invalid_transition');
                exit;
            }
        }

        $orders = $pdo->query("SELECT dh.*, kh.HoTenKH AS customer_name FROM DONHANG dh LEFT JOIN KHACHHANG kh ON dh.MaKH = kh.MaKH ORDER BY dh.NgayTao DESC")->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-donhang.php';
        include './views/quantri/quantri-chantrang.php';
    }

    public function chiTietDonHang()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Chi tiết đơn hàng - VAB Admin';
        $trangHienTai = 'admin-orders';
        $nhungTuController = true;

        $pdo = getConnection();
        $id  = (int)($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
            $new_status     = $_POST['status'] ?? '';

            // Lấy trạng thái hiện tại của đơn hàng
            $stmtStatus = $pdo->prepare("SELECT TrangThaiDH FROM DONHANG WHERE MaDonHang = ?");
            $stmtStatus->execute([$id]);
            $current_status = $stmtStatus->fetchColumn();

            $allowedTransitions = [
                'pending'   => ['confirmed', 'shipping', 'delivered', 'completed', 'cancelled'],
                'confirmed' => ['shipping', 'delivered', 'completed', 'cancelled'],
                'shipping'  => ['delivered', 'completed', 'cancelled', 'refused'],
                'delivered' => ['completed', 'cancelled', 'returning', 'refused'],
                'completed' => ['cancelled', 'returning'],
                'cancelled' => [],
                'returning' => ['returned'],
                'returned'  => [],
                'refused'   => []
            ];

            $allowed = $allowedTransitions[$current_status] ?? [];
            if (in_array($new_status, $allowed)) {
                // Lấy MaKH của đơn hàng trước khi cập nhật
                $stmtKH = $pdo->prepare("SELECT MaKH FROM DONHANG WHERE MaDonHang = ?");
                $stmtKH->execute([$id]);
                $maKH = $stmtKH->fetchColumn();

                $maNV = $_SESSION['ma_nhanvien'] ?? null;
                $stmt = $pdo->prepare("UPDATE DONHANG SET TrangThaiDH = ?, MaNV = ? WHERE MaDonHang = ?");
                $stmt->execute([$new_status, $maNV, $id]);

                // Khôi phục số lượng hàng tồn kho nếu hủy đơn hàng, từ chối nhận hoặc trả hàng
                if (($new_status === 'cancelled' && $current_status !== 'cancelled') || ($new_status === 'returned' && $current_status !== 'returned') || ($new_status === 'refused' && $current_status !== 'refused')) {
                    $stmtItems = $pdo->prepare("SELECT MaSP, SoLuongSP, KichCo FROM CHITIETDONHANG WHERE MaDonHang = ?");
                    $stmtItems->execute([$id]);
                    $items = $stmtItems->fetchAll();

                    $stmtRestore = $pdo->prepare("UPDATE QUANLYSP SET SoLuong = SoLuong + ? WHERE MaSP = ? AND KichCo = ?");
                    foreach ($items as $item) {
                        if (!empty($item['MaSP'])) {
                            $stmtRestore->execute([$item['SoLuongSP'], $item['MaSP'], $item['KichCo']]);
                        }
                    }

                    // Khôi phục lượt sử dụng của mã giảm giá nếu có
                    $stmtVoucher = $pdo->prepare("SELECT MaGiamGia FROM DONHANG WHERE MaDonHang = ?");
                    $stmtVoucher->execute([$id]);
                    $voucherCode = $stmtVoucher->fetchColumn();
                    if (!empty($voucherCode)) {
                        $stmtVoucherRestore = $pdo->prepare("UPDATE MAGIAMGIA SET SoLuot = SoLuot + 1, DaDung = GREATEST(0, DaDung - 1) WHERE MaGiamGia = ?");
                        $stmtVoucherRestore->execute([$voucherCode]);
                    }
                }

                // Tự động nâng hạng thành viên nếu hoàn thành đơn hàng
                if ($new_status === 'completed' && $maKH) {
                    if (!class_exists('UserModel')) {
                        require_once './models/UserModel.php';
                    }
                    $userModel = new UserModel();
                    $userModel->updateMemberTier((int)$maKH);
                }

                // Gửi email nếu trạng thái chuyển sang 'confirmed' và phương thức thanh toán là 'sepay'
                if ($new_status === 'confirmed') {
                    $stmtPM = $pdo->prepare("SELECT PhuongThucTT FROM DONHANG WHERE MaDonHang = ?");
                    $stmtPM->execute([$id]);
                    $pm = strtolower($stmtPM->fetchColumn());

                    if ($pm === 'sepay') {
                        if (!function_exists('sendPaymentSuccessEmail')) {
                            require_once './helpers/mail_helper.php';
                        }
                        sendPaymentSuccessEmail($id);
                    }
                }

                header('Location: index.php?page=admin-order-detail&id=' . $id . '&msg=updated');
                exit;
            } else {
                header('Location: index.php?page=admin-order-detail&id=' . $id . '&msg=error_invalid_transition');
                exit;
            }
        }

        $stmt = $pdo->prepare("SELECT dh.*, kh.HoTenKH AS customer_name, nv.HoTenNV AS NguoiXuLy FROM DONHANG dh LEFT JOIN KHACHHANG kh ON dh.MaKH = kh.MaKH LEFT JOIN NHANVIEN nv ON dh.MaNV = nv.MaNV WHERE dh.MaDonHang = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();

        if (!$order) {
            header('Location: index.php?page=admin-orders');
            exit;
        }

        $stmt = $pdo->prepare("SELECT ct.*, sp.AnhChinh as AnhChinh FROM CHITIETDONHANG ct LEFT JOIN SANPHAM sp ON ct.MaSP = sp.MaSP WHERE ct.MaDonHang = ?");
        $stmt->execute([$id]);
        $items = $stmt->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-chitietdonhang.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ REVENUE ============
    public function doanhThu()
    {
        $this->checkRole(['admin', 'manager']);
        $tieuDeTrang   = 'Quản lý doanh thu - VAB Admin';
        $trangHienTai = 'admin-revenue';
        $nhungTuController = true;

        $pdo = getConnection();

        // === KPI CARDS ===
        // Doanh thu hôm nay
        $stmt = $pdo->query("SELECT COALESCE(SUM(TongTien),0) FROM DONHANG WHERE TrangThaiDH='completed' AND DATE(NgayTao)=CURDATE()");
        $revenueToday = (float)$stmt->fetchColumn();

        // Doanh thu tháng này
        $stmt = $pdo->query("SELECT COALESCE(SUM(TongTien),0) FROM DONHANG WHERE TrangThaiDH='completed' AND YEAR(NgayTao)=YEAR(NOW()) AND MONTH(NgayTao)=MONTH(NOW())");
        $revenueThisMonth = (float)$stmt->fetchColumn();

        // Doanh thu năm nay
        $stmt = $pdo->query("SELECT COALESCE(SUM(TongTien),0) FROM DONHANG WHERE TrangThaiDH='completed' AND YEAR(NgayTao)=YEAR(NOW())");
        $revenueThisYear = (float)$stmt->fetchColumn();

        // Sản phẩm đã bán tháng này
        $stmt = $pdo->query("SELECT COALESCE(SUM(ct.SoLuongSP),0) FROM CHITIETDONHANG ct JOIN DONHANG dh ON ct.MaDonHang=dh.MaDonHang WHERE dh.TrangThaiDH='completed' AND YEAR(dh.NgayTao)=YEAR(NOW()) AND MONTH(dh.NgayTao)=MONTH(NOW())");
        $productsSoldThisMonth = (int)$stmt->fetchColumn();

        // So sánh tháng trước
        $stmt = $pdo->query("SELECT COALESCE(SUM(TongTien),0) FROM DONHANG WHERE TrangThaiDH='completed' AND YEAR(NgayTao)=YEAR(DATE_SUB(NOW(),INTERVAL 1 MONTH)) AND MONTH(NgayTao)=MONTH(DATE_SUB(NOW(),INTERVAL 1 MONTH))");
        $revenueLastMonth = (float)$stmt->fetchColumn();
        $monthGrowth = $revenueLastMonth > 0 ? round(($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth * 100, 1) : 0;

        // === DỮ LIỆU BẢNG & BIỂU ĐỒ ===
        // Theo ngày (30 ngày gần nhất)
        $stmt = $pdo->query("
            SELECT DATE(NgayTao) AS day, COUNT(*) AS order_count, COALESCE(SUM(TongTien),0) AS revenue
            FROM DONHANG WHERE TrangThaiDH='completed'
            GROUP BY DATE(NgayTao) ORDER BY day DESC
        ");
        $dbDailyRevenue = $stmt->fetchAll();
        $dailyMap = [];
        foreach ($dbDailyRevenue as $r) {
            $dailyMap[$r['day']] = $r;
        }
        $dailyRevenue = [];
        for ($i = 0; $i < 7; $i++) {
            $dateStr = date('Y-m-d', strtotime("-$i days"));
            if (isset($dailyMap[$dateStr])) {
                $dailyRevenue[] = $dailyMap[$dateStr];
            } else {
                $dailyRevenue[] = [
                    'day' => $dateStr,
                    'order_count' => 0,
                    'revenue' => 0.0
                ];
            }
        }

        // Theo tháng (12 tháng gần nhất)
        $stmt = $pdo->query("
            SELECT DATE_FORMAT(NgayTao,'%Y-%m') AS month, COUNT(*) AS order_count, COALESCE(SUM(TongTien),0) AS revenue
            FROM DONHANG WHERE TrangThaiDH='completed'
            GROUP BY DATE_FORMAT(NgayTao,'%Y-%m') ORDER BY month DESC
        ");
        $dbMonthlyRevenue = $stmt->fetchAll();
        $monthlyMap = [];
        foreach ($dbMonthlyRevenue as $r) {
            $monthlyMap[$r['month']] = $r;
        }
        $monthlyRevenue = [];
        for ($i = 0; $i < 4; $i++) {
            $monthStr = date('Y-m', strtotime("-$i months"));
            if (isset($monthlyMap[$monthStr])) {
                $monthlyRevenue[] = $monthlyMap[$monthStr];
            } else {
                $monthlyRevenue[] = [
                    'month' => $monthStr,
                    'order_count' => 0,
                    'revenue' => 0.0
                ];
            }
        }

        // Theo năm (5 năm gần nhất)
        $stmt = $pdo->query("
            SELECT YEAR(NgayTao) AS year, COUNT(*) AS order_count, COALESCE(SUM(TongTien),0) AS revenue
            FROM DONHANG WHERE TrangThaiDH='completed'
            GROUP BY YEAR(NgayTao) ORDER BY year DESC
        ");
        $dbYearlyRevenue = $stmt->fetchAll();
        $yearlyMap = [];
        foreach ($dbYearlyRevenue as $r) {
            $yearlyMap[$r['year']] = $r;
        }
        $yearlyRevenue = [];
        for ($i = 0; $i < 4; $i++) {
            $yearVal = (int)date('Y') - $i;
            if (isset($yearlyMap[$yearVal])) {
                $yearlyRevenue[] = $yearlyMap[$yearVal];
            } else {
                $yearlyRevenue[] = [
                    'year' => $yearVal,
                    'order_count' => 0,
                    'revenue' => 0.0
                ];
            }
        }

        // === DỮ LIỆU JSON CHO CHART.JS ===
        // Chart ngày: đảo ngược để hiển thị tăng dần
        $dailyReversed = array_reverse($dailyRevenue);
        $chartDayLabels  = array_map(function ($r) {
            $w = date('w', strtotime($r['day']));
            $wd = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'][$w];
            return $wd . ' ' . date('d/m', strtotime($r['day']));
        }, $dailyReversed);
        $chartDayData    = array_map(fn($r) => (float)$r['revenue'], $dailyReversed);

        $monthlyReversed = array_reverse($monthlyRevenue);
        $chartMonthLabels = array_map(fn($r) => date('m/Y', strtotime($r['month'] . '-01')), $monthlyReversed);
        $chartMonthData   = array_map(fn($r) => (float)$r['revenue'], $monthlyReversed);

        $yearlyReversed = array_reverse($yearlyRevenue);
        $chartYearLabels  = array_map(fn($r) => 'Năm ' . $r['year'], $yearlyReversed);
        $chartYearData    = array_map(fn($r) => (float)$r['revenue'], $yearlyReversed);

        // === SẢN PHẨM BÁN CHẠY / BÁN ÍT ===
        // Lọc theo năm và tháng từ GET (mặc định: tháng + năm hiện tại)
        $filterYear  = isset($_GET['sp_year'])  && is_numeric($_GET['sp_year'])  ? (int)$_GET['sp_year']  : (int)date('Y');
        $filterMonth = isset($_GET['sp_month']) && is_numeric($_GET['sp_month']) ? (int)$_GET['sp_month'] : 0; // 0 = tất cả tháng trong năm

        if ($filterMonth > 0) {
            $whereProduct = "AND YEAR(dh.NgayTao)=$filterYear AND MONTH(dh.NgayTao)=$filterMonth";
        } else {
            $whereProduct = "AND YEAR(dh.NgayTao)=$filterYear";
        }

        $stmt = $pdo->query("
            SELECT ct.MaSP, ct.TenSP AS TenSP, SUM(ct.SoLuongSP) AS total_sold, SUM(ct.SoLuongSP * ct.DonGia) AS total_revenue
            FROM CHITIETDONHANG ct
            JOIN DONHANG dh ON ct.MaDonHang = dh.MaDonHang
            WHERE dh.TrangThaiDH = 'completed' $whereProduct
            GROUP BY ct.MaSP, ct.TenSP ORDER BY total_sold DESC LIMIT 10
        ");
        $bestSellers = $stmt->fetchAll();

        $stmt = $pdo->query("
            SELECT ct.MaSP, ct.TenSP AS TenSP, SUM(ct.SoLuongSP) AS total_sold, SUM(ct.SoLuongSP * ct.DonGia) AS total_revenue
            FROM CHITIETDONHANG ct
            JOIN DONHANG dh ON ct.MaDonHang = dh.MaDonHang
            WHERE dh.TrangThaiDH = 'completed' $whereProduct
            GROUP BY ct.MaSP, ct.TenSP ORDER BY total_sold ASC LIMIT 10
        ");
        $worstSellers = $stmt->fetchAll();

        // Danh sách năm có doanh thu
        $stmt = $pdo->query("SELECT DISTINCT YEAR(NgayTao) AS y FROM DONHANG WHERE TrangThaiDH='completed' ORDER BY y DESC");
        $availableYears = $stmt->fetchAll(PDO::FETCH_COLUMN);

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-doanhthu.php';
        include './views/quantri/quantri-chantrang.php';
    }


    // ============ SETTINGS ============
    public function cauhinh()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Cài đặt - VAB Admin';
        $trangHienTai = 'admin-settings';
        $nhungTuController = true;

        $pdo        = getConnection();
        $employeeId = $_SESSION['ma_nhanvien'];
        $error  = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update_info'])) {
                $name  = trim($_POST['name']  ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');

                if (empty($name) || empty($email)) {
                    $error = 'Họ tên và Email không được để trống!';
                } else {
                    try {
                        $stmt = $pdo->prepare("SELECT MaNV FROM NHANVIEN WHERE email = ? AND MaNV != ?");
                        $stmt->execute([$email, $employeeId]);
                        if ($stmt->fetch()) {
                            $error = 'Email này đã được sử dụng bởi một tài khoản khác!';
                        } else {
                            $stmt = $pdo->prepare("UPDATE NHANVIEN SET HoTenNV = ?, email = ?, SDT = ? WHERE MaNV = ?");
                            $stmt->execute([$name, $email, $phone, $employeeId]);
                            $_SESSION['ten_nhanvien'] = $name;
                            $success = 'Cập nhật thông tin tài khoản thành công!';
                        }
                    } catch (PDOException $e) {
                        $error = 'Lỗi hệ thống: ' . $e->getMessage();
                    }
                }
            } elseif (isset($_POST['change_password'])) {
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword     = $_POST['new_password']     ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                    $error = 'Vui lòng điền đầy đủ tất cả các trường mật khẩu!';
                } elseif ($newPassword !== $confirmPassword) {
                    $error = 'Mật khẩu mới và xác nhận mật khẩu không khớp!';
                } elseif (strlen($newPassword) < 6) {
                    $error = 'Mật khẩu mới phải có ít nhất 6 ký tự!';
                } else {
                    try {
                        $stmt = $pdo->prepare("SELECT MatKhauNV FROM NHANVIEN WHERE MaNV = ?");
                        $stmt->execute([$employeeId]);
                        $emp = $stmt->fetch();
                        if ($emp && password_verify($currentPassword, $emp['MatKhauNV'])) {
                            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("UPDATE NHANVIEN SET MatKhauNV = ? WHERE MaNV = ?");
                            $stmt->execute([$newHash, $employeeId]);
                            $success = 'Đổi mật khẩu thành công!';
                        } else {
                            $error = 'Mật khẩu hiện tại không chính xác!';
                        }
                    } catch (PDOException $e) {
                        $error = 'Lỗi hệ thống: ' . $e->getMessage();
                    }
                }
            }
        }

        $stmt = $pdo->prepare("SELECT * FROM NHANVIEN WHERE MaNV = ?");
        $stmt->execute([$employeeId]);
        $employee = $stmt->fetch();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-cauhinh.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ LOGIN ============
    public function dangNhap()
    {
        $tieuDeTrang   = 'Admin Login - VAB';
        $trangHienTai = 'admin-login';
        $nhungTuController = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = $_POST['email']    ?? '';
            $password = $_POST['password'] ?? '';

            $pdo  = getConnection();
            $stmt = $pdo->prepare("SELECT * FROM NHANVIEN WHERE email = ?");
            $stmt->execute([$email]);
            $employee = $stmt->fetch();

            if ($employee && password_verify($password, $employee['MatKhauNV'])) {
                if ($employee['TrangThai'] === 'locked') {
                    $error = 'Tài khoản của bạn đã bị khóa!';
                } else {
                    $_SESSION['ma_nhanvien']   = $employee['MaNV'];
                    $_SESSION['ten_nhanvien'] = $employee['HoTenNV'];
                    $_SESSION['vaitro_nhanvien'] = $employee['VaiTro'];
                    header('Location: index.php?page=admin-dashboard');
                    exit;
                }
            } else {
                $error = 'Email hoặc mật khẩu không đúng!';
            }
        }

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-dangnhap.php';
        include './views/quantri/quantri-chantrang.php';
    }

    public function dangXuat()
    {
        session_destroy();
        header('Location: index.php?page=admin-login');
        exit;
    }

    // ============ CATEGORIES (Danh muc) ============
    public function danhMuc()
    {
        $this->checkRole(['admin', 'manager']);
        $tieuDeTrang   = 'Quản lý danh mục - VAB Admin';
        $trangHienTai = 'admin-categories';
        $nhungTuController = true;

        $pdo = getConnection();
        $error = '';
        $success = '';

        $tab = $_GET['tab'] ?? 'category';
        if (!in_array($tab, ['category', 'brand', 'sport'])) {
            $tab = 'category';
        }

        // Xử lý thêm mới danh mục / thương hiệu / thể thao
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add_category'])) {
                $name = trim($_POST['name'] ?? '');
                $status = (int)($_POST['status'] ?? 1);

                if (!empty($name)) {
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->removeVietnameseAccents($name))));
                    try {
                        $stmt = $pdo->prepare("INSERT INTO DANHMUCSP (TenDM, DuongDan, TrangThai) VALUES (?, ?, ?)");
                        $stmt->execute([$name, $slug, $status]);
                        header('Location: index.php?page=admin-categories&tab=category&msg=added');
                        exit;
                    } catch (PDOException $e) {
                        $error = 'Lỗi hệ thống hoặc Tên danh mục đã tồn tại!';
                    }
                } else {
                    $error = 'Vui lòng nhập tên danh mục!';
                }
            }

            if (isset($_POST['add_brand'])) {
                $name = trim($_POST['name'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $status = (int)($_POST['status'] ?? 1);

                if (!empty($name)) {
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->removeVietnameseAccents($name))));
                    try {
                        $stmt = $pdo->prepare("INSERT INTO THUONGHIEU (TenThuongHieu, DuongDan, MoTa, TrangThai) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$name, $slug, $description, $status]);
                        header('Location: index.php?page=admin-categories&tab=brand&msg=added');
                        exit;
                    } catch (PDOException $e) {
                        $error = 'Lỗi hệ thống hoặc Tên thương hiệu đã tồn tại!';
                    }
                } else {
                    $error = 'Vui lòng nhập tên thương hiệu!';
                }
            }

            if (isset($_POST['add_sport'])) {
                $name = trim($_POST['name'] ?? '');
                $status = (int)($_POST['status'] ?? 1);

                if (!empty($name)) {
                    $slug = 'sport-' . strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->removeVietnameseAccents($name))));
                    try {
                        $stmt = $pdo->prepare("INSERT INTO THETHAO (TenSport, DuongDan, TrangThai) VALUES (?, ?, ?)");
                        $stmt->execute([$name, $slug, $status]);
                        header('Location: index.php?page=admin-categories&tab=sport&msg=added');
                        exit;
                    } catch (PDOException $e) {
                        $error = 'Lỗi hệ thống hoặc Tên bộ môn thể thao đã tồn tại!';
                    }
                } else {
                    $error = 'Vui lòng nhập tên bộ môn thể thao!';
                }
            }
        }

        // Xử lý bật tắt trạng thái nhanh
        if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
            $id = (int)$_GET['toggle_status'];
            if ($tab === 'category') {
                $stmt = $pdo->prepare("UPDATE DANHMUCSP SET TrangThai = IF(TrangThai=1, 0, 1) WHERE MaDanhMuc = ?");
                $stmt->execute([$id]);
            } elseif ($tab === 'brand') {
                $stmt = $pdo->prepare("UPDATE THUONGHIEU SET TrangThai = IF(TrangThai=1, 0, 1) WHERE MaThuongHieu = ?");
                $stmt->execute([$id]);
            } elseif ($tab === 'sport') {
                $stmt = $pdo->prepare("UPDATE THETHAO SET TrangThai = IF(TrangThai=1, 0, 1) WHERE MaTheThao = ?");
                $stmt->execute([$id]);
            }
            header('Location: index.php?page=admin-categories&tab=' . $tab . '&msg=updated');
            exit;
        }

        $categories = $pdo->query("SELECT * FROM DANHMUCSP ORDER BY MaDanhMuc DESC")->fetchAll();
        $brands = $pdo->query("SELECT * FROM THUONGHIEU ORDER BY MaThuongHieu DESC")->fetchAll();
        $sports = $pdo->query("SELECT * FROM THETHAO ORDER BY MaTheThao DESC")->fetchAll();
        $msg = $_GET['msg'] ?? '';

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-danhmuc.php';
        include './views/quantri/quantri-chantrang.php';
    }

    public function suaDanhMuc()
    {
        $this->checkRole(['admin', 'manager']);
        $tieuDeTrang   = 'Sửa danh mục - VAB Admin';
        $trangHienTai = 'admin-categories';
        $nhungTuController = true;

        $pdo = getConnection();
        $id  = (int)($_GET['id'] ?? 0);
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'] ?? 0;
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $status = (int)($_POST['status'] ?? 1);

            if (empty($slug)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->removeVietnameseAccents($name))));
            } else {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->removeVietnameseAccents($slug))));
            }

            if (!empty($name)) {
                try {
                    $stmt = $pdo->prepare("UPDATE DANHMUCSP SET TenDM = ?, DuongDan = ?, TrangThai = ? WHERE MaDanhMuc = ?");
                    $stmt->execute([$name, $slug, $status, $id]);
                    header('Location: index.php?page=admin-categories&msg=updated');
                    exit;
                } catch (PDOException $e) {
                    $error = 'Lỗi hệ thống hoặc Tên/Đường dẫn danh mục đã trùng lặp!';
                }
            } else {
                $error = 'Vui lòng nhập tên danh mục!';
            }
        }

        if (!$id) {
            header('Location: index.php?page=admin-categories');
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM DANHMUCSP WHERE MaDanhMuc = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();

        if (!$category) {
            header('Location: index.php?page=admin-categories');
            exit;
        }

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-suadanhmuc.php';
        include './views/quantri/quantri-chantrang.php';
    }

    public function xoaDanhMuc()
    {
        $this->checkRole(['admin', 'manager']);
        $pdo = getConnection();
        $id = (int)($_GET['id'] ?? 0);
        $tab = $_GET['tab'] ?? 'category';

        if ($id > 0) {
            if ($tab === 'category') {
                // Kiểm tra xem danh mục có sản phẩm nào không
                $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM SANPHAM WHERE MaDanhMuc = ?");
                $stmtCheck->execute([$id]);
                $hasProducts = (int)$stmtCheck->fetchColumn() > 0;

                if ($hasProducts) {
                    header('Location: index.php?page=admin-categories&tab=category&msg=error_has_products');
                    exit;
                } else {
                    $stmt = $pdo->prepare("DELETE FROM DANHMUCSP WHERE MaDanhMuc = ?");
                    $stmt->execute([$id]);
                    header('Location: index.php?page=admin-categories&tab=category&msg=deleted');
                    exit;
                }
            } elseif ($tab === 'brand') {
                // Kiểm tra xem thương hiệu có sản phẩm nào không
                $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM SANPHAM WHERE MaThuongHieu = ?");
                $stmtCheck->execute([$id]);
                $hasProducts = (int)$stmtCheck->fetchColumn() > 0;

                if ($hasProducts) {
                    header('Location: index.php?page=admin-categories&tab=brand&msg=error_has_products');
                    exit;
                } else {
                    $stmt = $pdo->prepare("DELETE FROM THUONGHIEU WHERE MaThuongHieu = ?");
                    $stmt->execute([$id]);
                    header('Location: index.php?page=admin-categories&tab=brand&msg=deleted');
                    exit;
                }
            } elseif ($tab === 'sport') {
                // Lấy tên bộ môn thể thao để kiểm tra trong sản phẩm
                $stmtGet = $pdo->prepare("SELECT TenSport FROM THETHAO WHERE MaTheThao = ?");
                $stmtGet->execute([$id]);
                $sportName = $stmtGet->fetchColumn();

                if ($sportName) {
                    // Kiểm tra xem bộ môn có sản phẩm nào không
                    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM SANPHAM WHERE TheThao = ?");
                    $stmtCheck->execute([$sportName]);
                    $hasProducts = (int)$stmtCheck->fetchColumn() > 0;

                    if ($hasProducts) {
                        header('Location: index.php?page=admin-categories&tab=sport&msg=error_has_products');
                        exit;
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM THETHAO WHERE MaTheThao = ?");
                        $stmt->execute([$id]);
                        header('Location: index.php?page=admin-categories&tab=sport&msg=deleted');
                        exit;
                    }
                }
            }
        }

        header('Location: index.php?page=admin-categories&tab=' . $tab);
        exit;
    }

    /** Helper chuyển đổi tiếng Việt không dấu */
    private function removeVietnameseAccents($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
        $str = preg_replace("/(đ)/", "d", $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|R|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        return $str;
    }
}
