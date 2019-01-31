ALTER TABLE `foto_upload` MODIFY COLUMN `date` DATE DEFAULT NULL COMMENT 'Дата съемки';
ALTER TABLE `foto_upload` MODIFY COLUMN `region_id` INTEGER(11) DEFAULT '0' COMMENT 'Район';
ALTER TABLE `foto_upload` MODIFY COLUMN `employee_id` INTEGER(11) DEFAULT NULL COMMENT 'Работник';
ALTER TABLE `foto_upload` MODIFY COLUMN `user_id` INTEGER(11) DEFAULT NULL COMMENT 'Менеджер';

ALTER TABLE `foto_upload` ADD COLUMN `ftp` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'флаг - закачан по FTP' AFTER status_id;