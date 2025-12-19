-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th12 18, 2025 lúc 11:31 AM
-- Phiên bản máy phục vụ: 9.1.0
-- Phiên bản PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `todolist_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `progress` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `content`, `start_time`, `end_time`, `progress`, `created_at`) VALUES
(60, 10, 'c', '', '2025-12-14 19:21:00', NULL, 37, '2025-12-14 19:21:52'),
(61, 10, 'c', '', '2025-12-14 19:21:00', NULL, 40, '2025-12-14 19:22:28'),
(63, 10, 's', '', '2025-12-14 21:51:00', NULL, 0, '2025-12-14 21:51:27'),
(64, 10, 's', '', '2025-12-14 21:51:00', NULL, 0, '2025-12-14 21:51:31'),
(65, 10, 's', '', '2025-12-14 21:51:00', '2025-12-19 23:33:00', 39, '2025-12-14 21:51:36'),
(66, 10, 'k', '', '2025-12-14 21:53:00', '2025-12-21 23:34:00', 60, '2025-12-14 21:53:51'),
(67, 10, 'DEADLINE CUỐI ĐỜI SV NĂM 4', '18/12: LÊN TRƯỜNG ĐĂNG KÍ THI LẠI. LÀM XONG HẾT WEB + WINDOW ĐỂ T7 THI DỚI NỘP CHO THẦY. LÀM CODE GAME CÙNG SAU. CHỈ NG DĨM SETTING MAP GAME. Soạn file word game\r\n\r\n19/12: LÀM WINDOW ĐỂ T7 THI + CODE GAME (BỎ TẬP). GẶP CÔ XUÂN\r\n\r\n20/12: THI TH WINDOW. HẠN NỘP ẤY. CODE GAME\r\nGẶP THẦY TÙNG.\r\n\r\n21/12: CODE GAME TOÀN BỘ XONG HẾT\r\n\r\n22/12: LÀM WORD\r\n', '2025-12-18 00:01:00', '2025-12-25 23:01:00', 100, '2025-12-18 00:10:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_verified` tinyint(1) DEFAULT '0',
  `verification_token` varchar(64) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `is_verified`, `verification_token`, `avatar`) VALUES
(10, 'ngtien1924@gmail.com', 'ngtien1924@gmail.com', '$2y$12$jLPirWpYJpeSU8Z5LWxAf.7tZvJUpROe8hiFuZT19ZvGuhYS8VUji', '2025-12-14 01:38:47', 1, NULL, 'avatar_10_1765710996.png'),
(11, 'nguyenvana', 'pemun119@gmail.com', '$2y$12$32/2G12YJ/qb6Rvh5H71GeUUxp6zO117qSc1DOtVrA2pDek7wgPES', '2025-12-18 17:49:21', 1, NULL, NULL);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
