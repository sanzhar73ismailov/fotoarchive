package com.foto.uploader.fotouploader;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.util.Arrays;
import java.util.List;
import java.util.logging.Level;
import javax.swing.JLabel;

import javax.swing.JOptionPane;
import javax.swing.JTextArea;
import javax.swing.JTextField;
import javax.swing.SwingWorker;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

public class SanUploadTask extends SwingWorker<Void, String> {

    private static final Logger LOGGER = LogManager.getLogger();
    private static final int BUFFER_SIZE = 4096;
    static private final String newline = "\n";

    private final String host;
    private final int port;
    private final String username;
    private final String password;

    private final String destDir;
    private final File uploadFolder;
    JTextArea logTextArea;
    JLabel pBarLabel;
    private String testProperty;

    public SanUploadTask(String host, int port, String username, String password,
            String destDir, File folder, JTextArea logTextArea, JLabel pBarLabel) {
        this.host = host;
        this.port = port;
        this.username = username;
        this.password = password;
        this.destDir = destDir;
        this.uploadFolder = folder;
        this.logTextArea = logTextArea;
        this.pBarLabel = pBarLabel;
    }

    /**
     * Executed in background thread
     */
    @Override
    protected Void doInBackground() throws IOException {
        long start = System.currentTimeMillis();
        //1. Resize images
        SanFTPUtility util = null;
        String resizeFolder = null;
        try {
            resizeFolder = this.uploadFolder.getAbsolutePath() + File.separator + "temp";
            String zipArchive = resizeFolder + File.separator + System.currentTimeMillis() + ".zip";

            LOGGER.debug("resizeFolder = " + resizeFolder);
            LOGGER.debug("zipArchive = " + zipArchive);

            LOGGER.debug("START SanImageResizer");
            
            SanImageResizer imageResizer;
            imageResizer = new SanImageResizer(this.uploadFolder.getAbsolutePath(), resizeFolder);
            final File[] listOfFiles1 = imageResizer.getListOfFiles();
            publish(newline + "Начинается сжатие фото. Количество файлов: " + listOfFiles1.length);
            toLabel("Сжатие фото");
            int percentCompleted = 0;
            int percentResize = 0;
            for (int i = 0; i < listOfFiles1.length; i++) {
                File file = listOfFiles1[i];
                LOGGER.debug("File " + (i + 1) + "/" + listOfFiles1.length + " " + file.getName());
                publish("Изменение размера файла " + (i + 1) + "/" + listOfFiles1.length + " " + file.getName());
                try {
                    imageResizer.reSizeFile(file);
                } catch (SanFTPException ex) {
                    LOGGER.info(ex.getMessage());
                    publishError(ex);
                }
                percentResize = (i + 1) * 26 / listOfFiles1.length;
                percentCompleted = percentResize;
                LOGGER.trace("percentCompleted=" + percentCompleted);
                setProgress(percentCompleted);
            }
            publish("Сжатие фото закончено");
            LOGGER.debug("FINISH SanImageResizer");

            //2. Zip files
            LOGGER.debug("START ZipUtil");
            toLabel("Архивация");
            int numFilesToZip = new File(resizeFolder).list().length;
            publish(newline + "Начинается архивирование фото. Количество файлов: " + numFilesToZip);
            if(numFilesToZip == 0){
                throw new SanFTPException("Нет фото для архивирования");
            }
            ZipUtil zipUtil = new ZipUtil(resizeFolder, zipArchive);
            zipUtil.zipDirectory();
            percentCompleted += 4;
            LOGGER.debug("percentCompleted=" + percentCompleted);
            setProgress(percentCompleted);
            publish("Архивирование фото закончено");
            LOGGER.debug("FINISH ZipUtil");

            //3. FTP send
            LOGGER.debug("START SanFTPUtility");
            long bytes = new File(zipArchive).length();
            int mb = (int) bytes / 1024 / 1024;
            int kb = (int) ((bytes / 1024) % 1024);
            toLabel("Отправка по FTP. Размер архива " + (bytes / 1024 / 1024) + " MB " + kb + " KB");
            publish(newline + "Начинается отправка архива на сервер по FTP");
            util = new SanFTPUtility(host, port, username, password);

            util.connect();
            util.uploadFile(new File(zipArchive), destDir);

            FileInputStream inputStream = new FileInputStream(new File(zipArchive));
            byte[] buffer = new byte[BUFFER_SIZE];
            int bytesRead = -1;
            long totalBytesRead = 0;

            long fileSize = new File(zipArchive).length();

            int[] arrCheck = new int[10];
            int lastIndex = -1;
            while ((bytesRead = inputStream.read(buffer)) != -1) {
                util.writeFileBytes(buffer, 0, bytesRead);
                totalBytesRead += bytesRead;
                //percentCompleted = (int) (totalBytesRead * 70 / fileSize);
                int forUpload = (int) (totalBytesRead * 100 / fileSize);
                if (forUpload > 0 && forUpload % 10 == 0) {
                    if (!SanUtil.contains(arrCheck, forUpload)) {
                        arrCheck[++lastIndex] = forUpload;
                        publish("Загружено по FTP " + forUpload + "%");
                    }
                }

                int persentComplTotal = percentCompleted + (int) (totalBytesRead * 70 / fileSize);
                setProgress(persentComplTotal);
                //LOGGER.debug("persentComplTotal=" + persentComplTotal);
            }
            inputStream.close();
            util.finish();
            publish("Отправка архива на сервер по FTP закончено");
            //4. FTP rename from "*.zip_temp" to "*.zip" 
            publish(newline + "Переименовывание архива на сервере");
            toLabel("Переименовывание архива на сервере");
            boolean success = util.rename();
            if (!success) {
                throw new SanFTPException("Переименовывание архива на сервере не произошло");
            }
            LOGGER.debug("Successfuly renamed");
            publish("Переименовывание архива на сервере закончено успешно");
            publish(newline + "ОПЕРАЦИЯ ЗАВЕРШЕНА УСПЕШНО.");
            publish("Время:" + SanUtil.getFormattedTimeStr(System.currentTimeMillis() - start));
            toLabel("Операция завершена. Время:" + SanUtil.getFormattedTimeStr(System.currentTimeMillis() - start));
            LOGGER.debug("FINISH SanFTPUtility");
        } catch (Exception ex) {
            publishError(ex);
            toLabel("Ошибка: " + ex.getMessage());
            JOptionPane.showMessageDialog(null, "Ошибка при загрузке файла: " + ex.getMessage(),
                    "Error", JOptionPane.ERROR_MESSAGE);
            LOGGER.error("error", ex);
            setProgress(0);
            cancel(true);
        } finally {
            try {
                if (util != null) {
                    util.disconnect();
                }
            } catch (SanFTPException ex) {
                LOGGER.error("error", ex);
                publishError(ex);
            }
            if (resizeFolder != null) {
                SanUtil.delete(new File(resizeFolder));
            }
        }

        return null;
    }

    /**
     * Executed in Swing's event dispatching thread
     */
    @Override
    protected void done() {
        if (!isCancelled()) {
            JOptionPane.showMessageDialog(null,
                    "Файл успешно загружен!", "Message",
                    JOptionPane.INFORMATION_MESSAGE);
        }
    }

    @Override
    protected void process(List<String> chunks) {
        // Updates the messages text area
        for (final String string : chunks) {
            logTextArea.append(string + newline);
        }
    }

    private void toLabel(String text) {
        this.pBarLabel.setText(text);
    }

    private void publishError(Exception ex) {
        this.publish("*** Ошибка: " + ex.getMessage());
    }

}
