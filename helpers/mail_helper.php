<?php

/**
 * Mail Helper - Gửi email qua SMTP bằng PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function sendMail($toEmail, $toName, $subject, $body)
{
    // Tự động phát hiện cơ chế nạp PHPMailer
    if (file_exists('./vendor/autoload.php')) {
        require './vendor/autoload.php';
    } else {
        // Nhúng thủ công nếu người dùng tải PHPMailer thủ công đặt trong helpers
        $exceptionPath = './helpers/PHPMailer/src/Exception.php';
        $phpmailerPath = './helpers/PHPMailer/src/PHPMailer.php';
        $smtpPath      = './helpers/PHPMailer/src/SMTP.php';

        if (file_exists($exceptionPath) && file_exists($phpmailerPath) && file_exists($smtpPath)) {
            require $exceptionPath;
            require $phpmailerPath;
            require $smtpPath;
        } else {
            return [
                'success' => false,
                'message' => 'Không tìm thấy thư viện PHPMailer. Hãy chạy "composer require phpmailer/phpmailer" hoặc tải PHPMailer đặt vào thư mục "helpers/PHPMailer/"'
            ];
        }
    }

    // Nạp cấu hình email
    require './config/mail.php';

    $mail = new PHPMailer(true);

    try {
        // Thiết lập cấu hình SMTP
        $mail->isSMTP();
        $mail->Host       = $mailConfig['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailConfig['username'];
        $mail->Password   = $mailConfig['password'];
        $mail->SMTPSecure = ($mailConfig['encryption'] === 'ssl') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $mailConfig['port'];
        $mail->CharSet    = 'UTF-8';

        // Bỏ qua lỗi xác thực SSL trên localhost nếu cần thiết
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            )
        );

        // Thiết lập thông tin người gửi & người nhận
        $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        $mail->addAddress($toEmail, $toName);

        // Tự động tìm và nhúng các ảnh cục bộ (logo, sản phẩm) thành CID attachments để hiển thị được trên mọi thiết bị
        preg_match_all('/<img[^>]+src="([^"]+)"/i', $body, $matches);
        if (!empty($matches[1])) {
            $imgIndex = 1;
            $processed = [];
            foreach ($matches[1] as $imgUrl) {
                if (isset($processed[$imgUrl])) continue;
                $processed[$imgUrl] = true;
                
                $assetsPos = strpos($imgUrl, 'assets/');
                if ($assetsPos !== false) {
                    $relativePath = substr($imgUrl, $assetsPos);
                    $localPath = './' . $relativePath;
                    if (file_exists($localPath)) {
                        $cid = 'img_cid_' . $imgIndex++;
                        try {
                            $mail->addEmbeddedImage($localPath, $cid);
                            $body = str_replace($imgUrl, 'cid:' . $cid, $body);
                        } catch (Exception $e) {
                            // Bỏ qua nếu lỗi nhúng ảnh
                        }
                    }
                }
            }
        }

        // Thiết lập nội dung Email
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();

        // Ghi log gửi mail thành công
        file_put_contents('./mail_errors.log', date('[Y-m-d H:i:s] ') . "SUCCESS: Gửi email thành công tới {$toEmail} (Tiêu đề: {$subject})\n", FILE_APPEND);

        return [
            'success' => true,
            'message' => 'Gửi email thành công'
        ];
    } catch (Exception $e) {
        // Ghi log lỗi chi tiết
        $errLog = date('[Y-m-d H:i:s] ') . "FAILURE: Lỗi gửi mail tới {$toEmail}. ErrorInfo: " . $mail->ErrorInfo . " | Exception: " . $e->getMessage() . "\n";
        file_put_contents('./mail_errors.log', $errLog, FILE_APPEND);

        return [
            'success' => false,
            'message' => 'Lỗi gửi mail: ' . $mail->ErrorInfo
        ];
    }
}

/**
 * Gửi email thông báo thanh toán thành công cho đơn hàng SePay.
 */
