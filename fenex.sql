-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 20, 2026 at 06:31 PM
-- Server version: 5.7.11
-- PHP Version: 7.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fenex`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `objednavky`
--

CREATE TABLE `objednavky` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`ID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `order_id` (`order_id`),
  FOREIGN KEY (`order_id`) REFERENCES `objednavky` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','read','replied') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produkty`
--

CREATE TABLE `produkty` (
  `ID` int(11) NOT NULL,
  `Meno` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `Popis` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `Cena` float NOT NULL,
  `Obrazok` mediumblob,
  `mime_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produkty`
--

INSERT INTO `produkty` (`ID`, `Meno`, `Popis`, `Cena`, `Obrazok`, `mime_type`) VALUES
(1, 'Tričko_Classic', 'Ľahké a priedušné tričko, ktoré sa hodí ku každému outfitu. Príjemný materiál zabezpečí celodenné pohodlie.', 14.99, NULL, NULL),
(2, 'Mikina_Comfort', 'Pohodlná a mäkká mikina vhodná na každý deň. Moderný strih a kvalitný materiál zaručia dlhú životnosť.', 29.99, NULL, NULL),
(3, 'Teplaky_Realx', 'Štýlové tepláky s príjemným materiálom pre maximálny komfort. Ideálne na šport, domáce nosenie aj voľný čas.', 24.99, NULL, NULL),
(4, 'Zimná_Bunda', 'Teplá zimná bunda do chladného počasia s výbornou izoláciou. Funkčný dizajn ťa ochráni pred vetrom aj snehom.', 69.99, NULL, NULL),
(5, 'Čiapka_Cool', 'Teplá a pohodlná čiapka ideálna do chladných dní. Jednoduchý dizajn doplní každý outfit.', 9.99, NULL, NULL),
(6, 'Tenisky_Speed', 'Ľahké a pohodlné tenisky na celodenné nosenie. Moderný vzhľad a kvalitná podrážka pre skvelý komfort.', 49.99, NULL, NULL),
(7, 'Rukavice_Warm', 'Mäkké a hrejivé rukavice pre maximálnu ochranu v zime. Funkčný strih zabezpečí pohodlie a flexibilitu.', 12.99, NULL, NULL),
(8, 'Batoh_Urban', 'Praktický batoh s veľkým úložným priestorom. Odolný materiál a pohodlné nosenie na každý deň.', 39.99, NULL, NULL),
(9, 'Šiltovka_Street', 'Štýlová šiltovka s nastaviteľným pásikom. Ideálna na slnečné dni aj bežné nosenie.', 14.99, NULL, NULL),
(10, 'Ponožky_Soft', 'Priedušné a pohodlné ponožky na každodenné použitie. Kvalitný materiál drží tvar a odvádza vlhkosť.', 5.99, NULL, NULL),
(11, 'Slúchadlá_Bass', 'Kvalitné slúchadlá s čistým zvukom a pohodlným nosením. Perfektné na hudbu, prácu aj cestovanie.', 59.99, NULL, NULL),
(12, 'Ruksak_Trek', 'Odolný ruksak vhodný do mesta aj prírody. Praktické priečinky udržia tvoje veci prehľadne uložené.', 44.99, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `produkty`
--
ALTER TABLE `produkty`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `produkty`
--
ALTER TABLE `produkty`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
