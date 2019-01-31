-- загрузка тестовых данных
-- clean tables
delete from employee;
delete from user;
delete from foto_upload;
delete from customer;
commit;
-- 1. employee
INSERT INTO   `employee`(
  `id`,
  `phone_number`,
  `last_name`,
  `first_name`,
  `patronymic_name`,
  `status`
) 
VALUE (
  1,
  '23423444',
  'Петров',
  'Игорь',
  'Ноиколаевич',
  1
);
INSERT INTO 
  `employee`
(
  `id`,
  `phone_number`,
  `last_name`,
  `first_name`,
  `patronymic_name`,
  `status`
) 
VALUE (
  2,
  '37723444',
  'Сидоров',
  'Альберт',
  'Симонович',
  1
);
INSERT INTO 
  `employee`
(
  `id`,
  `phone_number`,
  `last_name`,
  `first_name`,
  `patronymic_name`,
  `status`
) 
VALUE (
  3,
  '234234777',
  'Александрова',
  'Анна',
  'Дмитриевна',
  1
);
commit;




--2. user
INSERT INTO 
  `user`
(
  `id`,
  `login`,
  `password`
) 
VALUE (
  1,
  'admin',
  MD5('123456')
);
INSERT INTO 
  `user`
(
  `id`,
  `login`,
  `password`
) 
VALUE (
  2,
  'user1',
  MD5('123456')
);
commit;




-- 3. foto_upload
INSERT INTO 
  `foto_upload`
(
  `id`,
  `employee_id`,
  `date`,
  `region_id`,
  `user_id`
) 
VALUE (
  null,
  2,
  '2017-05-01',
  20,
  1
);

INSERT INTO 
  `foto_upload`
(
  `id`,
  `employee_id`,
  `date`,
  `region_id`,
  `user_id`
) 
VALUE (
  null,
  1,
  '2017-07-01',
  30,
  2
);
commit;

INSERT INTO  region (id, name) VALUE (10,'a01');
INSERT INTO  region (id, name) VALUE (20,'a02');
INSERT INTO  region (id, name) VALUE (30,'a03');
INSERT INTO  region (id, name) VALUE (40,'a04');
INSERT INTO  region (id, name) VALUE (50,'a05');
INSERT INTO  region (id, name) VALUE (60,'a06');
INSERT INTO  region (id, name) VALUE (70,'a07');
INSERT INTO  region (id, name) VALUE (80,'a08');
INSERT INTO  region (id, name) VALUE (90,'a09');
INSERT INTO  region (id, name) VALUE (100,'b01');
INSERT INTO  region (id, name) VALUE (110,'b02');
INSERT INTO  region (id, name) VALUE (120,'b03');
INSERT INTO  region (id, name) VALUE (130,'b04');
INSERT INTO  region (id, name) VALUE (140,'b05');
INSERT INTO  region (id, name) VALUE (150,'b06');
INSERT INTO  region (id, name) VALUE (160,'c01');
INSERT INTO  region (id, name) VALUE (170,'c02');
INSERT INTO  region (id, name) VALUE (180,'c03');
INSERT INTO  region (id, name) VALUE (190,'d01');
INSERT INTO  region (id, name) VALUE (200,'d02');
INSERT INTO  region (id, name) VALUE (210,'d03');
INSERT INTO  region (id, name) VALUE (220,'d04');
INSERT INTO  region (id, name) VALUE (230,'d05');
INSERT INTO  region (id, name) VALUE (240,'d06');
INSERT INTO  region (id, name) VALUE (250,'d07');
INSERT INTO  region (id, name) VALUE (260,'d08');
INSERT INTO  region (id, name) VALUE (270,'d09');
INSERT INTO  region (id, name) VALUE (280,'d10');
INSERT INTO  region (id, name) VALUE (290,'k01');
INSERT INTO  region (id, name) VALUE (300,'k02');
INSERT INTO  region (id, name) VALUE (310,'k03');
INSERT INTO  region (id, name) VALUE (320,'k04');
INSERT INTO  region (id, name) VALUE (330,'n01');
INSERT INTO  region (id, name) VALUE (340,'n02');
INSERT INTO  region (id, name) VALUE (350,'s01');
INSERT INTO  region (id, name) VALUE (360,'s02');
INSERT INTO  region (id, name) VALUE (370,'s03');
INSERT INTO  region (id, name) VALUE (380,'s04');
INSERT INTO  region (id, name) VALUE (390,'s05');
INSERT INTO  region (id, name) VALUE (400,'s06');
INSERT INTO  region (id, name) VALUE (410,'v01');
INSERT INTO  region (id, name) VALUE (420,'v02');

-- customer
INSERT INTO `customer` (  `name`,  `email`) VALUE ('ТОО Стулья',  'javajan@mail.ru');
INSERT INTO `customer` (  `name`,  `email`) VALUE ('ТОО Столы',  'sanzhar73@mail.ru');
commit;