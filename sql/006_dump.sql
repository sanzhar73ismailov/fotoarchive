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
AUTO_INCREMENT=7 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
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
AUTO_INCREMENT=4 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

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
  `foto_category_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Категория фото (внеш.кл. к таблице foto_category)',
  `deleted` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Удален (1 да, 0 нет)',
  `width` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'ширина изображения в px',
  `height` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'высота изображения в px',
  `insert_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
AUTO_INCREMENT=126 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

#
# Structure for the `foto_category` table : 
#

CREATE TABLE `foto_category` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `foto_upload_id` INTEGER(11) NOT NULL,
  `category_name` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Наименование категории',
  `customer_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Заказчик',
  `price` FLOAT(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Цена в рублях',
  `file_path` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'путь к  файлу-архиву',
  `file_zip` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'название файла-архива',
  `email_send` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Почта посылалась хоть раз (1 - да, 0 - нет)',
  `email_send_manual` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Почта отправлена в ручную (1 - да, 0 - нет)',
  `email_result` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Результат последней отправки (1 - отрпавилась, 0 - не отправилась)',
  `email_error` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'В случае не отправки текст ошибки',
  `email_date` DATETIME NOT NULL COMMENT 'Дата последней отправки почты',
  `insert_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `foto_upl_cust_catname` (`foto_upload_id`, `customer_id`, `category_name`),
  KEY `foto_upload_id` (`foto_upload_id`),
  KEY `customer_id` (`customer_id`)
)ENGINE=InnoDB
AUTO_INCREMENT=173 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

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
AUTO_INCREMENT=77 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

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
AUTO_INCREMENT=3 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

#
# Definition for the `foto_category_view` view : 
#

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW foto_category_view AS 
  select 
    `fc`.`id` AS `id`,
    `fc`.`foto_upload_id` AS `foto_upload_id`,
    `fc`.`category_name` AS `category_name`,
    `fc`.`customer_id` AS `customer_id`,
    `fc`.`price` AS `price`,
    `fc`.`file_path` AS `file_path`,
    `fc`.`file_zip` AS `file_zip`,
    `fc`.`email_send` AS `email_send`,
    `fc`.`email_send_manual` AS `email_send_manual`,
    `fc`.`email_result` AS `email_result`,
    `fc`.`email_error` AS `email_error`,
    `fc`.`email_date` AS `email_date`,
    `fc`.`insert_datetime` AS `insert_datetime`,
    `customer`.`name` AS `customer_name`,
    `customer`.`email` AS `customer_email`,
    count(`f`.`id`) AS `foto_number`,
    (`fc`.`price` * count(`f`.`id`)) AS `category_sum` 
  from 
    ((`foto_category` `fc` left join `foto` `f` on((`fc`.`id` = `f`.`foto_category_id`))) join `customer` on((`fc`.`customer_id` = `customer`.`id`))) 
  where 
    (`f`.`deleted` = 0) 
  group by 
    `fc`.`id`;

#
# Data for the `customer` table  (LIMIT 0,500)
#

INSERT INTO `customer` (`id`, `name`, `email`, `insert_datetime`) VALUES 
  (5,'ТОО Стулья','javajan@mail.ru','2017-11-19 17:26:02'),
  (6,'ТОО Столы','sanzhar73@mail.ru','2017-11-19 17:26:02');
COMMIT;

#
# Data for the `employee` table  (LIMIT 0,500)
#

INSERT INTO `employee` (`id`, `phone_number`, `last_name`, `first_name`, `patronymic_name`, `status`, `insert_datetime`) VALUES 
  (1,'23423444','Петров','Игорь','Ноиколаевич',1,'2017-11-17 09:05:47'),
  (2,'37723444','Сидоров','Альберт','Симонович',1,'2017-11-17 09:05:47'),
  (3,'234234777','Александрова','Анна','Дмитриевна',1,'2017-11-17 09:05:47');
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
  (2,'user1','e10adc3949ba59abbe56e057f20f883e','2017-11-14 06:49:27');
COMMIT;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;