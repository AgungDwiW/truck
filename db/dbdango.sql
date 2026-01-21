-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.5-10.3.16-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             8.0.0.4396
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for dbdango
DROP DATABASE IF EXISTS `dbdango`;
CREATE DATABASE IF NOT EXISTS `dbdango` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `dbdango`;


-- Dumping structure for table dbdango.tbl_actionplant
DROP TABLE IF EXISTS `tbl_actionplant`;
CREATE TABLE IF NOT EXISTS `tbl_actionplant` (
  `seq` int(10) DEFAULT NULL,
  `ref_hasil` int(10) DEFAULT NULL,
  `action_plan` varchar(300) DEFAULT NULL,
  `create_by` varchar(8) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dbdango.tbl_actionplant: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_actionplant` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_actionplant` ENABLE KEYS */;


-- Dumping structure for table dbdango.tbl_hasil
DROP TABLE IF EXISTS `tbl_hasil`;
CREATE TABLE IF NOT EXISTS `tbl_hasil` (
  `seq` int(10) NOT NULL AUTO_INCREMENT,
  `plant_id` varchar(4) NOT NULL DEFAULT '0',
  `area_id` varchar(8) NOT NULL DEFAULT '0',
  `kategori` varchar(50) DEFAULT NULL,
  `temuan` varchar(500) DEFAULT NULL,
  `resiko` varchar(500) DEFAULT NULL,
  `rekomendasi` varchar(500) DEFAULT NULL,
  `keterangan` enum('Y','N') DEFAULT NULL,
  `create_by` varchar(50) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`seq`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dbdango.tbl_hasil: ~0 rows (approximately)
/*!40000 ALTER TABLE `tbl_hasil` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_hasil` ENABLE KEYS */;


-- Dumping structure for table dbdango.tbl_kategori
DROP TABLE IF EXISTS `tbl_kategori`;
CREATE TABLE IF NOT EXISTS `tbl_kategori` (
  `kode` varchar(10) NOT NULL,
  `kategori` varchar(50) DEFAULT '0',
  `aktif` enum('Y','N') DEFAULT 'Y',
  PRIMARY KEY (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dbdango.tbl_kategori: ~11 rows (approximately)
/*!40000 ALTER TABLE `tbl_kategori` DISABLE KEYS */;
REPLACE INTO `tbl_kategori` (`kode`, `kategori`, `aktif`) VALUES
	('FA04', 'FA04 Fixed Asset Mgn', 'Y'),
	('GS08', 'GS08 Good & Service Pricing\'', 'Y'),
	('INV06', 'INV06 Incomming Material', 'Y'),
	('INV07', 'INV07 Incoming Material qty tolerance limits', 'Y'),
	('INV08', 'INV08 R&P Consumption', 'Y'),
	('INV09', 'INV09 Sensitive Movement', 'Y'),
	('INV10', 'INV10 Pro Declaration', 'Y'),
	('INV12', 'INV12 Order & Delivery Handover', 'Y'),
	('INV13', 'INV13 Delivery Handover', 'Y'),
	('INV16', 'INV16 R&P PID', 'Y'),
	('PR17', 'PR17 Travel & Entertainment expenses', 'Y');
/*!40000 ALTER TABLE `tbl_kategori` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
