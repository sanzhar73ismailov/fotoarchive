package com.foto.uploader.fotouploader;

import java.io.*;
import java.awt.*;
import java.awt.event.*;
import java.beans.PropertyChangeEvent;
import java.beans.PropertyChangeListener;
import java.util.logging.Level;
import javax.swing.*;
import javax.swing.SwingUtilities;
import javax.swing.filechooser.*;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

public class SwingFtp extends JPanel implements ActionListener,
        PropertyChangeListener {

    private static final Logger LOGGER = LogManager.getLogger();

    static private final String newline = "\n";
    final static int interval = 1000;
    int pbarResizeCounter;
    JButton openButton, uploadButton;
    JTextArea log;
    JFileChooser fc;
    JProgressBar progressBar;
    File folderToUpload;
    JLabel uploadFolderInfoLb;
    JTextField uploadFolderInfoTf;
    JLabel labelResize;

    static final int MY_MINIMUM = 0;
    static final int MY_MAXIMUM = 100;

    public SwingFtp() {
        super(new BorderLayout());

        log = new JTextArea(5, 20);

        try {
            SanUtil.readProperties();
            log.append("Используемые настройки: " + newline);
            log.append("FTP host: " + Settings.FTP_HOST + newline);
            log.append("FTP порт: " + Settings.FTP_PORT + newline);
            log.append("FTP пользователь: " + Settings.FTP_LOGIN + newline);
            log.append("FTP папка: " + Settings.FTP_FOLDER + newline);
            log.append("Макс. размер изображения: " + Settings.IMAGE_SIZE + newline);
            log.append("Папка локальная по умолчанию: " + Settings.HOME_DIR + newline);
        } catch (SanFTPException ex) {
            LOGGER.error("error", ex);
            log.append("ОШИБКА: " + ex.getMessage() + newline);
        }

        log.setMargin(new Insets(5, 5, 5, 5));
        log.setEditable(false);
        JScrollPane logScrollPane = new JScrollPane(log);
        uploadFolderInfoLb = new JLabel("Папка для загрузки: ");
        uploadFolderInfoTf = new JTextField("Не выбрана");
        uploadFolderInfoTf.setEditable(false);
        uploadFolderInfoTf.setPreferredSize(new Dimension(420, 20));

        fc = new JFileChooser(Settings.HOME_DIR);
        fc.setDialogTitle("Выберите папку с фото для загрузки на сервер: ");
        fc.setFileSelectionMode(JFileChooser.DIRECTORIES_ONLY);

        openButton = new JButton("Выбрать папку...");
        openButton.addActionListener(this);

        uploadButton = new JButton("Начать закачку");

        uploadButton.addActionListener(this);
        uploadButton.setActionCommand("upload");

        JPanel upperPanel = new JPanel();
        upperPanel.setLayout(new BoxLayout(upperPanel, BoxLayout.PAGE_AXIS));

        JPanel folderPanel = new JPanel();
        folderPanel.add(uploadFolderInfoLb);
        folderPanel.add(uploadFolderInfoTf);

        JPanel buttonPanel = new JPanel(); //use FlowLayout
        buttonPanel.add(openButton);
        buttonPanel.add(uploadButton);

        upperPanel.add(folderPanel);
        upperPanel.add(buttonPanel);

        JPanel pbarPanel = new JPanel();

        labelResize = new JLabel("");

        //pbarPanel.setLayout(new BoxLayout(pbarPanel, BoxLayout.PAGE_AXIS));
        pbarPanel.setLayout(new GridLayout(0, 2, 10, 10));
        progressBar = new JProgressBar(MY_MINIMUM, MY_MAXIMUM);
        progressBar.setStringPainted(true);

        pbarPanel.add(labelResize);
        pbarPanel.add(progressBar);

        add(upperPanel, BorderLayout.PAGE_START);
        add(logScrollPane, BorderLayout.CENTER);
        add(pbarPanel, BorderLayout.SOUTH);

    }

    public void actionPerformed(ActionEvent e) {
        LOGGER.debug("START");

        //Handle open button action.
        if (e.getSource() == openButton) {
            int returnVal = fc.showOpenDialog(SwingFtp.this);

            if (returnVal == JFileChooser.APPROVE_OPTION) {
                this.folderToUpload = fc.getSelectedFile();
                //This is where a real application would open the file.
                log.append("Открытие папки: " + this.folderToUpload.getAbsolutePath() + "." + newline);
                this.uploadFolderInfoTf.setText(folderToUpload.getAbsolutePath());
                log.append("Чтобы начать закачку файлов нажмите кнопку \"Начать закачку\" " + newline);

                this.progressBar.setValue(0);
                this.uploadButton.setBackground(Color.green);

            } else {
                log.append("Open command cancelled by user." + newline);
            }
            log.setCaretPosition(log.getDocument().getLength());

            //Handle save button action.
        } else if (e.getSource() == uploadButton) {
            String host = Settings.FTP_HOST;
            int port = Integer.parseInt(Settings.FTP_PORT);
            String username = Settings.FTP_LOGIN;
            String password = Settings.FTP_PASS;
            String uploadPath = Settings.FTP_FOLDER;
            //String filePath = "S:\\temp\\3\\foto00.jpg";//filePicker.getSelectedFilePath();

            if (this.folderToUpload == null) {
                this.log.append("Папка не выбрана" + newline);
                return;
            }

            this.progressBar.setValue(0);
            this.uploadButton.setEnabled(false);
            this.openButton.setEnabled(false);
            SanUploadTask task = new SanUploadTask(host, port, username, password,
                    uploadPath, this.folderToUpload, this.log, this.labelResize);
            task.addPropertyChangeListener(this);
            task.execute();
        }
        LOGGER.debug("FINISH");
    }

    /**
     * Create the GUI and show it. For thread safety, this method should be
     * invoked from the event dispatch thread.
     */
    public static void createAndShowGUI() {
        JFrame frame = new JFrame("Загрузка фото");
        frame.setTitle(Settings.APP_NAME + " ver. " + new SanUtil().getManifestVersion());
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        frame.setSize(640, 480);

        frame.add(new SwingFtp());

        //frame.pack();
        frame.setVisible(true);
    }

    @Override
    public void propertyChange(PropertyChangeEvent evt) {
        System.out.println("evt = " + evt);
        System.out.println(evt.getPropertyName() + "=" + evt.getNewValue().getClass());
        if ("progress" == evt.getPropertyName()) {
            int progress = (Integer) evt.getNewValue();
            progressBar.setValue(progress);
        } else if (evt.getPropertyName().equals("state")) {
            if (evt.getNewValue().toString().equals("DONE")) {
                this.uploadButton.setEnabled(true);
                this.uploadButton.setBackground(null);
                this.openButton.setEnabled(true);
                this.fc.setSelectedFile(null);
                if (this.folderToUpload != null) {
                    this.fc.setCurrentDirectory(new File(this.folderToUpload.getParent()));
                }
                this.folderToUpload = null;
                this.uploadFolderInfoTf.setText("");
            }
        }
    }
}
