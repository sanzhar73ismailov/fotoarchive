/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.foto.uploader.fotouploader;

import static com.foto.uploader.fotouploader.ZipUtil.zipFolder;
import org.junit.After;
import org.junit.AfterClass;
import org.junit.Before;
import org.junit.BeforeClass;
import org.junit.Test;
import static org.junit.Assert.*;

/**
 *
 * @author admin
 */
public class MyTest {
    
    /**
     * Test of performSomeTask method, of class Log4J2XmlConf.
     */
    @Test
    public void testLog4J2XmlConfPerformSomeTask() {
        System.out.println("performSomeTask");
        Log4J2XmlConf instance = new Log4J2XmlConf();
        instance.performSomeTask();
    }
    
    @Test
    public void testZipFile() {
        String folderResizedImg = "S:\\temp\\демяшкина 11.12 а04\\resize";
        String zipDirName = "S:\\temp\\демяшкина_11.12_а04.zip";
        //ZipUtil.zipFolder(folderResizedImg, zipDirName);
    }
    
}
