# README #

### What is this repository for? ###

* CRM Raskleika 
* Version см. ниже, в разделе Версия проекта
* [Learn Markdown](https://bitbucket.org/tutorials/markdowndemo)

##Содержимое
  * Файлы проекта 
    components - компоненты страниц (шапка, подвал)
    css - css файлы
    fonts - шрифты
    functions - php файлы обработчики
    index.php - стартовая страница
    js - javascript файлы
    uploads - папки с jpeg файлами отчетов
   * sql - скрипты по созданию базы, таблиц (DDL).
    max_file_uploads в php.ini (20 по умолчанию увеличить до 200) - уже не обязательно (качаем через ajax)
    java_prj - проект на java для загрузки файлов



### Настройка проекта ###

* Создать базу в MYSQL СУБД и накатить последовательно файлы из папки sql.
* В файле includes/config.php заполнить данные для подключения к БД
* В файле includes/config.php заполнить данные email smtp
* в консоли в папке проекта запустить composer.bat
* Залить файл на хостинг (index.php как стартовая страница).

## Версия проекта
# Версия ведется в данном файле. 
# Формат: `yy.mm.dd.idx`
* `yy`  - год, до 2х знаков
* `mm`  - месяц, до 2х знаков
* `dd`  - день, до 2х знаков
* `idx` - порядковый номер ревизии в текущем дне, начиная с 1

###17.12.31.2 sanzhar73@gmail.com
 + Alt для картинок вставил
###17.12.31.1 sanzhar73@gmail.com
 + Java FTP client - улучшил обработку ошибок:
           - если файл изображение не поддерживается, он пропускается, на log поле выходит сообщение.
				 Поддерживаются следующие: GIF, PNG, JPEG, BMP, and WBMP)
		   - если файлов нет для отправки, выходит ошибка
###17.12.30.1 sanzhar73@gmail.com
 + Уменьшение отображаемых картинок на странице отчета (экскизы средставами Fat Free Image)
###17.12.29.1 sanzhar73@gmail.com
 + Страницу справочник категорий доработал - при редактировании задействованной категории можно менять только статус
 + для каждой категории выводится ссылки на отчеты в которых данная категория используется, при нажатии отчёт открывается 
   в отдельном окне.
###17.12.26.4 sanzhar73@gmail.com
 + Java FTP client - файл java_prj\README.MD
###17.12.26.3 sanzhar73@gmail.com
 + Java FTP client - Сборка проекта
###17.12.26.2 sanzhar73@gmail.com
 + Удаление отчета на странице списка отчетов - добработал
###17.12.26.1 sanzhar73@gmail.com
 + Удаление отчета на странице списка отчетов
###17.12.25.3 sanzhar73@gmail.com
 + Java FTP client - Добавил считывание с property файла
###17.12.25.2 sanzhar73@gmail.com
 + Java FTP client - продолжил работу по GUI (Swing)
###17.12.25.1 sanzhar73@gmail.com
 + Java FTP client - продолжил работу по GUI (Swing)
###17.12.24.2 sanzhar73@gmail.com
 + Java FTP client - начал работу по GUI (Swing)
###17.12.24.1 sanzhar73@gmail.com
 + На стороне сервера сделал крон проверку и загрузку фото из zip файлов из папки ftp
###17.12.23.1 sanzhar73@gmail.com
 + создал проект на java для загрузки файлов (папка java_prj)
###17.12.21.2 sanzhar73@gmail.com
 + Убрал ошибку при логрировании в index.php, 
 ~ Подправил страницу входа
###17.12.21.1 sanzhar73@gmail.com
 ~ Отправка почты - теперь учитывается суммарный размер архива с фото и карта района.
    Если все в сумме превышает 25 мб, аттачментом уходит только фото района.
###17.12.20.1 sanzhar73@gmail.com
 + Удаление фото на вкладке Загрузка
###17.12.19.1 sanzhar73@gmail.com
 + Чтобы карта района прикреплялась к почте (если есть)
###17.12.19.1 sanzhar73@gmail.com
 + Доработал страницу регионов - загрузка, удаление карт районов
###17.12.18.2 sanzhar73@gmail.com
 + Дорабатывал страницу регионов - загрузка, удалени карт районов
###17.12.18.1 sanzhar73@gmail.com
 + Создал настройки (таблицу, функционал, форму)
 + Настройки почты, размеров изображения, размера аттачмента перенес в базу (таблица settings) 
 + Переделал foto_category_view (см. 013_settings_create_view.sql)
 + Добавил колонку file_name в таблицу region
###17.12.16.1 sanzhar73@gmail.com
 + Сделал, чтобы файлы уходили по почте в виде ссылок
###17.12.15.2 sanzhar73@gmail.com
 + Лимит на 25 мб почты убрал
 + 2048 фото изменил сжатие
###17.12.15.1 sanzhar73@gmail.com
 + Замечания по загрузке
###17.12.12.2 sanzhar73@gmail.com
 + нумерацию на странице списка отчетов 
###17.12.12.1 sanzhar73@gmail.com
 ~ Исправил замечания: изменил названия категорий 
 + листать фото при проверке кликом мыши по фото
###17.12.10.3 sanzhar73@gmail.com
 ~ Поменял размеры фото на 800 для загрузки
###17.12.10.2 sanzhar73@gmail.com
 ~ Подправил страницу загрузки - скрываются, показыватся панели с загрузочными файлами
###17.12.10.1 sanzhar73@gmail.com
 ~ Подправил страницу загрузки - наличие прежних имен происходит на клиенте
###17.12.9.5 sanzhar73@gmail.com
 ~ Подправил страницу загрузки - картинки с именами файлов и в несколько колонок
