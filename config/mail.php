<?php

/**
 * SMTP Mail Configuration
 * Chỉ cấu hình Gmail SMTP tại đây, không hardcode ở nhiều nơi.
 * password là App Password (mật khẩu ứng dụng) 16 ký tự của Gmail.
 */
// chạy trên local 
$mailConfig = [
    'host'       => 'smtp.gmail.com',
    'port'       => 587,
    'encryption' => 'tls',
    'username'   => 'vabshop2026@gmail.com',
    'password'   => 'tavvkicubpnjkbca',
    'from_email' => 'vabshop2026@gmail.com',
    'from_name'  => 'VAB SHOP'
];
