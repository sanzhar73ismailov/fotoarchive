package com.foto.uploader.fotouploader;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.zip.ZipEntry;
import java.util.zip.ZipOutputStream;
import javax.swing.SwingWorker;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

public class ZipUtil {

    private static Logger LOGGER = LogManager.getLogger();
    List<String> filesListInDir = new ArrayList<String>();
    File folderToZip;
    String archiveName;
    
    public static void main(String[] args) throws IOException {
         ZipUtil zipUtil = new ZipUtil("S:\\temp\\3_res", "S:\\temp\\3_res\\1.zip");
         zipUtil.zipDirectory();
    }

    public ZipUtil(String dir, String archiveName) throws IOException {
        this.folderToZip = new File(dir);
         populateFilesList(this.folderToZip);
         this.archiveName = archiveName;
    }
    

    public static void zipFolder(String folderResizedImg, String arcName) throws IOException {
        LOGGER.debug("START");
        long start = System.currentTimeMillis();
        //File file = new File("S:\\temp\\file1.txt");
        //String zipFileName = "S:\\temp\\file1.zip";
        //File dir = new File(folderResizedImg);
        //zipSingleFile(file, zipFileName);
        ZipUtil zipFiles = new ZipUtil(folderResizedImg,arcName);
        //zipFiles.zipDirectory();
        LOGGER.debug("dif = " + SanUtil.getFormattedTimeStr(System.currentTimeMillis() - start));
        LOGGER.debug("FINISH");
    }

    /**
     * This method zips the directory
     *
     * @param dir
     * @param zipDirName
     */
    public void zipDirectory() {
        try {
           
            //now zip files one by one
            //create ZipOutputStream to write to the zip file
            FileOutputStream fos = new FileOutputStream(this.archiveName);
            ZipOutputStream zos = new ZipOutputStream(fos);
            for (int i = 0; i < filesListInDir.size(); i++) {
                String filePath = filesListInDir.get(i);
                this.zipFile(filePath, zos);
            }
            zos.close();
            fos.close();
            LOGGER.debug("************ Zip DONE");
        } catch (IOException e) {
            LOGGER.error("error", e);
        }
    }

    public void zipFile(String filePath, ZipOutputStream zos) throws IOException {

        ZipEntry ze = new ZipEntry(filePath.substring(this.folderToZip.getAbsolutePath().length() + 1, filePath.length()));
        zos.putNextEntry(ze);
        //read the file and write to ZipOutputStream
        FileInputStream fis = new FileInputStream(filePath);
        byte[] buffer = new byte[1024];
        int len;
        while ((len = fis.read(buffer)) > 0) {
            zos.write(buffer, 0, len);
        }
        zos.closeEntry();
        fis.close();

    }

    /**
     * This method populates all the files in a directory to a List
     *
     * @param dir
     * @throws IOException
     */
    private void populateFilesList(File dir) throws IOException {
        File[] files = dir.listFiles();
        for (File file : files) {
            if (file.isFile()) {
                filesListInDir.add(file.getAbsolutePath());
            } else {
                populateFilesList(file);
            }
        }
    }

    /**
     * This method compresses the single file to zip format
     *
     * @param file
     * @param zipFileName
     */
    private static void zipSingleFile(File file, String zipFileName) {
        try {
            //create ZipOutputStream to write to the zip file
            FileOutputStream fos = new FileOutputStream(zipFileName);
            ZipOutputStream zos = new ZipOutputStream(fos);
            //add a new Zip Entry to the ZipOutputStream
            ZipEntry ze = new ZipEntry(file.getName());
            zos.putNextEntry(ze);
            //read the file and write to ZipOutputStream
            FileInputStream fis = new FileInputStream(file);
            byte[] buffer = new byte[1024];
            int len;
            while ((len = fis.read(buffer)) > 0) {
                zos.write(buffer, 0, len);
            }

            //Close the zip entry to write to zip file
            zos.closeEntry();
            //Close resources
            zos.close();
            fis.close();
            fos.close();
            LOGGER.debug(file.getCanonicalPath() + " is zipped to " + zipFileName);

        } catch (IOException e) {
            LOGGER.error("error", e);
        }

    }
}
