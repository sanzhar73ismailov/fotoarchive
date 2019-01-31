/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.foto.uploader.fotouploader;

import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.net.URL;
import java.net.URLClassLoader;
import java.util.Properties;
import java.util.jar.Attributes;
import java.util.jar.Manifest;

/**
 *
 * @author admin
 */
public class SanUtil {

    public static String getFormattedTimeStr(long mls) {
        return (mls / 1000 / 60 + " мин. " + mls / 1000 % 60 + " сек");
    }

    public static void delete(File file) throws IOException {
        for (File childFile : file.listFiles()) {
            if (childFile.isDirectory()) {
                delete(childFile);
            } else if (!childFile.delete()) {
                throw new IOException();
            }
        }
        if (!file.delete()) {
            throw new IOException();
        }
    }

    public static boolean contains(final int[] array, final int v) {

        boolean result = false;

        for (int i : array) {
            if (i == v) {
                result = true;
                break;
            }
        }

        return result;
    }

    public static void printClassPath() {

        ClassLoader cl = ClassLoader.getSystemClassLoader();

        URL[] urls = ((URLClassLoader) cl).getURLs();

        for (URL url : urls) {
            System.out.println(url.getFile());
        }

    }

    public static void readProperties() throws SanFTPException {
        Properties prop = new Properties();

        InputStream input = null;

        try {
            String filename = "application.properties";
            input = Main.class.getClassLoader().getResourceAsStream(filename);
            if (input == null) {
                throw new SanFTPException("Не найден файл с настройками: " + filename);
            }
            //input = new FileInputStream("application.properties");
            // load a properties file
            prop.load(input);
            //LOGGER.debug("prop: " + prop);

            Settings.FTP_FOLDER = prop.getProperty("ftp_folder");
            Settings.FTP_HOST = prop.getProperty("ftp_host");
            Settings.FTP_PORT = prop.getProperty("ftp_port", "21");
            Settings.FTP_LOGIN = prop.getProperty("ftp_login");
            Settings.FTP_PASS = prop.getProperty("ftp_pass");
            Settings.IMAGE_SIZE = Integer.parseInt(prop.getProperty("image_size", "2048"));
            Settings.HOME_DIR = prop.getProperty("home_dir", "");

        } catch (IOException ex) {
            throw new SanFTPException(ex);
        } finally {
            if (input != null) {
                try {
                    input.close();
                } catch (IOException e) {
                    throw new SanFTPException(e);
                }
            }
        }
    }

    public String getManifestVersion() {
        URLClassLoader cl = (URLClassLoader) getClass().getClassLoader();
        try {
            URL url = cl.findResource("META-INF/MANIFEST.MF");
            Manifest manifest = new Manifest(url.openStream());
            Attributes attr = manifest.getMainAttributes();
            String value = attr.getValue("Manifest-Version");
            return value;
        } catch (IOException E) {
            // handle
        }
        return null;
    }
}
