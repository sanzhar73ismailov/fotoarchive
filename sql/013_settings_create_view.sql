CREATE TABLE `settings` (
  `id` TINYINT(4) NOT NULL DEFAULT '1',
  `email_login` VARCHAR(30) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `email_pass` VARCHAR(30) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `email_sender` VARCHAR(30) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `email_from` VARCHAR(30) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `email_file_limit_mb` INTEGER(11) NOT NULL COMMENT 'размер файла, пересылаемый как прикрепление, если больше не прикрепляется',
  `max_img_width` INTEGER(11) NOT NULL COMMENT 'Максимальные размеры изображения в пикселях',
  `email_msg_tmpl` TEXT COLLATE utf8_general_ci NOT NULL COMMENT 'Шаблон эл. почты сообщения',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';


/* Data for the `settings` table  (Records 1 - 1) */

INSERT INTO `settings` (`id`, `email_login`, `email_pass`, `email_sender`, `email_from`, `email_file_limit_mb`, `max_img_width`, `email_msg_tmpl`) VALUES 
  (1, 'raskleyka73@yandex.ru', 'Ra123456Ra123456', 'raskleyka73@yandex.ru', 'raskleyka73@yandex.ru', 25, 2048, 
  'Здравствуйте <REPRESENTATIVE>.\r\n\r\n<p>Отправляем отчет в виде файла <FILENAME>. Можете скачать его по ссылке <FILELINK>.\r\n\r\n<p>Карта района: \r\n<br><REGION_MAP>\r\n\r\n<p>С уважением, <EMAIL_FROM>.');

COMMIT;

ALTER TABLE region  ADD COLUMN `file_name` VARCHAR(30) COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Название файла';

-- создание view foto_category_view

drop VIEW foto_category_view;
CREATE VIEW foto_category_view AS 
	select 
		fuc.*,
		cat.customer_id,
		cat.name category_name,
		cat.status,
		cust.name customer_name,
		cust.name_official,
		cust.email,
		cust.phone_number,
		cust.representative,
        r.id region_id,
        r.name region_name,
		r.file_name region_file_name,
		count(fcat.id) foto_number,
		(fcat.price * count(fcat.id)) AS category_sum 
	from 
		foto_upload_category fuc
		left join category cat on (cat.id=fuc.category_id)
		left join customer cust on (cust.id= cat.customer_id)
		inner join foto_category fcat on (fcat.foto_upload_category_id=fuc.id)
        inner join foto_upload fu on (fuc.`foto_upload_id`=fu.id)
        inner join region r on (fu.`region_id`=r.`id`)
	group by fuc.id;

