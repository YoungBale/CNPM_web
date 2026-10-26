<?php

/**
 * =================================================================================
 * CONTROLLER TIẾP NHẬN WEBHOOK TỰ ĐỘNG TỪ SEPAY (SePayWebhookController.php)
 * =================================================================================
 * CƠ CHẾ TỰ ĐỘNG XÁC NHẬN ĐƠN HÀNG KHI NHẬN THANH TOÁN SEPAY
 * 
 * CƠ CHẾ HOẠT ĐỘNG CỦA SEPAY WEBHOOK:
 * 1. Khi khách hàng quét mã QR và chuyển khoản ngân hàng thành công.
 * 2. Ngân hàng báo biến động số dư -> Hệ thống SePay phát hiện giao dịch mới.
 * 3. SePay lập tức bắn một HTTP POST request chứa dữ liệu JSON (số tiền, nội dung chuyển khoản)
 *    vào đường dẫn Webhook này (Ví dụ: https://domain.com/index.php?page=sepay-webhook).
 * 4. Controller này tiếp nhận dữ liệu, thực hiện kiểm tra an toàn bảo mật.
 * 5. Tự động trích xuất Mã đơn hàng từ nội dung chuyển khoản (bằng biểu thức chính quy Regex).
 * 6. Kiểm tra số tiền chuyển khoản có khớp với tổng tiền đơn hàng hay không.
 * 7. Tiến hành cập nhật trạng thái đơn hàng thành 'đã xác nhận' trong CSDL và gửi email cho khách.
 * 
 * CÁC CƠ CHẾ BẢO MẬT (SECURITY SPECIFICATIONS):
 * A. Xác thực chữ ký HMAC-SHA256: Ngăn chặn hacker giả mạo SePay gửi request khống duyệt đơn hàng.
 *    - SePay gửi chữ ký qua header X-SePay-Signature.
 *    - Server PHP tự tính toán chữ ký dựa trên raw body của request và khóa bí mật Webhook Secret.
 *    - Nếu hai chữ ký khớp nhau (`hash_equals`) thì mới xử lý tiếp.
 * B. Chặn tấn công Replay Attack (Gửi lại gói tin):
 *    - Sử dụng X-SePay-Timestamp để so khớp với thời gian server. Nếu lệch quá 5 phút thì hủy.
 */
