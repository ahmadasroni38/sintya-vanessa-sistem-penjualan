/*
SQLyog Community v13.2.0 (64 bit)
MySQL - 8.0.44-0ubuntu0.22.04.1 : Database - vassion_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`vassion_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `vassion_db`;

/*Data for the table `account_balance_histories` */

insert  into `account_balance_histories`(`id`,`chart_of_account_id`,`balance`,`debit_total`,`credit_total`,`period_start`,`period_end`,`calculated_by`,`created_at`,`updated_at`) values 
(1,1,0.00,0.00,0.00,'2026-01-01','2026-01-31','1','2026-01-12 12:07:02','2026-01-12 12:29:56'),
(3,2,20000000.00,20000000.00,0.00,'2026-01-01','2026-01-31','1','2026-01-12 12:30:06','2026-01-12 12:31:48'),
(4,3,0.00,0.00,0.00,'2026-01-01','2026-01-31','1','2026-01-12 12:30:28','2026-01-12 12:30:28'),
(5,33,0.00,0.00,0.00,'2026-01-01','2026-01-31','1','2026-01-12 12:44:47','2026-01-12 12:44:47'),
(6,34,0.00,0.00,0.00,'2026-01-01','2026-01-31','1','2026-01-12 12:44:54','2026-01-12 12:44:54'),
(7,35,50000000.00,50000000.00,0.00,'2026-01-01','2026-01-31','1','2026-01-12 12:45:00','2026-01-12 12:45:00');

/*Data for the table `chart_of_account_audits` */

