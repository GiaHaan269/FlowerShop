-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th9 13, 2024 lúc 09:36 AM
-- Phiên bản máy phục vụ: 10.4.28-MariaDB
-- Phiên bản PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `perfume`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `product_name`, `product_image`, `quantity`, `price`, `total_price`) VALUES
(9, 25, 75, 'Gloss Bomb Oil Luminizing Lip Oil N\' Gloss', 'fentybomboil.jpg', 1, 821000.00, 821000.00),
(10, 25, 72, 'Gloss Bomb Stix High-Shine Gloss Stick', 'fentyhighshine.jpg', 2, 790000.00, 1580000.00),
(11, 26, 75, 'Gloss Bomb Oil Luminizing Lip Oil N\' Gloss', 'fentybomboil.jpg', 1, 821000.00, 821000.00),
(12, 26, 72, 'Gloss Bomb Stix High-Shine Gloss Stick', 'fentyhighshine.jpg', 1, 790000.00, 790000.00),
(13, 26, 74, 'Gloss Bomb Heat Universal Lip Luminizer + Plumper', 'fentyluminiplumper.jpg', 1, 821000.00, 821000.00),
(14, 27, 72, 'Gloss Bomb Stix High-Shine Gloss Stick', 'fentyhighshine.jpg', 1, 790000.00, 790000.00),
(15, 28, 72, 'Gloss Bomb Stix High-Shine Gloss Stick', 'fentyhighshine.jpg', 1, 790000.00, 790000.00),
(16, 28, 75, 'Gloss Bomb Oil Luminizing Lip Oil N\' Gloss', 'fentybomboil.jpg', 1, 821000.00, 821000.00),
(17, 29, 72, 'Gloss Bomb Stix High-Shine Gloss Stick', 'fentyhighshine.jpg', 3, 790000.00, 2370000.00),
(18, 29, 71, 'Poutsicle Hydrating Lip Stain', 'fentyb1.jpg', 1, 884000.00, 884000.00),
(19, 29, 73, 'Gloss Bomb Universal Lip Luminizer', 'fentylumi.jpg', 1, 663000.00, 663000.00);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
