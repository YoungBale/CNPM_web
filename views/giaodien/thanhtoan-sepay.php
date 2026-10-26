<?php
/**
 * =================================================================================
 * VIEW TRANG THANH TOÁN QR SEPAY (views/giaodien/thanhtoan-sepay.php)
 * =================================================================================
 * TRANG NÀY HIỂN THỊ MÃ QR ĐỘNG CHUYỂN KHOẢN NGÂN HÀNG VÀ CHỜ ĐỢI KHÁCH THANH TOÁN.
 * 
 * CƠ CHẾ SINH MÃ QR ĐỘNG (VietQR API):
 * - Hệ thống gọi API từ img.vietqr.io với các tham số:
 *   + vietqrBankId: Mã định danh ngân hàng chuẩn (Ví dụ: MB, VCB, ACB).
 *   + account_number: Số tài khoản ngân hàng của shop.
 *   + amount: Số tiền của đơn hàng (bắt buộc đúng từng đồng để SePay khớp tiền).
 *   + addInfo: Nội dung chuyển khoản (bắt buộc ghi đúng cú pháp VABORD[MaDonHang]).
 * 
 * CƠ CHẾ AJAX POLLING (Hỏi đáp vấn đáp):
 * - Giáo viên hỏi: "Làm sao trang web biết khi nào khách chuyển khoản thành công để chuyển trang?"
 *   + Trả lời: "Hệ thống sử dụng cơ chế Polling (thăm dò ý kiến). Javascript ở client (cuối file) 
 *     sẽ dùng hàm setInterval() để tự động gọi AJAX fetch ngầm đến API `kiemtraTrangthaiSepay` 
 *     mỗi 3 giây. Nếu database trả về trạng thái đã xác nhận (do SePay Webhook cập nhật trước đó),
 *     Javascript sẽ dừng vòng lặp, hiển thị thông báo thành công và chuyển hướng người dùng."
 */

if (!isset($nhungTuController)) {
    header('Location: index.php?page=home');
    exit;
}

// Nạp cấu hình tài khoản SePay của cửa hàng
$sepayConfig = require './config/sepay.php';
$memo = 'VABORD' . $order['MaDonHang']; // Tạo nội dung chuyển khoản bắt buộc dạng: VABORD102
$amount = $order['TongTien'];

// Ánh xạ tên ngân hàng viết tắt sang mã VietQR chuẩn quy định bởi Napas
$vietqrBankMap = [
    'mbbank'      => 'MB',
    'vietcombank' => 'VCB',
    'techcombank' => 'TCB',
    'vietinbank'  => 'CTG',
    'agribank'    => 'VBA',
    'sacombank'   => 'STB',
    'dongabank'   => 'DAB',
    'tpbank'      => 'TPB',
    'vpbank'      => 'VPB',
    'shb'         => 'SHB',
    'vib'         => 'VIB',
    'hdbank'      => 'HDB',
    'acb'         => 'ACB',
];
$sepayBankId = trim($sepayConfig['bank_id']);
$lowerBankId = strtolower($sepayBankId);
$vietqrBankId = isset($vietqrBankMap[$lowerBankId]) ? $vietqrBankMap[$lowerBankId] : $sepayBankId;

// Đường dẫn tạo ảnh QR động theo chuẩn VietQR thông qua thư viện mở VietQR.io
$qrUrl = "https://img.vietqr.io/image/" . $vietqrBankId . "-" . $sepayConfig['account_number'] . "-compact2.png"
    . "?amount=" . $amount
    . "&addInfo=" . urlencode($memo)
    . "&accountName=" . urlencode($sepayConfig['account_name']);
?>

<!-- ĐƯỜNG DẪN ĐIỀU HƯỚNG (BREADCRUMB) -->
<section class="breadcrumb-section" style="background: var(--color-bg-light); padding: 30px 0; border-bottom: 1px solid #eee;">
    <div class="container">
        <h1 class="fw-bold text-dark mb-1" style="font-size: 28px;">Thanh toán đơn hàng</h1>
        <nav>
            <ol class="breadcrumb mb-0" style="background: none; padding: 0;">
                <li class="breadcrumb-item"><a href="index.php?page=home" class="text-decoration-none" style="color: var(--color-primary);">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="index.php?page=orders" class="text-decoration-none" style="color: var(--color-primary);">Đơn hàng</a></li>
                <li class="breadcrumb-item active text-secondary" aria-current="page">Thanh toán chuyển khoản</li>
            </ol>
        </nav>
    </div>
</section>