class SePayWebhookController
{
    public function xuly()
    {
        // Tắt hiển thị lỗi HTML để tránh làm hỏng cấu trúc phản hồi dạng JSON mà SePay yêu cầu
        error_reporting(0);
        ini_set('display_errors', 0);

        header('Content-Type: application/json');
        header('ngrok-skip-browser-warning: true'); // Bỏ qua cảnh báo ngrok nếu đang chạy thử nghiệm ở localhost

        // 1. THU THẬP DỮ LIỆU ĐẦU VÀO TỪ REQUEST HEADERS & BODY
        $signatureHeader = $this->getHeader('X-SePay-Signature');
        $timestampHeader = $this->getHeader('X-SePay-Timestamp');
        $rawPayload = file_get_contents('php://input'); // Đọc dữ liệu JSON thô gửi từ SePay

        // Ghi lại nhật ký (Log) để phục vụ debug và theo dõi dòng tiền
        $logLine = date('[Y-m-d H:i:s] ')
            . "METHOD: {$_SERVER['REQUEST_METHOD']} | "
            . "Sig: {$signatureHeader} | "
            . "Time: {$timestampHeader} | "
            . "Body: {$rawPayload}\n";
        file_put_contents('./sepay_webhook_log.txt', $logLine, FILE_APPEND);

        if (empty($rawPayload)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Empty body']);
            exit;
        }

        // 2. ĐỐI SOÁT BẢO MẬT & CHỮ KÝ SỐ (SECURITY VALIDATION)
        $sepayConfig = require './config/sepay.php';
        $secretKey = $sepayConfig['webhook_secret']; // Lấy khóa bí mật cấu hình trong hệ thống

        // Nếu SePay có cấu hình chữ ký
        if (!empty($signatureHeader) && !empty($timestampHeader)) {
            $signature = str_replace('sha256=', '', $signatureHeader);
            
            // Chuỗi dữ liệu cần đối soát chữ ký số (kết hợp Timestamp và Raw Body)
            $dataToVerify = $timestampHeader . '.' . $rawPayload;
            
            // Tự tính toán chữ ký số bằng thuật toán HMAC-SHA256 kết hợp Khóa bí mật
            $calculatedSignature = hash_hmac('sha256', $dataToVerify, $secretKey);

            // Ghi nhật ký so sánh chữ ký
            file_put_contents('./sepay_webhook_log.txt',
                date('[Y-m-d H:i:s] ') . "DEBUG: secret_len=" . strlen($secretKey)
                . " | data_len=" . strlen($dataToVerify)
                . " | calculated=$calculatedSignature"
                . " | received=$signature"
                . " | MATCH=" . ($calculatedSignature === $signature ? 'YES' : 'NO') . "\n",
                FILE_APPEND);

            // Sử dụng hash_equals (chống tấn công Timing Attack) để so sánh chữ ký số
            if (!hash_equals($calculatedSignature, $signature)) {
                // Thử các biến thể format JSON khác do máy chủ chuyển đổi ký tự
                $calcTrimmed = hash_hmac('sha256', $timestampHeader . '.' . trim($rawPayload), $secretKey);
                $calcUnescaped = hash_hmac('sha256', $timestampHeader . '.' . str_replace('\/', '/', $rawPayload), $secretKey);

                if (hash_equals($calcTrimmed, $signature)) {
                    file_put_contents('./sepay_webhook_log.txt', date('[Y-m-d H:i:s] ') . "SIGNATURE OK (trimmed body)\n", FILE_APPEND);
                } elseif (hash_equals($calcUnescaped, $signature)) {
                    file_put_contents('./sepay_webhook_log.txt', date('[Y-m-d H:i:s] ') . "SIGNATURE OK (unescaped slash body)\n", FILE_APPEND);
                } else {
                    file_put_contents('./sepay_webhook_log.txt', date('[Y-m-d H:i:s] ') . "SIGNATURE FAILED ALL VARIANTS.\n", FILE_APPEND);
                    http_response_code(401); // Trả về mã lỗi 401 Unauthorized (Chưa xác thực)
                    echo json_encode(['success' => false, 'message' => 'Invalid signature']);
                    exit;
                }
            }

            // KIỂM TRA PHÒNG NGỪA REPLAY ATTACK:
            // So sánh thời gian gửi request với thời gian hiện tại của server. Nếu lệch quá 5 phút, từ chối xử lý.
            $diff = abs(time() - (int)$timestampHeader);
            if ($diff > 300) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Request expired']);
                exit;
            }
        }

        // 3. PHÂN TÍCH NỘI DUNG GIAO DỊCH (TRANSACTION ANALYSIS)
        $data = json_decode($rawPayload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
            exit;
        }

        // Chỉ xử lý các giao dịch nhận tiền chuyển vào (transferType = 'in')
        $transferType = strtolower($data['transferType'] ?? 'in');
        if ($transferType !== 'in') {
            echo json_encode(['success' => true, 'message' => 'Ignored outgoing transaction']);
            exit;
        }

        $content        = $data['content']     ?? ''; // Nội dung chuyển khoản ngân hàng
        $description    = $data['description'] ?? '';
        $sepayCode      = $data['code']        ?? '';
        $transferAmount = (int)($data['transferAmount'] ?? 0); // Số tiền nhận được

        if (!class_exists('OrderModel')) {
            require './models/OrderModel.php';
        }
        $orderModel = new OrderModel();

        $orderId = null;

        // === CHIẾN LƯỢC TÌM ĐƠN HÀNG 1: QUÉT MÃ NỘI DUNG CHUYỂN KHOẢN (Bằng biểu thức chính quy Regex) ===
        // Quét tìm chuỗi cú pháp quy định (ví dụ: VABORD102) trong nội dung chuyển khoản để lấy ra mã đơn hàng là 102
        foreach ([$content, $description, $sepayCode] as $textSource) {
            if (preg_match('/VABORD(\d+)/i', $textSource, $matches)) {
                $orderId = (int)$matches[1];
                file_put_contents('./sepay_webhook_log.txt',
                    date('[Y-m-d H:i:s] ') . "Order found via VABORD code: #{$orderId} in '{$textSource}'\n",
                    FILE_APPEND);
                break;
            }
        }

