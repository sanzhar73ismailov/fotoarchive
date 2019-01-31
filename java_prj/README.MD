# README #

### Java FTP client - десктопное приложение (FTP клиент) для загрузки фото на сайт.

### Что оно делает
* Повзволяет выбрать папку через GUI
* Сжимает все фото во временную папку
* Архивирует сжатые фота
* Отправляет по FTP на сервер, в заданную папку

### Сборка проекта
* Для сборки использовать maven ver.3 и выше
* Java ver 1.7 и выше
* в папке проект mvn clean install
* скопировать в отдельную папку 
   файл fotouploader\target\fotouploader.jar 
   файлы библиотек, папкой (fotouploader\target\libs\*jar) 
   файл fotouploader\target\classes\application.properties
   файл fotouploader\target\classes\log4j2.xml 

   В папку java_prj/dist  

### Запуск
   1) прописать путь к JAVA_HOME (если переменная не установлена) в файл java_prj/dist/fotouploader.bat 
      IF NOT DEFINED JAVA_HOME SET JAVA_HOME=<path to java>
	  Например, 
	  IF NOT DEFINED JAVA_HOME SET JAVA_HOME=C:\Program Files\Java\jre7
   2) В файле java_prj/dist/application.properties прописать актуальные настройки
   3) Запустить java_prj/dist/fotouploader.bat 
 