insert  into `chart_of_account_audits`(`id`,`chart_of_account_id`,`event_type`,`old_values`,`new_values`,`user_id`,`user_name`,`ip_address`,`user_agent`,`created_at`,`updated_at`) values 
(1,33,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-0000\",\"account_name\":\"AKTIVA\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"parent_id\":null,\"level\":1,\"updated_at\":\"2026-01-12 12:42:13\",\"created_at\":\"2026-01-12 12:42:13\",\"id\":33,\"balance_updated_at\":\"2026-01-12 12:42:13\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:13','2026-01-12 12:42:13'),
(2,34,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-1000\",\"account_name\":\"AKTIVA LANCAR\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"parent_id\":33,\"level\":2,\"updated_at\":\"2026-01-12 12:42:13\",\"created_at\":\"2026-01-12 12:42:13\",\"id\":34,\"balance_updated_at\":\"2026-01-12 12:42:13\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:13','2026-01-12 12:42:13'),
(3,35,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-1100\",\"account_name\":\"Kas\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"parent_id\":34,\"level\":3,\"updated_at\":\"2026-01-12 12:42:13\",\"created_at\":\"2026-01-12 12:42:13\",\"id\":35,\"balance_updated_at\":\"2026-01-12 12:42:13\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:13','2026-01-12 12:42:13'),
(4,36,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-1200\",\"account_name\":\"Bank\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"parent_id\":34,\"level\":3,\"updated_at\":\"2026-01-12 12:42:14\",\"created_at\":\"2026-01-12 12:42:14\",\"id\":36,\"balance_updated_at\":\"2026-01-12 12:42:14\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:14','2026-01-12 12:42:14'),
(5,37,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-1300\",\"account_name\":\"Piutang Usaha\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"parent_id\":34,\"level\":3,\"updated_at\":\"2026-01-12 12:42:14\",\"created_at\":\"2026-01-12 12:42:14\",\"id\":37,\"balance_updated_at\":\"2026-01-12 12:42:14\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:14','2026-01-12 12:42:14'),
(6,38,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-1400\",\"account_name\":\"Persediaan Barang\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"parent_id\":34,\"level\":3,\"updated_at\":\"2026-01-12 12:42:14\",\"created_at\":\"2026-01-12 12:42:14\",\"id\":38,\"balance_updated_at\":\"2026-01-12 12:42:14\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:14','2026-01-12 12:42:14'),
(7,39,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-1500\",\"account_name\":\"Perlengkapan\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"parent_id\":34,\"level\":3,\"updated_at\":\"2026-01-12 12:42:14\",\"created_at\":\"2026-01-12 12:42:14\",\"id\":39,\"balance_updated_at\":\"2026-01-12 12:42:14\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:14','2026-01-12 12:42:14'),
(8,40,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-2000\",\"account_name\":\"AKTIVA TETAP\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"parent_id\":33,\"level\":2,\"updated_at\":\"2026-01-12 12:42:14\",\"created_at\":\"2026-01-12 12:42:14\",\"id\":40,\"balance_updated_at\":\"2026-01-12 12:42:14\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:15','2026-01-12 12:42:15'),
(9,41,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-2100\",\"account_name\":\"Tanah\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"parent_id\":40,\"level\":3,\"updated_at\":\"2026-01-12 12:42:15\",\"created_at\":\"2026-01-12 12:42:15\",\"id\":41,\"balance_updated_at\":\"2026-01-12 12:42:15\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:15','2026-01-12 12:42:15'),
(10,42,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-2200\",\"account_name\":\"Bangunan\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"parent_id\":40,\"level\":3,\"updated_at\":\"2026-01-12 12:42:15\",\"created_at\":\"2026-01-12 12:42:15\",\"id\":42,\"balance_updated_at\":\"2026-01-12 12:42:15\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:15','2026-01-12 12:42:15'),
(11,43,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-2210\",\"account_name\":\"Akumulasi Penyusutan Bangunan\",\"account_type\":\"asset\",\"normal_balance\":\"credit\",\"parent_id\":40,\"level\":3,\"updated_at\":\"2026-01-12 12:42:15\",\"created_at\":\"2026-01-12 12:42:15\",\"id\":43,\"balance_updated_at\":\"2026-01-12 12:42:15\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:15','2026-01-12 12:42:15'),
(12,44,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-2300\",\"account_name\":\"Peralatan\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"parent_id\":40,\"level\":3,\"updated_at\":\"2026-01-12 12:42:15\",\"created_at\":\"2026-01-12 12:42:15\",\"id\":44,\"balance_updated_at\":\"2026-01-12 12:42:15\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:15','2026-01-12 12:42:15'),
(13,45,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-2310\",\"account_name\":\"Akumulasi Penyusutan Peralatan\",\"account_type\":\"asset\",\"normal_balance\":\"credit\",\"parent_id\":40,\"level\":3,\"updated_at\":\"2026-01-12 12:42:15\",\"created_at\":\"2026-01-12 12:42:15\",\"id\":45,\"balance_updated_at\":\"2026-01-12 12:42:15\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:16','2026-01-12 12:42:16'),
(14,46,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-2400\",\"account_name\":\"Kendaraan\",\"account_type\":\"asset\",\"normal_balance\":\"debit\",\"parent_id\":40,\"level\":3,\"updated_at\":\"2026-01-12 12:42:16\",\"created_at\":\"2026-01-12 12:42:16\",\"id\":46,\"balance_updated_at\":\"2026-01-12 12:42:16\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:16','2026-01-12 12:42:16'),
(15,47,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"1-2410\",\"account_name\":\"Akumulasi Penyusutan Kendaraan\",\"account_type\":\"asset\",\"normal_balance\":\"credit\",\"parent_id\":40,\"level\":3,\"updated_at\":\"2026-01-12 12:42:16\",\"created_at\":\"2026-01-12 12:42:16\",\"id\":47,\"balance_updated_at\":\"2026-01-12 12:42:16\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:16','2026-01-12 12:42:16'),
(16,48,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"2-0000\",\"account_name\":\"KEWAJIBAN\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"parent_id\":null,\"level\":1,\"updated_at\":\"2026-01-12 12:42:16\",\"created_at\":\"2026-01-12 12:42:16\",\"id\":48,\"balance_updated_at\":\"2026-01-12 12:42:16\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:16','2026-01-12 12:42:16'),
(17,49,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"2-1000\",\"account_name\":\"KEWAJIBAN LANCAR\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"parent_id\":48,\"level\":2,\"updated_at\":\"2026-01-12 12:42:16\",\"created_at\":\"2026-01-12 12:42:16\",\"id\":49,\"balance_updated_at\":\"2026-01-12 12:42:16\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:16','2026-01-12 12:42:16'),
(18,50,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"2-1100\",\"account_name\":\"Hutang Usaha\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"parent_id\":49,\"level\":3,\"updated_at\":\"2026-01-12 12:42:16\",\"created_at\":\"2026-01-12 12:42:16\",\"id\":50,\"balance_updated_at\":\"2026-01-12 12:42:16\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:17','2026-01-12 12:42:17'),
(19,51,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"2-1200\",\"account_name\":\"Hutang Gaji\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"parent_id\":49,\"level\":3,\"updated_at\":\"2026-01-12 12:42:17\",\"created_at\":\"2026-01-12 12:42:17\",\"id\":51,\"balance_updated_at\":\"2026-01-12 12:42:17\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:17','2026-01-12 12:42:17'),
(20,52,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"2-1300\",\"account_name\":\"Hutang Pajak\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"parent_id\":49,\"level\":3,\"updated_at\":\"2026-01-12 12:42:17\",\"created_at\":\"2026-01-12 12:42:17\",\"id\":52,\"balance_updated_at\":\"2026-01-12 12:42:17\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:17','2026-01-12 12:42:17'),
(21,53,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"2-2000\",\"account_name\":\"KEWAJIBAN JANGKA PANJANG\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"parent_id\":48,\"level\":2,\"updated_at\":\"2026-01-12 12:42:17\",\"created_at\":\"2026-01-12 12:42:17\",\"id\":53,\"balance_updated_at\":\"2026-01-12 12:42:17\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:17','2026-01-12 12:42:17'),
(22,54,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"2-2100\",\"account_name\":\"Hutang Bank\",\"account_type\":\"liability\",\"normal_balance\":\"credit\",\"parent_id\":53,\"level\":3,\"updated_at\":\"2026-01-12 12:42:17\",\"created_at\":\"2026-01-12 12:42:17\",\"id\":54,\"balance_updated_at\":\"2026-01-12 12:42:17\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:17','2026-01-12 12:42:17'),
(23,55,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"3-0000\",\"account_name\":\"MODAL\",\"account_type\":\"equity\",\"normal_balance\":\"credit\",\"parent_id\":null,\"level\":1,\"updated_at\":\"2026-01-12 12:42:17\",\"created_at\":\"2026-01-12 12:42:17\",\"id\":55,\"balance_updated_at\":\"2026-01-12 12:42:17\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:18','2026-01-12 12:42:18'),
(24,56,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"3-1000\",\"account_name\":\"Modal Pemilik\",\"account_type\":\"equity\",\"normal_balance\":\"credit\",\"parent_id\":55,\"level\":2,\"updated_at\":\"2026-01-12 12:42:18\",\"created_at\":\"2026-01-12 12:42:18\",\"id\":56,\"balance_updated_at\":\"2026-01-12 12:42:18\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:18','2026-01-12 12:42:18'),
(25,57,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"3-2000\",\"account_name\":\"Prive\",\"account_type\":\"equity\",\"normal_balance\":\"debit\",\"parent_id\":55,\"level\":2,\"updated_at\":\"2026-01-12 12:42:18\",\"created_at\":\"2026-01-12 12:42:18\",\"id\":57,\"balance_updated_at\":\"2026-01-12 12:42:18\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:18','2026-01-12 12:42:18'),
(26,58,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"3-3000\",\"account_name\":\"Laba Ditahan\",\"account_type\":\"equity\",\"normal_balance\":\"credit\",\"parent_id\":55,\"level\":2,\"updated_at\":\"2026-01-12 12:42:18\",\"created_at\":\"2026-01-12 12:42:18\",\"id\":58,\"balance_updated_at\":\"2026-01-12 12:42:18\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:18','2026-01-12 12:42:18'),
(27,59,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"4-0000\",\"account_name\":\"PENDAPATAN\",\"account_type\":\"revenue\",\"normal_balance\":\"credit\",\"parent_id\":null,\"level\":1,\"updated_at\":\"2026-01-12 12:42:18\",\"created_at\":\"2026-01-12 12:42:18\",\"id\":59,\"balance_updated_at\":\"2026-01-12 12:42:18\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:18','2026-01-12 12:42:18'),
(28,60,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"4-1000\",\"account_name\":\"Pendapatan Penjualan\",\"account_type\":\"revenue\",\"normal_balance\":\"credit\",\"parent_id\":59,\"level\":2,\"updated_at\":\"2026-01-12 12:42:18\",\"created_at\":\"2026-01-12 12:42:18\",\"id\":60,\"balance_updated_at\":\"2026-01-12 12:42:18\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:19','2026-01-12 12:42:19'),
(29,61,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"4-2000\",\"account_name\":\"Pendapatan Jasa\",\"account_type\":\"revenue\",\"normal_balance\":\"credit\",\"parent_id\":59,\"level\":2,\"updated_at\":\"2026-01-12 12:42:19\",\"created_at\":\"2026-01-12 12:42:19\",\"id\":61,\"balance_updated_at\":\"2026-01-12 12:42:19\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:19','2026-01-12 12:42:19'),
(30,62,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"4-3000\",\"account_name\":\"Pendapatan Lain-lain\",\"account_type\":\"revenue\",\"normal_balance\":\"credit\",\"parent_id\":59,\"level\":2,\"updated_at\":\"2026-01-12 12:42:19\",\"created_at\":\"2026-01-12 12:42:19\",\"id\":62,\"balance_updated_at\":\"2026-01-12 12:42:19\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:19','2026-01-12 12:42:19'),
(31,63,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"5-0000\",\"account_name\":\"BEBAN\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"parent_id\":null,\"level\":1,\"updated_at\":\"2026-01-12 12:42:19\",\"created_at\":\"2026-01-12 12:42:19\",\"id\":63,\"balance_updated_at\":\"2026-01-12 12:42:19\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:19','2026-01-12 12:42:19'),
(32,64,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"5-1000\",\"account_name\":\"BEBAN OPERASIONAL\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"parent_id\":63,\"level\":2,\"updated_at\":\"2026-01-12 12:42:19\",\"created_at\":\"2026-01-12 12:42:19\",\"id\":64,\"balance_updated_at\":\"2026-01-12 12:42:19\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:19','2026-01-12 12:42:19'),
(33,65,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"5-1100\",\"account_name\":\"Beban Gaji\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"parent_id\":64,\"level\":3,\"updated_at\":\"2026-01-12 12:42:19\",\"created_at\":\"2026-01-12 12:42:19\",\"id\":65,\"balance_updated_at\":\"2026-01-12 12:42:19\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:20','2026-01-12 12:42:20'),
(34,66,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"5-1200\",\"account_name\":\"Beban Listrik\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"parent_id\":64,\"level\":3,\"updated_at\":\"2026-01-12 12:42:20\",\"created_at\":\"2026-01-12 12:42:20\",\"id\":66,\"balance_updated_at\":\"2026-01-12 12:42:20\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:20','2026-01-12 12:42:20'),
(35,67,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"5-1300\",\"account_name\":\"Beban Air\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"parent_id\":64,\"level\":3,\"updated_at\":\"2026-01-12 12:42:20\",\"created_at\":\"2026-01-12 12:42:20\",\"id\":67,\"balance_updated_at\":\"2026-01-12 12:42:20\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:20','2026-01-12 12:42:20'),
(36,68,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"5-1400\",\"account_name\":\"Beban Telepon\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"parent_id\":64,\"level\":3,\"updated_at\":\"2026-01-12 12:42:20\",\"created_at\":\"2026-01-12 12:42:20\",\"id\":68,\"balance_updated_at\":\"2026-01-12 12:42:20\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:20','2026-01-12 12:42:20'),
(37,69,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"5-1500\",\"account_name\":\"Beban Transportasi\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"parent_id\":64,\"level\":3,\"updated_at\":\"2026-01-12 12:42:20\",\"created_at\":\"2026-01-12 12:42:20\",\"id\":69,\"balance_updated_at\":\"2026-01-12 12:42:20\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:20','2026-01-12 12:42:20'),
(38,70,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"5-1600\",\"account_name\":\"Beban Perlengkapan\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"parent_id\":64,\"level\":3,\"updated_at\":\"2026-01-12 12:42:20\",\"created_at\":\"2026-01-12 12:42:20\",\"id\":70,\"balance_updated_at\":\"2026-01-12 12:42:20\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:21','2026-01-12 12:42:21'),
(39,71,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"5-1700\",\"account_name\":\"Beban Penyusutan\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"parent_id\":64,\"level\":3,\"updated_at\":\"2026-01-12 12:42:21\",\"created_at\":\"2026-01-12 12:42:21\",\"id\":71,\"balance_updated_at\":\"2026-01-12 12:42:21\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:21','2026-01-12 12:42:21'),
(40,72,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"5-2000\",\"account_name\":\"BEBAN LAIN-LAIN\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"parent_id\":63,\"level\":2,\"updated_at\":\"2026-01-12 12:42:21\",\"created_at\":\"2026-01-12 12:42:21\",\"id\":72,\"balance_updated_at\":\"2026-01-12 12:42:21\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:21','2026-01-12 12:42:21'),
(41,73,'created',NULL,'{\"is_active\":true,\"opening_balance\":0,\"current_balance\":0,\"account_code\":\"5-2100\",\"account_name\":\"Beban Bunga\",\"account_type\":\"expense\",\"normal_balance\":\"debit\",\"parent_id\":72,\"level\":3,\"updated_at\":\"2026-01-12 12:42:21\",\"created_at\":\"2026-01-12 12:42:21\",\"id\":73,\"balance_updated_at\":\"2026-01-12 12:42:21\"}',NULL,NULL,'127.0.0.1','Symfony','2026-01-12 12:42:21','2026-01-12 12:42:21');

/*Data for the table `chart_of_accounts` */

insert  into `chart_of_accounts`(`id`,`account_code`,`account_name`,`account_type`,`normal_balance`,`parent_id`,`level`,`is_active`,`description`,`metadata`,`opening_balance`,`current_balance`,`balance_updated_at`,`created_at`,`updated_at`,`created_by`,`updated_by`,`deleted_at`) values 
(1,'1100','Aset Lancar','asset','debit',NULL,1,1,'Kelompok aset lancar',NULL,0.00,0.00,'2026-01-12 12:29:57','2026-01-12 10:16:20','2026-01-12 12:29:57','system','system',NULL),
(2,'1101','Kas','asset','debit',NULL,2,1,'Kas di toko',NULL,0.00,0.00,'2026-01-12 13:25:44','2026-01-12 10:16:20','2026-01-12 13:25:44','system','system',NULL),
(3,'1102','Kas di Bank','asset','debit',NULL,2,1,'Saldo rekening bank',NULL,0.00,0.00,'2026-01-12 12:30:28','2026-01-12 10:16:20','2026-01-12 12:30:28','system','system',NULL),
(4,'1103','Piutang Usaha','asset','debit',NULL,2,1,'Piutang pelanggan',NULL,0.00,0.00,NULL,'2026-01-12 10:16:20',NULL,'system','system',NULL),
(5,'1104','Persediaan Barang Dagang','asset','debit',NULL,2,1,'Stok produk kecantikan',NULL,0.00,0.00,NULL,'2026-01-12 10:16:20',NULL,'system','system',NULL),
(6,'1105','Uang Muka Sewa','asset','debit',NULL,2,1,'Uang muka sewa toko mall',NULL,0.00,0.00,NULL,'2026-01-12 10:16:20',NULL,'system','system',NULL),
(7,'1200','Aset Tetap','asset','debit',NULL,1,1,'Kelompok aset tetap',NULL,0.00,0.00,NULL,'2026-01-12 10:16:20',NULL,'system','system',NULL),
(8,'1201','Peralatan Toko','asset','debit',NULL,2,1,'Rak display, etalase',NULL,0.00,0.00,NULL,'2026-01-12 10:16:20',NULL,'system','system',NULL),
(9,'1202','Perlengkapan Toko','asset','debit',NULL,2,1,'Perlengkapan operasional',NULL,0.00,0.00,NULL,'2026-01-12 10:16:20',NULL,'system','system',NULL),
(10,'1203','Akumulasi Penyusutan Aset Tetap','asset','credit',NULL,2,1,'Akumulasi penyusutan',NULL,0.00,0.00,NULL,'2026-01-12 10:16:20',NULL,'system','system',NULL),
(11,'2100','Liabilitas Jangka Pendek','liability','credit',NULL,1,1,'Kewajiban jangka pendek',NULL,0.00,0.00,NULL,'2026-01-12 10:18:32',NULL,'system','system',NULL),
(12,'2101','Utang Usaha','liability','credit',NULL,2,1,'Utang ke supplier',NULL,0.00,0.00,NULL,'2026-01-12 10:18:32',NULL,'system','system',NULL),
(13,'2102','Utang Sewa Toko','liability','credit',NULL,2,1,'Sewa toko mall',NULL,0.00,0.00,NULL,'2026-01-12 10:18:32',NULL,'system','system',NULL),
(14,'2103','Utang Pajak','liability','credit',NULL,2,1,'Pajak terutang',NULL,0.00,0.00,NULL,'2026-01-12 10:18:32',NULL,'system','system',NULL),
(15,'2104','Utang Gaji','liability','credit',NULL,2,1,'Gaji karyawan belum dibayar',NULL,0.00,0.00,NULL,'2026-01-12 10:18:32',NULL,'system','system',NULL),
(16,'3100','Ekuitas','equity','credit',NULL,1,1,'Modal pemilik usaha',NULL,0.00,0.00,NULL,'2026-01-12 10:18:39',NULL,'system','system',NULL),
(17,'3101','Modal Pemilik','equity','credit',NULL,2,1,'Setoran modal awal',NULL,0.00,0.00,'2026-01-12 13:25:44','2026-01-12 10:18:39','2026-01-12 13:25:44','system','system',NULL),
(18,'3102','Prive Pemilik','equity','debit',NULL,2,1,'Pengambilan pribadi pemilik',NULL,0.00,0.00,NULL,'2026-01-12 10:18:39',NULL,'system','system',NULL),
(19,'3103','Laba Ditahan','equity','credit',NULL,2,1,'Akumulasi laba usaha',NULL,0.00,0.00,NULL,'2026-01-12 10:18:39',NULL,'system','system',NULL),
(20,'4100','Pendapatan','revenue','credit',NULL,1,1,'Pendapatan usaha',NULL,0.00,0.00,NULL,'2026-01-12 10:18:53',NULL,'system','system',NULL),
(21,'4101','Penjualan Produk Kecantikan','revenue','credit',NULL,2,1,'Penjualan skincare & kosmetik',NULL,0.00,1000000.00,'2026-01-12 15:22:48','2026-01-12 10:18:53','2026-01-12 15:22:48','system','system',NULL),
(22,'4102','Diskon Penjualan','revenue','debit',NULL,2,1,'Diskon kepada pelanggan',NULL,0.00,0.00,NULL,'2026-01-12 10:18:53',NULL,'system','system',NULL),
(23,'4103','Pendapatan Lain-lain','revenue','credit',NULL,2,1,'Pendapatan di luar usaha utama',NULL,0.00,0.00,NULL,'2026-01-12 10:18:53',NULL,'system','system',NULL),
(24,'5100','Beban Operasional','expense','debit',NULL,1,1,'Beban operasional usaha',NULL,0.00,0.00,NULL,'2026-01-12 10:19:01',NULL,'system','system',NULL),
(25,'5101','Beban Harga Pokok Penjualan','expense','debit',NULL,2,1,'HPP produk kecantikan',NULL,0.00,0.00,NULL,'2026-01-12 10:19:01',NULL,'system','system',NULL),
(26,'5102','Beban Sewa Toko','expense','debit',NULL,2,1,'Sewa toko mall',NULL,0.00,0.00,NULL,'2026-01-12 10:19:01',NULL,'system','system',NULL),
(27,'5103','Beban Gaji Karyawan','expense','debit',NULL,2,1,'Gaji penjaga toko',NULL,0.00,0.00,NULL,'2026-01-12 10:19:01',NULL,'system','system',NULL),
(28,'5104','Beban Listrik & Air','expense','debit',NULL,2,1,'Biaya utilitas toko',NULL,0.00,0.00,NULL,'2026-01-12 10:19:01',NULL,'system','system',NULL),
(29,'5105','Beban Promosi & Marketing','expense','debit',NULL,2,1,'Iklan & promo',NULL,0.00,0.00,NULL,'2026-01-12 10:19:01',NULL,'system','system',NULL),
(30,'5106','Beban Perlengkapan','expense','debit',NULL,2,1,'Pemakaian perlengkapan',NULL,0.00,0.00,NULL,'2026-01-12 10:19:01',NULL,'system','system',NULL),
(31,'5107','Beban Penyusutan Aset','expense','debit',NULL,2,1,'Penyusutan aset tetap',NULL,0.00,0.00,NULL,'2026-01-12 10:19:01',NULL,'system','system',NULL),
(32,'5108','Beban Administrasi','expense','debit',NULL,2,1,'ATK & administrasi',NULL,0.00,0.00,NULL,'2026-01-12 10:19:01',NULL,'system','system',NULL),
(33,'1-0000','AKTIVA','asset','debit',NULL,1,1,NULL,NULL,0.00,0.00,'2026-01-12 12:44:48','2026-01-12 12:42:13','2026-01-12 12:44:48','system','system',NULL),
(34,'1-1000','AKTIVA LANCAR','asset','debit',33,2,1,NULL,NULL,0.00,0.00,'2026-01-12 12:44:54','2026-01-12 12:42:13','2026-01-12 12:44:54','system','system',NULL),
(35,'1-1100','Kas','asset','debit',34,3,1,NULL,NULL,0.00,51160000.00,'2026-01-12 22:58:41','2026-01-12 12:42:13','2026-01-12 22:58:41','system','system',NULL),
(36,'1-1200','Bank','asset','debit',34,3,1,NULL,NULL,0.00,200000000.00,'2026-01-12 14:07:08','2026-01-12 12:42:14','2026-01-12 14:07:08','system','system',NULL),
(37,'1-1300','Piutang Usaha','asset','debit',34,3,1,NULL,NULL,0.00,75000000.00,'2026-01-12 14:07:08','2026-01-12 12:42:14','2026-01-12 14:07:08','system','system',NULL),
(38,'1-1400','Persediaan Barang','asset','debit',34,3,1,NULL,NULL,0.00,150000000.00,'2026-01-12 14:07:08','2026-01-12 12:42:14','2026-01-12 14:07:08','system','system',NULL),
(39,'1-1500','Perlengkapan','asset','debit',34,3,1,NULL,NULL,0.00,10000000.00,'2026-01-12 14:07:09','2026-01-12 12:42:14','2026-01-12 14:07:09','system','system',NULL),
(40,'1-2000','AKTIVA TETAP','asset','debit',33,2,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:14','2026-01-12 12:42:14','2026-01-12 12:42:14','system','system',NULL),
(41,'1-2100','Tanah','asset','debit',40,3,1,NULL,NULL,0.00,200000000.00,'2026-01-12 14:07:09','2026-01-12 12:42:15','2026-01-12 14:07:09','system','system',NULL),
(42,'1-2200','Bangunan','asset','debit',40,3,1,NULL,NULL,0.00,300000000.00,'2026-01-12 14:07:09','2026-01-12 12:42:15','2026-01-12 14:07:09','system','system',NULL),
(43,'1-2210','Akumulasi Penyusutan Bangunan','asset','credit',40,3,1,NULL,NULL,0.00,50000000.00,'2026-01-12 14:07:09','2026-01-12 12:42:15','2026-01-12 14:07:09','system','system',NULL),
(44,'1-2300','Peralatan','asset','debit',40,3,1,NULL,NULL,0.00,100000000.00,'2026-01-12 14:07:09','2026-01-12 12:42:15','2026-01-12 14:07:09','system','system',NULL),
(45,'1-2310','Akumulasi Penyusutan Peralatan','asset','credit',40,3,1,NULL,NULL,0.00,20000000.00,'2026-01-12 14:07:09','2026-01-12 12:42:15','2026-01-12 14:07:09','system','system',NULL),
(46,'1-2400','Kendaraan','asset','debit',40,3,1,NULL,NULL,0.00,150000000.00,'2026-01-12 14:07:09','2026-01-12 12:42:16','2026-01-12 14:07:09','system','system',NULL),
(47,'1-2410','Akumulasi Penyusutan Kendaraan','asset','credit',40,3,1,NULL,NULL,0.00,30000000.00,'2026-01-12 14:07:09','2026-01-12 12:42:16','2026-01-12 14:07:09','system','system',NULL),
(48,'2-0000','KEWAJIBAN','liability','credit',NULL,1,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:16','2026-01-12 12:42:16','2026-01-12 12:42:16','system','system',NULL),
(49,'2-1000','KEWAJIBAN LANCAR','liability','credit',48,2,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:16','2026-01-12 12:42:16','2026-01-12 12:42:16','system','system',NULL),
(50,'2-1100','Hutang Usaha','liability','credit',49,3,1,NULL,NULL,0.00,50000000.00,'2026-01-12 14:07:09','2026-01-12 12:42:16','2026-01-12 14:07:09','system','system',NULL),
(51,'2-1200','Hutang Gaji','liability','credit',49,3,1,NULL,NULL,0.00,15000000.00,'2026-01-12 14:07:09','2026-01-12 12:42:17','2026-01-12 14:07:09','system','system',NULL),
(52,'2-1300','Hutang Pajak','liability','credit',49,3,1,NULL,NULL,0.00,10000000.00,'2026-01-12 14:07:10','2026-01-12 12:42:17','2026-01-12 14:07:10','system','system',NULL),
(53,'2-2000','KEWAJIBAN JANGKA PANJANG','liability','credit',48,2,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:17','2026-01-12 12:42:17','2026-01-12 12:42:17','system','system',NULL),
(54,'2-2100','Hutang Bank','liability','credit',53,3,1,NULL,NULL,0.00,100000000.00,'2026-01-12 14:07:10','2026-01-12 12:42:17','2026-01-12 14:07:10','system','system',NULL),
(55,'3-0000','MODAL','equity','credit',NULL,1,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:17','2026-01-12 12:42:17','2026-01-12 12:42:17','system','system',NULL),
(56,'3-1000','Modal Pemilik','equity','credit',55,2,1,NULL,NULL,0.00,960000000.00,'2026-01-12 14:07:10','2026-01-12 12:42:18','2026-01-12 14:07:10','system','system',NULL),
(57,'3-2000','Prive','equity','debit',55,2,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:18','2026-01-12 12:42:18','2026-01-12 12:42:18','system','system',NULL),
(58,'3-3000','Laba Ditahan','equity','credit',55,2,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:18','2026-01-12 12:42:18','2026-01-12 12:42:18','system','system',NULL),
(59,'4-0000','PENDAPATAN','revenue','credit',NULL,1,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:18','2026-01-12 12:42:18','2026-01-12 12:42:18','system','system',NULL),
(60,'4-1000','Pendapatan Penjualan','revenue','credit',59,2,1,NULL,NULL,0.00,160000.00,'2026-01-12 22:58:41','2026-01-12 12:42:18','2026-01-12 22:58:41','system','system',NULL),
(61,'4-2000','Pendapatan Jasa','revenue','credit',59,2,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:19','2026-01-12 12:42:19','2026-01-12 12:42:19','system','system',NULL),
(62,'4-3000','Pendapatan Lain-lain','revenue','credit',59,2,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:19','2026-01-12 12:42:19','2026-01-12 12:42:19','system','system',NULL),
(63,'5-0000','BEBAN','expense','debit',NULL,1,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:19','2026-01-12 12:42:19','2026-01-12 12:42:19','system','system',NULL),
(64,'5-1000','BEBAN OPERASIONAL','expense','debit',63,2,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:19','2026-01-12 12:42:19','2026-01-12 12:42:19','system','system',NULL),
(65,'5-1100','Beban Gaji','expense','debit',64,3,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:19','2026-01-12 12:42:19','2026-01-12 12:42:19','system','system',NULL),
(66,'5-1200','Beban Listrik','expense','debit',64,3,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:20','2026-01-12 12:42:20','2026-01-12 12:42:20','system','system',NULL),
(67,'5-1300','Beban Air','expense','debit',64,3,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:20','2026-01-12 12:42:20','2026-01-12 12:42:20','system','system',NULL),
(68,'5-1400','Beban Telepon','expense','debit',64,3,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:20','2026-01-12 12:42:20','2026-01-12 12:42:20','system','system',NULL),
(69,'5-1500','Beban Transportasi','expense','debit',64,3,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:20','2026-01-12 12:42:20','2026-01-12 12:42:20','system','system',NULL),
(70,'5-1600','Beban Perlengkapan','expense','debit',64,3,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:20','2026-01-12 12:42:20','2026-01-12 12:42:20','system','system',NULL),
(71,'5-1700','Beban Penyusutan','expense','debit',64,3,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:21','2026-01-12 12:42:21','2026-01-12 12:42:21','system','system',NULL),
(72,'5-2000','BEBAN LAIN-LAIN','expense','debit',63,2,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:21','2026-01-12 12:42:21','2026-01-12 12:42:21','system','system',NULL),
(73,'5-2100','Beban Bunga','expense','debit',72,3,1,NULL,NULL,0.00,0.00,'2026-01-12 12:42:21','2026-01-12 12:42:21','2026-01-12 12:42:21','system','system',NULL);

/*Data for the table `customers` */

insert  into `customers`(`id`,`customer_code`,`customer_name`,`email`,`phone`,`address`,`city`,`province`,`postal_code`,`contact_person`,`contact_person_phone`,`credit_limit`,`current_balance`,`status`,`notes`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'CUS0001','Test Customer','test@example.com','08123456789',NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,'active',NULL,'2026-01-04 09:54:56','2026-01-04 09:54:56',NULL);

/*Data for the table `failed_jobs` */

/*Data for the table `journal_entries` */

insert  into `journal_entries`(`id`,`entry_number`,`entry_date`,`reference_number`,`description`,`entry_type`,`status`,`total_debit`,`total_credit`,`currency`,`exchange_rate`,`created_by`,`posted_by`,`posted_at`,`approved_at`,`metadata`,`created_at`,`updated_at`,`deleted_at`,`approved_by`,`updated_by`) values 
(1,'JE-202600001','2026-01-12','TEST-001','Test Journal Entry','general','draft',0.00,0.00,'IDR',1.0000,1,NULL,NULL,NULL,NULL,'2026-01-12 06:33:53','2026-01-12 13:25:36','2026-01-12 13:25:36',NULL,NULL),
(3,'JE-202600002','2026-01-01','N00123','Setor Modal Awal Pemilik Utama','general','cancelled',0.00,0.00,'IDR',1.0000,1,1,'2026-01-12 12:31:21',NULL,NULL,'2026-01-12 12:09:30','2026-01-12 13:25:44',NULL,NULL,NULL),
(8,'JE-202500001','2025-01-01','OB-2025','Jurnal Saldo Awal per 1 Januari 2025','opening','posted',1235000000.00,1235000000.00,'IDR',1.0000,1,1,'2026-01-12 14:07:08',NULL,NULL,'2026-01-12 12:43:02','2026-01-12 14:07:08',NULL,NULL,NULL),
(10,'JE-202600003','2026-01-12','HBH76767','Pendapatan Testing','general','posted',1000000.00,1000000.00,'IDR',1.0000,1,1,'2026-01-12 15:22:48',NULL,NULL,'2026-01-12 15:21:59','2026-01-12 15:22:48',NULL,NULL,NULL),
(11,'JE-202600004','2026-01-12','SO-2026-00001','Sales Transaction - SO-2026-00001 - Test Customer','general','posted',0.00,0.00,'IDR',1.0000,1,1,'2026-01-12 22:58:40',NULL,NULL,'2026-01-12 22:58:41','2026-01-12 22:58:41',NULL,NULL,NULL);

/*Data for the table `journal_entry_approvals` */

/*Data for the table `journal_entry_attachments` */

/*Data for the table `journal_entry_details` */

insert  into `journal_entry_details`(`id`,`journal_entry_id`,`account_id`,`transaction_type`,`amount`,`debit_amount`,`credit_amount`,`quantity`,`unit_price`,`tax_rate`,`tax_amount`,`reconciliation_id`,`description`,`created_at`,`updated_at`,`department_id`,`project_id`,`deleted_at`) values 
(1,1,2,'debit',1000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Test Debit','2026-01-12 06:33:53','2026-01-12 13:25:36',NULL,NULL,'2026-01-12 13:25:36'),
(2,1,1,'credit',1000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Test Credit','2026-01-12 06:33:53','2026-01-12 13:25:36',NULL,NULL,'2026-01-12 13:25:36'),
(5,3,2,'debit',20000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Modal Awal','2026-01-12 12:09:30','2026-01-12 12:31:13',NULL,NULL,'2026-01-12 12:31:13'),
(6,3,17,'credit',20000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Modal Awal','2026-01-12 12:09:30','2026-01-12 12:31:13',NULL,NULL,'2026-01-12 12:31:13'),
(7,3,2,'debit',20000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Modal Awal','2026-01-12 12:31:13','2026-01-12 12:31:13',NULL,NULL,NULL),
(8,3,17,'credit',20000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Modal Awal','2026-01-12 12:31:13','2026-01-12 12:31:13',NULL,NULL,NULL),
(9,8,35,'debit',50000000.00,50000000.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Kas','2026-01-12 12:43:02','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(10,8,36,'debit',200000000.00,200000000.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Bank','2026-01-12 12:43:03','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(11,8,37,'debit',75000000.00,75000000.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Piutang Usaha','2026-01-12 12:43:03','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(12,8,38,'debit',150000000.00,150000000.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Persediaan Barang','2026-01-12 12:43:03','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(13,8,39,'debit',10000000.00,10000000.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Perlengkapan','2026-01-12 12:43:03','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(14,8,41,'debit',200000000.00,200000000.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Tanah','2026-01-12 12:43:03','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(15,8,42,'debit',300000000.00,300000000.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Bangunan','2026-01-12 12:43:03','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(16,8,43,'credit',50000000.00,0.00,50000000.00,NULL,NULL,NULL,0.00,NULL,'Akumulasi Penyusutan Bangunan','2026-01-12 12:43:03','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(17,8,44,'debit',100000000.00,100000000.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Peralatan','2026-01-12 12:43:03','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(18,8,45,'credit',20000000.00,0.00,20000000.00,NULL,NULL,NULL,0.00,NULL,'Akumulasi Penyusutan Peralatan','2026-01-12 12:43:03','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(19,8,46,'debit',150000000.00,150000000.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Kendaraan','2026-01-12 12:43:03','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(20,8,47,'credit',30000000.00,0.00,30000000.00,NULL,NULL,NULL,0.00,NULL,'Akumulasi Penyusutan Kendaraan','2026-01-12 12:43:04','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(21,8,50,'credit',50000000.00,0.00,50000000.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Hutang Usaha','2026-01-12 12:43:04','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(22,8,51,'credit',15000000.00,0.00,15000000.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Hutang Gaji','2026-01-12 12:43:04','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(23,8,52,'credit',10000000.00,0.00,10000000.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Hutang Pajak','2026-01-12 12:43:04','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(24,8,54,'credit',100000000.00,0.00,100000000.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Hutang Bank','2026-01-12 12:43:04','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(25,8,56,'credit',960000000.00,0.00,960000000.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Modal Pemilik','2026-01-12 12:43:04','2026-01-12 13:33:09',NULL,NULL,'2026-01-12 13:33:09'),
(26,8,35,'debit',50000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Kas','2026-01-12 13:33:09','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(27,8,36,'debit',200000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Bank','2026-01-12 13:33:09','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(28,8,37,'debit',75000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Piutang Usaha','2026-01-12 13:33:09','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(29,8,38,'debit',150000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Persediaan Barang','2026-01-12 13:33:09','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(30,8,39,'debit',10000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Perlengkapan','2026-01-12 13:33:09','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(31,8,41,'debit',200000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Tanah','2026-01-12 13:33:09','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(32,8,42,'debit',300000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Bangunan','2026-01-12 13:33:09','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(33,8,43,'credit',50000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Akumulasi Penyusutan Bangunan','2026-01-12 13:33:09','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(34,8,44,'debit',100000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Peralatan','2026-01-12 13:33:09','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(35,8,45,'credit',20000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Akumulasi Penyusutan Peralatan','2026-01-12 13:33:10','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(36,8,46,'debit',150000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Kendaraan','2026-01-12 13:33:10','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(37,8,47,'credit',30000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Akumulasi Penyusutan Kendaraan','2026-01-12 13:33:10','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(38,8,50,'credit',50000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Hutang Usaha','2026-01-12 13:33:10','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(39,8,51,'credit',15000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Hutang Gaji','2026-01-12 13:33:10','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(40,8,52,'credit',10000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Hutang Pajak','2026-01-12 13:33:10','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(41,8,54,'credit',100000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Hutang Bank','2026-01-12 13:33:10','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(42,8,56,'credit',960000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Modal Pemilik','2026-01-12 13:33:10','2026-01-12 13:36:30',NULL,NULL,'2026-01-12 13:36:30'),
(43,8,35,'debit',50000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Kas','2026-01-12 13:36:30','2026-01-12 13:36:30',NULL,NULL,NULL),
(44,8,36,'debit',200000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Bank','2026-01-12 13:36:30','2026-01-12 13:36:30',NULL,NULL,NULL),
(45,8,37,'debit',75000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Piutang Usaha','2026-01-12 13:36:31','2026-01-12 13:36:31',NULL,NULL,NULL),
(46,8,38,'debit',150000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Persediaan Barang','2026-01-12 13:36:31','2026-01-12 13:36:31',NULL,NULL,NULL),
(47,8,39,'debit',10000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Perlengkapan','2026-01-12 13:36:31','2026-01-12 13:36:31',NULL,NULL,NULL),
(48,8,41,'debit',200000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Tanah','2026-01-12 13:36:31','2026-01-12 13:36:31',NULL,NULL,NULL),
(49,8,42,'debit',300000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Bangunan','2026-01-12 13:36:31','2026-01-12 13:36:31',NULL,NULL,NULL),
(50,8,43,'credit',50000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Akumulasi Penyusutan Bangunan','2026-01-12 13:36:31','2026-01-12 13:36:31',NULL,NULL,NULL),
(51,8,44,'debit',100000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Peralatan','2026-01-12 13:36:31','2026-01-12 13:36:31',NULL,NULL,NULL),
(52,8,45,'credit',20000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Akumulasi Penyusutan Peralatan','2026-01-12 13:36:31','2026-01-12 13:36:31',NULL,NULL,NULL),
(53,8,46,'debit',150000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Kendaraan','2026-01-12 13:36:31','2026-01-12 13:36:31',NULL,NULL,NULL),
(54,8,47,'credit',30000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Akumulasi Penyusutan Kendaraan','2026-01-12 13:36:32','2026-01-12 13:36:32',NULL,NULL,NULL),
(55,8,50,'credit',50000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Hutang Usaha','2026-01-12 13:36:32','2026-01-12 13:36:32',NULL,NULL,NULL),
(56,8,51,'credit',15000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Hutang Gaji','2026-01-12 13:36:32','2026-01-12 13:36:32',NULL,NULL,NULL),
(57,8,52,'credit',10000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Hutang Pajak','2026-01-12 13:36:32','2026-01-12 13:36:32',NULL,NULL,NULL),
(58,8,54,'credit',100000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Hutang Bank','2026-01-12 13:36:32','2026-01-12 13:36:32',NULL,NULL,NULL),
(59,8,56,'credit',960000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Saldo awal Modal Pemilik','2026-01-12 13:36:32','2026-01-12 13:36:32',NULL,NULL,NULL),
(60,10,21,'credit',1000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Penjualan Test','2026-01-12 15:21:59','2026-01-12 15:22:10',NULL,NULL,'2026-01-12 15:22:10'),
(61,10,35,'debit',1000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Penjualan Test','2026-01-12 15:21:59','2026-01-12 15:22:10',NULL,NULL,'2026-01-12 15:22:10'),
(62,10,21,'credit',1000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Penjualan Test','2026-01-12 15:22:10','2026-01-12 15:22:10',NULL,NULL,NULL),
(63,10,35,'debit',1000000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Penjualan Test','2026-01-12 15:22:11','2026-01-12 15:22:11',NULL,NULL,NULL),
(64,11,35,'debit',160000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Cash received from sale','2026-01-12 22:58:41','2026-01-12 22:58:41',NULL,NULL,NULL),
(65,11,60,'credit',160000.00,0.00,0.00,NULL,NULL,NULL,0.00,NULL,'Sales revenue - SO-2026-00001','2026-01-12 22:58:41','2026-01-12 22:58:41',NULL,NULL,NULL);

/*Data for the table `journal_entry_revisions` */

/*Data for the table `locations` */

insert  into `locations`(`id`,`name`,`code`,`description`,`address`,`city`,`state`,`country`,`postal_code`,`latitude`,`longitude`,`color`,`is_active`,`parent_id`,`metadata`,`created_at`,`updated_at`) values 
(1,'Gudang Utama','GDG-001',NULL,'Alamat Gudang Utama',NULL,NULL,NULL,NULL,NULL,NULL,'#10B981',1,NULL,NULL,'2026-01-04 09:50:00','2026-01-04 09:50:00'),
(3,'Test','TEST','Test','asasas','Denpasar','Bali','Indonesia','80225',NULL,NULL,'#10B981',1,1,'[]','2026-01-12 02:23:04','2026-01-12 02:23:17'),
(4,'Seminyak Village','TK_01',NULL,'Jl. Kayu aya',NULL,NULL,NULL,NULL,NULL,NULL,'#10B981',1,NULL,'[]','2026-01-12 23:45:02','2026-01-12 23:45:02');

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_reset_tokens_table',1),
(3,'2019_08_19_000000_create_failed_jobs_table',1),
(4,'2019_12_14_000001_create_personal_access_tokens_table',1),
(5,'2024_11_01_000002_add_stock_book_indexes',1),
(6,'2025_09_27_001117_create_roles_table',1),
(7,'2025_09_27_001502_create_permissions_table',1),
(8,'2025_09_27_001558_create_user_roles_table',1),
(9,'2025_09_27_001605_create_role_permissions_table',1),
(10,'2025_09_27_001611_create_user_permissions_table',1),
(11,'2025_09_27_032355_add_status_to_users_table',1),
(12,'2025_09_27_052201_create_locations_table',1),
(13,'2025_09_27_105455_add_profile_fields_to_users_table',1),
(14,'2025_10_24_041800_create_units_table',1),
(15,'2025_10_24_041810_create_product_categories_table',1),
(16,'2025_10_24_041818_create_chart_of_accounts_table',1),
(17,'2025_10_24_041828_create_journal_entries_table',1),
(18,'2025_10_24_041828_create_journal_entry_details_table',1),
(19,'2025_10_24_041829_create_products_table',1),
(20,'2025_10_24_041829_create_stock_in_table',1),
(21,'2025_10_24_041830_create_stock_adjustments_table',1),
(22,'2025_10_24_041831_create_stock_cards_table',1),
(23,'2025_10_24_041831_create_stock_opnames_table',1),
(24,'2025_10_26_000003_enhance_chart_of_accounts_table',1),
(25,'2025_10_26_000004_create_chart_of_account_audits_table',1),
(26,'2025_10_26_000005_create_account_balance_histories_table',1),
(27,'2025_10_28_000001_enhance_journal_entries_table',1),
(28,'2025_10_28_000002_enhance_journal_entry_details_table',2),
(29,'2025_10_28_000003_create_journal_entry_approvals_table',2),
(30,'2025_10_28_000004_create_journal_entry_attachments_table',2),
(31,'2025_10_28_000005_create_journal_entry_revisions_table',2),
(32,'2025_10_29_225151_create_stock_in_details_table',2),
(33,'2025_10_29_225219_update_stock_in_table_remove_product_fields',2),
(34,'2025_10_30_035307_create_stock_mutations_table',2),
(35,'2025_10_30_035344_create_stock_mutation_details_table',2),
(36,'2025_12_04_180000_create_missing_tables',3),
(37,'2025_11_01_034904_add_total_items_and_description_to_stock_adjustments',4),
(38,'2026_01_11_060132_convert_stock_adjustments_to_master_detail',5),
(39,'2025_10_31_222904_create_stock_opnames_tables',6),
(40,'2025_11_01_034456_add_total_items_and_notes_to_stock_opnames_table',7),
(41,'2025_11_01_034552_add_adjustment_type_and_counted_by_to_stock_opname_details',8),
(42,'2025_11_01_103455_create_customers_table',9),
(43,'2025_11_01_103502_create_sales_table',10),
(44,'2025_11_01_103505_create_sale_details_table',11),
(45,'2025_11_02_023049_add_deleted_at_to_customers_table',12),
(46,'2025_11_02_043628_create_settings_table',12),
(47,'2025_11_03_111658_create_password_reset_otps_table',12),
(48,'2026_01_12_062711_add_soft_deletes_to_journal_entry_details_table',12),
(49,'2026_01_13_000000_fix_sales_table_structure',13),
(50,'2026_01_12_224752_fix_sale_details_column_names',14);

/*Data for the table `password_reset_otps` */

/*Data for the table `password_reset_tokens` */

/*Data for the table `permissions` */

insert  into `permissions`(`id`,`name`,`display_name`,`description`,`group`,`is_active`,`created_at`,`updated_at`) values 
(1,'view-dashboard','View Dashboard','View Dashboard','General',1,'2025-10-30 06:44:29','2025-10-30 06:44:29'),
(2,'view-coa','View Chart of Accounts','View Chart of Accounts','General',1,'2025-10-30 06:44:29','2025-10-30 06:44:29'),
(3,'create-coa','Create Chart of Accounts','Create Chart of Accounts','General',1,'2025-10-30 06:44:29','2025-10-30 06:44:29'),
(4,'edit-coa','Edit Chart of Accounts','Edit Chart of Accounts','General',1,'2025-10-30 06:44:29','2025-10-30 06:44:29'),
(5,'delete-coa','Delete Chart of Accounts','Delete Chart of Accounts','General',1,'2025-10-30 06:44:29','2025-10-30 06:44:29'),
(6,'view-journal','View Journal Entries','View Journal Entries','General',1,'2025-10-30 06:44:29','2025-10-30 06:44:29'),
(7,'create-journal','Create Journal Entries','Create Journal Entries','General',1,'2025-10-30 06:44:29','2025-10-30 06:44:29'),
(8,'edit-journal','Edit Journal Entries','Edit Journal Entries','General',1,'2025-10-30 06:44:29','2025-10-30 06:44:29'),
(9,'delete-journal','Delete Journal Entries','Delete Journal Entries','General',1,'2025-10-30 06:44:29','2025-10-30 06:44:29'),
(10,'post-journal','Post Journal Entries','Post Journal Entries','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(11,'view-neraca-lajur','View Neraca Lajur','View Neraca Lajur','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(12,'view-neraca','View Neraca','View Neraca','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(13,'view-laba-rugi','View Laba Rugi','View Laba Rugi','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(14,'view-perubahan-modal','View Perubahan Modal','View Perubahan Modal','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(15,'view-arus-kas','View Arus Kas','View Arus Kas','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(16,'view-products','View Products','View Products','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(17,'create-products','Create Products','Create Products','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(18,'edit-products','Edit Products','Edit Products','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(19,'delete-products','Delete Products','Delete Products','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(20,'view-stock-in','View Stock In','View Stock In','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(21,'create-stock-in','Create Stock In','Create Stock In','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(22,'edit-stock-in','Edit Stock In','Edit Stock In','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(23,'delete-stock-in','Delete Stock In','Delete Stock In','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(24,'post-stock-in','Post Stock In','Post Stock In','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(25,'view-mutation','View Stock Mutation','View Stock Mutation','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(26,'create-mutation','Create Stock Mutation','Create Stock Mutation','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(27,'edit-mutation','Edit Stock Mutation','Edit Stock Mutation','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(28,'delete-mutation','Delete Stock Mutation','Delete Stock Mutation','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(29,'approve-mutation','Approve Stock Mutation','Approve Stock Mutation','General',1,'2025-10-30 06:44:30','2025-10-30 06:44:30'),
(30,'receive-mutation','Receive Stock Mutation','Receive Stock Mutation','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(31,'view-adjustment','View Stock Adjustment','View Stock Adjustment','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(32,'create-adjustment','Create Stock Adjustment','Create Stock Adjustment','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(33,'edit-adjustment','Edit Stock Adjustment','Edit Stock Adjustment','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(34,'delete-adjustment','Delete Stock Adjustment','Delete Stock Adjustment','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(35,'approve-adjustment','Approve Stock Adjustment','Approve Stock Adjustment','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(36,'view-opname','View Stock Opname','View Stock Opname','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(37,'create-opname','Create Stock Opname','Create Stock Opname','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(38,'edit-opname','Edit Stock Opname','Edit Stock Opname','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(39,'delete-opname','Delete Stock Opname','Delete Stock Opname','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(40,'complete-opname','Complete Stock Opname','Complete Stock Opname','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(41,'view-stock-card','View Stock Card','View Stock Card','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(42,'view-locations','View Locations','View Locations','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(43,'create-locations','Create Locations','Create Locations','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(44,'edit-locations','Edit Locations','Edit Locations','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(45,'delete-locations','Delete Locations','Delete Locations','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(46,'view-users','View Users','View Users','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(47,'create-users','Create Users','Create Users','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(48,'edit-users','Edit Users','Edit Users','General',1,'2025-10-30 06:44:31','2025-10-30 06:44:31'),
(49,'delete-users','Delete Users','Delete Users','General',1,'2025-10-30 06:44:32','2025-10-30 06:44:32'),
(50,'view-roles','View Roles','View Roles','General',1,'2025-10-30 06:44:32','2025-10-30 06:44:32'),
(51,'create-roles','Create Roles','Create Roles','General',1,'2025-10-30 06:44:32','2025-10-30 06:44:32'),
(52,'edit-roles','Edit Roles','Edit Roles','General',1,'2025-10-30 06:44:32','2025-10-30 06:44:32'),
(53,'delete-roles','Delete Roles','Delete Roles','General',1,'2025-10-30 06:44:32','2025-10-30 06:44:32'),
(54,'view-permissions','View Permissions','View Permissions','General',1,'2025-10-30 06:44:32','2025-10-30 06:44:32'),
(55,'assign-permissions','Assign Permissions','Assign Permissions','General',1,'2025-10-30 06:44:32','2025-10-30 06:44:32'),
(56,'view-stock-masuk','view-stock-masuk','view-stock-masuk',NULL,1,'2026-01-04 10:36:56','2026-01-04 10:36:56'),
(57,'view-mutasi','view-mutasi','view-mutasi',NULL,1,'2026-01-04 10:37:08','2026-01-04 10:37:08'),
(58,'view-stockopname','view-stockopname','view-stockopname',NULL,1,'2026-01-04 10:37:35','2026-01-04 10:37:35'),
(59,'view-buku-stock','view-buku-stock','view-buku-stock',NULL,1,'2026-01-04 10:37:48','2026-01-04 10:37:48'),
(60,'view-sale','view-sale','view-sale',NULL,1,'2026-01-04 10:38:04','2026-01-04 10:38:04'),
(61,'view-customer','view-customer','view-customer',NULL,1,'2026-01-04 10:38:14','2026-01-04 10:38:14'),
(62,'view-settings','view-settings','view-settings',NULL,1,'2026-01-12 23:31:55','2026-01-12 23:31:55');

/*Data for the table `personal_access_tokens` */

/*Data for the table `product_categories` */

insert  into `product_categories`(`id`,`code`,`name`,`description`,`parent_id`,`is_active`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'','General','General Category',NULL,1,'2026-01-04 09:50:43','2026-01-04 09:50:43',NULL);

/*Data for the table `products` */

insert  into `products`(`id`,`product_code`,`product_name`,`description`,`product_type`,`category_id`,`unit_id`,`purchase_price`,`selling_price`,`minimum_stock`,`maximum_stock`,`location_id`,`is_active`,`created_at`,`updated_at`,`deleted_at`) values 
(2,'PROD-001','Test Product','Test Product Description','finished_goods',1,1,5000.00,10000.00,10,100,NULL,1,'2026-01-07 09:58:50','2026-01-04 09:50:43',NULL),
(3,'AWMW-001','AWMW - Soulful blend brew coffee',NULL,'finished_goods',NULL,1,150000.00,160000.00,10,100,NULL,1,'2026-01-07 09:58:50','2026-01-12 02:35:42',NULL),
(4,'AWMW-002','AWMW - Gratitude blend coffee',NULL,'finished_goods',NULL,1,0.00,160000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(5,'AWMW-003','AWMW - Incense Stick - Frangipani',NULL,'finished_goods',NULL,1,0.00,50000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(6,'AWMW-004','AWMW - Incense Stick - Cempakha',NULL,'finished_goods',NULL,1,0.00,50000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(7,'AWMW-005','AWMW - Incense Stick - Nylang-ylang',NULL,'finished_goods',NULL,1,0.00,50000.00,10,100,NULL,1,'2026-01-07 09:58:50','2026-01-07 23:34:39','2026-01-07 23:34:39'),
(8,'AWMW-006','AWMW - Incense Stick - Sandalwood',NULL,'finished_goods',NULL,1,0.00,50000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(9,'VASSION-001','VASSION - Coffee Body Scrub',NULL,'finished_goods',NULL,1,0.00,90000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(10,'VASSION-002','VASSION - Coffee x orange Body Scrub',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(11,'VASSION-003','VASSION - Sugar Scrub Rose Petal Vanilla',NULL,'finished_goods',NULL,1,0.00,130000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(12,'VASSION-004','VASSION - Sugar Scrub Rose Petal Vanilla',NULL,'finished_goods',NULL,1,0.00,70000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(13,'VASSION-005','VASSION - Sugar Scrub Orange Bergamot',NULL,'finished_goods',NULL,1,0.00,130000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(14,'VASSION-006','VASSION - Sugar Scrub Orange Bergamot',NULL,'finished_goods',NULL,1,0.00,70000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(15,'VASSION-007','VASSION - Body Butter Choco',NULL,'finished_goods',NULL,1,0.00,120000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(16,'VASSION-008','VASSION - Pure Virgin Coconut Oil',NULL,'finished_goods',NULL,1,0.00,90000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(17,'VASSION-009','VASSION - Body Oil Lavender',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(18,'VASSION-010','VASSION - Body Oil Lavender',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(19,'VASSION-011','VASSION - Body Oil Lemongrass',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(20,'VASSION-012','VASSION - Body Oil Lemongrass',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(21,'VASSION-013','VASSION - Body massage tools',NULL,'finished_goods',NULL,1,0.00,60000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(22,'VASSION-014','VASSION - EO The Pure Eucalyptus',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(23,'VASSION-015','VASSION - EO The Pure Tea Tree',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(24,'VASSION-016','VASSION - EO The Pure Peppermint',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(25,'VASSION-017','VASSION - EO The Pure Lavender',NULL,'finished_goods',NULL,1,0.00,120000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(26,'VASSION-018','VASSION - EO The Pure Lemongrass',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(27,'VASSION-019','VASSION - EO The Pure Rosemary',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(28,'VASSION-020','VASSION - Perfume Would you date me',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(29,'VASSION-021','VASSION - Perfume I am Sweet But Spicy',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(30,'VASSION-022','VASSION - Perfume Sweet but pshyco',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(31,'VASSION-023','VASSION - Perfume Naughty by Nature',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(32,'VASSION-024','VASSION - Perfume S.S Vol.01',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(33,'VASSION-025','VASSION - Perfume S.S Vol.02',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(34,'VASSION-026','VASSION - Linen Spray Lavender mix',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(35,'VASSION-027','VASSION - Linen Spray Jasmine mix',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(36,'VASSION-028','VASSION - Hair oil',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(37,'VASSION-029','VASSION - Hair/head massage',NULL,'finished_goods',NULL,1,0.00,40000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(38,'VASSION-030','VASSION - Room Spray Tuberose mix',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(39,'VASSION-031','VASSION - Room Spray Peppermint mix',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(40,'VASSION-032','VASSION - Room Spray Sweet Orange mix',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(41,'VASSION-033','VASSION - Crystal Deodorant',NULL,'finished_goods',NULL,1,0.00,50000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(42,'VASSION-034','VASSION - Wooden Crystal Deodorant',NULL,'finished_goods',NULL,1,0.00,70000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(43,'VASSION-035','VASSION - Crystal Deodorant Mangoosten',NULL,'finished_goods',NULL,1,0.00,70000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(44,'VASSION-036','VASSION - Crystal Deodorant Black',NULL,'finished_goods',NULL,1,0.00,70000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(45,'VASSION-037','VASSION - Crystal Deodorant Aloe vera',NULL,'finished_goods',NULL,1,0.00,70000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(46,'VASSION-038','VASSION - Peppermint candle set',NULL,'finished_goods',NULL,1,0.00,55000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(47,'VASSION-039','VASSION - Rose candle Pink Aluminium',NULL,'finished_goods',NULL,1,0.00,149000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(48,'VASSION-040','VASSION - Wax Melt Rose',NULL,'finished_goods',NULL,1,0.00,79000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(49,'VASSION-041','VASSION - Wax Melt Lavender',NULL,'finished_goods',NULL,1,0.00,79000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(50,'VASSION-042','VASSION - Wax Melt Peppermint',NULL,'finished_goods',NULL,1,0.00,79000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(51,'VASSION-043','VASSION - Wax Melt Sweet Orange',NULL,'finished_goods',NULL,1,0.00,79000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(52,'VASSION-044','VASSION - Wax Melt Vanilla',NULL,'finished_goods',NULL,1,0.00,79000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(53,'VASSION-045','VASSION - Pillar Candle Love',NULL,'finished_goods',NULL,1,0.00,50000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(54,'VASSION-046','VASSION - Pillar Candle',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(55,'VASSION-047','VASSION - Pillar Candle',NULL,'finished_goods',NULL,1,0.00,90000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(56,'VASSION-048','VASSION - I am blushing candle pink',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(57,'VASSION-049','VASSION - Date Night candle ',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(58,'VASSION-050','VASSION - Candle tuberose',NULL,'finished_goods',NULL,1,0.00,135000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(59,'VASSION-051','VASSION - Marble pink candle',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(60,'VASSION-052','VASSION - Marble green candle',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(61,'VASSION-053','VASSION - Marble green candle',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(62,'VASSION-054','VASSION - Amber jar candle',NULL,'finished_goods',NULL,1,0.00,200000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(63,'VASSION-055','VASSION - Loofah',NULL,'finished_goods',NULL,1,0.00,25000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(64,'VASSION-056','VASSION - Wooden Stick Brush Shower',NULL,'finished_goods',NULL,1,0.00,189000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(65,'VASSION-057','VASSION - Wooden Handy Brush Shower',NULL,'finished_goods',NULL,1,0.00,70000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(66,'VASSION-058','VASSION - Shower Puff White Rami',NULL,'finished_goods',NULL,1,0.00,55000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(67,'VASSION-059','VASSION - Shower Sponge White',NULL,'finished_goods',NULL,1,0.00,60000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(68,'VASSION-060','VASSION - Shower massage wooden stick',NULL,'finished_goods',NULL,1,0.00,240000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(69,'VASSION-061','VASSION - Coconut sponge Round',NULL,'finished_goods',NULL,1,0.00,20000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(70,'VASSION-062','VASSION - Coconut sponge Rectangle',NULL,'finished_goods',NULL,1,0.00,20000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(71,'VASSION-063','VASSION - Batu Apung',NULL,'finished_goods',NULL,1,0.00,20000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(72,'VASSION-064','VASSION - Wooden foot brush',NULL,'finished_goods',NULL,1,0.00,110000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(73,'VASSION-065','VASSION - Tooth Brush Bamboo - Black',NULL,'finished_goods',NULL,1,0.00,22000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(74,'VASSION-066','VASSION - Tooth Brush Bamboo - Brown',NULL,'finished_goods',NULL,1,0.00,22000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(75,'VASSION-067','VASSION - Hair Brush Wooden Cleaner',NULL,'finished_goods',NULL,1,0.00,45000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(76,'VASSION-068','VASSION - Cat Hair Brush Wooden',NULL,'finished_goods',NULL,1,0.00,135000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(77,'VASSION-069','VASSION - Oval Hair Brush Wooden',NULL,'finished_goods',NULL,1,0.00,135000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(78,'VASSION-070','VASSION - Square Hair Brush Wooden',NULL,'finished_goods',NULL,1,0.00,135000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(79,'VASSION-071','VASSION - Makeup Organizer set (+cotton bud)',NULL,'finished_goods',NULL,1,0.00,160000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(80,'VASSION-072','VASSION - Cotton Bud Bamboo refill',NULL,'finished_goods',NULL,1,0.00,25000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(81,'VASSION-073','VASSION - Cotton Net Bag - Peach',NULL,'finished_goods',NULL,1,0.00,120000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(82,'VASSION-074','VASSION - Cotton Net Bag - Pink',NULL,'finished_goods',NULL,1,0.00,120000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(83,'VASSION-075','VASSION - Cotton Net Bag - White',NULL,'finished_goods',NULL,1,0.00,120000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(84,'VASSION-076','VASSION - Travel bag canvas White',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(85,'VASSION-077','VASSION - Tumbler stainless black',NULL,'finished_goods',NULL,1,0.00,299000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(86,'VASSION-078','VASSION - Tumbler glass SMALL',NULL,'finished_goods',NULL,1,0.00,130000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(87,'VASSION-079','VASSION - Tumbler glass LARGE',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(88,'VASSION-080','VASSION - Tumbler with straw',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(89,'VASSION-081','VASSION - Tumbler attach',NULL,'finished_goods',NULL,1,0.00,450000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(90,'VASSION-082','VASSION - Sticker',NULL,'finished_goods',NULL,1,0.00,25000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(91,'VASSION-083','VASSION - Note book mini \"Filled with sweet\" GREEN',NULL,'finished_goods',NULL,1,0.00,99000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(92,'VASSION-084','VASSION - Note book mini \"Filled with sweet\" PINK',NULL,'finished_goods',NULL,1,0.00,99000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(93,'VASSION-085','VASSION - Note book \"Dont Cry\" Pink',NULL,'finished_goods',NULL,1,0.00,99000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(94,'VASSION-086','VASSION - Canvas Printed Aesthetic',NULL,'finished_goods',NULL,1,0.00,75000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(95,'VASSION-087','VASSION - Bingkai Canvas Printed',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(96,'VASSION-088','VASSION - Chamomile Hair Clip',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(97,'VASSION-089','VASSION - Frangipani Hair Clip',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(98,'VASSION-090','VASSION - Frangipani Hair Clip ',NULL,'finished_goods',NULL,1,0.00,60000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(99,'VASSION-091','VASSION - Frangipani Hair Clip ',NULL,'finished_goods',NULL,1,0.00,50000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(100,'VASSION-092','VASSION - Wavy Hair Clip',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(101,'VASSION-093','VASSION - Nude Variant Hair Clip',NULL,'finished_goods',NULL,1,0.00,50000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(102,'VASSION-094','VASSION - Nude Variant Hair Clip',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(103,'VASSION-095','VASSION - Small Hair Clip',NULL,'finished_goods',NULL,1,0.00,100000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(104,'VASSION-096','VASSION - Fur Hair Clip',NULL,'finished_goods',NULL,1,0.00,120000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(105,'VASSION-097','VASSION - Butterfly hair clip',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(106,'VASSION-098','VASSION - Shell hair clip',NULL,'finished_goods',NULL,1,0.00,80000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(107,'VASSION-099','VASSION - Hand held fan',NULL,'finished_goods',NULL,1,0.00,60000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(108,'VASSION-100','VASSION - Fan',NULL,'finished_goods',NULL,1,0.00,50000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(109,'VASSION-101','VASSION - Chrismast decoration',NULL,'finished_goods',NULL,1,0.00,30000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(110,'VASSION-102','VASSION - Burner ceramic mushroom',NULL,'finished_goods',NULL,1,0.00,90000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(111,'SECOND_SKIN-001','SECOND SKIN - Rayon Tenun Sarong Handmade',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(112,'SECOND_SKIN-002','SECOND SKIN - Cotton Tenun Sarong Handmade',NULL,'finished_goods',NULL,1,0.00,270000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(113,'SECOND_SKIN-003','SECOND SKIN - Weaving scarf cotton',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(114,'SECOND_SKIN-004','SECOND SKIN - Weaving sarong cotton ',NULL,'finished_goods',NULL,1,0.00,229000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(115,'SECOND_SKIN-005','SECOND SKIN - Woven sarong rayon',NULL,'finished_goods',NULL,1,0.00,220000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(116,'SECOND_SKIN-006','SECOND SKIN - Niki Bikini Top - Army',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(117,'SECOND_SKIN-007','SECOND SKIN - Niki Bikini Bottom - Army',NULL,'finished_goods',NULL,1,0.00,350000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(118,'SECOND_SKIN-008','SECOND SKIN - Niki Bikini Top - Beige',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(119,'SECOND_SKIN-009','SECOND SKIN - Niki Bikini Bottom - Beige',NULL,'finished_goods',NULL,1,0.00,350000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(120,'SECOND_SKIN-010','SECOND SKIN - Niki Bikini Top - Indigo',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(121,'SECOND_SKIN-011','SECOND SKIN - Niki Bikini Bottom - Indigo',NULL,'finished_goods',NULL,1,0.00,350000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(122,'SECOND_SKIN-012','SECOND SKIN - Niki Bikini Top - Mustard',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(123,'SECOND_SKIN-013','SECOND SKIN - Niki Bikini Bottom - Mustard',NULL,'finished_goods',NULL,1,0.00,350000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(124,'SECOND_SKIN-014','SECOND SKIN - Niki Bikini Top - Army',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(125,'SECOND_SKIN-015','SECOND SKIN - Niki Bikini Bottom - Army',NULL,'finished_goods',NULL,1,0.00,350000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(126,'SECOND_SKIN-016','SECOND SKIN - Niki Bikini Top - Beige',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(127,'SECOND_SKIN-017','SECOND SKIN - Niki Bikini Bottom - Beige',NULL,'finished_goods',NULL,1,0.00,350000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(128,'SECOND_SKIN-018','SECOND SKIN - Niki Bikini Top - Indigo',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(129,'SECOND_SKIN-019','SECOND SKIN - Niki Bikini Bottom - Indigo',NULL,'finished_goods',NULL,1,0.00,350000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(130,'SECOND_SKIN-020','SECOND SKIN - Niki Bikini Top - Mustard',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(131,'SECOND_SKIN-021','SECOND SKIN - Niki Bikini Bottom - Mustard',NULL,'finished_goods',NULL,1,0.00,350000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(132,'SECOND_SKIN-022','SECOND SKIN - Triangle Bikini Top - Champagne',NULL,'finished_goods',NULL,1,0.00,330000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(133,'SECOND_SKIN-023','SECOND SKIN - Triangle Bikini Top - Cedar',NULL,'finished_goods',NULL,1,0.00,330000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(134,'SECOND_SKIN-024','SECOND SKIN - Triangle Bikini Top - Lilac',NULL,'finished_goods',NULL,1,0.00,330000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(135,'SECOND_SKIN-025','SECOND SKIN - Triangle Bikini Top - Sage',NULL,'finished_goods',NULL,1,0.00,330000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(136,'SECOND_SKIN-026','SECOND SKIN - Triangle Bikini Bottom - Champagne',NULL,'finished_goods',NULL,1,0.00,330000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(137,'SECOND_SKIN-027','SECOND SKIN - Triangle Bikini Bottom - Cedar',NULL,'finished_goods',NULL,1,0.00,330000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(138,'SECOND_SKIN-028','SECOND SKIN - Triangle Bikini Bottom - Lilac',NULL,'finished_goods',NULL,1,0.00,330000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(139,'SECOND_SKIN-029','SECOND SKIN - Triangle Bikini Bottom - Sage',NULL,'finished_goods',NULL,1,0.00,330000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(140,'SECOND_SKIN-030','SECOND SKIN - Triangle Bikini Top - Champagne',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(141,'SECOND_SKIN-031','SECOND SKIN - Triangle Bikini Top - Cedar',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(142,'SECOND_SKIN-032','SECOND SKIN - Triangle Bikini Top - Lilac',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(143,'SECOND_SKIN-033','SECOND SKIN - Triangle Bikini Top - Sage',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(144,'SECOND_SKIN-034','SECOND SKIN - Triangle Bikini Top - Cocktail ',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(145,'SECOND_SKIN-035','SECOND SKIN - Triangle Bikini Bottom - Champagne',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(146,'SECOND_SKIN-036','SECOND SKIN - Triangle Bikini Bottom - Cedar',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(147,'SECOND_SKIN-037','SECOND SKIN - Triangle Bikini Bottom - Lilac',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(148,'SECOND_SKIN-038','SECOND SKIN - Triangle Bikini Bottom - Sage',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(149,'SECOND_SKIN-039','SECOND SKIN - Triangle Bikini Bottom - Cocktail ',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(150,'SECOND_SKIN-040','SECOND SKIN - Triangle Bikini bottom - Black',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(151,'SECOND_SKIN-041','SECOND SKIN - Triangle Bikini Top - Black',NULL,'finished_goods',NULL,1,0.00,330000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(152,'SECOND_SKIN-042','SECOND SKIN - Triangle Bikini bottom - Black',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(153,'SECOND_SKIN-043','SECOND SKIN - Vintage Bikini Top - Winter',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(154,'SECOND_SKIN-044','SECOND SKIN - Vintage Bikini Top - Biscuit',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(155,'SECOND_SKIN-045','SECOND SKIN - Vintage Bikini Top - Skin',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(156,'SECOND_SKIN-046','SECOND SKIN - Vintage Bikini Top - Cocoa',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(157,'SECOND_SKIN-047','SECOND SKIN - Vintage Bikini Top - Winter',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(158,'SECOND_SKIN-048','SECOND SKIN - Vintage Bikini Top - Biscuit',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(159,'SECOND_SKIN-049','SECOND SKIN - Vintage Bikini Top - Skin',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(160,'SECOND_SKIN-050','SECOND SKIN - Vintage Bikini Top - Cocoa',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(161,'SECOND_SKIN-051','SECOND SKIN - Vintage Bikini Bottom - Winter',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(162,'SECOND_SKIN-052','SECOND SKIN - Vintage Bikini Bottom - Biscuit',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(163,'SECOND_SKIN-053','SECOND SKIN - Vintage Bikini Bottom - Skin',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(164,'SECOND_SKIN-054','SECOND SKIN - Vintage Bikini Bottom - Cocoa',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(165,'SECOND_SKIN-055','SECOND SKIN - Vintage Bikini Bottom - Winter',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(166,'SECOND_SKIN-056','SECOND SKIN - Vintage Bikini Bottom - Biscuit',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(167,'SECOND_SKIN-057','SECOND SKIN - Vintage Bikini Bottom - Skin',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(168,'SECOND_SKIN-058','SECOND SKIN - Vintage Bikini Bottom - Cocoa',NULL,'finished_goods',NULL,1,0.00,320000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(169,'SECOND_SKIN-059','SECOND SKIN - Summer Cotton Bag 1 Pocket',NULL,'finished_goods',NULL,1,0.00,370000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(170,'SECOND_SKIN-060','SECOND SKIN - Summer Cotton Bag 2 Pocket',NULL,'finished_goods',NULL,1,0.00,370000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(171,'SECOND_SKIN-061','SECOND SKIN - Summer Cotton Bag 2 Pocket',NULL,'finished_goods',NULL,1,0.00,390000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(172,'SECOND_SKIN-062','SECOND SKIN - New Summer Bag 2 Pocket',NULL,'finished_goods',NULL,1,0.00,390000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(173,'SECOND_SKIN-063','SECOND SKIN - Niki Cotton Bag',NULL,'finished_goods',NULL,1,0.00,370000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(174,'SECOND_SKIN-064','SECOND SKIN - New Niki Cotton Bag Natural ',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(175,'SECOND_SKIN-065','SECOND SKIN - New Niki Cotton Bag ',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(176,'SECOND_SKIN-066','SECOND SKIN - Pillow Bag',NULL,'finished_goods',NULL,1,0.00,350000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(177,'SECOND_SKIN-067','SECOND SKIN - Pillow Bag',NULL,'finished_goods',NULL,1,0.00,350000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(178,'SECOND_SKIN-068','SECOND SKIN - Pillow Bag',NULL,'finished_goods',NULL,1,0.00,350000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(179,'SECOND_SKIN-069','SECOND SKIN - Pillow Bag',NULL,'finished_goods',NULL,1,0.00,350000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(180,'SECOND_SKIN-070','SECOND SKIN - Natural Canvas Bag',NULL,'finished_goods',NULL,1,0.00,300000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(181,'SECOND_SKIN-071','SECOND SKIN - Beach Sarong Rayon 180x100cm',NULL,'finished_goods',NULL,1,0.00,120000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(182,'SECOND_SKIN-072','SECOND SKIN - Scrunchie Set',NULL,'finished_goods',NULL,1,0.00,120000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(183,'SECOND_SKIN-073','SECOND SKIN - Scrunchie ',NULL,'finished_goods',NULL,1,0.00,50000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(184,'SECOND_SKIN-074','SECOND SKIN - Beach towel',NULL,'finished_goods',NULL,1,0.00,499000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(185,'BUBBLE-001','BUBBLE - Wooden post card',NULL,'finished_goods',NULL,1,0.00,95000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(186,'SECOND_SKIN-075','SECOND SKIN - Tumbler',NULL,'finished_goods',NULL,1,0.00,400000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(187,'SECOND_SKIN-076','SECOND SKIN - Sleeping eye mask ',NULL,'finished_goods',NULL,1,0.00,130000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(188,'SECOND_SKIN-077','SECOND SKIN - Panties SS Winter',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(189,'SECOND_SKIN-078','SECOND SKIN - Panties SS Winter',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(190,'SECOND_SKIN-079','SECOND SKIN - Panties SS Winter',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(191,'SECOND_SKIN-080','SECOND SKIN - Panties SS Lilac',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(192,'SECOND_SKIN-081','SECOND SKIN - Panties SS Lilac',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(193,'SECOND_SKIN-082','SECOND SKIN - Panties SS Lilac',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(194,'SECOND_SKIN-083','SECOND SKIN - Panties SS Skin',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(195,'SECOND_SKIN-084','SECOND SKIN - Panties SS Skin',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(196,'SECOND_SKIN-085','SECOND SKIN - Panties SS Skin',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(197,'SECOND_SKIN-086','SECOND SKIN - Panties SS Cocoa',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(198,'SECOND_SKIN-087','SECOND SKIN - Panties SS Cocoa',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(199,'SECOND_SKIN-088','SECOND SKIN - Panties SS Cocoa',NULL,'finished_goods',NULL,1,0.00,170000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(200,'SECOND_SKIN-089','SECOND SKIN - Print shirt',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(201,'SECOND_SKIN-090','SECOND SKIN - Validation dress black',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(202,'SECOND_SKIN-091','SECOND SKIN - Knitting backless dress black',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(203,'SECOND_SKIN-092','SECOND SKIN - Knitting backless dress brown',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(204,'SECOND_SKIN-093','SECOND SKIN - Side chick dress mini',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(205,'SECOND_SKIN-094','SECOND SKIN - Side chick dress maxi dress',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(206,'SECOND_SKIN-095','SECOND SKIN - Homie pant printed',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(207,'SECOND_SKIN-096','SECOND SKIN - Verona green pants ',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(208,'SECOND_SKIN-097','SECOND SKIN - Milano pants green size L',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(209,'SECOND_SKIN-098','SECOND SKIN - Milano pants green size m',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(210,'SECOND_SKIN-099','SECOND SKIN - Milano pants yellow size L',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(211,'SECOND_SKIN-100','SECOND SKIN - Milano pants yellow size m',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(212,'SECOND_SKIN-101','SECOND SKIN - Milano pants yellow size s',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(213,'SECOND_SKIN-102','SECOND SKIN - Linen office pants brown',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(214,'SECOND_SKIN-103','SECOND SKIN - Linen office pants beise',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(215,'SECOND_SKIN-104','SECOND SKIN - Fairy white skirt',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(216,'SECOND_SKIN-105','SECOND SKIN - Linen pants belah pinggir',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(217,'SECOND_SKIN-106','SECOND SKIN - Rope skirt cotton white',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(218,'SECOND_SKIN-107','SECOND SKIN - Maxi dress tile print blue brown',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(219,'SECOND_SKIN-108','SECOND SKIN - Maxi sheer tile dress',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(220,'SECOND_SKIN-109','SECOND SKIN - Knitting dress ikat broken white',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(221,'SECOND_SKIN-110','SECOND SKIN - Basic midi dress cacao',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(222,'SECOND_SKIN-111','SECOND SKIN - One piece bamboo',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(223,'SECOND_SKIN-112','SECOND SKIN - Butterfly skirt yellow',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(224,'SECOND_SKIN-113','SECOND SKIN - Butterfly skirt green',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(225,'SECOND_SKIN-114','SECOND SKIN - Blue lagoon top',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(226,'SECOND_SKIN-115','SECOND SKIN - Blue lagoon skirt',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(227,'SECOND_SKIN-116','SECOND SKIN - Sable top',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(228,'SECOND_SKIN-117','SECOND SKIN - Sable top',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(229,'SECOND_SKIN-118','SECOND SKIN - Oldys top brown size s',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(230,'SECOND_SKIN-119','SECOND SKIN - Oldys top beige size m',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(231,'SECOND_SKIN-120','SECOND SKIN - Basic tanktop',NULL,'finished_goods',NULL,1,0.00,279000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(232,'SECOND_SKIN-121','SECOND SKIN - Blanket ',NULL,'finished_goods',NULL,1,0.00,850000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(233,'SECOND_SKIN-122','SECOND SKIN - Blanket ',NULL,'finished_goods',NULL,1,0.00,950000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(234,'SECOND_SKIN-123','SECOND SKIN - Mix socks',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(235,'SECHO-001','SECHO - Glass coaster ceramic',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(236,'SECHO-002','SECCHO - Teko ceramic',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(237,'SECHO-003','SECCHO - Jb pot set',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(238,'SECHO-004','SECCHO - Pineapple fork',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(239,'SECHO-005','SECCHO - Pineapple spoon ',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(240,'SECHO-006','SECCHO - Summer bottle opener',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(241,'SECHO-007','SECCHO - Mermaid plate sea shell',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(242,'SECHO-008','SECCHO - Mermaid plate pearl shell',NULL,'finished_goods',NULL,1,0.00,NULL,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(243,'VASSION-103','VASSION - Kipas',NULL,'finished_goods',NULL,1,0.00,50000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(244,'VASSION-104','VASSION - Hairpin',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(245,'VASSION-105','VASSION - Clutch',NULL,'finished_goods',NULL,1,0.00,150000.00,10,100,NULL,1,'2026-01-07 09:58:50',NULL,NULL),
(246,'FG00001','Test','Test','finished_goods',1,1,10.00,100.00,100,100,NULL,1,'2026-01-11 05:00:53','2026-01-11 05:01:23','2026-01-11 05:01:23'),
(247,'FG00002','asasXX',NULL,'raw_material',NULL,1,0.00,0.00,0,0,NULL,1,'2026-01-12 02:10:41','2026-01-12 02:11:20','2026-01-12 02:11:20');

/*Data for the table `role_permissions` */

insert  into `role_permissions`(`id`,`role_id`,`permission_id`,`created_at`,`updated_at`) values 
(1,1,1,'2025-10-30 06:44:32','2025-10-30 06:44:32'),
(16,1,16,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(17,1,17,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(18,1,18,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(19,1,19,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(20,1,20,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(21,1,21,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(22,1,22,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(23,1,23,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(24,1,24,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(25,1,25,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(26,1,26,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(27,1,27,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(28,1,28,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(29,1,29,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(30,1,30,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(31,1,31,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(32,1,32,'2025-10-30 06:44:33','2025-10-30 06:44:33'),
(33,1,33,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(34,1,34,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(35,1,35,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(36,1,36,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(37,1,37,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(38,1,38,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(39,1,39,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(40,1,40,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(41,1,41,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(42,1,42,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(43,1,43,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(44,1,44,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(45,1,45,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(47,1,47,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(48,1,48,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(49,1,49,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(51,1,51,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(52,1,52,'2025-10-30 06:44:34','2025-10-30 06:44:34'),
(53,1,53,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(55,1,55,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(56,2,1,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(57,2,2,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(58,2,3,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(59,2,4,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(60,2,6,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(61,2,7,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(62,2,8,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(63,2,10,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(64,2,11,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(65,2,12,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(66,2,13,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(67,2,14,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(68,2,15,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(69,3,1,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(86,3,41,'2025-10-30 06:44:36','2025-10-30 06:44:36'),
(94,1,56,'2026-01-04 10:38:28','2026-01-04 10:38:28'),
(95,1,57,'2026-01-04 10:38:28','2026-01-04 10:38:28'),
(96,1,58,'2026-01-04 10:38:28','2026-01-04 10:38:28'),
(97,1,59,'2026-01-04 10:38:28','2026-01-04 10:38:28'),
(98,1,60,'2026-01-04 10:38:28','2026-01-04 10:38:28'),
(99,1,61,'2026-01-04 10:38:28','2026-01-04 10:38:28'),
(100,3,12,'2026-01-04 10:39:24','2026-01-04 10:39:24'),
(101,3,11,'2026-01-04 10:39:24','2026-01-04 10:39:24'),
(102,3,13,'2026-01-04 10:39:24','2026-01-04 10:39:24'),
(103,3,14,'2026-01-04 10:39:24','2026-01-04 10:39:24'),
(104,3,15,'2026-01-04 10:39:25','2026-01-04 10:39:25'),
(105,3,59,'2026-01-04 10:39:25','2026-01-04 10:39:25'),
(109,2,5,'2026-01-04 10:41:28','2026-01-04 10:41:28'),
(110,2,9,'2026-01-04 10:41:28','2026-01-04 10:41:28'),
(111,1,50,'2026-01-12 23:24:58','2026-01-12 23:24:58'),
(113,1,54,'2026-01-12 23:24:58','2026-01-12 23:24:58'),
(114,1,46,'2026-01-12 23:24:58','2026-01-12 23:24:58'),
(115,1,62,'2026-01-12 23:32:02','2026-01-12 23:32:02');

/*Data for the table `roles` */

insert  into `roles`(`id`,`name`,`display_name`,`description`,`permissions`,`is_active`,`created_at`,`updated_at`) values 
(1,'Admin','Admin','Full system access',NULL,1,'2025-10-30 06:44:32','2026-01-04 10:19:50'),
(2,'Accounting Manager','Accounting Manager','Manage accounting module',NULL,1,'2025-10-30 06:44:35','2025-10-30 06:44:35'),
(3,'Owner','Owner','Manage report and users',NULL,1,'2025-10-30 06:44:35','2026-01-04 10:20:16');

/*Data for the table `sale_details` */

insert  into `sale_details`(`id`,`sale_id`,`product_id`,`quantity`,`unit_price`,`discount_percent`,`discount_amount`,`tax_percent`,`tax_amount`,`total_price`,`notes`,`created_at`,`updated_at`) values 
(1,2,3,1.00,160000.00,0.00,0.00,0.00,0.00,160000.00,NULL,'2026-01-12 22:49:24','2026-01-12 22:49:24');

/*Data for the table `sales` */

insert  into `sales`(`id`,`transaction_number`,`transaction_date`,`customer_id`,`location_id`,`subtotal`,`tax_amount`,`discount_amount`,`total_amount`,`paid_amount`,`change_amount`,`payment_method`,`notes`,`status`,`created_by`,`posted_by`,`posted_at`,`created_at`,`updated_at`,`deleted_at`) values 
(2,'SO-2026-00001','2026-01-12',1,1,160000.00,0.00,0.00,160000.00,160000.00,0.00,'cash','asasas','posted',1,1,'2026-01-12 22:58:40','2026-01-12 22:49:24','2026-01-12 22:58:40',NULL),
(3,'SO-2026-00002','2026-01-12',1,1,160000.00,0.00,0.00,160000.00,200000.00,40000.00,'cash',NULL,'draft',1,NULL,NULL,'2026-01-12 23:56:49','2026-01-13 00:20:24','2026-01-13 00:20:24');

/*Data for the table `settings` */

insert  into `settings`(`id`,`logo_sistem`,`nama_sistem`,`deskripsi_sistem`,`nama_perusahaan`,`alamat_lengkap`,`email_perusahaan`,`nomor_telepon`,`footer_text`,`report_checker_name`,`report_approver_name`,`created_at`,`updated_at`) values 
(1,'1768259462_69657f86da71d.jpg','VASSION','Vassion Bali adalah aplikasi manajemen terpadu berbasis web yang dirancang untuk membantu organisasi dan pelaku usaha di Bali mengelola data, aset, serta aktivitas operasional secara efisien dan aman. Dikembangkan menggunakan teknologi modern Laravel, Vue.js, dan MySQL, Vassion Bali menghadirkan tampilan yang bersih, responsif, serta mudah digunakan, mendukung transformasi digital menuju tata kelola yang transparan dan profesional.','PT. SECONDSKIN JAYA GEMILANG','Jl. Legian Kaja No. 456, Badung Regency Bali - Indonesia, 80361','secondskinbyvassion@gmail.com','089888788765','Version 1.0.0',NULL,NULL,'2025-11-02 04:45:36','2026-01-12 23:11:02');

/*Data for the table `stock_adjustment_details` */

insert  into `stock_adjustment_details`(`id`,`stock_adjustment_id`,`product_id`,`system_quantity`,`actual_quantity`,`difference_quantity`,`adjustment_type`,`reason`,`notes`,`created_at`,`updated_at`,`deleted_at`) values 
(1,1,238,0,10,10,'increase','asasa',NULL,'2026-01-11 06:03:23','2026-01-11 06:03:35','2026-01-11 06:03:35'),
(2,1,238,0,10,10,'increase','asasa',NULL,'2026-01-11 06:03:35','2026-01-11 06:03:45','2026-01-11 06:03:45'),
(3,2,239,0,20,20,'increase','Stock Opname: OPN-2026-00001','asasa','2026-01-11 06:10:20','2026-01-11 06:10:20',NULL),
(4,3,6,0,15,15,'increase','Baru ketemu',NULL,'2026-01-12 02:52:18','2026-01-12 02:52:29','2026-01-12 02:52:29'),
(5,3,6,0,15,15,'increase','Baru ketemu',NULL,'2026-01-12 02:52:29','2026-01-12 02:52:29',NULL);

/*Data for the table `stock_adjustments` */

insert  into `stock_adjustments`(`id`,`adjustment_number`,`adjustment_date`,`description`,`total_items`,`location_id`,`notes`,`status`,`created_by`,`approved_by`,`approved_at`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'ADJ-202600001','2026-01-11','asasa',1,1,'asasa','draft',1,NULL,NULL,'2026-01-11 06:03:22','2026-01-11 06:03:45','2026-01-11 06:03:45'),
(2,'ADJ-202600002','2026-01-11','Auto-generated from Stock Opname: OPN-2026-00001',1,1,'asasa','posted',1,1,'2026-01-11 06:10:21','2026-01-11 06:10:20','2026-01-11 06:10:21',NULL),
(3,'ADJ-202600003','2026-01-12',NULL,1,1,NULL,'posted',1,1,'2026-01-12 02:52:35','2026-01-12 02:52:18','2026-01-12 02:52:35',NULL);

/*Data for the table `stock_balances` */

insert  into `stock_balances`(`id`,`product_id`,`location_id`,`current_balance`,`minimum_stock`,`maximum_stock`,`status`,`last_transaction_date`,`last_transaction_type`,`created_at`,`updated_at`) values 
(1,2,1,100.00,10.00,100.00,'in_stock','2026-01-04','stock_in','2026-01-04 09:55:29','2026-01-04 09:55:29'),
(2,242,1,200.00,10.00,100.00,'overstock','2026-01-11','stock_in','2026-01-11 05:30:28','2026-01-11 05:30:28'),
(3,239,1,20.00,10.00,100.00,'in_stock','2026-01-11','adjustment','2026-01-11 06:10:21','2026-01-11 06:10:21'),
(4,3,1,9.00,10.00,100.00,'low_stock','2026-01-12','sale','2026-01-11 07:00:09','2026-01-12 22:58:41'),
(5,3,3,10.00,10.00,100.00,'in_stock','2026-01-12','mutation','2026-01-12 02:41:08','2026-01-12 02:41:08'),
(6,6,1,15.00,10.00,100.00,'in_stock','2026-01-12','adjustment','2026-01-12 02:52:35','2026-01-12 02:52:35');

/*Data for the table `stock_cards` */

insert  into `stock_cards`(`id`,`product_id`,`location_id`,`transaction_date`,`transaction_type`,`reference_id`,`reference_number`,`quantity_in`,`quantity_out`,`balance`,`unit_price`,`notes`,`created_at`,`updated_at`) values 
(3,239,1,'2026-01-11','adjustment',2,'ADJ-202600002',20,0,20,0.00,'Stock Opname: OPN-2026-00001','2026-01-11 06:10:21','2026-01-11 06:10:21'),
(5,3,1,'2026-01-12','stock_in',7,'SI-202601-00003',20,0,20,150000.00,'asasa','2026-01-12 02:36:27','2026-01-12 02:36:27'),
(6,3,1,'2026-01-12','mutation_out',1,'SM-202600001',0,10,10,0.00,'asasa','2026-01-12 02:41:07','2026-01-12 02:41:07'),
(7,3,3,'2026-01-12','mutation_in',1,'SM-202600001',10,0,10,0.00,'asasa','2026-01-12 02:41:07','2026-01-12 02:41:07'),
(8,6,1,'2026-01-12','adjustment',3,'ADJ-202600003',15,0,15,0.00,'Baru ketemu','2026-01-12 02:52:35','2026-01-12 02:52:35'),
(9,3,1,'2026-01-12','sale',2,'SO-2026-00001',0,1,9,160000.00,'asasas','2026-01-12 22:58:40','2026-01-12 22:58:40');

/*Data for the table `stock_in` */

insert  into `stock_in`(`id`,`transaction_number`,`transaction_date`,`location_id`,`total_price`,`supplier_name`,`reference_number`,`notes`,`status`,`created_by`,`posted_by`,`posted_at`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'SI-TEST-1767520528','2026-01-04',1,0.00,NULL,NULL,NULL,'cancelled',1,1,'2026-01-04 09:55:28','2026-01-04 09:55:28','2026-01-12 02:34:54',NULL),
(2,'SI-202620529','2026-01-11',1,10000000.00,'aasasa','asasas','asasa','cancelled',1,1,'2026-01-11 05:30:28','2026-01-11 05:29:46','2026-01-11 05:32:25',NULL),
(3,'SI-202620530','2026-01-11',1,40000.00,'asasa','asasa','asas','draft',1,NULL,NULL,'2026-01-11 05:38:31','2026-01-11 05:40:16','2026-01-11 05:40:16'),
(5,'SI-202601-00001','2026-01-11',1,1200000.00,'asas','asas','asasa','draft',1,NULL,NULL,'2026-01-11 05:43:49','2026-01-11 05:43:57','2026-01-11 05:43:57'),
(6,'SI-202601-00002','2026-01-11',1,144000.00,'gavsgavsa','ahsbhas','ahshavsh','cancelled',1,1,'2026-01-11 07:00:08','2026-01-11 06:59:03','2026-01-11 07:01:54',NULL),
(7,'SI-202601-00003','2026-01-12',1,3000000.00,'XXX','XXX','XXX','posted',1,1,'2026-01-12 02:36:26','2026-01-12 02:36:07','2026-01-12 02:36:26',NULL);

/*Data for the table `stock_in_details` */

insert  into `stock_in_details`(`id`,`stock_in_id`,`product_id`,`quantity`,`unit_price`,`total_price`,`notes`,`created_at`,`updated_at`) values 
(1,1,2,100.00,10.00,1000.00,NULL,'2026-01-04 09:55:28','2026-01-04 09:55:28'),
(3,2,242,200.00,50000.00,10000000.00,'gavsgvaa','2026-01-11 05:30:17','2026-01-11 05:30:17'),
(6,6,3,120.00,1200.00,144000.00,NULL,'2026-01-11 06:59:03','2026-01-11 06:59:03'),
(8,7,3,20.00,150000.00,3000000.00,'asasa','2026-01-12 02:36:19','2026-01-12 02:36:19');

/*Data for the table `stock_mutation_details` */

insert  into `stock_mutation_details`(`id`,`stock_mutation_id`,`product_id`,`quantity`,`available_stock`,`notes`,`created_at`,`updated_at`) values 
(2,1,3,10.00,20.00,'asasa','2026-01-12 02:40:37','2026-01-12 02:40:37');

/*Data for the table `stock_mutations` */

insert  into `stock_mutations`(`id`,`transaction_number`,`transaction_date`,`from_location_id`,`to_location_id`,`reference_number`,`notes`,`status`,`created_by`,`submitted_by`,`submitted_at`,`approved_by`,`approved_at`,`completed_by`,`completed_at`,`rejection_reason`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'SM-202600001','2026-01-12',1,3,'asas','asasa','completed',1,1,'2026-01-12 02:40:52',1,'2026-01-12 02:40:59',1,'2026-01-12 02:41:07',NULL,'2026-01-12 02:39:56','2026-01-12 02:41:07',NULL);

/*Data for the table `stock_opname_details` */

insert  into `stock_opname_details`(`id`,`stock_opname_id`,`product_id`,`system_quantity`,`physical_quantity`,`difference_quantity`,`adjustment_type`,`notes`,`counted_by`,`created_at`,`updated_at`) values 
(2,1,239,0,20,20,'increase','asasa',NULL,'2026-01-11 06:10:09','2026-01-11 06:10:09'),
(3,2,3,10,100,90,'increase',NULL,NULL,'2026-01-12 04:18:02','2026-01-12 04:18:02');

/*Data for the table `stock_opnames` */

insert  into `stock_opnames`(`id`,`opname_number`,`opname_date`,`location_id`,`total_items`,`description`,`notes`,`status`,`created_by`,`completed_by`,`completed_at`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'OPN-2026-00001','2026-01-11',1,1,'asas','asasa','completed',1,1,'2026-01-11 06:10:20','2026-01-11 06:09:53','2026-01-11 06:10:20',NULL),
(2,'OPN-2026-00002','2026-01-12',1,1,'sasasa','asasa','draft',1,NULL,NULL,'2026-01-12 04:18:02','2026-01-12 04:18:02',NULL);

/*Data for the table `units` */

insert  into `units`(`id`,`code`,`name`,`symbol`,`description`,`is_active`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'PCS','Pieces',NULL,'Pieces Unit',1,'2026-01-04 09:50:43','2026-01-04 09:50:43',NULL);

/*Data for the table `user_permissions` */

/*Data for the table `user_roles` */

insert  into `user_roles`(`id`,`user_id`,`role_id`,`is_active`,`assigned_at`,`expires_at`,`created_at`,`updated_at`) values 
(1,1,1,1,'2025-10-30 14:44:38',NULL,'2025-10-30 06:44:38','2026-01-12 23:27:19'),
(2,1,2,0,'2025-10-30 14:44:38',NULL,'2025-10-30 06:44:38','2026-01-12 23:27:19'),
(3,1,3,0,'2025-10-30 14:44:38',NULL,'2025-10-30 06:44:38','2026-01-12 23:27:19'),
(5,2,2,1,'2026-01-04 10:48:38',NULL,'2026-01-04 10:48:38','2026-01-04 10:48:38'),
(7,3,3,1,'2026-01-04 10:50:28',NULL,'2026-01-04 10:50:27','2026-01-04 10:50:28'),
(8,4,1,1,'2026-01-04 10:53:48',NULL,'2026-01-04 10:53:47','2026-01-04 10:53:48');

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`email_verified_at`,`status`,`password`,`remember_token`,`created_at`,`updated_at`,`date_of_birth`,`phone_number`,`address`,`avatar_url`) values 
(1,'Sintya','superadmin@sintiya.com','2025-10-30 06:44:38','active','$2y$10$ko6EHMkjkOBHHCtwkK8iPO1dNXmXJpDmQAk79MRg07qwRpgbkg6/e',NULL,'2025-10-30 06:44:38','2026-01-04 11:02:12',NULL,'081234567890','Jakarta',NULL),
(2,'Intan','intan@sintiya.com','2025-10-30 06:44:38','active','$2y$12$Yo6K6iNLag9EyOq4qk8x9uCTO3KjqfY6iSwgi9x9yNMQMD2OJhoQq',NULL,'2025-10-30 06:44:38','2026-01-04 11:02:00',NULL,'081234567891','Jakarta',NULL),
(3,'Vani','vani@sintiya.com','2025-10-30 06:44:38','active','$2y$12$4w4k8PYUawmyXAaeSK3ogu65x0sJY5NCaRwpTwFGq/hCBAXTtfHx2',NULL,'2025-10-30 06:44:38','2026-01-04 11:00:51',NULL,'081234567892','Bandung',NULL),
(4,'Komang','komang@sintiya.com','2025-10-30 06:44:38','active','$2y$12$1J512JSrUsY2Y7CQu2ywfe06rVkkF6.wsTI3OGLE4D/XJtAJYI/8a',NULL,'2025-10-30 06:44:38','2026-01-04 11:01:45',NULL,'081234567893','Surabaya',NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
