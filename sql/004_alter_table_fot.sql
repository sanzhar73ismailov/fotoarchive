ALTER TABLE `foto` ADD COLUMN `foto_old_name` VARCHAR(30) NOT NULL DEFAULT '' COMMENT 'Предыдущее имя файла (до загрузки)' AFTER `foto_name`;
ALTER TABLE `foto` ADD COLUMN `address_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Родитель id (id foto, которое является адресом)' AFTER `id`;
ALTER TABLE foto_category  ADD COLUMN `file_path` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'путь к  файлу-архиву' AFTER `price`;
ALTER TABLE foto_category  ADD COLUMN `file_zip` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'название файла-архива' AFTER `file_path`;
ALTER TABLE foto_category  ADD COLUMN `email_send` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Почта посылалась хоть раз (1 - да, 0 - нет)' AFTER `file_zip`;
ALTER TABLE foto_category  ADD COLUMN `email_result` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Результат последней отправки (1 - отрпавилась, 0 - не отправилась)' AFTER `email_send`;
ALTER TABLE foto_category  ADD COLUMN `email_error` VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'В случае не отправки текст ошибки' AFTER `email_result`;
ALTER TABLE foto_category  ADD COLUMN `email_date`DATETIME NOT NULL COMMENT 'Дата последней отправки почты' AFTER `email_error`;
ALTER TABLE foto_category  ADD COLUMN `email_send_manual` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Почта отправлена в ручную (1 - да, 0 - нет)' AFTER `email_send`;