<!-- KHU VỰC CHÍNH HIỂN THỊ THANH TOÁN -->
<section class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card border-0 shadow" style="border-radius: 20px; overflow: hidden; background: #ffffff;">
                    <div class="row g-0">
                        
                        <!-- CỘT TRÁI: THÔNG TIN VIETQR -->
                        <div class="col-md-6 p-4 p-lg-5 text-center d-flex flex-column align-items-center justify-content-center" style="border-right: 1px solid #eee; background: #fafafa;">
                            <h5 class="fw-bold text-dark mb-3">Quét mã VietQR để thanh toán</h5>
                            <p class="text-muted small mb-4">Sử dụng ứng dụng ngân hàng (Mobile Banking) quét mã QR bên dưới để chuyển khoản tự động.</p>
                            
                            <!-- Khung chứa ảnh mã QR -->
                            <div class="position-relative p-3 bg-white rounded shadow-sm border mb-4" style="max-width: 280px; width: 100%;">
                                <img src="<?= $qrUrl ?>" alt="VietQR Payment Code" class="img-fluid" style="border-radius: 10px; width: 100%; height: auto;">
                                <div class="qr-border-corner top-left"></div>
                                <div class="qr-border-corner top-right"></div>
                                <div class="qr-border-corner bottom-left"></div>
                                <div class="qr-border-corner bottom-right"></div>
                            </div>
                            
                            <!-- Hiệu ứng tải radar chờ thanh toán -->
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-2 text-warning">
                                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true" style="background-color: #f25c19;"></span>
                                <strong style="color: #f25c19; font-size: 14px;">Đang chờ chuyển khoản...</strong>
                            </div>
                            <span class="text-muted" style="font-size: 12px; max-width: 250px; display: block;">Hệ thống sẽ tự động xác nhận sau khi nhận được tiền. Vui lòng không đóng trang này.</span>
                        </div>
                        
                        <!-- CỘT PHẢI: CHI TIẾT CHUYỂN KHOẢN (Cho khách hàng nhập tay nếu không quét được QR) -->
                        <div class="col-md-6 p-4 p-lg-5 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                                    <h5 class="fw-bold text-dark mb-0">Chi tiết thanh toán</h5>
                                    <span class="badge bg-secondary rounded-pill px-3 py-1.5" style="font-size: 12px;">Đơn hàng #<?= $order['MaDonHang'] ?></span>
                                </div>
                                
                                <div class="payment-details-list">
                                    <!-- Ngân hàng -->
                                    <div class="detail-row mb-3 d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted d-block small uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Ngân hàng</span>
                                            <strong class="text-dark fs-6"><?= htmlspecialchars($sepayConfig['bank_id']) ?></strong>
                                        </div>
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 btn-copy" data-clipboard="<?= htmlspecialchars($sepayConfig['bank_id']) ?>"><i class="far fa-copy"></i></button>
                                    </div>
                                    
                                    <!-- Số tài khoản -->
                                    <div class="detail-row mb-3 d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted d-block small uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Số tài khoản</span>
                                            <strong class="text-dark fs-6"><?= htmlspecialchars($sepayConfig['account_number']) ?></strong>
                                        </div>
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 btn-copy" data-clipboard="<?= htmlspecialchars($sepayConfig['account_number']) ?>"><i class="far fa-copy"></i></button>
                                    </div>
                                    
                                    <!-- Tên chủ tài khoản -->
                                    <div class="detail-row mb-3 d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted d-block small uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Chủ tài khoản</span>
                                            <strong class="text-dark fs-6"><?= htmlspecialchars($sepayConfig['account_name']) ?></strong>
                                        </div>
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 btn-copy" data-clipboard="<?= htmlspecialchars($sepayConfig['account_name']) ?>"><i class="far fa-copy"></i></button>
                                    </div>
                                    
                                    <!-- Số tiền chuyển -->
                                    <div class="detail-row mb-3 d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted d-block small uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Số tiền</span>
                                            <strong class="text-danger fs-5"><?= number_format($amount, 0, ',', '.') ?>đ</strong>
                                        </div>
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 btn-copy" data-clipboard="<?= $amount ?>"><i class="far fa-copy"></i></button>
                                    </div>
                                    
                                    <!-- Nội dung chuyển khoản bắt buộc (Cú pháp SePay khớp đơn) -->
                                    <div class="detail-row mb-4 d-flex align-items-center justify-content-between p-3 bg-light rounded" style="border: 1px dashed #f25c19;">
                                        <div>
                                            <span class="text-warning d-block small uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px; color: #f25c19 !important;">Nội dung chuyển khoản bắt buộc</span>
                                            <strong class="text-dark fs-5" style="color: #f25c19 !important; font-family: monospace; letter-spacing: 1px;"><?= $memo ?></strong>
                                        </div>
                                        <button class="btn btn-sm btn-warning text-white rounded-pill px-3 btn-copy" style="background-color: #f25c19; border-color: #f25c19;" data-clipboard="<?= $memo ?>"><i class="far fa-copy me-1"></i>Copy</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 border-top pt-4 text-center text-md-start">
                                <a href="index.php?page=orders" class="btn btn-link text-decoration-none text-muted" onclick="return confirm('Hủy giao diện thanh toán để quay lại trang quản lý đơn hàng?')">
                                    <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách đơn hàng
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MÀN HÌNH BÁO THANH TOÁN THÀNH CÔNG (SUCCESS OVERLAY) -->
<div id="payment-success-overlay" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-white d-flex flex-column align-items-center justify-content-center" style="z-index: 9999; animation: fadeIn 0.4s ease;">
    <div class="text-center p-5 rounded border shadow" style="max-width: 480px; width: 90%;">
        <div class="mb-4 text-success" style="font-size: 64px;">
            <i class="fas fa-check-circle"></i>
        </div>
        <h3 class="fw-bold text-dark mb-2">Thanh toán thành công!</h3>
        <p class="text-muted mb-4">Cảm ơn bạn đã lựa chọn VAB SHOP. Đơn hàng của bạn đã được thanh toán và xác nhận tự động.</p>
        <div class="spinner-border text-warning" role="status" style="width: 24px; height: 24px; color: #f25c19 !important;"></div>
        <p class="text-secondary small mt-3 mb-0">Đang chuyển hướng về trang đơn hàng của bạn...</p>
    </div>
