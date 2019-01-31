<?php
class Status {
	/*
	update status set name='ASSIGNED_NUMBER', name_ru='Присвоен номер', descr='' where id = 0;
	update status set name='UPLOADED', name_ru='Загружен', descr='Фото загружены' where id = 1;
	update status set name='CATEGORIES_ADDED', name_ru='Загружен*', descr='Фото загружены и категории присовоены' where id = 2;
	update status set name='CHECKED', name_ru='Проверен', descr='К фото выбраны категории' where id = 3;
	update status set name='SENT', name_ru='Отправлен', descr='Когда отчет отправлен заказчику' where id = 4;
    */
    public static $ASSIGNED_NUMBER = 0;
    
    //public static $CREATED = 1;
    public static $UPLOADED = 1;
    
    public static $CATEGORIES_ADDED = 2;
    
    //public static $MATCHED = 3;
    public static $CHECKED = 3;
    
    public static $SENT = 4;
    
    
}