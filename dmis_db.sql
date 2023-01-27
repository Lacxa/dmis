-- Adminer 4.8.1 MySQL 5.5.5-10.6.11-MariaDB-1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `chief_complaints`;
CREATE TABLE `chief_complaints` (
  `comp_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comp_token` int(11) NOT NULL,
  `comp_name` varchar(100) NOT NULL,
  `comp_author` varchar(100) NOT NULL,
  `comp_descriptions` text DEFAULT NULL,
  PRIMARY KEY (`comp_id`),
  UNIQUE KEY `comp_token` (`comp_token`),
  UNIQUE KEY `comp_name` (`comp_name`),
  KEY `comp_author` (`comp_author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `diseases`;
CREATE TABLE `diseases` (
  `dis_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `dis_title` varchar(255) NOT NULL,
  `dis_alias` varchar(20) DEFAULT NULL,
  `dis_token` varchar(100) NOT NULL,
  `dis_category` int(11) NOT NULL DEFAULT 200,
  `dis_author` varchar(100) NOT NULL,
  `dis_regdate` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`dis_id`),
  KEY `dis_category` (`dis_category`),
  KEY `dis_token` (`dis_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `disease_categories`;
CREATE TABLE `disease_categories` (
  `discat_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `discat_name` varchar(100) NOT NULL,
  `discat_author` varchar(100) NOT NULL,
  `discat_token` int(11) NOT NULL,
  `discat_description` text DEFAULT NULL,
  `discat_regdate` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`discat_id`),
  UNIQUE KEY `discat_name` (`discat_name`),
  UNIQUE KEY `discat_token` (`discat_token`),
  KEY `discat_author` (`discat_author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `employee`;
CREATE TABLE `employee` (
  `emp_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `emp_pf` varchar(100) NOT NULL,
  `emp_category` int(11) NOT NULL,
  `emp_role` int(11) NOT NULL,
  `emp_fname` varchar(20) NOT NULL,
  `emp_mname` varchar(20) DEFAULT NULL,
  `emp_lname` varchar(20) NOT NULL,
  `emp_mail` varchar(100) NOT NULL,
  `emp_phone` varchar(15) NOT NULL,
  `emp_password` varchar(255) NOT NULL DEFAULT '$2y$10$it08qiLF3HrGDFjY1Lnp1uw0AJ9s4u7t5TkAPU8meNY2jmuTa0uAa',
  `emp_isActive` int(11) NOT NULL DEFAULT 1,
  `emp_isIncharge` int(11) NOT NULL DEFAULT 0,
  `emp_isFirstLogin` int(11) NOT NULL DEFAULT 1,
  `emp_regdate` datetime NOT NULL DEFAULT current_timestamp(),
  `emp_pwd_changed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`emp_id`),
  UNIQUE KEY `emp_pf` (`emp_pf`),
  UNIQUE KEY `emp_mail` (`emp_mail`),
  UNIQUE KEY `emp_phone` (`emp_phone`),
  KEY `emp_category` (`emp_category`),
  KEY `emp_role` (`emp_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `employee_category`;
CREATE TABLE `employee_category` (
  `cat_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(100) NOT NULL,
  `cat_alias` varchar(20) NOT NULL,
  `cat_description` text DEFAULT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `investigation_category`;
CREATE TABLE `investigation_category` (
  `icat_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `icat_name` varchar(100) NOT NULL,
  `icat_alias` varchar(20) DEFAULT NULL,
  `icat_token` int(11) DEFAULT NULL,
  `icat_description` text DEFAULT NULL,
  PRIMARY KEY (`icat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `investigation_subcategory`;
CREATE TABLE `investigation_subcategory` (
  `isub_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `isub_category` int(11) NOT NULL,
  `isub_token` int(11) NOT NULL,
  `isub_name` varchar(100) NOT NULL,
  `isub_alias` varchar(20) DEFAULT NULL,
  `isub_description` text DEFAULT NULL,
  PRIMARY KEY (`isub_id`),
  KEY `isub_category` (`isub_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


SET NAMES utf8mb4;

DROP TABLE IF EXISTS `login_history`;
CREATE TABLE `login_history` (
  `log_id` varchar(40) NOT NULL,
  `log_emp_pf` varchar(40) NOT NULL,
  `log_ip` varchar(40) NOT NULL,
  `log_platform` varchar(40) NOT NULL,
  `log_browser` varchar(100) NOT NULL,
  `log_time` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `log_emp_pf` (`log_emp_pf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `medicines`;
CREATE TABLE `medicines` (
  `med_id` varchar(40) NOT NULL,
  `med_name` varchar(255) NOT NULL,
  `med_alias` varchar(50) NOT NULL,
  `med_token` int(11) NOT NULL,
  `med_category` int(11) NOT NULL,
  `med_format` int(11) NOT NULL,
  `med_is_active` int(11) NOT NULL DEFAULT 1,
  `med_author` varchar(100) NOT NULL,
  `med_descriptions` text DEFAULT NULL,
  `med_regdate` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`med_id`),
  UNIQUE KEY `med_token` (`med_token`),
  KEY `med_category` (`med_category`),
  KEY `med_author` (`med_author`),
  KEY `med_format` (`med_format`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `medicine_categories`;
CREATE TABLE `medicine_categories` (
  `medcat_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `medcat_name` varchar(100) NOT NULL,
  `medcat_author` varchar(100) NOT NULL,
  `medcat_token` int(11) NOT NULL,
  `medcat_description` text DEFAULT NULL,
  `medcat_regdate` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`medcat_id`),
  UNIQUE KEY `medcat_name` (`medcat_name`),
  KEY `medcat_author` (`medcat_author`),
  KEY `medcat_token` (`medcat_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `medicine_formats`;
CREATE TABLE `medicine_formats` (
  `format_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `format_name` varchar(100) NOT NULL,
  `format_author` varchar(100) NOT NULL,
  `format_token` int(11) NOT NULL,
  `format_description` text DEFAULT NULL,
  `format_regdate` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`format_id`),
  UNIQUE KEY `format_name` (`format_name`),
  UNIQUE KEY `format_token` (`format_token`),
  KEY `format_author` (`format_author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `medicine_units`;
CREATE TABLE `medicine_units` (
  `mu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mu_name` varchar(50) NOT NULL,
  `mu_unit` varchar(50) NOT NULL,
  `mu_token` int(11) NOT NULL,
  `mu_author` varchar(50) NOT NULL,
  `mu_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`mu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `version` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `patient`;
CREATE TABLE `patient` (
  `pat_id` varchar(40) NOT NULL,
  `pat_file_no` varchar(100) NOT NULL,
  `pat_fname` varchar(100) NOT NULL,
  `pat_mname` varchar(100) DEFAULT NULL,
  `pat_lname` varchar(100) NOT NULL,
  `pat_dob` date DEFAULT NULL,
  `pat_regdate` datetime DEFAULT current_timestamp(),
  `pat_gender` varchar(20) NOT NULL,
  `pat_occupation` varchar(100) NOT NULL,
  `pat_phone` varchar(15) NOT NULL,
  `pat_address` varchar(100) NOT NULL,
  `pat_em_name` varchar(100) NOT NULL,
  `pat_em_number` varchar(15) NOT NULL,
  `pat_nhif_card_no` varchar(100) DEFAULT NULL,
  `pat_nhif_auth_no` varchar(100) DEFAULT NULL,
  `pat_vote_no` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`pat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `patient_record`;
CREATE TABLE `patient_record` (
  `rec_id` varchar(40) NOT NULL,
  `rec_patient_id` varchar(40) NOT NULL,
  `rec_attendant_file_no` varchar(100) DEFAULT NULL,
  `rec_patient_file` varchar(100) NOT NULL,
  `rec_blood_pressure` varchar(100) DEFAULT NULL,
  `rec_pulse_rate` varchar(100) DEFAULT NULL,
  `rec_weight` varchar(100) DEFAULT NULL,
  `rec_height` varchar(100) DEFAULT NULL,
  `rec_temeperature` varchar(100) DEFAULT NULL,
  `rec_respiration` varchar(100) DEFAULT NULL,
  `rec_care` varchar(100) DEFAULT '0',
  `rec_regdate` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`rec_id`),
  KEY `rec_patient_id` (`rec_patient_id`),
  KEY `rec_patient_file` (`rec_patient_file`),
  KEY `rec_attendant_file_no` (`rec_attendant_file_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `patient_symptoms`;
CREATE TABLE `patient_symptoms` (
  `sy_id` varchar(40) NOT NULL,
  `sy_record_id` varchar(40) NOT NULL,
  `sy_record_patient_pf` varchar(100) DEFAULT NULL,
  `sy_complaints` text DEFAULT NULL,
  `sy_descriptions` text DEFAULT NULL,
  `sy_lab` int(11) NOT NULL DEFAULT 0,
  `sy_investigations` text DEFAULT NULL,
  `sy_diseases` varchar(100) DEFAULT NULL,
  `sy_medicines` text DEFAULT NULL,
  `sy_time` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`sy_id`),
  KEY `sy_record_id` (`sy_record_id`),
  KEY `sy_record_patient_pf` (`sy_record_patient_pf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

INSERT INTO `patient_symptoms` (`sy_id`, `sy_record_id`, `sy_record_patient_pf`, `sy_complaints`, `sy_descriptions`, `sy_lab`, `sy_investigations`, `sy_diseases`, `sy_medicines`, `sy_time`) VALUES
('1',	'1',	'W9-K5-MD-U4',	'111:14$$$days~amplifying$$$cough....._101:2$$$days~null',	'This is examination....',	1,	'146~@text:Positive&&@file:null^^115~@text:Abnormal&&@file:null',	'B52_A05',	'10000001~NORZOLE$(Doses$|$Antibiotics$|$Tablet):$null_956U95K3~+++20$days_MKM5FD0Q~+++null',	'2022-12-24 17:23:48'),
('10',	'10',	'QX-8X-0U-NU',	'101:5$$$days~null',	NULL,	0,	NULL,	'B52',	'903053X0~+++null',	'2023-01-16 14:08:40'),
('11',	'11',	'K8-P0-HA-HP',	'123:Today$$$morning~null',	NULL,	0,	NULL,	'A43_A00_A01',	'NXRD3U2G~+++30$days_FFY6G234~+++2$doses',	'2023-01-16 15:39:13'),
('1dcbb2292b9e4f47af42af5fac557032',	'ad04e0657e5e41b58e813acf0eb88791',	'W9-K5-MD-U4',	'101:5$$$days~null',	NULL,	1,	'124~@text:T&&@file:9ca70e9a8d3319b2f5a4a8ebe7010df8.pdf^^123~@text:35&&@file:null^^121~@text:Positive&&@file:null',	'B50',	'EX809MNP~+++null',	'2023-01-23 18:41:53'),
('2',	'2',	'AC-YX-Y7-H6',	'110:1$$$week~null_112:1$$$week~null_115:5$$$days~null',	'This is examination....',	1,	'120~@text:T&&@file:null^^148~@text:Positive&&@file:null^^118~@text:High&&@file:null^^140~@text:Positive&&@file:Test-One.pdf^^121~@text:Abnormal&&@file:null',	'A51_Z33',	'3UMQ75MN~+++null_3D3781WG~+++null',	'2022-12-24 17:35:28'),
('2108be43274b432d909fd5fd5399519d',	'79ef4fdf0e2847c793efff5a8a009ca6',	'K8-P0-HA-HP',	'104:6$$$days~null',	NULL,	0,	NULL,	'A01',	'NXRD3U2G~+++40$days$dose',	'2023-01-22 21:37:22'),
('3',	'3',	'K8-P0-HA-HP',	'118:5$$$days~null',	NULL,	0,	NULL,	'A28',	'R586MNY8~+++null',	'2022-12-24 18:13:59'),
('4',	'4',	'W9-K5-MD-U4',	'111:5$$$days~She$$$has$$$in$$$tough$$$days$$$of$$$coughing$$$for$$$five$$$days$$$now',	'<p>It is cough</p>',	0,	NULL,	'A31',	'G0KDRYRW~+++30$days_NM6FDM6G~+++2$bottles_PK0HP56C~+++1$bottle',	'2022-12-27 10:42:47'),
('5',	'5',	'W9-K5-MD-U4',	'101:3$$$days~null_102:5$$$days~null',	NULL,	1,	'108~@text:20&&@file:Test-One.pdf^^156~@text:2&&@file:null^^105~@text:34&&@file:Test-One-OOP-ITT-06103.pdf^^103~@text:43&&@file:Test-One.pdf',	'A00_N21',	'E549R996~+++30$days',	'2022-12-27 11:14:50'),
('774e776d23714de7a4fbf13e5f3a4aa6',	'09ec1da6e13049cd9ae23bbcede57676',	'W9-K5-MD-U4',	'102:5$$$days~null',	NULL,	0,	NULL,	'A01',	'903053X0~+++30$days',	'2023-01-22 19:07:13'),
('9',	'9',	'QX-8X-0U-NU',	'123:2$$$days~null',	NULL,	1,	'124~@text:16&&@file:null^^123~@text:2&&@file:null^^115~@text:Negative&&@file:null^^121~@text:Positive&&@file:null^^117~@text:Abnormal&&@file:null^^148~@text:Positive&&@file:null',	'G51',	'903053X0~+++78',	'2023-01-10 14:16:09'),
('a0a751285b554106843e686edb17f239',	'013340c0b5024dc780f8e9449ffd5087',	'W9-K5-MD-U4',	'101:4$$$days~null',	NULL,	1,	'124~null^^123~null^^146~null^^115~null',	NULL,	NULL,	'2023-01-27 14:38:58'),
('aad5ea6fce234d0e8cbd48321a35e29e',	'd797d7c856de43aeb497dd059eeb3e26',	'QX-8X-0U-NU',	'100:130$$$months~null',	NULL,	1,	'124~@text:T&&@file:4ac4a6ee13c43328877687416d45f99d.pdf^^123~@text:Negative&&@file:null^^134~@text:10&&@file:null^^125~@text:8&&@file:null',	'A00',	'PF95073R~+++null',	'2023-01-24 16:55:31'),
('b5e5b8039d0a4efb8469e2eb17286bfc',	'8036c9af8ea24c239657e1e88b30e6cf',	'QX-8X-0U-NU',	'100:6$$$days~null',	NULL,	0,	NULL,	'R07',	'903053X0~+++2$doses',	'2023-01-18 02:21:15'),
('c83a29d392d7413ba7a48e84a5be5c70',	'9ffe493f8155409794c8c2a4bf806776',	'3H-4W-DQ-3A',	'104:5$$$days~null',	NULL,	0,	NULL,	'B52',	'EX809MNP~+++null',	'2023-01-23 01:10:14');

DROP TABLE IF EXISTS `patient_visit`;
CREATE TABLE `patient_visit` (
  `vs_id` varchar(40) NOT NULL,
  `vs_record_id` varchar(40) NOT NULL,
  `vs_record_patient_pf` varchar(100) DEFAULT NULL,
  `vs_visit` varchar(100) DEFAULT NULL,
  `vs_attendants` varchar(255) DEFAULT NULL,
  `vs_time` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`vs_id`),
  KEY `vs_record_id` (`vs_record_id`),
  KEY `vs_record_patient_pf` (`vs_record_patient_pf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `stock`;
CREATE TABLE `stock` (
  `st_id` varchar(40) NOT NULL,
  `st_batch` varchar(40) NOT NULL,
  `st_code` varchar(40) NOT NULL,
  `st_medicine` int(11) NOT NULL,
  `st_unit` int(11) NOT NULL,
  `st_unit_value` varchar(100) NOT NULL,
  `st_total` int(11) NOT NULL DEFAULT 0,
  `st_usage` int(11) NOT NULL DEFAULT 0,
  `st_author` varchar(20) NOT NULL,
  `st_date_created` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`st_id`),
  UNIQUE KEY `st_code` (`st_code`),
  KEY `st_parent` (`st_batch`),
  KEY `st_medicine` (`st_medicine`),
  KEY `st_author` (`st_author`),
  KEY `st_unit` (`st_unit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `stock_batches`;
CREATE TABLE `stock_batches` (
  `sb_id` varchar(40) NOT NULL,
  `sb_number` varchar(40) NOT NULL,
  `sb_supplier` varchar(100) NOT NULL,
  `sb_entry_date` date NOT NULL,
  `sb_descrptions` varchar(100) DEFAULT NULL,
  `sb_author` varchar(100) DEFAULT NULL,
  `sb_active` int(11) DEFAULT 0,
  `sb_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`sb_id`),
  UNIQUE KEY `sb_number` (`sb_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `stock_usage`;
CREATE TABLE `stock_usage` (
  `su_id` varchar(40) NOT NULL,
  `su_record` varchar(40) NOT NULL,
  `su_stock` varchar(40) NOT NULL,
  `su_usage` int(11) NOT NULL,
  `su_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  `role_alias` varchar(20) NOT NULL,
  `role_descriptions` text DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`),
  UNIQUE KEY `role_alias` (`role_alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


-- 2023-01-27 13:25:31
