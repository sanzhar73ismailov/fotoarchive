-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Дек 08 2017 г., 11:19
-- Версия сервера: 5.6.37
-- Версия PHP: 7.0.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `fotoarchive`
--

-- --------------------------------------------------------

--
-- Структура таблицы `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT 'Наименование категории',
  `customer_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Заказчик',
  `price` float(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Цена в рублях',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Статус категории (1 - активный, 0 - неактивный)',
  `insert_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Наименование',
  `name_official` varchar(200) NOT NULL DEFAULT '' COMMENT 'Официальное наименование',
  `email` varchar(32) NOT NULL COMMENT 'Эл. почта',
  `phone_number` varchar(30) NOT NULL DEFAULT '' COMMENT 'Телефон',
  `representative` varchar(50) NOT NULL DEFAULT '' COMMENT 'Представитель',
  `insert_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Заказчики';

-- --------------------------------------------------------

--
-- Структура таблицы `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `phone_number` varchar(12) NOT NULL DEFAULT '',
  `last_name` varchar(32) NOT NULL DEFAULT '',
  `first_name` varchar(32) DEFAULT '',
  `patronymic_name` varchar(32) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Статус сотрудника (1 - активный, 0 - неактивный)',
  `insert_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `foto`
--

CREATE TABLE `foto` (
  `id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Родитель id (id foto, которое является адресом)',
  `foto_upload_id` int(11) NOT NULL,
  `foto_name` varchar(100) NOT NULL,
  `foto_old_name` varchar(100) NOT NULL DEFAULT '' COMMENT 'Предыдущее имя файла (до загрузки)',
  `is_address` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'является ли адресом',
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Удален (1 да, 0 нет)',
  `width` int(11) NOT NULL DEFAULT '0' COMMENT 'ширина изображения в px',
  `height` int(11) NOT NULL DEFAULT '0' COMMENT 'высота изображения в px',
  `insert_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `foto_category`
--

CREATE TABLE `foto_category` (
  `id` int(11) NOT NULL,
  `foto_id` int(11) NOT NULL DEFAULT '0',
  `foto_upload_category_id` int(11) NOT NULL DEFAULT '0' COMMENT 'внеш.ключ к таблице foto_upload_category',
  `price` float(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Цена в рублях',
  `foto_upload_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Внеш. ключ. к таблице foto_upload'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Фото-Категории';

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `foto_category_view`
-- (See below for the actual view)
--
CREATE TABLE `foto_category_view` (
`id` int(11)
,`foto_upload_id` int(11)
,`category_id` int(11)
,`file_path` varchar(100)
,`file_zip` varchar(100)
,`email_send` tinyint(1)
,`email_send_manual` tinyint(1)
,`email_result` tinyint(1)
,`email_error` varchar(100)
,`email_date` datetime
,`price` float(6,2)
,`insert_datetime` timestamp
,`customer_id` int(11)
,`category_name` varchar(100)
,`status` tinyint(1)
,`customer_name` varchar(100)
,`name_official` varchar(200)
,`email` varchar(32)
,`phone_number` varchar(30)
,`representative` varchar(50)
,`foto_number` bigint(21)
,`category_sum` double(19,2)
);

-- --------------------------------------------------------

--
-- Структура таблицы `foto_upload`
--

CREATE TABLE `foto_upload` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Работник',
  `date` date NOT NULL COMMENT 'Дата съемки',
  `region_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Район',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Менеджер',
  `status_id` int(11) NOT NULL DEFAULT '0' COMMENT 'статус отчета (вн.ключ таблицы status)',
  `insert_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `foto_upload_category`
--

CREATE TABLE `foto_upload_category` (
  `id` int(11) NOT NULL,
  `foto_upload_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Категория',
  `file_path` varchar(100) NOT NULL DEFAULT '' COMMENT 'путь к  файлу-архиву',
  `file_zip` varchar(100) NOT NULL DEFAULT '' COMMENT 'название файла-архива',
  `email_send` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Почта посылалась хоть раз (1 - да, 0 - нет)',
  `email_send_manual` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Почта отправлена в ручную (1 - да, 0 - нет)',
  `email_result` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Результат последней отправки (1 - отрпавилась, 0 - не отправилась)',
  `email_error` varchar(100) NOT NULL DEFAULT '' COMMENT 'В случае не отправки текст ошибки',
  `email_date` datetime NOT NULL COMMENT 'Дата последней отправки почты',
  `price` float(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Цена в рублях',
  `insert_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `region`
--

CREATE TABLE `region` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL COMMENT 'Наименование',
  file_name varchar(30) NOT NULL COMMENT 'Наименование файла картинки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Районы';

--
-- Дамп данных таблицы `region`
--

INSERT INTO `region` (`id`, `name`) VALUES
(300, 'a01'),
(310, 'a02'),
(320, 'a03'),
(330, 'a04'),
(340, 'a05'),
(350, 'a06'),
(360, 'a07'),
(370, 'a08'),
(380, 'a09'),
(10, 'b01'),
(20, 'b02'),
(30, 'b03'),
(40, 'b04'),
(50, 'b05'),
(60, 'b06'),
(210, 'c01'),
(220, 'c02'),
(230, 'c03'),
(70, 'd01'),
(80, 'd02'),
(90, 'd03'),
(100, 'd04'),
(110, 'd05'),
(120, 'd06'),
(130, 'd07'),
(140, 'd08'),
(150, 'd09'),
(160, 'd10'),
(170, 'k01'),
(180, 'k02'),
(190, 'k03'),
(200, 'k04'),
(410, 'n01'),
(420, 'n02'),
(240, 's01'),
(250, 's02'),
(260, 's03'),
(270, 's04'),
(280, 's05'),
(290, 's06'),
(390, 'v01'),
(400, 'v02');

-- --------------------------------------------------------

--
-- Структура таблицы `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT 'Наименование',
  `name_ru` varchar(20) NOT NULL DEFAULT '' COMMENT 'Наименование на русском (для GUI)',
  `descr` varchar(200) NOT NULL DEFAULT '' COMMENT 'описание'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `status`
--

INSERT INTO `status` (`id`, `name`, `name_ru`, `descr`) VALUES
(0, 'draft', 'Черновик', 'когда отчет создан, но одно из полей или все поля не заполнены или фото не загружены'),
(1, 'created', 'Фото загружены', 'когда файлы загружены, данные по сотруднику, дате отчета, пользователю заполнены'),
(2, 'categories_added', 'Присвоены категории', 'когда к отчету созданы категории - список кратких описание объявлений с привязкой к заказчику'),
(3, 'matched', 'Сопоставен', 'когда фотографии сопоставлены с категориями'),
(4, 'closed', 'Закрыт', 'когда отчет закрыт, то есть не подлежит изменениям');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `login` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `insert_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `login`, `password`, `insert_datetime`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', '2017-11-14 05:49:27'),
(2, 'user1', 'e10adc3949ba59abbe56e057f20f883e', '2017-11-14 05:49:27'),
(3, 'user2', 'e10adc3949ba59abbe56e057f20f883e', '2017-11-30 08:15:34');

-- --------------------------------------------------------

--
-- Структура для представления `foto_category_view`
--
DROP TABLE IF EXISTS `foto_category_view`;

CREATE VIEW `foto_category_view`  AS  select `fuc`.`id` AS `id`,`fuc`.`foto_upload_id` AS `foto_upload_id`,`fuc`.`category_id` AS `category_id`,`fuc`.`file_path` AS `file_path`,`fuc`.`file_zip` AS `file_zip`,`fuc`.`email_send` AS `email_send`,`fuc`.`email_send_manual` AS `email_send_manual`,`fuc`.`email_result` AS `email_result`,`fuc`.`email_error` AS `email_error`,`fuc`.`email_date` AS `email_date`,`fuc`.`price` AS `price`,`fuc`.`insert_datetime` AS `insert_datetime`,`cat`.`customer_id` AS `customer_id`,`cat`.`name` AS `category_name`,`cat`.`status` AS `status`,`cust`.`name` AS `customer_name`,`cust`.`name_official` AS `name_official`,`cust`.`email` AS `email`,`cust`.`phone_number` AS `phone_number`,`cust`.`representative` AS `representative`,count(`fcat`.`id`) AS `foto_number`,(`fcat`.`price` * count(`fcat`.`id`)) AS `category_sum` from (((`foto_upload_category` `fuc` left join `category` `cat` on((`cat`.`id` = `fuc`.`category_id`))) left join `customer` `cust` on((`cust`.`id` = `cat`.`customer_id`))) join `foto_category` `fcat` on((`fcat`.`foto_upload_category_id` = `fuc`.`id`))) group by `fuc`.`id` ;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `foto_upl_cust_catname` (`name`,`customer_id`,`price`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Индексы таблицы `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `foto`
--
ALTER TABLE `foto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `foto_upload_id` (`foto_upload_id`,`foto_old_name`),
  ADD KEY `foto_upload_id_2` (`foto_upload_id`);

--
-- Индексы таблицы `foto_category`
--
ALTER TABLE `foto_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foto_id` (`foto_id`),
  ADD KEY `foto_upload_id` (`foto_upload_id`);

--
-- Индексы таблицы `foto_upload`
--
ALTER TABLE `foto_upload`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `region_id` (`region_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `foto_upload_category`
--
ALTER TABLE `foto_upload_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foto_upload_id` (`foto_upload_id`),
  ADD KEY `customer_id` (`category_id`);

--
-- Индексы таблицы `region`
--
ALTER TABLE `region`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT для таблицы `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT для таблицы `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT для таблицы `foto`
--
ALTER TABLE `foto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT для таблицы `foto_category`
--
ALTER TABLE `foto_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT для таблицы `foto_upload`
--
ALTER TABLE `foto_upload`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT для таблицы `foto_upload_category`
--
ALTER TABLE `foto_upload_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `category_fk` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

--
-- Ограничения внешнего ключа таблицы `foto`
--
ALTER TABLE `foto`
  ADD CONSTRAINT `foto_fk` FOREIGN KEY (`foto_upload_id`) REFERENCES `foto_upload` (`id`);

--
-- Ограничения внешнего ключа таблицы `foto_category`
--
ALTER TABLE `foto_category`
  ADD CONSTRAINT `foto_category_fk` FOREIGN KEY (`foto_id`) REFERENCES `foto` (`id`);

--
-- Ограничения внешнего ключа таблицы `foto_upload`
--
ALTER TABLE `foto_upload`
  ADD CONSTRAINT `foto_upload_fk` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`),
  ADD CONSTRAINT `foto_upload_fk1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`),
  ADD CONSTRAINT `foto_upload_fk2` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`),
  ADD CONSTRAINT `foto_upload_fk3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
