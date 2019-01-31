package com.foto.uploader.fotouploader;

import java.io.File;
import java.io.IOException;
import java.util.logging.Level;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

/**
 *
 * @author admin
 */
@Deprecated
public class MainPrev {

    private static final Logger LOGGER = LogManager.getLogger();
    
    
    
    

    public static void main(String[] args) {
        LOGGER.debug("START");
        long start = System.currentTimeMillis();

        try {

            //String folderSrcImg = "S:\\temp\\3";
            //String folderSrcImg = "S:\\temp\\36_files";
            String folderSrcImg = "S:\\temp\\демяшкина 11.12 а04";
            String folderResizedImg = folderSrcImg + File.separator + "_temp_resized";
            String arcName = folderResizedImg + File.separator + System.currentTimeMillis() + ".zip";

            
            resizeImages(folderSrcImg, folderResizedImg);
            zipFolder(folderResizedImg, arcName);
            sendByFtp(arcName);
            
            SanUtil.delete(new File(folderResizedImg));
            //sendByFtp("S:\\temp\\1514014200728.zip");

        } catch (Exception ex) {
            LOGGER.error("error", ex);
        }
        LOGGER.info("dif = " + SanUtil.getFormattedTimeStr(System.currentTimeMillis() - start));
        LOGGER.debug("FINISH");
    }

    private static void resizeImages(String folderSrcImg, String folderResizedImg) throws IOException {
        //SanImageResizer.resizeFolder(folderSrcImg, folderResizedImg);
        SanImageResizer imageResizer = new SanImageResizer(folderSrcImg, folderResizedImg);
        for(File file : imageResizer.listOfFiles){
            try {
                imageResizer.reSizeFile(file);
            } catch (SanFTPException ex) {
                java.util.logging.Logger.getLogger(MainPrev.class.getName()).log(Level.SEVERE, null, ex);
            }
        }
    }

    private static void zipFolder(String folderResizedImg, String arcName) {
        //ZipUtil.zipFolder(folderResizedImg, arcName);
    }

    private static void sendByFtp(String arcName) throws Exception {
        LOGGER.debug("START");
        long start = System.currentTimeMillis();
        SanFtpUploader ftpUploader = new SanFtpUploader(Settings.FTP_HOST, Settings.FTP_LOGIN, Settings.FTP_PASS);
        ftpUploader.uploadFile(arcName);
         System.out.println("dif = " + SanUtil.getFormattedTimeStr(System.currentTimeMillis()-start));
        LOGGER.debug("FINISH");

    }

}
