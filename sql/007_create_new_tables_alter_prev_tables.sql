CREATE TABLE category (
  id INTEGER(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Наименование категории',
  customer_id INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Заказчик',
  price FLOAT(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Цена в рублях',
  insert_datetime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY foto_upl_cust_catname (name, customer_id, price),
  KEY customer_id (customer_id)
)ENGINE=InnoDB
CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';


CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `foto_category_view` AS 
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