package com.foto.uploader.fotouploader;

import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;
import javax.swing.SwingUtilities;
import javax.swing.UIManager;
import javax.swing.filechooser.FileSystemView;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

/**
 *
 * @author admin
 */
public class Main {

    private static final Logger LOGGER = LogManager.getLogger();

    public static void main(String[] args) {
        System.out.println("*** ver. app. " + new SanUtil().getManifestVersion());
        //SanUtil.printClassPath();
        //readProperties();
        SwingUtilities.invokeLater(new Runnable() {
            public void run() {
                try {
                    //Turn off metal's use of bold fonts
                    UIManager.put("swing.boldMetal", Boolean.FALSE);
                    //UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
                    SwingFtp.createAndShowGUI();
                } catch (Exception ex) {
                    LOGGER.error("error", ex);
                }

            }
        });
    }

    

}
