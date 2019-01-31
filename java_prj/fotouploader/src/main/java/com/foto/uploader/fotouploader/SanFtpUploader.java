package com.foto.uploader.fotouploader;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.PrintWriter;

import org.apache.commons.net.PrintCommandListener;
import org.apache.commons.net.ftp.FTP;
import org.apache.commons.net.ftp.FTPClient;
import org.apache.commons.net.ftp.FTPReply;

@Deprecated
public class SanFtpUploader {

    FTPClient ftp = null;
    

    public SanFtpUploader(String host, String user, String pwd) throws Exception {
        ftp = new FTPClient();
        ftp.addProtocolCommandListener(new PrintCommandListener(new PrintWriter(System.out)));
        int reply;
        ftp.connect(host);
        reply = ftp.getReplyCode();
        if (!FTPReply.isPositiveCompletion(reply)) {
            ftp.disconnect();
            throw new Exception("Exception in connecting to FTP Server");
        }
        ftp.login(user, pwd);
        ftp.setFileType(FTP.BINARY_FILE_TYPE);
        ftp.enterLocalPassiveMode();
    }

    public void uploadFile(String localFileFullName, String fileName, String hostDir)
            throws Exception {
        try (InputStream input = new FileInputStream(new File(localFileFullName))) {
            String tempFileName = hostDir + fileName+"_temp";
            this.ftp.storeFile(tempFileName, input);
            ftp.rename(tempFileName, hostDir + fileName);
        }
    }

    public void uploadFile(String localFileFullName)
            throws Exception {
        File file = new File(localFileFullName);
        uploadFile(file.getAbsolutePath(), file.getName(), Settings.FTP_FOLDER);
    }

    public void disconnect() {
        if (this.ftp.isConnected()) {
            try {
                this.ftp.logout();
                this.ftp.disconnect();
            } catch (IOException f) {
                // do nothing as file is already saved to server
            }
        }
    }

    public static void sendFile(String filePath) throws Exception {
        File file = new File(filePath);
        if (file.isFile()) {
            SanFtpUploader ftpUploader = new SanFtpUploader(Settings.FTP_HOST, Settings.FTP_LOGIN, Settings.FTP_PASS);
            ftpUploader.uploadFile(file.getAbsolutePath(), file.getName(), Settings.FTP_FOLDER);
            System.out.println(" >>>File End " + file.getName());

        } else if (file.isDirectory()) {
            System.out.println("Directory " + file.getName());
        }
    }


    public static void sendFolder(String folderPath) throws Exception {
        long start = System.currentTimeMillis();
        System.out.println("Start");
        SanFtpUploader ftpUploader = new SanFtpUploader(Settings.FTP_HOST, Settings.FTP_LOGIN, Settings.FTP_PASS);
        //FTP server path is relative. So if FTP account HOME directory is "/home/pankaj/public_html/" and you need to upload 
        // files to "/home/pankaj/public_html/wp-content/uploads/image2/", you should pass directory parameter as "/wp-content/uploads/image2/"

        File folder = new File(folderPath);
        File[] listOfFiles = folder.listFiles();

        for (int i = 0; i < listOfFiles.length; i++) {
            if (listOfFiles[i].isFile()) {
                File file = listOfFiles[i];

                System.out.println("File start<<< " + (i + 1) + "/" + listOfFiles.length + ": " + file.getAbsolutePath());
                //ftpUploader.
                ftpUploader.uploadFile(file.getAbsolutePath(), file.getName(), Settings.FTP_FOLDER);
                System.out.println(" >>>File End " + file.getName());

            } else if (listOfFiles[i].isDirectory()) {
                System.out.println("Directory " + listOfFiles[i].getName());
            }
        }
        ftpUploader.disconnect();
        System.out.println("Done");
        System.out.println("dif = " + SanUtil.getFormattedTimeStr( System.currentTimeMillis()-start));
    }

}
