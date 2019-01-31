@ECHO OFF
SetLocal
IF NOT DEFINED JAVA_HOME  ECHO JAVA_HOME is not set
IF NOT DEFINED JAVA_HOME SET JAVA_HOME=C:\Program Files\Java\jre7
"%JAVA_HOME%\bin\java.exe" -jar  fotouploader.jar