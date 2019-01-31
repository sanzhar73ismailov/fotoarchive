ALTER TABLE `foto_upload` ADD COLUMN `status_id` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'статус отчета (вн.ключ таблицы status)' AFTER `user_id`;
CREATE TABLE `status` (
  `id` INTEGER(11) NOT NULL,
  `name` VARCHAR(20) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Наименование',
  `name_ru` VARCHAR(20) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Наименование на русском (для GUI)',
  `descr` VARCHAR(200) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'описание',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

INSERT INTO status (id,name,name_ru,descr) VALUE (0,'draft','Черновик','когда отчет создан, но одно из полей или все поля не заполнены или фото не загружены');
INSERT INTO status (id,name,name_ru,descr) VALUE (1,'created','Фото загружены','когда файлы загружены, данные по сотруднику, дате отчета, пользователю заполнены');
INSERT INTO status (id,name,name_ru,descr) VALUE (2,'categories_added','Присвоены категории','когда к отчету созданы категории - список кратких описание объявлений с привязкой к заказчику');
INSERT INTO status (id,name,name_ru,descr) VALUE (3,'matched','Сопоставен','когда фотографии сопоставлены с категориями');
INSERT INTO status (id,name,name_ru,descr) VALUE (4,'closed','Закрыт','когда отчет закрыт, то есть не подлежит изменениям');
commit;

ALTER TABLE `foto_upload` ADD INDEX  (`status_id`);
ALTER TABLE `foto_upload` ADD CONSTRAINT `foto_upload_fk` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`);

ALTER TABLE `foto` ADD COLUMN `width` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'ширина изображения в px' AFTER `deleted`;
ALTER TABLE `foto` ADD COLUMN `height` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'высота изображения в px' AFTER `width`;