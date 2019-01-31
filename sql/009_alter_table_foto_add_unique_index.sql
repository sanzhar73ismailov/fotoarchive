ALTER TABLE `foto` ADD UNIQUE `foto_upload_id` (`foto_upload_id`, `foto_old_name`);
ALTER TABLE category  ADD COLUMN `status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'Статус категории (1 - активный, 0 - неактивный)' AFTER price;
ALTER TABLE `foto_category` CHANGE COLUMN `category_id` `foto_upload_category_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'внеш.ключ к таблице foto_upload_category';

ALTER TABLE `foto_category` ADD COLUMN `price` FLOAT(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Цена в рублях' AFTER foto_upload_category_id;
ALTER TABLE `foto_category` ADD COLUMN `foto_upload_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Внеш. ключ. к таблице foto_upload' AFTER price;

ALTER TABLE `foto_upload_category` CHANGE COLUMN `cutegoty_id` `category_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Категория';

ALTER TABLE `foto_upload_category` ADD COLUMN `price` FLOAT(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Цена в рублях' AFTER email_date;





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
        inner join `region` r on (fu.`region_id`=r.`id`)
		
	group by fuc.id;
