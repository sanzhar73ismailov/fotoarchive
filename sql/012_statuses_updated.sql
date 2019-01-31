update status set name='assigned_number', name_ru='Присвоен номер', descr='' where id = 0;
update status set name='uploaded', name_ru='Загружен', descr='Фото загружены' where id = 1;
update status set name='categories_added', name_ru='Загружен', descr='Фото загружены и категории присовоены' where id = 2;
update status set name='checked', name_ru='Проверен', descr='К фото выбраны категории' where id = 3;
update status set name='sent', name_ru='Отправлен', descr='Когда отчет отправлен заказчику' where id = 4;