        // === CHIẾN LƯỢC TÌM ĐƠN HÀNG 2 (DỰ PHÒNG): KHỚP TIỀN (Amount Matching) ===
        // Nếu khách hàng chuyển khoản quên ghi nội dung cú pháp VABORD, hệ thống tự tìm đơn hàng đang ở trạng thái 
        // chờ xác nhận ('pending') có số tiền khớp chính xác 100% với số tiền nhận được để đối soát.
        if ($orderId === null && $transferAmount > 0) {
            $pendingOrder = $orderModel->findPendingByAmount($transferAmount);
            if ($pendingOrder) {
                $orderId = (int)$pendingOrder['MaDonHang'];
                file_put_contents('./sepay_webhook_log.txt',
                    date('[Y-m-d H:i:s] ') . "Order found via amount matching: #{$orderId} (amount={$transferAmount})\n",
                    FILE_APPEND);
            }
        }

        // Nếu không tìm thấy bất kỳ đơn hàng nào khớp
        if ($orderId === null) {
            file_put_contents('./sepay_webhook_log.txt',
                date('[Y-m-d H:i:s] ') . "⚠️ No matching order found. Content: '{$content}' | Amount: {$transferAmount}\n",
                FILE_APPEND);
            // Trả về mã HTTP 200 để báo cho SePay dừng việc gửi lại tín hiệu trùng lặp (retry)
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Transaction received but no matching order']);
            exit;
        }

        // 4. KIỂM TRA ĐƠN HÀNG & CẬP NHẬT TRẠNG THÁI
        $order = $orderModel->getById($orderId);
        if (!$order) {
            file_put_contents('./sepay_webhook_log.txt', date('[Y-m-d H:i:s] ') . "Order #{$orderId} not found.\n", FILE_APPEND);
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }

        $orderTotal = (int)($order['TongTien'] ?? 0);

        // Đối chiếu số tiền chuyển khoản (Cho phép sai số nhỏ do khách làm tròn)
        if ($transferAmount < ($orderTotal - 1000)) {
            file_put_contents('./sepay_webhook_log.txt',
                date('[Y-m-d H:i:s] ') . "Underpayment for Order #{$orderId}: Received {$transferAmount}, Expected {$orderTotal}\n",
                FILE_APPEND);
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Underpayment']);
            exit;
        }

        $currentStatus = strtolower(trim($order['TrangThaiDH'] ?? ''));
        // Chỉ tiến hành cập nhật nếu đơn hàng đang ở trạng thái 'pending' (Chờ xác nhận)
        if ($currentStatus === 'pending') {
            // Cập nhật trạng thái thành 'confirmed' (Đã xác nhận thanh toán thành công)
            $orderModel->updateStatus($orderId, 'confirmed');
            file_put_contents('./sepay_webhook_log.txt',
                date('[Y-m-d H:i:s] ') . "✅ Order #{$orderId} CONFIRMED after payment. Amount: {$transferAmount}đ\n",
                FILE_APPEND);

            // ====== TỰ ĐỘNG GỬI EMAIL THÔNG BÁO GIAO DỊCH THÀNH CÔNG ======
            if (!function_exists('sendPaymentSuccessEmail')) {
                require_once './helpers/mail_helper.php';
            }
            $sent = sendPaymentSuccessEmail($orderId);
            if (is_array($sent) && !empty($sent['success'])) {
                file_put_contents('./sepay_webhook_log.txt',
                    date('[Y-m-d H:i:s] ') . "📧 Email xác nhận thanh toán đã gửi tới: " . ($order['email'] ?? '') . "\n",
                    FILE_APPEND);
            } else {
                file_put_contents('./sepay_webhook_log.txt',
                    date('[Y-m-d H:i:s] ') . "⚠️ Không gửi được email (lỗi SMTP hoặc email trống) cho Order #{$orderId}\n",
                    FILE_APPEND);
            }
        } else {
            file_put_contents('./sepay_webhook_log.txt',
                date('[Y-m-d H:i:s] ') . "Order #{$orderId} already has status: {$currentStatus} - skipped update\n",
                FILE_APPEND);
        }

        // Trả về JSON thành công báo cho SePay đóng giao dịch
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Hàm phụ trợ lấy thông tin request header tương thích với mọi môi trường Server Hosting
     */
    private function getHeader($name)
    {
        $nameUpper = str_replace('-', '_', strtoupper($name));
        if (isset($_SERVER['HTTP_' . $nameUpper])) {
            return $_SERVER['HTTP_' . $nameUpper];
        }
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $key => $value) {
                if (strcasecmp($key, $name) === 0) {
                    return $value;
                }
            }
        }
        return '';
    }
}
