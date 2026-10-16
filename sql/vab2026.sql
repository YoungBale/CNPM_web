-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2026 at 11:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vab2026`
--

-- --------------------------------------------------------

--
-- Table structure for table `CHITIETDONHANG`
--

CREATE TABLE `CHITIETDONHANG` (
  `MaCTDH` int(11) NOT NULL,
  `MaDonHang` int(11) NOT NULL,
  `MaSP` int(11) DEFAULT NULL,
  `KichCo` varchar(50) DEFAULT '',
  `TenSP` varchar(255) NOT NULL,
  `SoLuongSP` int(11) NOT NULL DEFAULT 1,
  `DonGia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `CHITIETDONHANG`
--

INSERT INTO `CHITIETDONHANG` (`MaCTDH`, `MaDonHang`, `MaSP`, `KichCo`, `TenSP`, `SoLuongSP`, `DonGia`) VALUES
(1, 1, NULL, 'S', 'Ao The Thao Nam Nike Dri-FIT', 1, 790000),
(2, 2, NULL, 'S', 'Ao The Thao Nam Nike Dri-FIT', 1, 790000),
(3, 3, NULL, '39', 'Giày Thể Thao Chạy Bộ Nam Nike Run Defy', 1, 1500000),
(4, 4, NULL, '39', 'Giày Thể Thao Chạy Bộ Nam Nike Run Defy', 1, 1500000),
(5, 5, NULL, '39', 'Giày Thể Thao Chạy Bộ Nam Nike Run Defy', 1, 1500000),
(6, 6, 14, '39', 'Giày Thể Thao Tập Luyện Nữ Nike W Nike Flex Train', 1, 500000),
(7, 7, 14, '39', 'Giày Thể Thao Tập Luyện Nữ Nike W Nike Flex Train', 1, 500000),
(8, 8, 15, '39', 'Giày Thể Thao Chạy Bộ Nữ Nike W Nike Vomero ', 1, 600000),
(9, 9, 19, 'L', 'áo', 1, 10000),
(10, 10, 16, 'S', 'Vớ Thể Thao Unisex Nike U Nk Nsw Everyday Essential Cr', 1, 2000),
(12, 12, 13, 'M', 'Product 1', 1, 600000),
(13, 13, 27, 'L', 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Trẻ Em Unisex Nike Jr Legend 10 Club Fg/Mgv', 1, 100000),
(14, 14, 16, 'S', 'Vớ Thể Thao Unisex Nike U Nk Nsw Everyday Essential Cr', 1, 2000),
(15, 15, 27, 'L', 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Trẻ Em Unisex Nike Jr Legend 10 Club Fg/Mgv', 1, 100000),
(16, 16, 16, 'S', 'Vớ Thể Thao Unisex Nike U Nk Nsw Everyday Essential Cr', 1, 2000),
(17, 17, 16, 'S', 'Vớ Thể Thao Unisex Nike U Nk Nsw Everyday Essential Cr', 1, 2000),
(18, 18, 16, 'L', 'Vớ Thể Thao Unisex Nike U Nk Nsw Everyday Essential Cr', 1, 2000),
(19, 19, 16, 'L', 'Vớ Thể Thao Unisex Nike U Nk Nsw Everyday Essential Cr', 1, 2000),
(20, 20, 28, 'L', 'Áo Tay Ngắn Thể Thao Tập Luyện Nam Nike As M Nk Df Tee Std Flex', 1, 180000),
(21, 21, 28, 'L', 'Áo Tay Ngắn Thể Thao Tập Luyện Nam Nike As M Nk Df Tee Std Flex', 1, 180000),
(22, 22, 15, '41', 'Giày Thể Thao Chạy Bộ Nữ Nike W Nike Vomero ', 5, 600000),
(23, 23, 27, 'L', 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Trẻ Em Unisex Nike Jr Legend 10 Club Fg/Mgv', 1, 100000),
(24, 24, 15, '40', 'Giày Thể Thao Chạy Bộ Nữ Nike W Nike Vomero ', 5, 600000),
(25, 25, 23, 'L', 'Quần Ngắn Thể Thao Bóng Đá Nam Nike Fcb M Nk Df Strk Short Kz 3R', 1, 150000),
(26, 26, 27, 'L', 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Trẻ Em Unisex Nike Jr Legend 10 Club Fg/Mgv', 1, 100000),
(27, 27, 27, 'L', 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Trẻ Em Unisex Nike Jr Legend 10 Club Fg/Mgv', 1, 100000),
(28, 28, 25, 'L', 'Áo ba lỗ thể thao Tập Luyện Nam Nike AS M NK DF TEE STD SLVLS FLEX', 1, 100000),
(29, 29, 22, 'L', 'Quần Ngắn Thể Thao Bóng Đá Nam Nike Cfc M Nk Df Strk Short Kz3R', 1, 200000),
(30, 30, 25, 'L', 'Áo ba lỗ thể thao Tập Luyện Nam Nike AS M NK DF TEE STD SLVLS FLEX', 1, 100000),
(31, 31, 16, 'L', 'Vớ Thể Thao Unisex Nike U Nk Nsw Everyday Essential Cr', 1, 2000),
(32, 32, 28, 'L', 'Áo Tay Ngắn Thể Thao Tập Luyện Nam Nike As M Nk Df Tee Std Flex', 1, 180000),
(33, 32, 19, 'L', 'áo', 1, 10000),
(34, 32, 24, 'L', 'Áo Tay Ngắn Thể Thao Bóng Đá Nam Nike Fcb M Nk Df Strk Ss Top K 3R', 1, 100000),
(35, 33, 27, 'L', 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Trẻ Em Unisex Nike Jr Legend 10 Club Fg/Mgv', 1, 100000),
(36, 34, 26, 'M', 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Nam Nike Zoom Vapor 16 Academy Fg/Mg', 1, 120000),
(37, 35, 21, 'L', 'Áo Tay Ngắn Thể Thao Bóng Đá Trẻ Em Unisex Nike Fcb Y Nk Df Jsy Ss Stad Hm', 1, 200000),
(38, 36, 26, 'M', 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Nam Nike Zoom Vapor 16 Academy Fg/Mg', 1, 120000),
(39, 37, 22, 'L', 'Quần Ngắn Thể Thao Bóng Đá Nam Nike Cfc M Nk Df Strk Short Kz3R', 1, 200000),
(40, 38, 27, 'L', 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Trẻ Em Unisex Nike Jr Legend 10 Club Fg/Mgv', 1, 100000),
(41, 39, 20, 'L', 'Quần Ngắn Thể Thao Nam Nike As M Nk Df Challenger 7Bf Sh', 1, 250000),
(42, 40, 21, 'L', 'Áo Tay Ngắn Thể Thao Bóng Đá Trẻ Em Unisex Nike Fcb Y Nk Df Jsy Ss Stad Hm', 1, 200000),
(43, 41, 16, 'L', 'Vớ Thể Thao Unisex Nike U Nk Nsw Everyday Essential Cr', 1, 2000),
(44, 42, 27, 'L', 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Trẻ Em Unisex Nike Jr Legend 10 Club Fg/Mgv', 1, 100000),
(45, 43, 16, 'L', 'Vớ Thể Thao Unisex Nike U Nk Nsw Everyday Essential Cr', 1, 2000),
(46, 44, 16, 'L', 'Vớ Thể Thao Unisex Nike U Nk Nsw Everyday Essential Cr', 1, 2000),
(47, 45, 23, 'L', 'Quần Ngắn Thể Thao Bóng Đá Nam Nike Fcb M Nk Df Strk Short Kz 3R', 1, 150000),
(48, 46, 26, 'M', 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Nam Nike Zoom Vapor 16 Academy Fg/Mg', 1, 120000),
(49, 47, 23, 'L', 'Quần Ngắn Thể Thao Bóng Đá Nam Nike Fcb M Nk Df Strk Short Kz 3R', 1, 150000),
(50, 48, 26, 'M', 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Nam Nike Zoom Vapor 16 Academy Fg/Mg', 1, 120000),
(51, 49, 28, 'L', 'Áo Tay Ngắn Thể Thao Tập Luyện Nam Nike As M Nk Df Tee Std Flex', 1, 180000),
(52, 50, 62, 'L', 'Áo T-Shirt Tập Gym Nam Adidas Màu Xanh Rêu Trơn', 1, 210000),
(53, 51, 19, 'L', 'áo', 1, 10000);

-- --------------------------------------------------------

--
-- Table structure for table `DANHGIASP`
--

CREATE TABLE `DANHGIASP` (
  `MaDG` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `MaKH` int(11) DEFAULT NULL,
  `TenKH` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT '',
  `SoSao` tinyint(4) NOT NULL DEFAULT 5,
  `NoiDung` text DEFAULT NULL,
  `TrangThai` enum('pending','approved','rejected') DEFAULT 'pending',
  `NgayDG` timestamp NOT NULL DEFAULT current_timestamp(),
  `MaNV` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `DANHGIASP`
--

INSERT INTO `DANHGIASP` (`MaDG`, `MaSP`, `MaKH`, `TenKH`, `email`, `SoSao`, `NoiDung`, `TrangThai`, `NgayDG`, `MaNV`) VALUES
(1, 22, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', 5, 'đẹp', 'approved', '2026-06-22 16:42:04', 1),
(2, 20, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', 5, 'đẹp lắm', 'approved', '2026-06-22 20:00:46', 1);

-- --------------------------------------------------------

--
-- Table structure for table `DANHMUCSP`
--

CREATE TABLE `DANHMUCSP` (
  `MaDanhMuc` int(11) NOT NULL,
  `TenDM` varchar(255) NOT NULL,
  `DuongDan` varchar(255) NOT NULL,
  `TrangThai` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `DANHMUCSP`
--

INSERT INTO `DANHMUCSP` (`MaDanhMuc`, `TenDM`, `DuongDan`, `TrangThai`) VALUES
(1, 'Ao', 'ao', 1),
(2, 'Quan', 'quan', 1),
(3, 'Giay', 'giay', 1),
(4, 'Phu kien', 'phu-kien', 1);

-- --------------------------------------------------------

--
-- Table structure for table `DONHANG`
--

CREATE TABLE `DONHANG` (
  `MaDonHang` int(11) NOT NULL,
  `MaKH` int(11) DEFAULT NULL,
  `HoTenKH` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `SDT` varchar(20) NOT NULL,
  `DiaChi` text NOT NULL,
  `PhuongThucTT` varchar(100) DEFAULT 'COD',
  `GhiChu` text DEFAULT NULL,
  `TongTien` int(11) NOT NULL,
  `TrangThaiDH` enum('pending','confirmed','shipping','delivered','completed','cancelled','returning','returned','refused') DEFAULT 'pending',
  `MaGiamGia` varchar(50) DEFAULT NULL,
  `SoTienGiam` int(11) DEFAULT 0,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
  `MaNV` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `DONHANG`
--

INSERT INTO `DONHANG` (`MaDonHang`, `MaKH`, `HoTenKH`, `email`, `SDT`, `DiaChi`, `PhuongThucTT`, `GhiChu`, `TongTien`, `TrangThaiDH`, `MaGiamGia`, `SoTienGiam`, `NgayTao`, `MaNV`) VALUES
(1, 2, 'Test Customer', 'admin@vab.com', '0987654321', '123 Test Street, Hanoi', 'sepay', 'Deliver during office hours', 790000, 'confirmed', NULL, 0, '2026-06-15 19:53:09', NULL),
(2, 2, 'Test Customer', 'admin@vab.com', '0987654321', '123 Test Street, Hanoi', 'sepay', 'Deliver during office hours', 790000, 'confirmed', NULL, 0, '2026-06-15 20:02:37', NULL),
(3, 2, 'Test Customer', 'admin@vab.com', '0987654321', '123 Test Street, Hanoi', 'sepay', 'Deliver during office hours', 1500000, 'confirmed', NULL, 0, '2026-06-15 20:12:28', NULL),
(4, 2, 'Test Customer', 'admin@vab.com', '0987654321', '123 Test Street, Hanoi', 'sepay', 'Deliver during office hours', 1500000, 'confirmed', NULL, 0, '2026-06-15 20:20:00', NULL),
(5, 2, 'Test Customer', 'admin@vab.com', '0987654321', '123 Test Street, Hanoi', 'sepay', 'Deliver during office hours', 1500000, 'confirmed', NULL, 0, '2026-06-15 20:22:56', NULL),
(6, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 500000, 'completed', NULL, 0, '2026-06-16 00:35:12', NULL),
(7, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 500000, 'completed', NULL, 0, '2026-06-16 00:57:58', NULL),
(8, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'sepay', '', 600000, 'shipping', NULL, 0, '2026-06-16 01:05:32', NULL),
(9, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 10000, 'completed', NULL, 0, '2026-06-16 01:59:43', NULL),
(10, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'sepay', '', 2000, 'confirmed', NULL, 0, '2026-06-16 03:15:35', NULL),
(11, 1, 'Test Customer', 'testcustomer@example.com', '0987654321', '123 Test Street', 'COD', 'Test coupon order', 540000, 'shipping', NULL, 60000, '2026-06-16 03:50:18', NULL),
(12, 1, 'Test Customer', 'testcustomer@example.com', '0987654321', '123 Test Street', 'COD', 'Test coupon order', 540000, 'completed', NULL, 60000, '2026-06-16 03:50:49', NULL),
(13, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'sepay', '', 100000, 'pending', NULL, 0, '2026-06-17 06:47:58', NULL),
(14, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'sepay', '', 2000, 'confirmed', NULL, 0, '2026-06-17 06:49:00', NULL),
(15, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 100000, 'completed', NULL, 0, '2026-06-17 06:56:00', NULL),
(16, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'sepay', '', 2000, 'cancelled', NULL, 0, '2026-06-17 06:56:31', NULL),
(17, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'sepay', '', 2000, 'confirmed', NULL, 0, '2026-06-17 06:56:57', NULL),
(18, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'sepay', '', 2000, 'confirmed', NULL, 0, '2026-06-17 07:00:39', NULL),
(19, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'sepay', '', 2000, 'confirmed', NULL, 0, '2026-06-17 07:03:10', NULL),
(20, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 180000, 'pending', NULL, 0, '2026-06-20 06:53:49', NULL),
(21, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'sepay', '', 180000, 'pending', NULL, 0, '2026-06-20 07:00:20', NULL),
(22, 4, 'vy', 'vy2@gmail.com', '0987654321', '716 tân phú ', 'COD', '', 3000000, 'completed', NULL, 0, '2026-06-20 16:14:27', NULL),
(23, 4, 'vy', 'vy2@gmail.com', '0987654321', '716 tân phú ', 'sepay', '', 70000, 'pending', 'VANG20', 30000, '2026-06-20 16:19:25', NULL),
(24, 5, 'vy', 'vy3@gmail.com', '0893876537', '762 tân phú', 'COD', '', 3000000, 'completed', NULL, 0, '2026-06-20 16:49:57', NULL),
(25, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 150000, 'pending', NULL, 0, '2026-06-20 17:23:51', NULL),
(26, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 100000, 'completed', NULL, 0, '2026-06-20 17:25:06', NULL),
(27, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 100000, 'pending', NULL, 0, '2026-06-20 17:29:13', NULL),
(28, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 100000, 'pending', NULL, 0, '2026-06-20 17:30:56', NULL),
(29, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 200000, 'completed', NULL, 0, '2026-06-20 17:41:08', NULL),
(30, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 100000, 'pending', NULL, 0, '2026-06-20 17:45:32', NULL),
(31, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'sepay', '', 2000, 'completed', NULL, 0, '2026-06-20 17:48:00', NULL),
(32, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 290000, 'pending', NULL, 0, '2026-06-20 18:09:34', NULL),
(33, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 100000, 'pending', NULL, 0, '2026-06-20 18:26:03', NULL),
(34, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 120000, 'pending', NULL, 0, '2026-06-20 18:31:18', NULL),
(35, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 200000, 'pending', NULL, 0, '2026-06-20 18:34:42', NULL),
(36, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 120000, 'cancelled', NULL, 0, '2026-06-20 18:39:02', NULL),
(37, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 200000, 'delivered', NULL, 0, '2026-06-20 18:42:02', NULL),
(38, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 100000, 'cancelled', NULL, 0, '2026-06-20 18:45:36', NULL),
(39, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 250000, 'completed', NULL, 0, '2026-06-20 18:49:35', NULL),
(40, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 200000, 'pending', NULL, 0, '2026-06-23 18:52:13', NULL),
(41, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'sepay', '', 2000, 'confirmed', NULL, 0, '2026-06-23 19:03:18', NULL),
(42, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 100000, 'confirmed', NULL, 0, '2026-06-23 20:46:57', NULL),
(43, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'sepay', '', 2000, 'confirmed', NULL, 0, '2026-06-23 20:51:42', NULL),
(44, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'sepay', '', 2000, 'confirmed', NULL, 0, '2026-06-23 20:53:43', NULL),
(45, 7, 'Duc Binh Vo', 'voducbinh000@gmail.com', '0332164469', 'hehe', 'COD', 'hoho', 150000, 'pending', NULL, 0, '2026-06-24 05:54:12', NULL),
(46, 7, 'Duc Binh Vo', 'voducbinh000@gmail.com', '0332164469', 'hehe', 'COD', '', 120000, 'pending', NULL, 0, '2026-06-24 05:57:32', NULL),
(47, 3, 'TC Vỹ', 'truongvy1022@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 150000, 'pending', NULL, 0, '2026-06-24 05:57:52', NULL),
(48, 8, 'Nguyễn Thành Ngữ', 'tngu8425@gmail.com', '0865634267', '88', 'COD', '', 120000, 'pending', NULL, 0, '2026-06-24 06:00:04', NULL),
(49, 7, 'Duc Binh Vo', 'voducbinh000@gmail.com', '0332164469', 'hehe', 'COD', '', 180000, 'pending', NULL, 0, '2026-06-24 06:00:30', NULL),
(50, 1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '0123456789', '110d tân quý, tân phú, HCM', 'COD', '', 189000, 'returned', 'GIAMGIA', 21000, '2026-06-30 10:45:56', 1),
(51, 2, 'Admin VAB', 'admin@vab.com', '0903456789', '110d tân quý, tân phú, HCM', 'COD', '', 10000, 'refused', NULL, 0, '2026-06-30 11:37:54', 1);

-- --------------------------------------------------------

--
-- Table structure for table `KHACHHANG`
--

CREATE TABLE `KHACHHANG` (
  `MaKH` int(11) NOT NULL,
  `HoTenKH` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `MatKhauKH` varchar(255) NOT NULL,
  `SDT` varchar(20) DEFAULT '',
  `TrangThai` enum('active','locked') DEFAULT 'active',
  `HangKH` enum('silver','gold','diamond') DEFAULT 'silver',
  `DiaChi` text DEFAULT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `KHACHHANG`
--

INSERT INTO `KHACHHANG` (`MaKH`, `HoTenKH`, `email`, `MatKhauKH`, `SDT`, `TrangThai`, `HangKH`, `DiaChi`, `NgayTao`) VALUES
(1, 'trương chí vỹ', 'truongvy2762005@gmail.com', '$2y$10$lXEh3tF4MLO5umUUH6gvmeZs5/.dyZ6SO7knFI9eoEXsdeEsrlzEO', '0123456789', 'active', 'gold', '110d tân quý, tân phú, HCM', '2026-06-15 18:39:34'),
(2, 'Admin VAB', 'admin@vab.com', '$2y$10$WKXyGL/iwnyE5eQY2wS4aupJeHcZAAA8XtZvLvoEFx138.69xX5im', '0903456789', 'active', 'silver', '110d tân quý, tân phú, HCM', '2026-06-15 19:53:09'),
(3, 'TC Vỹ', 'truongvy1022@gmail.com', '$2y$10$epVwPwCtP4z/2.jIKjNZQu1FNBhq6kA1mSsSVharg3udguEvyL9im', '0123456789', 'active', 'silver', '110d tân quý, tân phú, HCM', '2026-06-20 06:54:30'),
(4, 'vy', 'vy2@gmail.com', '$2y$10$T/wkKTTivNi0fX4QGQF5tOaSH9x5motR1Buhd1MQ3RC5VU44DgsbC', '0987654321', 'active', 'gold', '716 tân phú ', '2026-06-20 16:02:51'),
(5, 'vy', 'vy3@gmail.com', '$2y$10$xG6m4fowXmDV.5NFVypiFexUCoF1/SYHgqX9p5p/f9WOZF3MByBz.', '0893876537', 'active', 'gold', '762 tân phú', '2026-06-20 16:48:57'),
(6, 'vy4', 'vy4@gmail.com', '$2y$10$Bocjdjl4UarDl9NsiGvHmOM51afH0aZ7dTZOyGpFOhFE5i8T3VPKG', '098765765', 'active', 'silver', NULL, '2026-06-23 18:19:58'),
(7, 'Duc Binh Vo', 'voducbinh000@gmail.com', '$2y$10$UL6QcWMMO1E1tJ6iMRkgbuLAK.o96yrXGj.pUA9xz5LNYABIbvxX.', '0332164469', 'active', 'silver', 'hehe', '2026-06-24 05:52:38'),
(8, 'Nguyễn Thành Ngữ', 'tngu8425@gmail.com', '$2y$10$OTM0IaAUFJHpDpUx21PIcO/OZZE9TV13BZ78/uEGcNNMQfRFOQMKG', '0865634267', 'active', 'silver', '88', '2026-06-24 05:59:37');

-- --------------------------------------------------------

--
-- Table structure for table `MAGIAMGIA`
--

CREATE TABLE `MAGIAMGIA` (
  `MaGiamGia` varchar(50) NOT NULL,
  `Ten` varchar(255) NOT NULL,
  `Loai` enum('percent','fixed') DEFAULT 'percent',
  `GiaTri` int(11) NOT NULL COMMENT 'Gia tri giam (% hoac so tien)',
  `DonHangToiThieu` int(11) DEFAULT 0,
  `SoLuot` int(11) DEFAULT 100 COMMENT 'So luot toi da',
  `DaDung` int(11) DEFAULT 0 COMMENT 'So luot da su dung',
  `NgayBD` date NOT NULL,
  `NgayKT` date NOT NULL,
  `TrangThai` enum('active','inactive','expired') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `MAGIAMGIA`
--

INSERT INTO `MAGIAMGIA` (`MaGiamGia`, `Ten`, `Loai`, `GiaTri`, `DonHangToiThieu`, `SoLuot`, `DaDung`, `NgayBD`, `NgayKT`, `TrangThai`) VALUES
('GIAMGIA', 'giảm giá ', 'percent', 10, 0, 100, 0, '2026-06-16', '2026-07-16', 'active'),
('KIMCUONG20', 'Ưu đãi hạng Kim Cương giảm 20%', 'percent', 20, 0, 999999, 0, '2026-01-01', '2030-12-31', 'active'),
('SALE20', 'Giam 20% the thao', 'percent', 20, 1000000, 30, 3, '2025-03-01', '2025-05-31', 'active'),
('SUMMER', 'Summer Sale 25%', 'percent', 25, 2000000, 20, 0, '2025-06-01', '2025-08-31', 'active'),
('VAB50K', 'Giam 50.000d', 'fixed', 50000, 300000, 50, 8, '2025-01-01', '2025-06-30', 'active'),
('VANG20', 'Ưu đãi hạng Vàng giảm 20%', 'percent', 20, 0, 999999, 1, '2026-01-01', '2030-12-31', 'active'),
('WELCOME', 'Giam 15% cho thanh vien moi', 'percent', 15, 0, 200, 45, '2025-01-01', '2025-12-31', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `NHANVIEN`
--

CREATE TABLE `NHANVIEN` (
  `MaNV` int(11) NOT NULL,
  `HoTenNV` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `MatKhauNV` varchar(255) NOT NULL,
  `SDT` varchar(20) DEFAULT '',
  `TrangThai` enum('active','locked') DEFAULT 'active',
  `VaiTro` enum('admin','manager','staff') DEFAULT 'staff',
  `NgayLapTK` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `NHANVIEN`
--

INSERT INTO `NHANVIEN` (`MaNV`, `HoTenNV`, `email`, `MatKhauNV`, `SDT`, `TrangThai`, `VaiTro`, `NgayLapTK`) VALUES
(1, 'Admin VAB', 'admin@vab.com', '$2y$10$WKXyGL/iwnyE5eQY2wS4aupJeHcZAAA8XtZvLvoEFx138.69xX5im', '0903456789', 'active', 'admin', '2026-06-15 18:25:10'),
(2, 'Quan ly Nguyen', 'manager@vab.com', '$2y$10$WKXyGL/iwnyE5eQY2wS4aupJeHcZAAA8XtZvLvoEFx138.69xX5im', '0909999999', 'active', 'manager', '2026-06-15 18:25:10'),
(3, 'Nhan vien Tran', 'staff@vab.com', '$2y$10$WKXyGL/iwnyE5eQY2wS4aupJeHcZAAA8XtZvLvoEFx138.69xX5im', '0908888888', 'active', 'staff', '2026-06-15 18:25:10'),
(4, 'vỹ', 'truongchivy@gmail.com', '$2y$10$QAPU/MqibTH7Vqyvn1Pj6uRfnkeEKanWLUPYFhzPyOhe/lgOzBZeS', '0987654567', 'active', 'staff', '2026-06-30 11:41:10'),
(5, 'vỹ', 'chivy@gmail.com', '$2y$10$ufrHnO.uN2VV8V2Sh79sC.kW8i7FdckmJ2lr9xwEK62hZuLlGjsSa', '0987654567', 'active', 'staff', '2026-06-30 11:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `QUANLYSP`
--

CREATE TABLE `QUANLYSP` (
  `MaSP` int(11) NOT NULL,
  `KichCo` varchar(50) NOT NULL,
  `SoLuong` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `QUANLYSP`
--

INSERT INTO `QUANLYSP` (`MaSP`, `KichCo`, `SoLuong`) VALUES
(13, '39', 10),
(13, '40', 10),
(13, '41', 10),
(13, '42', 10),
(14, '39', 8),
(14, '40', 10),
(14, '41', 10),
(14, '42', 10),
(15, '39', 9),
(15, '40', 5),
(15, '41', 5),
(15, '42', 10),
(16, 'L', 4),
(16, 'M', 10),
(16, 'S', 10),
(19, 'L', 8),
(19, 'M', 10),
(19, 'S', 10),
(19, 'XL', 10),
(20, 'L', 0),
(20, 'M', 0),
(20, 'S', 0),
(20, 'XL', 0),
(21, 'L', 8),
(21, 'M', 10),
(21, 'S', 10),
(21, 'XL', 10),
(22, 'L', 8),
(22, 'M', 10),
(22, 'S', 10),
(22, 'XL', 7),
(23, 'L', 7),
(23, 'M', 10),
(23, 'S', 10),
(23, 'XL', 10),
(24, 'L', 6),
(24, 'M', 5),
(24, 'S', 3),
(24, 'XL', 8),
(25, 'L', 8),
(25, 'M', 10),
(25, 'S', 10),
(25, 'XL', 10),
(26, 'L', 0),
(26, 'M', 2),
(26, 'S', 0),
(26, 'XL', 0),
(27, 'L', 3),
(27, 'M', 10),
(27, 'S', 10),
(27, 'XL', 10),
(28, 'L', 4),
(28, 'M', 15),
(28, 'S', 20),
(28, 'XL', 9),
(29, 'L', 10),
(29, 'M', 10),
(29, 'S', 10),
(29, 'XL', 10),
(30, 'L', 10),
(30, 'M', 10),
(30, 'S', 10),
(30, 'XL', 10),
(31, 'L', 10),
(31, 'M', 10),
(31, 'S', 10),
(31, 'XL', 10),
(32, 'L', 10),
(32, 'M', 10),
(32, 'S', 10),
(32, 'XL', 10),
(33, 'L', 10),
(33, 'M', 10),
(33, 'S', 10),
(33, 'XL', 10),
(35, 'L', 10),
(35, 'M', 10),
(35, 'S', 10),
(35, 'XL', 10),
(36, 'L', 10),
(36, 'M', 10),
(36, 'S', 10),
(36, 'XL', 10),
(37, 'L', 10),
(37, 'M', 10),
(37, 'S', 10),
(37, 'XL', 10),
(38, 'L', 10),
(38, 'M', 10),
(38, 'S', 10),
(38, 'XL', 10),
(39, 'L', 10),
(39, 'M', 10),
(39, 'S', 10),
(39, 'XL', 10),
(40, 'L', 10),
(40, 'M', 10),
(40, 'S', 10),
(40, 'XL', 10),
(41, 'L', 10),
(41, 'M', 10),
(41, 'S', 10),
(41, 'XL', 10),
(42, 'L', 10),
(42, 'M', 10),
(42, 'S', 10),
(42, 'XL', 10),
(43, 'L', 10),
(43, 'M', 10),
(43, 'S', 10),
(43, 'XL', 10),
(44, 'L', 10),
(44, 'M', 10),
(44, 'S', 10),
(44, 'XL', 10),
(45, 'L', 10),
(45, 'M', 10),
(45, 'S', 10),
(45, 'XL', 10),
(46, 'L', 10),
(46, 'M', 10),
(46, 'S', 10),
(46, 'XL', 10),
(47, 'L', 10),
(47, 'M', 10),
(47, 'S', 10),
(47, 'XL', 10),
(48, 'L', 10),
(48, 'M', 10),
(48, 'S', 10),
(48, 'XL', 10),
(49, 'L', 10),
(49, 'M', 10),
(49, 'S', 10),
(49, 'XL', 10),
(50, 'L', 10),
(50, 'M', 10),
(50, 'S', 10),
(50, 'XL', 10),
(51, 'L', 10),
(51, 'M', 10),
(51, 'S', 10),
(51, 'XL', 10),
(52, 'L', 10),
(52, 'M', 10),
(52, 'S', 10),
(52, 'XL', 10),
(53, 'L', 10),
(53, 'M', 10),
(53, 'S', 10),
(53, 'XL', 10),
(54, 'L', 10),
(54, 'M', 10),
(54, 'S', 10),
(54, 'XL', 10),
(55, 'L', 10),
(55, 'M', 10),
(55, 'S', 10),
(55, 'XL', 10),
(56, 'L', 10),
(56, 'M', 10),
(56, 'S', 10),
(56, 'XL', 10),
(57, 'L', 10),
(57, 'M', 10),
(57, 'S', 10),
(57, 'XL', 10),
(58, 'L', 10),
(58, 'M', 10),
(58, 'S', 10),
(58, 'XL', 10),
(59, 'L', 10),
(59, 'M', 10),
(59, 'S', 10),
(59, 'XL', 10),
(60, 'L', 10),
(60, 'M', 10),
(60, 'S', 10),
(60, 'XL', 10),
(61, 'L', 10),
(61, 'M', 10),
(61, 'S', 10),
(61, 'XL', 10),
(62, 'L', 10),
(62, 'M', 10),
(62, 'S', 10),
(62, 'XL', 10),
(63, 'L', 10),
(63, 'M', 10),
(63, 'S', 10),
(63, 'XL', 10),
(64, 'L', 10),
(64, 'M', 10),
(64, 'S', 10),
(64, 'XL', 10),
(65, 'L', 10),
(65, 'M', 10),
(65, 'S', 10),
(65, 'XL', 10),
(66, 'L', 10),
(66, 'M', 10),
(66, 'S', 10),
(66, 'XL', 10),
(67, 'L', 10),
(67, 'M', 10),
(67, 'S', 10),
(67, 'XL', 10),
(68, 'L', 10),
(68, 'M', 10),
(68, 'S', 10),
(68, 'XL', 10),
(72, 'L', 10),
(72, 'M', 10),
(72, 'S', 10),
(72, 'XL', 10);

-- --------------------------------------------------------

--
-- Table structure for table `SANPHAM`
--

CREATE TABLE `SANPHAM` (
  `MaSP` int(11) NOT NULL,
  `TenSP` varchar(255) NOT NULL,
  `DuongDan` varchar(255) NOT NULL,
  `MaDanhMuc` int(11) DEFAULT NULL,
  `Gia` int(11) NOT NULL,
  `GiaKhuyenMai` int(11) DEFAULT NULL,
  `AnhChinh` varchar(255) DEFAULT '',
  `AnhPhu` text DEFAULT NULL,
  `MoTaSP` text DEFAULT NULL,
  `MaThuongHieu` int(11) DEFAULT NULL,
  `TheThao` varchar(255) DEFAULT '',
  `GioiTinh` varchar(50) DEFAULT '',
  `KichCo` varchar(255) DEFAULT '',
  `TrangThai` tinyint(4) DEFAULT 1,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
  `MaNV` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `SANPHAM`
--

INSERT INTO `SANPHAM` (`MaSP`, `TenSP`, `DuongDan`, `MaDanhMuc`, `Gia`, `GiaKhuyenMai`, `AnhChinh`, `AnhPhu`, `MoTaSP`, `MaThuongHieu`, `TheThao`, `GioiTinh`, `KichCo`, `TrangThai`, `NgayTao`, `MaNV`) VALUES
(13, 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Nam Nike Tiempo Maestro Academy', 'gi-y-th-thao-b-ng-s-n-c-t-nhi-n-nam-nike-tiempo-maestro-academy', 3, 700000, 560000, '1781555373_giay3a.webp', '1781555373_sub_0_giay3d.webp,1781555373_sub_1_giay3c.webp,1781555373_sub_2_giay3b.webp', 'Tiempo Maestro Academy giúp bạn kiểm soát từng pha chạm bóng. Được chế tác với da FlyTouch, bề mặt mềm mại mang lại cảm giác bóng chính xác, giúp bạn dễ dàng làm chủ trận đấu và gây áp lực lên mọi hàng phòng ngự.\r\nCảm giác chạm bóng mềm mại\r\nPhần upper FlyTouch nhẹ mang lại cảm giác chạm bóng ổn định trong cả điều kiện ướt hoặc khô.\r\nĐộ bám cho nhiều loại sân\r\nĐinh dạng lưỡi (bladed studs) ở gót và mũi giày giúp bám sân tốt trên cả cỏ tự nhiên và cỏ nhân tạo. Đinh hình nón (conical studs) hỗ trợ bạn trụ và xoay người dễ dàng.', 1, 'Bóng đá', 'Nam', '39,40,41,42', 1, '2026-06-15 20:29:33', NULL),
(14, 'Giày Thể Thao Tập Luyện Nữ Nike W Nike Flex Train', 'gi-y-th-thao-t-p-luy-n-n-nike-w-nike-flex-train', 3, 500000, 350000, '1781555699_giay4a.webp', '1781555699_sub_0_giay4d.webp,1781555699_sub_1_giay4c.webp,1781555699_sub_2_giay4b.webp', 'Nâng. Tăng tốc. Vận động. Với Nike Flex Train, bạn có toàn quyền lựa chọn hành trình tập luyện của riêng mình. Đế ngoài linh hoạt kết hợp cùng thân giày nhẹ giúp mở ra nhiều lựa chọn tập luyện đa dạng.', 1, 'Gym', 'Nữ', '39,40,41,42', 1, '2026-06-15 20:34:59', NULL),
(15, 'Giày Thể Thao Chạy Bộ Nữ Nike W Nike Vomero ', 'gi-y-th-thao-ch-y-b-n-nike-w-nike-vomero-', 3, 600000, 450000, '1781555870_giay5a.webp', '1781555870_sub_0_giay5d.webp,1781555870_sub_1_giay5c.webp,1781555870_sub_2_giay5b.webp', 'Đệm tối đa của Vomero mang lại cảm giác êm ái cho những bước chạy thường ngày. Đây là trải nghiệm mềm mại, được đệm dày nhất của chúng tôi, với lớp ZoomX nhẹ trên cùng kết hợp cùng ReactX phản hồi nhanh trong đế giữa. Ngoài ra, họa tiết đế mới còn giúp chuyển động gót - mũi chân thêm mượt mà.', 1, 'Chạy bộ', 'Nữ', '39,40,41,42', 1, '2026-06-15 20:37:50', NULL),
(16, 'Vớ Thể Thao Unisex Nike U Nk Nsw Everyday Essential Cr', 'v-th-thao-unisex-nike-u-nk-nsw-everyday-essential-cr', 4, 2000, NULL, '1781573174_vo1.webp', '1781573174_sub_0_vo4.jpg,1781573174_sub_1_vo3.webp,1781573174_sub_2_vo2.webp', 'Công nghệ Dri-FIT giúp giữ cho bàn chân khô ráo và thoải mái.', 1, 'Bóng đá', 'Nam', 'L,M,S', 1, '2026-06-16 01:26:14', NULL),
(19, 'áo', '-o', 1, 10000, NULL, '1781575166_ao3lo.webp', '1781575166_sub_0_ao3lo2.jpg,1781575166_sub_1_ao3lo1.webp', '', 1, 'Chạy bộ', 'Nam', 'S,M,L,XL', 1, '2026-06-16 01:59:26', NULL),
(20, 'Quần Ngắn Thể Thao Nam Nike As M Nk Df Challenger 7Bf Sh', 'qu-n-ng-n-th-thao-nam-nike-as-m-nk-df-challenger-7bf-sh', 2, 250000, 190000, '1781625208_quan1.webp', '1781625208_sub_0_quan4.webp,1781625208_sub_1_quan3.webp,1781625208_sub_2_quan2.webp', 'Được thiết kế cho chạy bộ, tập luyện và yoga, quần short Challenger thấm hút mồ hôi giúp bạn luôn nhẹ nhàng, thoáng mát với phom rộng rãi, hỗ trợ tối đa chuyển động. Không chỉ dành riêng cho chạy bộ, quần còn có túi tiện lợi được bố trí để không gây khó chịu khi bạn chuyển từ đường chạy sang phòng gym.', 1, 'Gym', 'Nam', 'L,M,S,XL', 1, '2026-06-16 15:53:28', NULL),
(21, 'Áo Tay Ngắn Thể Thao Bóng Đá Trẻ Em Unisex Nike Fcb Y Nk Df Jsy Ss Stad Hm', '-o-tay-ng-n-th-thao-b-ng-tr-em-unisex-nike-fcb-y-nk-df-jsy-ss-stad-hm', 1, 200000, 150000, '1781625823_ao3a.webp', '1781625823_sub_0_ao3d.webp,1781625823_sub_1_ao3c.webp,1781625823_sub_2_ao3b.webp', 'Áo sân nhà F.C. Barcelona 2025/26 mang đến diện mạo mới cho một trong những thiết kế mang tính biểu tượng nhất của bóng đá thế giới. Sắc Blaugrana huyền thoại được kết hợp cùng các họa tiết lấy cảm hứng từ chuyển động và điểm nhấn màu sắc tươi sáng, tràn đầy năng lượng – tôn vinh sự đoàn kết và lòng trung thành của người hâm mộ dành cho CLB.', 1, 'Bóng đá', 'Nam', 'S,M,L,XL', 1, '2026-06-16 16:03:43', NULL),
(22, 'Quần Ngắn Thể Thao Bóng Đá Nam Nike Cfc M Nk Df Strk Short Kz3R', 'qu-n-ng-n-th-thao-b-ng-nam-nike-cfc-m-nk-df-strk-short-kz3r', 2, 200000, 190000, '1781626082_quan4a.webp', '1781626082_sub_0_quan5d.webp,1781626082_sub_1_quan4c.webp,1781626082_sub_2_quan4b.webp', 'Với những chi tiết thiết kế được tinh chỉnh riêng cho các ngôi sao bóng đá tương lai, phom dáng cổ điển kết hợp công nghệ thấm hút mồ hôi giúp bạn luôn mát mẻ và tự tin khi rèn luyện kỹ năng.', 1, 'Bóng đá', 'Nam', 'L,M,S,XL', 1, '2026-06-16 16:07:20', NULL),
(23, 'Quần Ngắn Thể Thao Bóng Đá Nam Nike Fcb M Nk Df Strk Short Kz 3R', 'qu-n-ng-n-th-thao-b-ng-nam-nike-fcb-m-nk-df-strk-short-kz-3r', 2, 150000, NULL, '1781626732_quan6b.webp', '1781626255_sub_0_quan6d.webp,1781626255_sub_1_quan6c.webp,1781626255_sub_2_quan6b.webp', 'Với những chi tiết thiết kế được tinh chỉnh riêng cho các ngôi sao bóng đá trẻ, phom dáng cổ điển cùng công nghệ thấm hút mồ hôi giúp bạn luôn mát mẻ và tự tin khi rèn luyện kỹ năng', 1, 'Bóng đá', 'Nam', 'L,M,S,XL', 1, '2026-06-16 16:10:55', NULL),
(24, 'Áo Tay Ngắn Thể Thao Bóng Đá Nam Nike Fcb M Nk Df Strk Ss Top K 3R', '-o-tay-ng-n-th-thao-b-ng-nam-nike-fcb-m-nk-df-strk-ss-top-k-3r', 1, 100000, NULL, '1781626434_ao4a.webp', '1781626434_sub_0_ao4d.webp,1781626434_sub_1_ao4c.webp,1781626434_sub_2_ao4b.webp', 'Với những chi tiết thiết kế được tạo ra riêng cho các ngôi sao bóng đá đang lên, dáng ôm cùng công nghệ thấm hút mồ hôi giúp bạn luôn mát mẻ và giữ sự tự tin khi rèn giũa kỹ năng.', 1, 'Bóng đá', 'Nam', 'L,M,S,XL', 1, '2026-06-16 16:12:59', NULL),
(25, 'Áo ba lỗ thể thao Tập Luyện Nam Nike AS M NK DF TEE STD SLVLS FLEX', '-o-ba-l-th-thao-t-p-luy-n-nam-nike-as-m-nk-df-tee-std-slvls-flex', 1, 100000, NULL, '1781626671_ao7a.webp', '1781626671_sub_0_ao7c.webp,1781626671_sub_1_ao7b.webp', '', 1, 'Gym', 'Nam', 'S,M,L,XL', 1, '2026-06-16 16:17:51', NULL),
(26, 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Nam Nike Zoom Vapor 16 Academy Fg/Mg', 'gi-y-th-thao-b-ng-s-n-c-t-nhi-n-nam-nike-zoom-vapor-16-academy-fg-mg', 3, 120000, NULL, '1781626883_giay4a.webp', '1781626883_sub_0_giay4d.webp,1781626883_sub_1_giay4c.webp,1781626883_sub_2_giay4b.webp', '', 1, 'Bóng đá', 'Nam', 'L,M,S,XL', 1, '2026-06-16 16:21:23', NULL),
(27, 'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Trẻ Em Unisex Nike Jr Legend 10 Club Fg/Mgv', 'gi-y-th-thao-b-ng-s-n-c-t-nhi-n-tr-em-unisex-nike-jr-legend-10-club-fg-mgv', 3, 100000, NULL, '1781627018_giay5a.webp', '1781627018_sub_0_giay5e.webp,1781627018_sub_1_giay5d.webp,1781627018_sub_2_giay5c.webp', '', 1, 'Bóng đá', 'Nam', 'S,M,L,XL', 1, '2026-06-16 16:23:38', NULL),
(28, 'Áo Tay Ngắn Thể Thao Tập Luyện Nam Nike As M Nk Df Tee Std Flex', '-o-tay-ng-n-th-thao-t-p-luy-n-nam-nike-as-m-nk-df-tee-std-flex', 1, 180000, NULL, '1781627213_ao8a.webp', '1781627213_sub_0_ao8d.webp,1781627213_sub_1_ao8c.webp,1781627213_sub_2_ao8b.webp', '', 1, 'Gym', 'Nam', 'S,M,L,XL', 1, '2026-06-16 16:26:53', NULL),
(29, 'Bộ Đồ Đá Banh Brazil Sân Nhà Vàng Cổ Điển', 'b-banh-brazil-s-n-nh-v-ng-c-i-n', 1, 300000, 220000, '1782660651_hinh1.jpg', '1782661652_sub_0_hinh1.1.1.jpg,1782661652_sub_1_hinh1.1.jpg', '', 1, 'Bóng đá', 'Nam', 'L,M,S,XL', 1, '2026-06-28 15:30:51', 1),
(30, 'Áo Đá Banh Đội Tuyển Nigeria Xanh Lá Hoạ Tiết', '-o-banh-i-tuy-n-nigeria-xanh-l-ho-ti-t', 1, 170000, NULL, '1782660927_hinh3.jpg', '1782661630_sub_0_hinh33.jpg,1782661630_sub_1_hinh333.jpg', '', 1, 'Chạy bộ', 'Nam', 'L,M,S,XL', 1, '2026-06-28 15:35:27', 1),
(31, 'Bộ Đồ Đá Banh Đội Tuyển Pháp Xanh Đậm', 'b-banh-i-tuy-n-ph-p-xanh-m', 1, 250000, 180000, '1782661004_hinh4.jpg', '1782661677_sub_0_hinh44.jpg,1782661677_sub_1_hinh444.jpg', '', 1, 'Bóng đá', 'Nam', 'L,M,S,XL', 1, '2026-06-28 15:36:44', 1),
(32, 'Áo Đá Banh Đội Tuyển Anh Đỏ Sân Khách', '-o-banh-i-tuy-n-anh-s-n-kh-ch', 1, 179000, NULL, '1782661102_hinhanh.jpg', '1782661697_sub_0_hinhanh11.jpg,1782661697_sub_1_hinhanh1.jpg', '', 1, 'Bóng đá', 'Nam', 'L,M,S,XL', 1, '2026-06-28 15:38:22', 1),
(33, 'Áo Đá Banh Đội Tuyển Hà Lan Trắng Phối Sọc Cam', '-o-banh-i-tuy-n-h-lan-tr-ng-ph-i-s-c-cam', 1, 330000, 250000, '1782661200_hinh7.jpg', '1782661732_sub_0_hinh77.jpg,1782661732_sub_1_hinh777.jpg', '', 1, 'Bóng đá', 'Nam', 'L,M,S,XL', 1, '2026-06-28 15:40:00', 1),
(35, 'Áo Đá Banh Đội Tuyển Anh Trắng Sân Nhà', '-o-banh-i-tuy-n-anh-tr-ng-s-n-nh-', 1, 150000, NULL, '1782661382_hinh10.jpg', '1782661752_sub_0_hinh100.jpg,1782661752_sub_1_hinh1000.jpg', '', 1, 'Bóng đá', 'Nam', 'L,M,S,XL', 1, '2026-06-28 15:43:02', 1),
(36, 'Áo T-Shirt Bóng Rổ Đen In Số 3', '-o-t-shirt-b-ng-r-en-in-s-3', 1, 46000, NULL, '1782661927_bongro1.avif', '1782661927_sub_0_bongro11.jpg,1782661927_sub_1_bongro111.jpg,1782661927_sub_2_bongro1111.jpg', '', 1, 'Bóng rổ', 'Nam', 'S,M,L,XL', 1, '2026-06-28 15:52:07', 1),
(37, 'Bộ Sát Nách Thể Thao Màu Xám (Dáng Nam)', 'b-s-t-n-ch-th-thao-m-u-x-m-d-ng-nam-', 1, 400000, NULL, '1782662055_bongro.jpg', '1782662055_sub_0_bongroo.jpg,1782662055_sub_1_bongrooo.jpg,1782662055_sub_2_bongroooo.jpg', '', 1, 'Bóng rổ', 'Nam', 'S,M,L,XL', 1, '2026-06-28 15:54:15', 1),
(38, 'Bộ Quần Áo Bóng Rổ Màu Xanh Dương Basic', 'b-qu-n-o-b-ng-r-m-u-xanh-d-ng-basic', 1, 560000, NULL, '1782662158_bongro2.jpg', '1782662158_sub_0_bongro22.jpg,1782662158_sub_1_bongro222jpg.avif,1782662158_sub_2_bongro2222.jpg', '', 1, 'Bóng rổ', 'Nam', 'L,M,S,XL', 1, '2026-06-28 15:55:58', 1),
(39, 'Áo Sát Nách Thể Thao Đen Viền Trắng', '-o-s-t-n-ch-th-thao-en-vi-n-tr-ng', 1, 450000, NULL, '1782662265_bongro4.jpg', '1782662265_sub_0_bongro44.jpg,1782662265_sub_1_bongro444.jpg,1782662265_sub_2_bongro4444.jpg', '', 1, 'Bóng rổ', 'Nam', 'S,M,L,XL', 1, '2026-06-28 15:57:45', 1),
(40, 'Áo Ba Lỗ Bóng Rổ Jordan Đen Số 23', '-o-ba-l-b-ng-r-jordan-en-s-23', 1, 480000, NULL, '1782662331_bongro5.jpg', '1782662331_sub_0_bongro55.jpg,1782662331_sub_1_bongro555.jpg,1782662331_sub_2_bongro5555.jpg', '', 1, 'Bóng rổ', 'Nam', 'S,M,L,XL', 1, '2026-06-28 15:58:51', 1),
(41, 'Set Áo Quần Đấu Dallas Mavericks Xanh Dương Số 77 (Móc Treo)', 'set-o-qu-n-u-dallas-mavericks-xanh-d-ng-s-77-m-c-treo-', 1, 370000, NULL, '1782662393_bongro7.jpg', '1782662393_sub_0_bongro77.jpg,1782662393_sub_1_bongro777.jpg,1782662393_sub_2_bongro7777.jpg', '', 1, 'Bóng rổ', 'Nam', 'L,M,S,XL', 1, '2026-06-28 15:59:53', 1),
(42, 'Áo Ba Lỗ Thể Thao Đen Trơn', '-o-ba-l-th-thao-en-tr-n', 1, 570000, NULL, '1782662700_bongro8.jpg', '1782662700_sub_0_bongro88.jpg,1782662700_sub_1_bongro888.jpg,1782662700_sub_2_bongro8888.jpg', '', 1, 'Bóng rổ', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:05:00', 1),
(43, 'Áo Polo Tennis Nam Adidas Trắng Phối Sọc Vai', '-o-polo-tennis-nam-adidas-tr-ng-ph-i-s-c-vai', 1, 560000, NULL, '1782663105_tenis.jpg', '1782663105_sub_0_teniss.jpg,1782663105_sub_1_tenisss.jpg,1782663105_sub_2_tenissss.jpg', '', 1, 'Tennis', 'Nam', 'S,M,L,XL', 1, '2026-06-28 16:11:45', 1),
(44, 'Áo Polo Tennis Nam Adidas Đen Phối Sọc Vai (Chính Diện)', '-o-polo-tennis-nam-adidas-en-ph-i-s-c-vai-ch-nh-di-n-', 1, 670000, NULL, '1782663182_tenis1.jpg', '1782663182_sub_0_tenis11.jpg,1782663182_sub_1_tenis111.jpg,1782663182_sub_2_tenis1111.jpg', '', 1, 'Tennis', 'Nam', 'S,M,L,XL', 1, '2026-06-28 16:13:02', 1),
(45, 'Áo Polo Tennis Nữ Adidas Xanh Đen Phối Cổ Trắng', '-o-polo-tennis-n-adidas-xanh-en-ph-i-c-tr-ng', 1, 680000, NULL, '1782663255_tenis22.jpg', '1782663255_sub_0_tenis2.jpg,1782663255_sub_1_tenis222.jpg,1782663255_sub_2_tenis2222.jpg', '', 1, 'Tennis', 'Nam', 'S,M,L,XL', 1, '2026-06-28 16:14:15', 1),
(46, 'Set Váy Tennis Nữ Adidas Trắng Phối Cổ Và Sọc Đỏ', 'set-v-y-tennis-n-adidas-tr-ng-ph-i-c-v-s-c-', 1, 830000, NULL, '1782663327_tenis3.jpg', '1782663327_sub_0_tenis33.jps.jpg,1782663327_sub_1_tenis333.jpg,1782663327_sub_2_tenis3333.jpg', '', 1, 'Tennis', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:15:27', 1),
(47, 'Set Bộ Đồ Tennis Nam Hoạ Tiết Kim Cương Màu Xanh Mint', 'set-b-tennis-nam-ho-ti-t-kim-c-ng-m-u-xanh-mint', 1, 769000, NULL, '1782663393_tenis5.jpg', '1782663393_sub_0_tenis55.jpg,1782663393_sub_1_tenis555.jpg,1782663393_sub_2_tenis5555.jpg', '', 1, 'Tennis', 'Nam', 'S,M,L,XL', 1, '2026-06-28 16:16:33', 1),
(48, 'Áo Polo Thể Thao Nam Trắng Hoạ Tiết Chìm Cổ Kéo Khóa (Adidas Originals)', '-o-polo-th-thao-nam-tr-ng-ho-ti-t-ch-m-c-k-o-kh-a-adidas-originals-', 1, 450000, NULL, '1782663461_tenis6.jpg', '1782663461_sub_0_tenis66.jpg,1782663461_sub_1_tenis666.jpg', '', 1, 'Tennis', 'Nam', 'S,M,L,XL', 1, '2026-06-28 16:17:41', 1),
(49, 'Set Đồ Tennis Nữ Áo Ba Lỗ Croptop Trắng & Quần Short Xanh Đen', 'set-tennis-n-o-ba-l-croptop-tr-ng-qu-n-short-xanh-en', 1, 567000, NULL, '1782663649_tenis7.jpg', '1782663649_sub_0_tenis77.jpg,1782663649_sub_1_tenis777.jpg,1782663649_sub_2_tenis7777.jpg', '', 1, 'Tennis', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:20:49', 1),
(50, 'Set Bộ Đồ Tennis Nữ Áo Ba Lỗ & Chân Váy Hoạ Tiết Kim Cương Hồng', 'set-b-tennis-n-o-ba-l-ch-n-v-y-ho-ti-t-kim-c-ng-h-ng', 1, 634000, NULL, '1782663699_tenis8.jpg', '1782663699_sub_0_tenis88.jpg,1782663699_sub_1_tenis888.jpg,1782663699_sub_2_tenis8888.jpg', '', 1, 'Tennis', 'Nam', 'S,M,L,XL', 1, '2026-06-28 16:21:39', 1),
(51, 'Set Váy Tennis Nữ Áo Sát Nách Cổ Chữ V Viền Xanh Lá (Adidas Originals)', 'set-v-y-tennis-n-o-s-t-n-ch-c-ch-v-vi-n-xanh-l-adidas-originals-', 1, 457000, NULL, '1782663751_tenis9.jpg', '1782663751_sub_0_tenis99.jpg,1782663751_sub_1_tenis999.jpg,1782663751_sub_2_tenis9999.jpg', '', 1, 'Tennis', 'Nam', 'S,M,L,XL', 1, '2026-06-28 16:22:31', 1),
(52, 'Quần Short Chạy Bộ Nam Dáng Ngắn Màu Xanh Rêu (Coolmate)', 'qu-n-short-ch-y-b-nam-d-ng-ng-n-m-u-xanh-r-u-coolmate-', 2, 112000, NULL, '1782664055_chaybo.jpg', '1782664055_sub_0_chayboo.jpg,1782664055_sub_1_chaybooo.jpg,1782664055_sub_2_chayboooo.jpg', '', 1, 'Chạy bộ', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:27:35', 1),
(53, 'Quần Short Thể Thao Nam Cạp Lưới Thoáng Khí Màu Xám Nhạt', 'qu-n-short-th-thao-nam-c-p-l-i-tho-ng-kh-m-u-x-m-nh-t', 2, 180000, NULL, '1782664133_chaybo1.jpg', '1782664133_sub_0_chaybo11.jpg,1782664133_sub_1_chaybo111.jpg,1782664133_sub_2_chaybo1111.jpg', '', 1, 'Chạy bộ', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:28:53', 1),
(54, 'Áo T-Shirt Chạy Bộ Nam Họa Tiết Phối Màu Ombre (Xanh - Cam - Vàng)', '-o-t-shirt-ch-y-b-nam-h-a-ti-t-ph-i-m-u-ombre-xanh---cam---v-ng-', 1, 200000, NULL, '1782664171_chaybo2.jpg', '1782664171_sub_0_chaybo22.jpg,1782664171_sub_1_chaybo222.jpg,1782664171_sub_2_chaybo2222.jpg', '', 1, 'Chạy bộ', 'Nam', 'S,M,L,XL', 1, '2026-06-28 16:29:31', 1),
(55, 'Quần Short Chạy Bộ 2 Lớp Nam Tích Hợp Đai Lưng Màu Xanh Dương', 'qu-n-short-ch-y-b-2-l-p-nam-t-ch-h-p-ai-l-ng-m-u-xanh-d-ng', 2, 190000, NULL, '1782664228_chaybo3.jpg', '1782664228_sub_0_chaybo33.jpg,1782664228_sub_1_chaybo333.jpg,1782664228_sub_2_chaybo3333.jpg', '', 1, 'Chạy bộ', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:30:28', 1),
(56, 'Áo T-Shirt Thể Thao Nam Trơn Màu Đen Thoáng Khí', '-o-t-shirt-th-thao-nam-tr-n-m-u-en-tho-ng-kh-', 1, 210000, NULL, '1782664315_chaybo4.jpg', '1782664315_sub_0_chaybo44.jpg,1782664315_sub_1_chaybo444.jpg,1782664315_sub_2_chaybo4444.jpg', '', 1, 'Chạy bộ', 'Nam', 'S,M,L,XL', 1, '2026-06-28 16:31:55', 1),
(57, 'Áo T-Shirt Chạy Bộ Nam Họa Tiết Sóng Sọc Màu Tím Phối Hồng Bạc', '-o-t-shirt-ch-y-b-nam-h-a-ti-t-s-ng-s-c-m-u-t-m-ph-i-h-ng-b-c', 1, 240000, NULL, '1782664387_chaybo5.jpg', '1782664387_sub_0_chaybo55.jpg,1782664387_sub_1_chaybo555.jpg,1782664387_sub_2_chaybo5555.jpg', '', 1, 'Chạy bộ', 'Nam', 'S,M,L,XL', 1, '2026-06-28 16:33:07', 1),
(58, 'Áo T-Shirt Thể Thao Nam Màu Hồng Phối Ống Tay Găng Chống Nắng (Dáng Chạy)', '-o-t-shirt-th-thao-nam-m-u-h-ng-ph-i-ng-tay-g-ng-ch-ng-n-ng-d-ng-ch-y-', 1, 230000, NULL, '1782664497_chaybo6.jpg', '1782664497_sub_0_chaybo66.jpg,1782664497_sub_1_chaybo666.jpg,1782664497_sub_2_chaybo6666.jpg', '', 1, 'Chạy bộ', 'Nam', 'S,M,L,XL', 1, '2026-06-28 16:34:57', 1),
(59, 'Áo T-Shirt Chạy Bộ Nam Họa Tiết Màu Xám Loang Khói (Dáng Chạy)', '-o-t-shirt-ch-y-b-nam-h-a-ti-t-m-u-x-m-loang-kh-i-d-ng-ch-y-', 1, 160000, NULL, '1782664643_chaybo7.jpg', '1782664643_sub_0_chaybo77.jpg,1782664643_sub_1_chaybo777jpg.jpg,1782664643_sub_2_chaybo7777.jpg', '', 1, 'Chạy bộ', 'Nam', 'S,M,L,XL', 1, '2026-06-28 16:37:23', 1),
(60, 'Quần Tights Dài Thể Thao Nam Màu Xám Chuột (Coolmate Active)', 'qu-n-tights-d-i-th-thao-nam-m-u-x-m-chu-t-coolmate-active-', 1, 130000, NULL, '1782664812_chaybo8.jpg', '1782664812_sub_0_chaybo88.jpg,1782664812_sub_1_chaybo888.jpg,1782664812_sub_2_chaybo8888.jpg', '', 1, 'Chạy bộ', 'Nam', 'S,M,L,XL', 1, '2026-06-28 16:40:12', 1),
(61, 'Set Đồ Tập Nữ Adidas Áo Bra & Quần Short Đen', 'set-t-p-n-adidas-o-bra-qu-n-short-en', 1, 230000, NULL, '1782665011_tapgym1.jpg', '1782665011_sub_0_tapgym11.jpg,1782665011_sub_1_tapgym111.jpg,1782665011_sub_2_tapgym1111.jpg', '', 1, 'Gym', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:43:31', 1),
(62, 'Áo T-Shirt Tập Gym Nam Adidas Màu Xanh Rêu Trơn', '-o-t-shirt-t-p-gym-nam-adidas-m-u-xanh-r-u-tr-n', 1, 210000, NULL, '1782665066_tapgym.jpg', '1782665066_sub_0_tapgymm.jpg,1782665066_sub_1_tapgymmm.jpg,1782665066_sub_2_tapgymmmm.jpg', '', 1, 'Gym', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:44:26', 1),
(63, 'Set Đồ Tập Nam Adidas Áo T-Shirt & Quần Short Đen ', 'set-t-p-nam-adidas-o-t-shirt-qu-n-short-en-', 1, 310000, NULL, '1782665127_tapgym2.jpg', '1782665127_sub_0_tapgym22.jpg,1782665127_sub_1_tapgym222.jpg,1782665127_sub_2_tapgym2222.jpg', '', 1, 'Gym', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:45:27', 1),
(64, 'Set Đồ Tập Nữ Liền Thân (Bodysuit) Adidas Cổ Yếm Màu Đen', 'set-t-p-n-li-n-th-n-bodysuit-adidas-c-y-m-m-u-en', 1, 410000, NULL, '1782665188_tapgym3.jpg', '1782665188_sub_0_tapgym33.jpg,1782665188_sub_1_tapgym333.jpg,1782665188_sub_2_tapgym3333.jpg', '', 1, 'Gym', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:46:28', 1),
(65, 'Set Đồ Tập Nam Adidas Màu Be Khaki (Áo T-Shirt & Quần Short)', 'set-t-p-nam-adidas-m-u-be-khaki-o-t-shirt-qu-n-short-', 1, 350000, NULL, '1782665248_tapgym4.jpg', '1782665248_sub_0_tapgym44.jpg,1782665248_sub_1_tapgym444.jpg,1782665248_sub_2_tapgym4444.jpg', '', 1, 'Gym', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:47:28', 1),
(66, 'Set Đồ Tập Nữ Áo Thun Tím Nhạt & Quần Short Đen', 'set-t-p-n-o-thun-t-m-nh-t-qu-n-short-en', 1, 370000, NULL, '1782665326_tapgym5.jpg', '1782665326_sub_0_tapgym77.jpg,1782665326_sub_1_tapgym777.jpg,1782665326_sub_2_tapgym7777.jpg', '', 1, 'Gym', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:48:46', 1),
(67, 'Set Đồ Tập Nam Adidas Áo T-Shirt Xanh Nhạt & Quần Short Đen', 'set-t-p-nam-adidas-o-t-shirt-xanh-nh-t-qu-n-short-en', 1, 320000, NULL, '1782665462_tapgym6.jpg', '1782665462_sub_0_tapgym66.jpg,1782665462_sub_1_tapgym666.jpg,1782665462_sub_2_tapgym6666.jpg', '', 1, 'Gym', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:51:02', 1),
(68, 'Set Đồ Tập Nữ Adidas Áo Bra Trắng & Quần Short Đen', 'set-t-p-n-adidas-o-bra-tr-ng-qu-n-short-en', 1, 123000, NULL, '1782665524_tapgym7.jpg', '1782665524_sub_0_tapgym77.jpg,1782665524_sub_1_tapgym777.jpg,1782665524_sub_2_tapgym7777.jpg', '', 1, 'Gym', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:52:04', 1),
(72, 'Set Đồ Tập Nữ Adidas Áo Bra & Quần Short Đen ', 'set-t-p-n-adidas-o-bra-qu-n-short-en-', 1, 433000, NULL, '1782665686_tapgym9.jpg', '1782665686_sub_0_tapgym11.jpg,1782665686_sub_1_tapgym99.jpg,1782665686_sub_2_tapgym999.jpg', '', 1, 'Gym', 'Nam', 'L,M,S,XL', 1, '2026-06-28 16:54:46', 1);

-- --------------------------------------------------------

--
-- Table structure for table `THETHAO`
--

CREATE TABLE `THETHAO` (
  `MaTheThao` int(11) NOT NULL,
  `TenSport` varchar(255) NOT NULL,
  `DuongDan` varchar(255) NOT NULL,
  `TrangThai` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `THETHAO`
--

INSERT INTO `THETHAO` (`MaTheThao`, `TenSport`, `DuongDan`, `TrangThai`) VALUES
(1, 'Chạy bộ', 'sport-running', 1),
(2, 'Gym', 'sport-gym', 1),
(3, 'Bóng đá', 'sport-football', 1),
(4, 'Bóng rổ', 'sport-basketball', 1),
(5, 'Tennis', 'sport-tennis', 1);

-- --------------------------------------------------------

--
-- Table structure for table `THONGBAO`
--

CREATE TABLE `THONGBAO` (
  `MaThongBao` int(11) NOT NULL,
  `MaKH` int(11) NOT NULL,
  `TieuDe` varchar(255) NOT NULL,
  `NoiDung` text NOT NULL,
  `TrangThai` tinyint(4) DEFAULT 0 COMMENT '0=chua doc, 1=da doc',
  `NgayTao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `THONGBAO`
--

INSERT INTO `THONGBAO` (`MaThongBao`, `MaKH`, `TieuDe`, `NoiDung`, `TrangThai`, `NgayTao`) VALUES
(1, 5, 'Chúc mừng! Bạn đã thăng hạng thành viên VÀNG', 'Chào bạn,\n\nChúc mừng tài khoản của bạn đã được nâng cấp lên hạng thành viên VÀNG nhờ tích lũy chi tiêu đạt mốc trên 2.000.000đ.\n\nBạn được hưởng đặc quyền ưu đãi dành riêng cho hạng VÀNG: Sử dụng mã giảm giá GOLD30 hoặc VANG30 để nhận ưu đãi giảm giá 30% cho các đơn hàng tiếp theo (tối đa 3 lần sử dụng).\n\nCảm ơn bạn đã luôn tin tưởng và đồng hành cùng VAB SHOP!', 1, '2026-06-20 23:50:25'),
(2, 1, 'Chúc mừng! Bạn đã thăng hạng thành viên VÀNG', 'Chào bạn,\n\nChúc mừng tài khoản của bạn đã được nâng cấp lên hạng thành viên VÀNG nhờ tích lũy chi tiêu đạt mốc trên 2.000.000đ.\n\nBạn được hưởng đặc quyền ưu đãi dành riêng cho hạng VÀNG: Sử dụng mã giảm giá VANG30 để nhận ưu đãi giảm giá 30% cho các đơn hàng tiếp theo (tối đa 3 lần sử dụng).\n\nCảm ơn bạn đã luôn tin tưởng và đồng hành cùng VAB SHOP!', 1, '2026-06-22 23:05:58');

-- --------------------------------------------------------

--
-- Table structure for table `THUONGHIEU`
--

CREATE TABLE `THUONGHIEU` (
  `MaThuongHieu` int(11) NOT NULL,
  `TenThuongHieu` varchar(255) NOT NULL,
  `DuongDan` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT '',
  `MoTa` text DEFAULT NULL,
  `TrangThai` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `THUONGHIEU`
--

INSERT INTO `THUONGHIEU` (`MaThuongHieu`, `TenThuongHieu`, `DuongDan`, `logo`, `MoTa`, `TrangThai`) VALUES
(1, 'Nike', 'nike', '', 'Thương hiệu thể thao số 1 thế giới', 1),
(2, 'Adidas', 'adidas', '', 'Thương hiệu thể thao Đức nổi tiếng', 1),
(3, 'Puma', 'puma', '', 'Thương hiệu thể thao toàn cầu', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `CHITIETDONHANG`
--
ALTER TABLE `CHITIETDONHANG`
  ADD PRIMARY KEY (`MaCTDH`),
  ADD KEY `MaDonHang` (`MaDonHang`),
  ADD KEY `MaSP` (`MaSP`);

--
-- Indexes for table `DANHGIASP`
--
ALTER TABLE `DANHGIASP`
  ADD PRIMARY KEY (`MaDG`),
  ADD KEY `MaSP` (`MaSP`),
  ADD KEY `MaKH` (`MaKH`),
  ADD KEY `fk_danhgiasp_nhanvien` (`MaNV`);

--
-- Indexes for table `DANHMUCSP`
--
ALTER TABLE `DANHMUCSP`
  ADD PRIMARY KEY (`MaDanhMuc`),
  ADD UNIQUE KEY `DuongDan` (`DuongDan`);

--
-- Indexes for table `DONHANG`
--
ALTER TABLE `DONHANG`
  ADD PRIMARY KEY (`MaDonHang`),
  ADD KEY `MaKH` (`MaKH`),
  ADD KEY `fk_donhang_magiamgia` (`MaGiamGia`),
  ADD KEY `fk_donhang_nhanvien` (`MaNV`);

--
-- Indexes for table `KHACHHANG`
--
ALTER TABLE `KHACHHANG`
  ADD PRIMARY KEY (`MaKH`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `MAGIAMGIA`
--
ALTER TABLE `MAGIAMGIA`
  ADD UNIQUE KEY `MaGiamGia` (`MaGiamGia`);

--
-- Indexes for table `NHANVIEN`
--
ALTER TABLE `NHANVIEN`
  ADD PRIMARY KEY (`MaNV`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `QUANLYSP`
--
ALTER TABLE `QUANLYSP`
  ADD PRIMARY KEY (`MaSP`,`KichCo`);

--
-- Indexes for table `SANPHAM`
--
ALTER TABLE `SANPHAM`
  ADD PRIMARY KEY (`MaSP`),
  ADD UNIQUE KEY `DuongDan` (`DuongDan`),
  ADD KEY `MaDanhMuc` (`MaDanhMuc`),
  ADD KEY `MaThuongHieu` (`MaThuongHieu`),
  ADD KEY `fk_sanpham_thethao` (`TheThao`),
  ADD KEY `fk_sanpham_nhanvien` (`MaNV`);

--
-- Indexes for table `THETHAO`
--
ALTER TABLE `THETHAO`
  ADD PRIMARY KEY (`MaTheThao`),
  ADD UNIQUE KEY `DuongDan` (`DuongDan`),
  ADD UNIQUE KEY `TenSport` (`TenSport`);

--
-- Indexes for table `THONGBAO`
--
ALTER TABLE `THONGBAO`
  ADD PRIMARY KEY (`MaThongBao`),
  ADD KEY `MaKH` (`MaKH`);

--
-- Indexes for table `THUONGHIEU`
--
ALTER TABLE `THUONGHIEU`
  ADD PRIMARY KEY (`MaThuongHieu`),
  ADD UNIQUE KEY `DuongDan` (`DuongDan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `CHITIETDONHANG`
--
ALTER TABLE `CHITIETDONHANG`
  MODIFY `MaCTDH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `DANHGIASP`
--
ALTER TABLE `DANHGIASP`
  MODIFY `MaDG` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `DANHMUCSP`
--
ALTER TABLE `DANHMUCSP`
  MODIFY `MaDanhMuc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `DONHANG`
--
ALTER TABLE `DONHANG`
  MODIFY `MaDonHang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `KHACHHANG`
--
ALTER TABLE `KHACHHANG`
  MODIFY `MaKH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `NHANVIEN`
--
ALTER TABLE `NHANVIEN`
  MODIFY `MaNV` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `SANPHAM`
--
ALTER TABLE `SANPHAM`
  MODIFY `MaSP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `THETHAO`
--
ALTER TABLE `THETHAO`
  MODIFY `MaTheThao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `THONGBAO`
--
ALTER TABLE `THONGBAO`
  MODIFY `MaThongBao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `THUONGHIEU`
--
ALTER TABLE `THUONGHIEU`
  MODIFY `MaThuongHieu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `CHITIETDONHANG`
--
ALTER TABLE `CHITIETDONHANG`
  ADD CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`MaDonHang`) REFERENCES `DONHANG` (`MaDonHang`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `SANPHAM` (`MaSP`) ON DELETE SET NULL;

--
-- Constraints for table `DANHGIASP`
--
ALTER TABLE `DANHGIASP`
  ADD CONSTRAINT `danhgiasp_ibfk_1` FOREIGN KEY (`MaSP`) REFERENCES `SANPHAM` (`MaSP`) ON DELETE CASCADE,
  ADD CONSTRAINT `danhgiasp_ibfk_2` FOREIGN KEY (`MaKH`) REFERENCES `KHACHHANG` (`MaKH`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_danhgiasp_nhanvien` FOREIGN KEY (`MaNV`) REFERENCES `NHANVIEN` (`MaNV`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `DONHANG`
--
ALTER TABLE `DONHANG`
  ADD CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `KHACHHANG` (`MaKH`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_donhang_magiamgia` FOREIGN KEY (`MaGiamGia`) REFERENCES `MAGIAMGIA` (`MaGiamGia`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_donhang_nhanvien` FOREIGN KEY (`MaNV`) REFERENCES `NHANVIEN` (`MaNV`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `QUANLYSP`
--
ALTER TABLE `QUANLYSP`
  ADD CONSTRAINT `quanlysp_ibfk_1` FOREIGN KEY (`MaSP`) REFERENCES `SANPHAM` (`MaSP`) ON DELETE CASCADE;

--
-- Constraints for table `SANPHAM`
--
ALTER TABLE `SANPHAM`
  ADD CONSTRAINT `fk_sanpham_nhanvien` FOREIGN KEY (`MaNV`) REFERENCES `NHANVIEN` (`MaNV`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sanpham_thethao` FOREIGN KEY (`TheThao`) REFERENCES `THETHAO` (`TenSport`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`MaDanhMuc`) REFERENCES `DANHMUCSP` (`MaDanhMuc`) ON DELETE SET NULL,
  ADD CONSTRAINT `sanpham_ibfk_2` FOREIGN KEY (`MaThuongHieu`) REFERENCES `THUONGHIEU` (`MaThuongHieu`) ON DELETE SET NULL;

--
-- Constraints for table `THONGBAO`
--
ALTER TABLE `THONGBAO`
  ADD CONSTRAINT `thongbao_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `KHACHHANG` (`MaKH`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