</div>

<style>
/* Bo viền 4 góc nhấp nháy cho khung QR */
.qr-border-corner {
    position: absolute;
    width: 20px;
    height: 20px;
    border: 3px solid #f25c19;
}
.qr-border-corner.top-left { top: 6px; left: 6px; border-right: none; border-bottom: none; }
.qr-border-corner.top-right { top: 6px; right: 6px; border-left: none; border-bottom: none; }
.qr-border-corner.bottom-left { bottom: 6px; left: 6px; border-right: none; border-top: none; }
.qr-border-corner.bottom-right { bottom: 6px; right: 6px; border-left: none; border-top: none; }

.detail-row {
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f3f5;
}
.detail-row:last-child {
    border-bottom: none;
}
.uppercase { text-transform: uppercase; }

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<!-- JAVASCRIPT: POLLING THĂM DÒ TRẠNG THÁI VÀ COPY NHANH -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. CƠ CHẾ POLLING (THĂM DÒ TỰ ĐỘNG)
    const orderId = <?= $order['MaDonHang'] ?>;
    // URL API kiểm tra trạng thái đơn hàng (Có thêm tham số thời gian t để tránh trình duyệt cache kết quả)
    const checkStatusUrl = `index.php?page=sepay-check-status&order_id=${orderId}&t=${Date.now()}`;
    const overlay = document.getElementById('payment-success-overlay');

    // Chạy ngầm hàm fetch dữ liệu mỗi 3 giây
    const intervalId = setInterval(function() {
        fetch(checkStatusUrl)
            .then(response => response.json())
            .then(data => {
                // Nếu CSDL ghi nhận trạng thái đơn hàng đã được Webhook cập nhật thành công
                if (data.status === 'completed' || data.status === 'confirmed' || data.status === 'delivered') {
                    clearInterval(intervalId); // Dừng ngay vòng lặp setInterval để tránh spam request
                    
                    // Hiển thị màn hình thông báo thanh toán thành công
                    overlay.classList.remove('d-none');
                    
                    // Chuyển hướng người dùng về trang danh sách đơn hàng sau 3 giây
                    setTimeout(function() {
                        window.location.href = 'index.php?page=orders';
                    }, 3000);
                }
            })
            .catch(error => console.error('Lỗi kiểm tra trạng thái thanh toán:', error));
    }, 3000); // 3000 ms = 3 giây

    // 2. LOGIC SAO CHÉP (COPY TO CLIPBOARD)
    const copyButtons = document.querySelectorAll('.btn-copy');
    copyButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const textToCopy = this.getAttribute('data-clipboard');
            // Sử dụng API Clipboard của trình duyệt hiện đại
            navigator.clipboard.writeText(textToCopy)
                .then(() => {
                    // Đổi nút thành dấu tích xanh báo copy thành công trong 1.5 giây
                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i>';
                    this.classList.remove('btn-outline-secondary', 'btn-warning');
                    this.classList.add('btn-success');
                    if (this.style.backgroundColor) this.style.backgroundColor = '#198754';
                    
                    setTimeout(() => {
                        this.innerHTML = originalHtml;
                        this.classList.remove('btn-success');
                        if (this.getAttribute('data-clipboard') === '<?= $memo ?>') {
                            this.classList.add('btn-warning');
                            this.style.backgroundColor = '#f25c19';
                        } else {
                            this.classList.add('btn-outline-secondary');
                        }
                    }, 1500);
                })
                .catch(err => {
                    console.error('Không thể copy:', err);
                });
        });
    });
});
</script>
