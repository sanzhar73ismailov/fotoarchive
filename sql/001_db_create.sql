/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

SET FOREIGN_KEY_CHECKS=0;

DROP DATABASE IF EXISTS `fotoarchive`;

CREATE DATABASE `fotoarchive`
    CHARACTER SET 'utf8'
    COLLATE 'utf8_general_ci';

USE `fotoarchive`;

#
# Structure for the `customer` table : 
#

CREATE TABLE `customer` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Наименование',
  `email` VARCHAR(32) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Эл. почта',
  `insert_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
COMMENT='Заказчики';

#
# Structure for the `employee` table : 
#

CREATE TABLE `employee` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `phone_number` VARCHAR(12) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `last_name` VARCHAR(32) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `first_name` VARCHAR(32) COLLATE utf8_general_ci DEFAULT '',
  `patronymic_name` VARCHAR(32) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'Статус сотрудника (1 - активный, 0 - неактивный)',
  `insert_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

#
# Structure for the `foto` table : 
#

CREATE TABLE `foto` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `foto_upload_id` INTEGER(11) NOT NULL,
  `foto_name` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `is_address` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'является ли адресом',
  `foto_category_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Категория фото (внеш.кл. к таблице foto_category)',
  `deleted` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Удален (1 да, 0 нет)',
  `insert_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
AUTO_INCREMENT=99 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

#
# Structure for the `foto_category` table : 
#

CREATE TABLE `foto_category` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `foto_upload_id` INTEGER(11) NOT NULL,
  `category_name` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Наименование категории',
  `customer_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Заказчик',
  `price` FLOAT(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Цена в рублях',
  `insert_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `foto_upl_cust_catname` (`foto_upload_id`, `customer_id`, `category_name`),
  KEY `foto_upload_id` (`foto_upload_id`),
  KEY `customer_id` (`customer_id`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

#
# Structure for the `foto_upload` table : 
#

CREATE TABLE `foto_upload` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `employee_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Работник',
  `date` DATE NOT NULL COMMENT 'Дата съемки',
  `region_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Район',
  `user_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Менеджер',
  `insert_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
AUTO_INCREMENT=45 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

#
# Structure for the `region` table : 
#

CREATE TABLE `region` (
  `id` INTEGER(11) NOT NULL,
  `name` VARCHAR(32) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Наименование',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)ENGINE=InnoDB
CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
COMMENT='Районы';

#
# Structure for the `user` table : 
#

CREATE TABLE `user` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(32) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `password` VARCHAR(255) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `insert_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

#
# Data for the `region` table  (LIMIT 0,500)
#

INSERT INTO `region` (`id`, `name`) VALUES 
  (10,'b01'),
  (20,'b02'),
  (30,'b03'),
  (40,'b04'),
  (50,'b05'),
  (60,'b06'),
  (70,'d01'),
  (80,'d02'),
  (90,'d03'),
  (100,'d04'),
  (110,'d05'),
  (120,'d06'),
  (130,'d07'),
  (140,'d08'),
  (150,'d09'),
  (160,'d10'),
  (170,'k01'),
  (180,'k02'),
  (190,'k03'),
  (200,'k04'),
  (210,'c01'),
  (220,'c02'),
  (230,'c03'),
  (240,'s01'),
  (250,'s02'),
  (260,'s03'),
  (270,'s04'),
  (280,'s05'),
  (290,'s06'),
  (300,'a01'),
  (310,'a02'),
  (320,'a03'),
  (330,'a04'),
  (340,'a05'),
  (350,'a06'),
  (360,'a07'),
  (370,'a08'),
  (380,'a09'),
  (390,'v01'),
  (400,'v02'),
  (410,'n01'),
  (420,'n02');
COMMIT;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;