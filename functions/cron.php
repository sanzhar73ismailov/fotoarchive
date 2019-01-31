<?php
//require_once 'fu';
class CreateReportFtp {
	public static function checkZips($f3) {
		
		try {
			$file_mask = ("ftp/*.zip");
			
			$file_zips = glob ( $file_mask );
			
			if ($file_zips) {
				debug_message ( 'in if($file_zips){' );
				$ftp_temp_dir = "ftp/temp";
				if (! file_exists ( $ftp_temp_dir )) {
					mkdir ( $ftp_temp_dir );
				}
				foreach ( $file_zips as $file_zip ) {
					$newZipLoc = $ftp_temp_dir . '/' . basename ( $file_zip );
					$ren = rename ( $file_zip, $newZipLoc );
					//debug_message ( $ren, 1 );
					$zip_extr_loc = $ftp_temp_dir . '/' . basename ( $file_zip, '.zip' );
					if (file_exists ( $zip_extr_loc )) {
						delTree ( $zip_extr_loc );
					}
					mkdir ( $zip_extr_loc );
					$zip = new ZipArchive ();
					if ($zip->open ( $newZipLoc ) === TRUE) {
						$zip->extractTo ( $zip_extr_loc );
						$zip->close ();
						debug_message ( 'OK extracted' );
						$reportId = createDraftReport ( $f3 , 1);
						debug_message ( '$reportId=' . $reportId );
						
						$image_files = glob ( $zip_extr_loc . '/*.*' );
						//debug_message ( $image_files, 1 );
						
						foreach ( $image_files as $image_file ) {
							ReportFormPage::uploadFile ( $f3, $result, $reportId, $image_file );
						}
						delTree ( $zip_extr_loc );
						unlink ( $newZipLoc );
					} else {
						debug_message ( 'ERROR extracted' );
					}
				}
				debug_message ( 'finish if($file_zips){' );
			}
		} catch ( Exception $e ) {
			debug_message ("ERROR EXC: " . $e->getMessage());
			process_exception ( $e );
		}
		
		
	}
}
?>