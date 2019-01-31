# SQL Manager 2007 for MySQL 4.4.0.3
# ---------------------------------------
# Host     : localhost
# Port     : 3306
# Database : fotoarchive


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE `fotoarchive`
    CHARACTER SET 'utf8'
    COLLATE 'utf8_general_ci';

USE `fotoarchive`;

#
# Structure for the `category` table : 
#

CREATE TABLE `category` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Наименование категории',
  `customer_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Заказчик',
  `price` FLOAT(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Цена в рублях',
  `insert_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `foto_upl_cust_catname` (`name`, `customer_id`, `price`),
  KEY `customer_id` (`customer_id`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

#
# Structure for the `customer` table : 
#

CREATE TABLE `customer` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Наименование',
  `name_official` VARCHAR(200) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Официальное наименование',
  `email` VARCHAR(32) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Эл. почта',
  `phone_number` VARCHAR(30) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Телефон',
  `representative` VARCHAR(50) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Представитель',
  `insert_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
)ENGINE=InnoDB
AUTO_INCREMENT=9 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
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
AUTO_INCREMENT=14 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

#
# Structure for the `foto` table : 
#

CREATE TABLE `foto` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `address_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Родитель id (id foto, которое является адресом)',
  `foto_upload_id` INTEGER(11) NOT NULL,
  `foto_name` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `foto_old_name` VARCHAR(30) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Предыдущее имя файла (до загрузки)',
  `is_address` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'является ли адресом',
  `deleted` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Удален (1 да, 0 нет)',
  `width` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'ширина изображения в px',
  `height` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'высота изображения в px',
  `insert_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
AUTO_INCREMENT=150 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

#
# Structure for the `foto_category` table : 
#

CREATE TABLE `foto_category` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `foto_id` INTEGER(11) NOT NULL DEFAULT '0',
  `category_id` INTEGER(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
COMMENT='Фото-Категории';

#
# Structure for the `status` table : 
#

CREATE TABLE `status` (
  `id` INTEGER(11) NOT NULL,
  `name` VARCHAR(20) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Наименование',
  `name_ru` VARCHAR(20) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Наименование на русском (для GUI)',
  `descr` VARCHAR(200) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'описание',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

#
# Structure for the `foto_upload` table : 
#

CREATE TABLE `foto_upload` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `employee_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Работник',
  `date` DATE NOT NULL COMMENT 'Дата съемки',
  `region_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Район',
  `user_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Менеджер',
  `status_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'статус отчета (вн.ключ таблицы status)',
  `insert_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `status_id` (`status_id`),
  CONSTRAINT `foto_upload_fk` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`)
)ENGINE=InnoDB
AUTO_INCREMENT=81 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

#
# Structure for the `foto_upload_category` table : 
#

CREATE TABLE `foto_upload_category` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `foto_upload_id` INTEGER(11) NOT NULL,
  `cutegoty_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Категория',
  `file_path` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'путь к  файлу-архиву',
  `file_zip` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'название файла-архива',
  `email_send` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Почта посылалась хоть раз (1 - да, 0 - нет)',
  `email_send_manual` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Почта отправлена в ручную (1 - да, 0 - нет)',
  `email_result` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Результат последней отправки (1 - отрпавилась, 0 - не отправилась)',
  `email_error` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'В случае не отправки текст ошибки',
  `email_date` DATETIME NOT NULL COMMENT 'Дата последней отправки почты',
  `insert_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `foto_upload_id` (`foto_upload_id`),
  KEY `customer_id` (`cutegoty_id`)
)ENGINE=InnoDB
AUTO_INCREMENT=179 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

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
AUTO_INCREMENT=4 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

#
# Data for the `customer` table  (LIMIT 0,500)
#

INSERT INTO `customer` (`id`, `name`, `name_official`, `email`, `phone_number`, `representative`, `insert_datetime`) VALUES 
  (5,'ТОО Стулья','','javajan@mail.ru','','','2017-11-19 17:26:02'),
  (6,'ТОО Столы','','sanzhar73@mail.ru','','','2017-11-19 17:26:02'),
  (7,'Диваны','','sanzhar73@gmail.com','','','2017-11-30 08:52:13'),
  (8,'ddd','','dasdas@dd','','','2017-11-30 08:52:31');
COMMIT;

#
# Data for the `employee` table  (LIMIT 0,500)
#

INSERT INTO `employee` (`id`, `phone_number`, `last_name`, `first_name`, `patronymic_name`, `status`, `insert_datetime`) VALUES 
  (1,'23423444','Петров','Игорь','Ноиколаевич',1,'2017-11-17 09:05:47'),
  (2,'37723444','Сидоров','Альберт','Симонович',1,'2017-11-17 09:05:47'),
  (3,'453535','Александрова1','Анна','Дмитриевна',1,'2017-11-17 09:05:47'),
  (4,'3434','выывавыа','ere','etert',1,'2017-11-30 07:27:03'),
  (5,'в','ы','ы','ы',1,'2017-11-30 07:40:09'),
  (6,'ц','ц','ц','ц',1,'2017-11-30 08:04:25'),
  (7,'апвапа','ы','вап','вапвап',1,'2017-11-30 08:10:23'),
  (8,'fsdfsdfsdf','ы','с','с',1,'2017-11-30 08:11:41'),
  (9,'s','ssя','sфывфы','s',0,'2017-11-30 08:24:43'),
  (10,'sdfsdfsd','Аложлдожл','g','hgfh',1,'2017-11-30 08:25:48'),
  (11,'йцуцйу','фыв','фыв','йцу',0,'2017-11-30 08:32:08'),
  (12,'в','цуцйу','в','в',1,'2017-11-30 08:33:42'),
  (13,'ф','я','я','ф',1,'2017-11-30 08:40:30');
COMMIT;

#
# Data for the `foto` table  (LIMIT 0,500)
#

INSERT INTO `foto` (`id`, `address_id`, `foto_upload_id`, `foto_name`, `foto_old_name`, `is_address`, `deleted`, `width`, `height`, `insert_datetime`) VALUES 
  (126,0,77,'uploads/directories/77/20171129-120410-1724260869.jpg','foto01.jpg',0,1,3456,2592,'2017-11-29 09:04:10'),
  (127,0,77,'uploads/directories/77/20171129-120410-635310517.jpg','foto02.jpg',0,1,4608,3456,'2017-11-29 09:04:10'),
  (128,0,77,'uploads/directories/77/20171129-120410-113965635.jpg','foto03.jpg',1,0,4608,3456,'2017-11-29 09:04:10'),
  (129,0,77,'uploads/directories/77/20171129-120410-460513067.jpg','foto04.jpg',1,0,4608,3456,'2017-11-29 09:04:10'),
  (130,129,77,'uploads/directories/77/20171129-120410-1430873141.jpg','foto05 - копия (3).jpg',0,0,4608,3456,'2017-11-29 09:04:10'),
  (131,129,77,'uploads/directories/77/20171129-120410-1750888623.jpg','foto06 - копия (4).jpg',0,0,4608,3456,'2017-11-29 09:04:10'),
  (132,129,77,'uploads/directories/77/20171129-120410-93065660.jpg','foto07 - копия (5).jpg',0,0,4608,3456,'2017-11-29 09:04:10'),
  (133,129,77,'uploads/directories/77/20171129-120410-290709291.jpg','foto08 - копия (2).jpg',0,0,4608,3456,'2017-11-29 09:04:10'),
  (134,129,77,'uploads/directories/77/20171129-120410-2050636271.jpg','foto09 - копия.jpg',0,0,4608,3456,'2017-11-29 09:04:10'),
  (135,129,77,'uploads/directories/77/20171129-120410-1744414489.jpg','foto09 - копия01.jpg',0,0,3456,2592,'2017-11-29 09:04:10'),
  (136,0,78,'uploads/directories/78/20171130-115618-1495535425.jpg','foto01.jpg',1,0,3456,2592,'2017-11-30 08:56:18'),
  (137,0,79,'uploads/directories/79/20171201-082532-769209442.jpg','foto01_adr01.jpg',1,0,3456,2592,'2017-12-01 05:25:32'),
  (138,137,79,'uploads/directories/79/20171201-082532-477181798.jpg','foto02_Адрес1Столы.jpg',0,0,3456,2592,'2017-12-01 05:25:32'),
  (139,136,78,'uploads/directories/78/20171201-084537-2093871788.jpg','foto01_adr01.jpg',0,0,3456,2592,'2017-12-01 05:45:37'),
  (140,136,78,'uploads/directories/78/20171201-084537-1580534162.jpg','foto02_Адрес1Столы.jpg',0,0,3456,2592,'2017-12-01 05:45:37'),
  (141,0,2,'uploads/directories/2/20171201-111047-926420794.jpg','foto01_adr01.jpg',0,0,3456,2592,'2017-12-01 08:10:47'),
  (142,0,2,'uploads/directories/2/20171201-111047-1839731907.jpg','foto02_Адрес1Столы.jpg',0,0,3456,2592,'2017-12-01 08:10:47'),
  (143,0,2,'uploads/directories/2/20171201-111047-1702830210.jpg','foto03_Адрес1Диваны.jpg',0,0,3456,2592,'2017-12-01 08:10:47'),
  (144,0,80,'uploads/directories/80/20171201-120550-334613262.jpg','foto01_adr01.jpg',1,0,3456,2592,'2017-12-01 09:05:50'),
  (145,144,80,'uploads/directories/80/20171201-120550-1788951956.jpg','foto02_Адрес1Столы.jpg',0,0,3456,2592,'2017-12-01 09:05:50'),
  (146,144,80,'uploads/directories/80/20171201-120550-1437003402.jpg','foto03_Адрес1Диваны.jpg',0,0,3456,2592,'2017-12-01 09:05:50'),
  (147,144,80,'uploads/directories/80/20171201-120550-1454339278.jpg','foto04_adr02.jpg',0,0,3456,2592,'2017-12-01 09:05:50'),
  (148,144,80,'uploads/directories/80/20171201-120550-716874161.jpg','foto05_Адрес2Столы.jpg',0,0,3456,2592,'2017-12-01 09:05:50'),
  (149,144,80,'uploads/directories/80/20171201-120550-437296334.jpg','foto06_Адрес2Диваны.jpg',0,1,3456,2592,'2017-12-01 09:05:50');
COMMIT;

#
# Data for the `status` table  (LIMIT 0,500)
#

INSERT INTO `status` (`id`, `name`, `name_ru`, `descr`) VALUES 
  (0,'draft','Черновик','когда отчет создан, но одно из полей или все поля не заполнены или фото не загружены'),
  (1,'created','Фото загружены','когда файлы загружены, данные по сотруднику, дате отчета, пользователю заполнены'),
  (2,'categories_added','Присвоены категории','когда к отчету созданы категории - список кратких описание объявлений с привязкой к заказчику'),
  (3,'matched','Сопоставен','когда фотографии сопоставлены с категориями'),
  (4,'closed','Закрыт','когда отчет закрыт, то есть не подлежит изменениям');
COMMIT;

#
# Data for the `foto_upload` table  (LIMIT 0,500)
#

INSERT INTO `foto_upload` (`id`, `employee_id`, `date`, `region_id`, `user_id`, `status_id`, `insert_datetime`) VALUES 
  (2,9,'2017-12-08',180,0,1,'2017-12-01 08:10:47'),
  (77,1,'2017-11-01',170,0,3,'2017-11-29 09:04:10'),
  (78,9,'2017-11-03',110,0,4,'2017-11-30 08:56:18'),
  (79,3,'2017-12-08',180,0,4,'2017-12-01 05:25:32'),
  (80,10,'2017-12-14',140,2,3,'2017-12-01 08:11:45');
COMMIT;

#
# Data for the `foto_upload_category` table  (LIMIT 0,500)
#

INSERT INTO `foto_upload_category` (`id`, `foto_upload_id`, `cutegoty_id`, `file_path`, `file_zip`, `email_send`, `email_send_manual`, `email_result`, `email_error`, `email_date`, `insert_datetime`) VALUES 
  (173,77,6,'','',0,0,0,'','0000-00-00 00:00:00','2017-11-29 09:06:35'),
  (174,77,6,'','',0,0,0,'','0000-00-00 00:00:00','2017-11-29 09:06:35'),
  (175,79,6,'arc_files/79','qq, k02, 2017-12-08, (1, фото).zip',1,0,1,'','2017-12-01 08:26:08','2017-12-01 05:25:42'),
  (176,78,5,'arc_files/78','aaa, d05, 2017-11-03, (2, фото).zip',1,0,1,'','2017-12-01 08:46:24','2017-12-01 05:45:47'),
  (177,80,6,'','',0,0,0,'','0000-00-00 00:00:00','2017-12-01 09:06:14'),
  (178,80,7,'','',0,0,0,'','0000-00-00 00:00:00','2017-12-01 09:06:14');
COMMIT;

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

#
# Data for the `user` table  (LIMIT 0,500)
#

INSERT INTO `user` (`id`, `login`, `password`, `insert_datetime`) VALUES 
  (1,'admin','e10adc3949ba59abbe56e057f20f883e','2017-11-14 06:49:27'),
  (2,'user1','e10adc3949ba59abbe56e057f20f883e','2017-11-14 06:49:27'),
  (3,'user2','e10adc3949ba59abbe56e057f20f883e','2017-11-30 09:15:34');
COMMIT;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;