###17.12.9.4 sanzhar73@gmail.com
 ~ Подправил страницу загрузки - время загрузки файлов (округлил)
###17.12.9.3 sanzhar73@gmail.com
 ~ Подправил страницу загрузки - добавил время загрузки файлов
###17.12.9.2 sanzhar73@gmail.com
 ~ Подправил страницу загрузки
###17.12.9.1 sanzhar73@gmail.com
 ~ Подправил слайдшоу отображение списка под минифото
###17.12.8.1 sanzhar73@gmail.com
 + Доделал слайдшоу.
 + Поставил индексы и внеш.ключи в базе.
###17.12.7.2 sanzhar73@gmail.com
 + Начал прикручивать слайдшоу
###17.12.7.1 sanzhar73@gmail.com
 + Доисправил отправку почты
###17.12.6.2 sanzhar73@gmail.com
 + Работа по отправке почты
###17.12.6.1 sanzhar73@gmail.com
 + Калькуляция
###17.12.5.2 sanzhar73@gmail.com
 + Изменял создание архивов
###17.12.5.1 sanzhar73@gmail.com
 + Переделал создание фото категорий для конкретного отчета + начал переделывать сопоставление
###17.12.4.1 sanzhar73@gmail.com
 + Работал над загрузкой файлов под ajax - доделал
 + Справочник по категориям
###17.12.3.1 sanzhar73@gmail.com
 + Работа над загрузкой файлов под ajax
###17.12.1.4 sanzhar73@gmail.com
 ~ Изменения по базе
###17.12.1.3 sanzhar73@gmail.com
 + JS проверки на форме сопоставления фото
###17.12.1.2 sanzhar73@gmail.com
 + Исправил на странице отправки почты, меняет статус отчета на форме в верху
###17.12.1.1 sanzhar73@gmail.com
 + Настройки почты перенес в functions/config.php
 - Убрал лишние debug логирования
###17.11.30.1 sanzhar73@gmail.com
 + Закончил делать формы для справочников
###17.11.29.2 sanzhar73@gmail.com
 + Начал делать формы для справочников
###17.11.29.1 sanzhar73@gmail.com
 + Сделал страницу входа
###17.11.28.2 sanzhar73@gmail.com
 + Подправил формы
###17.11.28.1 sanzhar73@gmail.com
 + Доработал проверку состояний при сохранении форм
###17.11.27.1 sanzhar73@gmail.com
 + Работы навигации, переделывал сохранение категорий
###17.11.24.1 sanzhar73@gmail.com
 + Работы по отправке почты - доделывал
###17.11.23.1 sanzhar73@gmail.com
 + Работы по отправке почты - реализовал на jquery (надо еще доделывать)
###17.11.22.1 sanzhar73@gmail.com
 + Работы по отправке почты (сырой вариант)
###17.11.21.1 sanzhar73@gmail.com
 + Работы по калькуляции (таблица по стоимости и таблица для отправки по заказчикам), файлы скачиваются
###17.11.20.2 sanzhar73@gmail.com
 + Начало работы по калькуляции
###17.11.20.1 sanzhar73@gmail.com
 + Работа по сопоставлению категорий объявлений с фотографиями
###17.11.19.1 sanzhar73@gmail.com
 + Работа по созданию категорий объявлений
###17.11.18.1 sanzhar73@gmail.com
 + Работа по загрузке файлов
###17.11.17.1 sanzhar73@gmail.com
 + Добавил колонку foto_old_name таблицу foto
 + Сделал форму отчета и загрузку файлов
###17.11.16.1 sanzhar73@gmail.com
+ Добавил колонку статусов в таблицу file_upload
+ Добавил таблицу статусов и заполнил данными (см. sql/003_statuses.sql)
+ Создал страницу создания отчета, переделал поля Фамилия и имя на выпадающий список, поле Район - тоже выпадающий список.
+ Начал работу по сохранению отчета (работа с javascript).
+ Перенес логику по списку отчетов и для формы отчета в контроллеры (папка pages).
+ Добавил (настроил) .htaccess файл в корне проекта. 

###17.11.14.1 sanzhar73@gmail.com
 + Добавил файл с загрузкой тестовых данных (см. sql/002_insert_test_data.sql)
 ~ Переделал проект под Fat Free (использование шаблонных Fat Free страниц htm)
 ~ Передел index.php под Fat Free
 + Добавил страницу списка отчетов
 - Удалил папку components
 ~ Файлы header.php и footer.php переименовал в *.htm и перенес во view/parts папку
 + создал functions/functions.php
 + добавил верхнее меню
 + добавил описание статусов отчетов (см. ниже)
 
###17.11.13.2 sanzhar73@gmail.com
 + Добавил, изменил таблицы базы данных (см. sql/001_db_create.sql)
###17.11.13.1 sanzhar73@gmail.com
 + Первый коммит проекта

Описание статусов (состояний) отчетов

В системе хранятся отчеты.
Отчет (строка в таблице file_upload) имеет различные состояния (поле status_id в таблице):
0. Состояние "Присовоен номер" (draft) - когда отчет создан, но фото не загружены полностью или частично.
1. Состояние "Загружен" (uploaded) - когда файлы загружены, данные по сотруднику, дате отчета, пользователю заполнены.
2. Состояние "Присвоены категории" (categories_added) - когда к отчету созданы категории - список кратких описание объявлений с привязкой к заказчику
3. Состояние "Проверен" (checked) - когда фотографии сопоставлены с категориями
4. Состояние "Отправлен" (sent) - когда отчет отправлен заказчику.