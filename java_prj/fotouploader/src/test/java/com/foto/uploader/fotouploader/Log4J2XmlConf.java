package com.foto.uploader.fotouploader;

import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

public class Log4J2XmlConf {

    private static Logger logger = LogManager.getLogger();

    public void performSomeTask() {
        logger.info("02_logger.getName()=" + logger.getName());
        logger.debug("This is a debug message");
        logger.info("This is an info message");
        logger.warn("This is a warn message");
        logger.error("This is an error message");
        logger.fatal("This is a fatal message");
    }
}
