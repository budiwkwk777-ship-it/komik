-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 28, 2025 at 03:38 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `komik_online`
--

-- --------------------------------------------------------

--
-- Table structure for table `chapter`
--

CREATE TABLE `chapter` (
  `id` int NOT NULL,
  `komik_id` int DEFAULT NULL,
  `chapter_number` decimal(10,2) DEFAULT NULL,
  `judul_chapter` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `images` text,
  `total_pages` int DEFAULT '0',
  `views` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chapter`
--

INSERT INTO `chapter` (`id`, `komik_id`, `chapter_number`, `judul_chapter`, `slug`, `images`, `total_pages`, `views`, `created_at`) VALUES
(25, 1, 1.00, 'Romance Dawn', 'one-piece-chapter-1', NULL, 3, 0, '2025-11-28 00:52:36'),
(26, 2, 1.00, 'Uzumaki Naruto', 'naruto-chapter-1', NULL, 3, 0, '2025-11-28 00:52:36'),
(27, 11, 1.00, 'Prologue', 'solo-leveling-chapter-1', NULL, 3, 0, '2025-11-28 00:52:36'),
(28, 12, 1.00, 'The First Test', 'tower-of-god-chapter-1', NULL, 3, 0, '2025-11-28 00:52:36'),
(29, 21, 1.00, 'Glory', 'the-kings-avatar-chapter-1', NULL, 3, 0, '2025-11-28 00:52:36'),
(30, 22, 1.00, 'Battle Start', 'battle-through-the-heavens-chapter-1', NULL, 3, 0, '2025-11-28 00:52:36'),
(37, 1, 1.00, 'Romance Dawn', 'one-piece-chapter-1', NULL, 3, 0, '2025-11-28 01:55:50'),
(38, 2, 1.00, 'Uzumaki Naruto', 'naruto-chapter-1', NULL, 3, 0, '2025-11-28 01:55:50'),
(39, 11, 1.00, 'Prologue', 'solo-leveling-chapter-1', NULL, 3, 0, '2025-11-28 01:55:50'),
(40, 12, 1.00, 'The First Test', 'tower-of-god-chapter-1', NULL, 3, 0, '2025-11-28 01:55:50'),
(41, 21, 1.00, 'Glory', 'the-kings-avatar-chapter-1', NULL, 3, 0, '2025-11-28 01:55:50'),
(42, 22, 1.00, 'Battle Start', 'battle-through-the-heavens-chapter-1', NULL, 3, 0, '2025-11-28 01:55:50');

-- --------------------------------------------------------

--
-- Table structure for table `chapter_pages`
--

CREATE TABLE `chapter_pages` (
  `id` int NOT NULL,
  `chapter_id` int DEFAULT NULL,
  `page_number` int DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `komik`
--

CREATE TABLE `komik` (
  `id` int NOT NULL,
  `judul` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `penulis` varchar(100) DEFAULT NULL,
  `status` enum('Ongoing','Completed','Hiatus') DEFAULT 'Ongoing',
  `sinopsis` text,
  `genre` varchar(255) DEFAULT NULL,
  `kategori` enum('Manga','Manhwa','Manhua') DEFAULT 'Manga',
  `total_chapter` int DEFAULT '0',
  `views` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `komik`
--

INSERT INTO `komik` (`id`, `judul`, `slug`, `cover`, `penulis`, `status`, `sinopsis`, `genre`, `kategori`, `total_chapter`, `views`, `created_at`) VALUES
(216, 'One Piece', 'one-piece', 'one_piece.jpg', 'Eiichiro Oda', 'Ongoing', 'Petualangan Luffy mencari One Piece', 'Adventure, Action, Fantasy', 'Manga', 0, 0, '2025-11-28 01:55:50'),
(217, 'Naruto', 'naruto', 'naruto.jpg', 'Masashi Kishimoto', 'Completed', 'Kisah ninja muda bernama Naruto', 'Action, Adventure, Fantasy', 'Manga', 0, 0, '2025-11-28 01:55:50'),
(218, 'Attack on Titan', 'attack-on-titan', 'aot.jpg', 'Hajime Isayama', 'Completed', 'Perjuangan melawan Titan', 'Action, Horror, Drama', 'Manga', 0, 0, '2025-11-28 01:55:50'),
(219, 'Demon Slayer', 'demon-slayer', 'demon_slayer.jpg', 'Koyoharu Gotouge', 'Completed', 'Petualangan Tanjiro melawan iblis', 'Action, Fantasy, Supernatural', 'Manga', 0, 0, '2025-11-28 01:55:50'),
(220, 'My Hero Academia', 'my-hero-academia', 'mha.jpg', 'Kohei Horikoshi', 'Ongoing', 'Kisah pahlawan super muda', 'Action, Superhero, School', 'Manga', 0, 0, '2025-11-28 01:55:50'),
(221, 'Dragon Ball', 'dragon-ball', 'dragon_ball.jpg', 'Akira Toriyama', 'Completed', 'Petualangan Goku mencari bola naga', 'Action, Adventure, Martial Arts', 'Manga', 0, 0, '2025-11-28 01:55:50'),
(222, 'Death Note', 'death-note', 'death_note.jpg', 'Tsugumi Ohba', 'Completed', 'Siswa SMA dengan buku kematian', 'Mystery, Psychological, Supernatural', 'Manga', 0, 0, '2025-11-28 01:55:50'),
(223, 'Hunter x Hunter', 'hunter-x-hunter', 'hunter_x_hunter.jpg', 'Yoshihiro Togashi', 'Ongoing', 'Petualangan Gon menjadi Hunter', 'Adventure, Fantasy, Martial Arts', 'Manga', 0, 0, '2025-11-28 01:55:50'),
(224, 'Bleach', 'bleach', 'bleach.jpg', 'Tite Kubo', 'Completed', 'Siswa SMA dengan kekuatan shinigami', 'Action, Supernatural, Adventure', 'Manga', 0, 0, '2025-11-28 01:55:50'),
(225, 'One Punch Man', 'one-punch-man', 'one_punch_man.jpg', 'ONE', 'Ongoing', 'Pahlawan yang bisa mengalahkan musuh dengan satu pukulan', 'Action, Comedy, Superhero', 'Manga', 0, 0, '2025-11-28 01:55:50'),
(226, 'Solo Leveling', 'solo-leveling', 'solo_leveling.jpg', 'Chugong', 'Completed', 'Hunter terlemah yang menjadi paling kuat', 'Action, Adventure, Fantasy', 'Manhwa', 0, 0, '2025-11-28 01:55:50'),
(227, 'Tower of God', 'tower-of-god', 'tower_of_god.jpg', 'SIU', 'Ongoing', 'Pemuda memasuki menara untuk menemukan temannya', 'Adventure, Fantasy, Mystery', 'Manhwa', 0, 0, '2025-11-28 01:55:50'),
(228, 'The Beginning After the End', 'the-beginning-after-the-end', 'tbate.jpg', 'TurtleMe', 'Ongoing', 'Raja bereinkarnasi di dunia fantasi', 'Adventure, Fantasy, Reincarnation', 'Manhwa', 0, 0, '2025-11-28 01:55:50'),
(229, 'Omniscient Reader\'s Viewpoint', 'omniscient-reader-viewpoint', 'orv.jpg', 'singNsong', 'Ongoing', 'Pembaca novel menjadi karakter dalam cerita', 'Action, Adventure, Fantasy', 'Manhwa', 0, 0, '2025-11-28 01:55:50'),
(230, 'Lookism', 'lookism', 'lookism.jpg', 'Taejun Pak', 'Ongoing', 'Remaja memiliki dua tubuh yang berbeda', 'Drama, School, Supernatural', 'Manhwa', 0, 0, '2025-11-28 01:55:50'),
(231, 'Wind Breaker', 'wind-breaker', 'wind_breaker.jpg', 'Yongseok Jo', 'Ongoing', 'Siswa SMA yang ahli bersepeda', 'Action, Sports, School', 'Manhwa', 0, 0, '2025-11-28 01:55:50'),
(232, 'Sweet Home', 'sweet-home', 'sweet_home.jpg', 'Kim Carnby', 'Completed', 'Bertahan hidup dari monster di apartemen', 'Horror, Thriller, Drama', 'Manhwa', 0, 0, '2025-11-28 01:55:50'),
(233, 'Bastard', 'bastard', 'bastard.jpg', 'Kim Carnby', 'Completed', 'Remaja dengan ayah pembunuh berantai', 'Psychological, Thriller, Drama', 'Manhwa', 0, 0, '2025-11-28 01:55:50'),
(234, 'True Beauty', 'true-beauty', 'true_beauty.jpg', 'Yaongyi', 'Ongoing', 'Gadis menggunakan makeup untuk menyembunyikan wajah asli', 'Romance, Drama, School', 'Manhwa', 0, 0, '2025-11-28 01:55:50'),
(235, 'The God of High School', 'the-god-of-high-school', 'goh.jpg', 'Yongje Park', 'Completed', 'Turnamen bela diri untuk siswa SMA', 'Action, Martial Arts, Supernatural', 'Manhwa', 0, 0, '2025-11-28 01:55:50'),
(236, 'The King\'s Avatar', 'the-kings-avatar', 'kings_avatar.jpg', 'Butterfly Blue', 'Completed', 'Legenda game e-sport kembali ke dunia profesional', 'Action, Game, Adventure', 'Manhua', 0, 0, '2025-11-28 01:55:50'),
(237, 'Battle Through the Heavens', 'battle-through-the-heavens', 'battle_heavens.jpg', 'Tian Can Tudou', 'Ongoing', 'Pemuda lemah menjadi master alkimia kuat', 'Action, Adventure, Fantasy', 'Manhua', 0, 0, '2025-11-28 01:55:50'),
(238, 'Soul Land', 'soul-land', 'soul_land.jpg', 'Tang Jia San Shao', 'Ongoing', 'Dunia dimana setiap orang memiliki jiwa martial', 'Action, Adventure, Fantasy', 'Manhua', 0, 0, '2025-11-28 01:55:50'),
(239, 'Star Martial God Technique', 'star-martial-god-technique', 'star_martial.jpg', 'Mad Snail', 'Ongoing', 'Pemuda dengan teknik martial legendaris', 'Action, Adventure, Fantasy', 'Manhua', 0, 0, '2025-11-28 01:55:50'),
(240, 'Tales of Demons and Gods', 'tales-of-demons-and-gods', 'tales_demons.jpg', 'Mad Snail', 'Ongoing', 'Master kuat bereinkarnasi ke masa mudanya', 'Action, Adventure, Fantasy', 'Manhua', 0, 0, '2025-11-28 01:55:50'),
(241, 'The Legendary Moonlight Sculptor', 'the-legendary-moonlight-sculptor', 'moonlight_sculptor.jpg', 'Nam-Hi-Sung', 'Ongoing', 'Pemain game menjadi sculptor legendaris', 'Adventure, Fantasy, Game', 'Manhua', 0, 0, '2025-11-28 01:55:50'),
(242, 'Against the Gods', 'against-the-gods', 'against_gods.jpg', 'Mars Gravity', 'Ongoing', 'Pemuda lemah mendapatkan kekuatan naga', 'Action, Adventure, Fantasy', 'Manhua', 0, 0, '2025-11-28 01:55:50'),
(243, 'Peerless Martial God', 'peerless-martial-god', 'peerless_martial.jpg', 'Jing Hengh', 'Ongoing', 'Pemuda dibuang menjadi martial god', 'Action, Adventure, Fantasy', 'Manhua', 0, 0, '2025-11-28 01:55:50'),
(244, 'Apotheosis', 'apotheosis', 'apotheosis.jpg', 'Kuang Sha', 'Ongoing', 'Budak menjadi dewa melalui cultivation', 'Action, Adventure, Fantasy', 'Manhua', 0, 0, '2025-11-28 01:55:50'),
(245, 'Martial Peak', 'martial-peak', 'martial_peak.jpg', 'Momoo', 'Ongoing', 'Pemuda mengejar puncak dunia martial', 'Action, Adventure, Fantasy', 'Manhua', 0, 0, '2025-11-28 01:55:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'admin', '2025-11-20 06:39:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chapter`
--
ALTER TABLE `chapter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `komik_id` (`komik_id`);

--
-- Indexes for table `chapter_pages`
--
ALTER TABLE `chapter_pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- Indexes for table `komik`
--
ALTER TABLE `komik`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chapter`
--
ALTER TABLE `chapter`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `chapter_pages`
--
ALTER TABLE `chapter_pages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `komik`
--
ALTER TABLE `komik`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chapter`
--
ALTER TABLE `chapter`
  ADD CONSTRAINT `chapter_ibfk_1` FOREIGN KEY (`komik_id`) REFERENCES `komik` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chapter_pages`
--
ALTER TABLE `chapter_pages`
  ADD CONSTRAINT `chapter_pages_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapter` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
