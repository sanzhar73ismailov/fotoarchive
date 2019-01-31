package com.foto.uploader.fotouploader;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import org.apache.commons.net.ftp.FTP;
import org.apache.commons.net.ftp.FTPClient;
import org.apache.commons.net.ftp.FTPReply;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

/**
 *
 * @author admin
 */
public class SanFTPUtility {

    private static Logger LOGGER = LogManager.getLogger();
    private final String host;
    private final int port;
    private final String username;
    private final String password;

    private String tempName;
    private String arcFinalName;
    private String hostDir;

    private FTPClient ftpClient = new FTPClient();
    private int replyCode;

    private OutputStream outputStream;

    public SanFTPUtility(String host, int port, String user, String pass) {
        LOGGER.debug("START constructor");
        this.host = host;
        this.port = port;
        this.username = user;
        this.password = pass;
        LOGGER.debug("FINISH constructor");
    }

    public boolean rename() throws IOException, SanFTPException {
        LOGGER.debug("START rename");
        this.connect();
        boolean success = ftpClient.changeWorkingDirectory(this.hostDir);
        if (!success) {
            throw new SanFTPException("Could not change working directory to "
                    + this.hostDir + ". The directory may not exist.");
        }
        boolean renamed = false;
        renamed = ftpClient.rename(this.tempName, this.arcFinalName);
        LOGGER.debug("FINISH rename=" + renamed);
        return renamed;

    }

    /**
     * Connect and login to the server.
     *
     * @throws SanFTPException
     */
    public void connect() throws SanFTPException {
        LOGGER.debug("START");
        try {
            ftpClient.connect(host, port);
            replyCode = ftpClient.getReplyCode();
            if (!FTPReply.isPositiveCompletion(replyCode)) {
                throw new SanFTPException("FTP serve refused connection.");
            }

            boolean logged = ftpClient.login(username, password);
            if (!logged) {
                // failed to login
                ftpClient.disconnect();
                throw new SanFTPException("Could not login to the server.");
            }

            ftpClient.enterLocalPassiveMode();

        } catch (IOException ex) {
            throw new SanFTPException("I/O error: " + ex.getMessage());
        }
        LOGGER.debug("FINISH");
    }

    /**
     * Start uploading a file to the server
     *
     * @param uploadFile the file to be uploaded
     * @param destDir destination directory on the server where the file is
     * stored
     * @throws SanFTPException if client-server communication error occurred
     */
    public void uploadFile(File uploadFile, String destDir) throws SanFTPException {
        LOGGER.debug("START uploadFile");
        try {
            LOGGER.debug("uploadFile.exists()=" + uploadFile.exists());
            this.hostDir = destDir;
            boolean success = ftpClient.changeWorkingDirectory(destDir);
            if (!success) {
                throw new SanFTPException("Could not change working directory to "
                        + destDir + ". The directory may not exist.");
            }

            success = ftpClient.setFileType(FTP.BINARY_FILE_TYPE);
            if (!success) {
                throw new SanFTPException("Could not set binary file type.");
            }

            this.arcFinalName = uploadFile.getName();
            this.tempName = this.arcFinalName + "_temp";

            outputStream = ftpClient.storeFileStream(this.tempName);

        } catch (IOException ex) {
            throw new SanFTPException("Error uploading file: " + ex.getMessage());
        }
        LOGGER.debug("FINISH uploadFile");
    }

    /**
     * Write an array of bytes to the output stream.
     */
    public void writeFileBytes(byte[] bytes, int offset, int length)
            throws IOException {
        outputStream.write(bytes, offset, length);
    }

    /**
     * Complete the upload operation.
     */
    public void finish() throws IOException {
        LOGGER.debug("START finish");
        if(outputStream !=null){
            outputStream.close();
        }
        ftpClient.completePendingCommand();
        LOGGER.debug("FINISH finish");
    }
    

    /**
     * Log out and disconnect from the server
     */
    public void disconnect() throws SanFTPException {
        if (ftpClient.isConnected()) {
            try {
                if (!ftpClient.logout()) {
                    throw new SanFTPException("Could not log out from the server");
                }
                ftpClient.disconnect();
            } catch (IOException ex) {
                throw new SanFTPException("Error disconnect from the server: "
                        + ex.getMessage());
            }
        }
    }
}
