package com.foto.uploader.fotouploader;

import java.awt.AlphaComposite;
import java.awt.Graphics2D;
import java.awt.Image;
import java.awt.RenderingHints;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;
import java.nio.file.CopyOption;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.logging.Level;
import javax.imageio.ImageIO;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

public class SanImageResizer {

    private static final Logger LOGGER = LogManager.getLogger();
    File[] listOfFiles;
    String folder;
    String folderResizedImg;

    public SanImageResizer(String folder, String folderResizedImg) throws IOException {
        this.folder = folder;
        this.folderResizedImg = folderResizedImg;
        File folderFile = new File(folder);
        listOfFiles = folderFile.listFiles();
        if (listOfFiles.length > 0) {
            final File myFolderResizedImg = new File(folderResizedImg);
            if (myFolderResizedImg.exists()) {
                SanUtil.delete(myFolderResizedImg);
            }
            myFolderResizedImg.mkdir();
        }
    }

    public File[] getListOfFiles() {
        return listOfFiles;
    }
    
    

    public static void main(String[] args) throws IOException {
        //String mainFolder = "S:\\temp\\демяшкина 11.12 а04\\";
        String mainFolder = "S:\\temp\\mix";
        String folderResizedImg = "S:\\temp\\mix_res";
        SanImageResizer imageResizer = new SanImageResizer(mainFolder, folderResizedImg);
        final File[] listOfFiles1 = imageResizer.getListOfFiles();
        for (int i = 0; i < listOfFiles1.length; i++) {
            File file = listOfFiles1[i];
            LOGGER.debug("File " + (i + 1) + "/" + listOfFiles1.length + " " + file.getName());
            try {
                imageResizer.reSizeFile(file);
            } catch (SanFTPException ex) {
                java.util.logging.Logger.getLogger(SanImageResizer.class.getName()).log(Level.SEVERE, null, ex);
            }
        }
        //resizeFolder(mainFolder, folderResizedImg);
    }

    public void resizeFolder() throws IOException, SanFTPException {
        LOGGER.debug("START");
        long start = System.currentTimeMillis();

        for (int i = 0; i < listOfFiles.length; i++) {
            reSizeFile(listOfFiles[i]);
        }
        LOGGER.debug("************ Image Resize DONE");
        LOGGER.debug("dif = " + SanUtil.getFormattedTimeStr(System.currentTimeMillis() - start));
        LOGGER.debug("FINISH");
    }

    public void reSizeFile(File file) throws IOException, SanFTPException {
        if (file.isFile()) {
            Image img;
            BufferedImage tempJPG;
            File newFileJPG;
            //if(i == 10) break;
            //LOGGER.debug("File " + (i + 1) + "/" + listOfFiles.length + " " + file.getName());
            img = ImageIO.read(file.getAbsoluteFile());
            if(img == null){
                throw new SanFTPException(String.format("Файл %s имеет неподдерживаемый формат. "
                        + "Поддерживаются следующие: GIF, PNG, JPEG, BMP, and WBMP", file.getName()));
            }
            final int height = img.getHeight(null);
            final int width = img.getWidth(null);

            LOGGER.trace("height = " + height);
            LOGGER.trace("width = " + width);

            if (width > Settings.IMAGE_SIZE || height > Settings.IMAGE_SIZE) {
                LOGGER.trace("file is big and will be resized!");
                int newHeight = height;
                int newWidth = width;
                if (width > height) {
                    newWidth = Settings.IMAGE_SIZE;
                    newHeight = (int) (Settings.IMAGE_SIZE / ((float) width / height));
                } else {
                    newHeight = Settings.IMAGE_SIZE;
                    newWidth = (int) (Settings.IMAGE_SIZE / ((float) height / width));
                }
                LOGGER.trace("newHeight = " + newHeight);
                LOGGER.trace("newWidth = " + newWidth);
                //tempPNG = resizeImage(img, newHeight, newWidth);
                tempJPG = resizeImage(img, newWidth, newHeight);
                //newFilePNG = new File(mainFolder + "/resize/" + listOfFiles[i].getName() + "_New.png");

                newFileJPG = new File(folderResizedImg + File.separator + file.getName());
                LOGGER.trace("newFileJPG = " + newFileJPG);
                ImageIO.write(tempJPG, "jpg", newFileJPG);
            } else {
                LOGGER.debug("just copy" + file.getName());
                Files.copy(file.toPath(), Paths.get(folderResizedImg + File.separator + file.getName()));
            }
        }
    }

    /**
     * This function resize the image file and returns the BufferedImage object
     * that can be saved to file system.
     */
    public static BufferedImage resizeImage(final Image image, int width, int height) {
        final BufferedImage bufferedImage = new BufferedImage(width, height, BufferedImage.TYPE_INT_RGB);
        final Graphics2D graphics2D = bufferedImage.createGraphics();
        graphics2D.setComposite(AlphaComposite.Src);
        //below three lines are for RenderingHints for better image quality at cost of higher processing time
        graphics2D.setRenderingHint(RenderingHints.KEY_INTERPOLATION, RenderingHints.VALUE_INTERPOLATION_BILINEAR);
        graphics2D.setRenderingHint(RenderingHints.KEY_RENDERING, RenderingHints.VALUE_RENDER_QUALITY);
        graphics2D.setRenderingHint(RenderingHints.KEY_ANTIALIASING, RenderingHints.VALUE_ANTIALIAS_ON);
        graphics2D.drawImage(image, 0, 0, width, height, null);
        graphics2D.dispose();
        return bufferedImage;
    }
}
