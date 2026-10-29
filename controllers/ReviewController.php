<?php
/**
 * =================================================================================
 * CONTROLLER QUẢN LÝ ĐÁNH GIÁ (ReviewController.php)
 * =================================================================================
 * Xử lý đánh giá sản phẩm từ khách hàng và kiểm duyệt trong trang quản trị.
 */
class ReviewController {

    /**
     * Khách hàng: Gửi đánh giá sản phẩm
     */
    public function luu() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['ma_nguoidung'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $userId = (int)$_SESSION['ma_nguoidung'];
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

        if ($productId <= 0) {
            header('Location: index.php?page=products');
            exit;
        }

        require './models/ReviewModel.php';
        $reviewModel = new ReviewModel();

        // 2. Kiểm tra điều kiện: Người dùng phải mua sản phẩm đó và đơn hàng đã giao thành công
        if (!$reviewModel->canUserReview($userId, $productId)) {
            $_SESSION['review_error'] = 'Bạn cần mua sản phẩm này và đơn hàng đã giao thành công mới có thể đánh giá.';
            header('Location: index.php?page=product-detail&id=' . $productId);
            exit;
        }

        // 3. Kiểm tra điều kiện: Mỗi người dùng chỉ được đánh giá sản phẩm đó một lần
        if ($reviewModel->hasUserReviewed($userId, $productId)) {
            $_SESSION['review_error'] = 'Bạn đã đánh giá sản phẩm này.';
            header('Location: index.php?page=product-detail&id=' . $productId);
            exit;
        }

        // 4. Kiểm tra dữ liệu đầu vào hợp lệ
        if ($rating < 1 || $rating > 5) {
            $_SESSION['review_error'] = 'Điểm đánh giá sao phải từ 1 đến 5.';
            header('Location: index.php?page=product-detail&id=' . $productId);
            exit;
        }

        if (empty($comment)) {
            $_SESSION['review_error'] = 'Nội dung bình luận không được bỏ trống.';
            header('Location: index.php?page=product-detail&id=' . $productId);
            exit;
        }

        // 5. Lưu đánh giá mới vào DB dưới trạng thái hiển thị luôn (approved)
        $success = $reviewModel->createReview($productId, $userId, $rating, $comment);

        if ($success) {
            $_SESSION['review_success'] = 'Đánh giá của bạn đã được gửi thành công!';
        } else {
            $_SESSION['review_error'] = 'Đã có lỗi xảy ra trong quá trình lưu đánh giá của bạn.';
        }
        $redirectTo = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'index.php?page=product-detail&id=' . $productId;
        header('Location: ' . $redirectTo);
        exit;
    }

    private function checkRole($allowedRoles) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['ma_nhanvien'])) {
            header('Location: index.php?page=admin-login');
            exit;
        }
        $vaiTro = strtolower($_SESSION['vaitro_nhanvien'] ?? 'staff');
        if (!in_array($vaiTro, $allowedRoles)) {
            $_SESSION['admin_error'] = 'Bạn không có quyền thực hiện chức năng này!';
            header('Location: index.php?page=admin-dashboard');
            exit;
        }
    }

    /**
     * Admin: Hiển thị danh sách tất cả các đánh giá
     */
    public function quantriDanhSach() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Kiểm tra quyền Admin
        if (!isset($_SESSION['ma_nhanvien'])) {
            header('Location: index.php?page=admin-login');
            exit;
        }

        require './models/ReviewModel.php';
        $reviewModel = new ReviewModel();
        $reviews = $reviewModel->getAllReviews();

        $tieuDeTrang = 'Quản lý đánh giá - VAB Admin';
        $trangHienTai = 'admin-reviews';
        $nhungTuController = true;

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-danhgia.php';
        include './views/quantri/quantri-chantrang.php';
    }

    /**
     * Admin: Phê duyệt đánh giá (approved)
     */
    public function duyet() {
        $this->checkRole(['admin', 'manager']);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            require './models/ReviewModel.php';
            $reviewModel = new ReviewModel();
            $maNV = $_SESSION['ma_nhanvien'] ?? null;
            $reviewModel->updateReviewStatus($id, 'approved', $maNV);
        }

        header('Location: index.php?page=admin-reviews&msg=approved');
        exit;
    }

    /**
     * Admin: Từ chối đánh giá (rejected)
     */
    public function tuChoi() {
        $this->checkRole(['admin', 'manager']);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            require './models/ReviewModel.php';
            $reviewModel = new ReviewModel();
            $maNV = $_SESSION['ma_nhanvien'] ?? null;
            $reviewModel->updateReviewStatus($id, 'rejected', $maNV);
        }

        header('Location: index.php?page=admin-reviews&msg=rejected');
        exit;
    }

    /**
     * Admin: Xóa đánh giá
     */
    public function xoa() {
        $this->checkRole(['admin', 'manager']);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            require './models/ReviewModel.php';
            $reviewModel = new ReviewModel();
            $reviewModel->deleteReview($id);
        }

        header('Location: index.php?page=admin-reviews&msg=deleted');
        exit;
    }
}