function sendPaymentSuccessEmail($orderId)
{
    $pdo = getConnection();
    
    // 1. Lấy thông tin đơn hàng
    $stmt = $pdo->prepare("SELECT * FROM DONHANG WHERE MaDonHang = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) return false;
    
    // 2. Lấy thông tin chi tiết các sản phẩm
    $stmtItems = $pdo->prepare("
        SELECT ct.*, sp.AnhChinh as AnhChinh 
        FROM CHITIETDONHANG ct 
        LEFT JOIN SANPHAM sp ON ct.MaSP = sp.MaSP 
        WHERE ct.MaDonHang = ?
    ");
    $stmtItems->execute([$orderId]);
    $cartItems = $stmtItems->fetchAll();
    
    // 3. Tính toán base URL tuyệt đối cho hình ảnh
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if (substr($baseDir, -1) !== '/') {
        $baseDir .= '/';
    }
    $absoluteBaseUrl = $protocol . '://' . $host . $baseDir;

    $productItemsRows = '';
    foreach ($cartItems as $item) {
        $img = $item['AnhChinh'] ?? '';
        $imgUrl = '';
        if (!empty($img)) {
            if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
                $imgUrl = $img;
            } elseif (strpos($img, 'assets/') === 0) {
                $imgUrl = $absoluteBaseUrl . $img;
            } else {
                $imgUrl = $absoluteBaseUrl . 'assets/images/products/' . $img;
            }
        } else {
            $imgUrl = "https://placehold.co/80x80/0a1628/fff?text=SP+" . ($item['MaSP'] ?? 0);
        }
        
        $productItemsRows .= '
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 12px; margin-bottom: 16px; border-collapse: separate;">
            <tr>
                <td width="70" style="padding: 16px; vertical-align: middle;">
                    <img src="' . htmlspecialchars($imgUrl) . '" width="60" height="60" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #d1d5db;" alt="' . htmlspecialchars($item['TenSP']) . '">
                </td>
                <td style="padding: 16px 8px; vertical-align: middle;">
                    <div style="font-size: 15px; font-weight: 700; color: #1f2937; margin-bottom: 4px;">' . htmlspecialchars($item['TenSP']) . '</div>';
        if (!empty($item['KichCo'])) {
            $productItemsRows .= '
                    <div style="font-size: 13px; color: #6b7280;">Size: ' . htmlspecialchars($item['KichCo']) . '</div>';
        }
        $productItemsRows .= '
                </td>
                <td align="right" style="padding: 16px; vertical-align: middle; font-size: 15px; font-weight: 700; color: #1f2937; white-space: nowrap;">
                    ' . number_format($item['DonGia'], 0, ',', '.') . 'đ<br>
                    <small style="color: #6b7280; font-size: 13px; font-weight: 500;">x ' . $item['SoLuongSP'] . '</small>
                </td>
            </tr>
        </table>';
    }

    // 5. Định dạng ngày đặt hàng và các chi phí
    $orderDate = date('d/m/Y H:i', strtotime($order['NgayTao']));
    $discount = (int)($order['SoTienGiam'] ?? 0);
    $discountRowEmail = '';
    if ($discount > 0) {
        $discountRowEmail = '
                <tr>
                    <td style="padding-bottom: 6px; color: #6b7280;">Mã giảm giá (' . htmlspecialchars($order['MaGiamGia']) . ')</td>
                    <td align="right" style="color: #f25c19; font-weight: 600; padding-bottom: 6px;">-' . number_format($discount, 0, ',', '.') . 'đ</td>
                </tr>';
    }
    
    // Tính tổng phụ = tổng tiền + số tiền giảm
    $subtotal = (int)$order['TongTien'] + $discount;
    $subtotalFormatted = number_format($subtotal, 0, ',', '.');
    $totalFormatted = number_format($order['TongTien'], 0, ',', '.');
    $orderDetailUrl = $absoluteBaseUrl . 'index.php?page=order-detail&id=' . $orderId;
    $customerEmail = trim($order['email'] ?? '');
    $customerName = $order['HoTenKH'] ?? 'Khách hàng';

    if (empty($customerEmail)) return false;

    $logoUrl = $absoluteBaseUrl . 'assets/images/logo/logo1.png';
    $paymentMethodText = 'Chuyển khoản SePay';

    // 6. Mẫu HTML email thanh toán thành công (Light Theme matching website palette)
    $emailBody = '
<div style="background-color: #f3f4f6; padding: 30px 15px; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #1f2937;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 20px; overflow: hidden; border: 1px solid #e5e7eb; border-collapse: separate; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
        <tr>
            <td style="padding: 40px 30px 20px 30px; text-align: center;">
                <!-- Brand Header -->
                <div style="font-size: 28px; font-weight: 900; color: #f25c19; letter-spacing: -0.5px; margin-bottom: 4px;">VAB SHOP</div>
                <div style="font-size: 11px; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: 3px;">Thời Trang Thể Thao Cao Cấp</div>
                <div style="height: 1px; background-color: #e5e7eb; margin-top: 20px; margin-bottom: 10px;"></div>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 30px 30px 30px;">
                
                <!-- Welcome Title -->
                <div style="margin-bottom: 20px;">
                    <h2 style="margin: 0 0 8px 0; font-size: 22px; font-weight: 800; color: #111827; text-align: left;">Thanh toán thành công!</h2>
                    <p style="margin: 0; color: #4b5563; font-size: 15px; line-height: 1.5;">Chúng tôi đã nhận được thanh toán cho đơn hàng của bạn.</p>
                </div>

                <!-- Product List -->
                <div style="margin-bottom: 24px;">
                    ' . $productItemsRows . '
                </div>
                
                <!-- Order Metadata -->
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 24px; font-size: 14px; color: #4b5563; line-height: 2;">
                    <tr>
                        <td style="color: #6b7280;">ID đơn hàng</td>
                        <td align="right" style="font-weight: bold; color: #111827;">#' . $orderId . '</td>
                    </tr>
                    <tr>
                        <td style="color: #6b7280;">Ngày đặt hàng</td>
                        <td align="right" style="color: #111827;">' . $orderDate . '</td>
                    </tr>
                    <tr>
                        <td style="color: #6b7280;">Phương thức thanh toán</td>
                        <td align="right" style="color: #111827; font-weight: 600;">' . $paymentMethodText . '</td>
                    </tr>
                </table>

                <!-- Summary Box -->
                <div style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <h4 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; color: #111827;">Tổng quan đơn hàng</h4>
                    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; color: #4b5563; border-collapse: collapse; line-height: 1.8;">
                        <tr>
                            <td style="color: #6b7280; padding-bottom: 6px;">Tổng phụ</td>
                            <td align="right" style="color: #111827; font-weight: 600; padding-bottom: 6px;">' . $subtotalFormatted . 'đ</td>
                        </tr>
                        ' . $discountRowEmail . '
                        <tr>
                            <td style="color: #6b7280; padding-bottom: 6px;">Vận chuyển</td>
                            <td align="right" style="color: #10b981; font-weight: 600; padding-bottom: 6px;">Miễn phí</td>
                        </tr>
                        <tr>
                            <td style="padding-top: 12px; border-top: 1px solid #d1d5db; font-weight: 700; color: #111827; font-size: 15px;">Tổng cộng</td>
                            <td align="right" style="padding-top: 12px; border-top: 1px solid #d1d5db; font-weight: 800; color: #f25c19; font-size: 18px;">' . $totalFormatted . 'đ</td>
                        </tr>
                    </table>
                </div>

                <!-- Shipping Card -->
                <div style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 30px;">
                    <h4 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; color: #111827;">Thông tin giao hàng</h4>
                    <p style="margin: 0 0 4px 0; color: #111827; font-weight: 700; font-size: 14px;">' . htmlspecialchars($customerName) . '</p>
                    <p style="margin: 0 0 4px 0; color: #4b5563; font-size: 13px;">📞 ' . htmlspecialchars($order['SDT'] ?? '') . '</p>
                    <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.4;">📍 ' . htmlspecialchars($order['DiaChi'] ?? '') . '</p>
                </div>

                <!-- Action Button -->
                <div style="text-align: center; margin-bottom: 10px;">
                    <a href="' . htmlspecialchars($orderDetailUrl) . '" style="display: inline-block; background-color: #e25862; color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 50px; font-size: 14px; font-weight: 700; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(226, 88, 98, 0.25); transition: background-color 0.3s;">Xem chi tiết đơn hàng</a>
                </div>
                
            </td>
        </tr>
        <!-- Footer -->
        <tr>
            <td style="background-color: #f3f4f6; border-top: 1px solid #e5e7eb; padding: 25px 30px; text-align: center; color: #6b7280; font-size: 12px;">
                <p style="margin: 0 0 6px 0; font-weight: 700; color: #4b5563;">Cảm ơn bạn đã lựa chọn mua sắm tại VAB SHOP!</p>
                <p style="margin: 0 0 10px 0; color: #9ca3af; line-height: 1.5;">Email này được gửi tự động từ hệ thống cửa hàng VAB SHOP. Vui lòng không trả lời trực tiếp email này.</p>
                <p style="margin: 0; color: #9ca3af;">&copy; 2026 VAB SHOP. All rights reserved.</p>
            </td>
        </tr>
    </table>
</div>';

    $subject = "✅ Thanh toán thành công - Đơn hàng #" . $orderId . " | VAB SHOP";
    return sendMail($customerEmail, $customerName, $subject, $emailBody);
}

