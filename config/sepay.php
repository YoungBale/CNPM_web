<?php
/**
 * SePay Config - Cấu hình tích hợp thanh toán VietQR & Webhook
 */
return [
    'bank_id'        => 'MBBank',             // Tên viết tắt của Ngân hàng (Vietcombank, MBBank, ACB, BIDV...)
    'account_number' => '8890359180209',       // Số tài khoản ngân hàng của bạn
    'account_name'   => 'TRUONG CHI VY',       // Tên chủ tài khoản (viết hoa không dấu)
    'webhook_secret' => 'whsec_okeKGSFNrnbKLicxG0Hv2yzzmlPLZFeB', // Khóa bảo mật Webhook (để kiểm tra chữ ký HMAC-SHA256 từ SePay)
];
