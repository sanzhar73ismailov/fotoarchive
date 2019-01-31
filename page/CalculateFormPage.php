<?php
class CalculateFormPage extends Page {
	private static $GENERAL_ARC_FOLDER = "arc_files";
	public function get($f3) {
		debug_message ( '- start CalculateFormPage->get' );
		checkAuth($f3);
		$f3->set ( 'title', 'Отправка по почте' );
		$f3->set('page', 'calculate'); // for menu tab activation
		$id = $this->resolveId ();
		$report = getReportWithDic ( $f3, $id );
		$categoryList = getCategoryViewListByReportId( $f3, $id );
		$f3->set ( 'report', $report );
		
		if(1==1) 
			$this->createZipFiles ( $report, $categoryList );
		
		$f3->set ( 'categories', getCategoryViewListByReportId ( $f3, $id ) );
		$f3->set ( 'totalRow', getTotalRowCategoryViewListByReportId ( $f3, $id ) );
		
		debug_message ( '- end CalculateFormPage->get' );
		echo \Template::instance ()->render ( 'view/calculate.htm' );
	}
	public function post($f3) {
		debug_message ( '- start CategoryMatchFormPage->post' );
		checkAuth($f3);
		$f3->set('page', 'calculate'); // for menu tab activation
		$test = false; // тестовая отправка (эмуляция)
		debug_message($f3->get('POST'),1);
		if ($this->f3->exists ( 'POST.send_email' )) {
			$json_result = [ ];
			$json_result ['by_hand'] = false;
			
			$report_id = $this->f3->get ( 'POST.report_id', 0 );
			$category = getCategoryViewById ( $f3, $this->f3->get ( 'POST.category_id', 0 ) );
			
			$categoryUpdate = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'foto_upload_category' );
			$categoryUpdate->load ( [ 
					'id=?',
					$category ['id'] 
			] );
			
			//debug_message($category['id'] , 1);
			//exit($json_result);
			//debug_message($categoryUpdate, 1);
			
			if ($this->f3->exists ( 'POST.mark_as_send' )) {
				$result = true;
				$json_result ['by_hand'] = true;
			} else {
				//if(rand(1, 1000)%2==0){
				//if (1==1) {
				//  http_response_code(500);
				//  exit();
				//}
				$result = sendMailWithAttach ($f3->get('config'), $category ['email'], $f3->get ('BASE',''), $category ['file_path'], $category ['file_zip'], $category, $test );
			}
			//debug_message($result, 1);
			//exit($json_result);
			$json_result ['category_id'] = $category ['id'];
			$json_result ['result'] = $result;
			$json_result ['error'] = '';
			$json_result ['all_sent'] = false;
			
			$categoryUpdate->email_send = 1;
			$categoryUpdate->email_date = date ( "Y-m-d H:i:s" );
			
			if ($result === true) {
				$categoryUpdate->email_result = 1;
				$categoryUpdate->email_error = '';
			} else {
				$categoryUpdate->email_result = 0;
				$categoryUpdate->email_error = $result;
				$json_result ['error'] = $result;
			}
			$categoryUpdate->save ();
			
			$resultAllSentByCategory = isAllSentByReportId ( $f3, $report_id );
			
			if ($resultAllSentByCategory ['all_sent']) {
				$report = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'foto_upload' );
				$report->load ( [ 
						'id=?',
						$resultAllSentByCategory ['report_id'] 
				] );
				$report->status_id = Status::$SENT;
				$report->save ();
				$json_result ['all_sent'] = true;
			}
			
			exit ( json_encode ( $json_result ) );
		}
		debug_message ( '- end CategoryMatchFormPage->post' );
		// $f3->reroute ( '/calculate/'.$id );
		// echo \Template::instance ()->render ( 'view/calculate.htm' );
	}
	protected function resolveId() {
		$id = 0;
		if ($this->f3->exists ( 'PARAMS.id' ))
			$id = $this->f3->get ( 'PARAMS.id' );
		
		return $id;
	}
	private function createZipFiles($report, $categoryList) {
		debug_message ( '- start CalculateFormPage->createZipFiles' );
		// create folder for files if it's not exists
		$target_dir = self::$GENERAL_ARC_FOLDER . '/' . $report ['id'];
// 		debug_message ( 'file_exists($target_dir) before=' . file_exists ( $target_dir ) );
		if (! file_exists ( $target_dir )) {
			mkdir ( $target_dir, 0777, true );
		}
// 		debug_message ( 'file_exists($target_dir) after=' . file_exists ( $target_dir ) );
		foreach ( $categoryList as $category ) {
			/*
			if ($category ['category_name'] == 'Общая сумма') {
				continue;
			}
			*/
			if ($category ['file_zip'] != '') {
				debug_message ( "in table category with id " . $category ['id'] . " already " . $category ['file_zip'] . " exists" );
				continue;
			}
			$zip = new ZipArchive ();
			
			$only_filename = $category ['category_name'] . ', ';
			$only_filename .= $report ['r_name'] . ', ';
			$only_filename .= $report ['date'] . ', ';
			$only_filename .= '(' . $category ['foto_number'];
			$only_filename .= ', фото).zip';
			
			$filenamezip = $target_dir . "/" . $only_filename;
			if ($zip->open ( $filenamezip, ZipArchive::CREATE ) !== TRUE) {
				debug_message ( "cannot open <$filenamezip>\n" );
				return;
			}
			$fotos = getFotosByCategoryId ( $this->f3, $category ['id'] );
// 			debug_message ( "<<< fotos start category['id'] = " . $category ['id'] );
			foreach ( $fotos as $foto ) {
				debug_message ( 'foto_name=' . $foto ['foto_old_name'] );
				// debug_message($foto,1);
				$foto_name_in_zip = $foto ['foto_old_name'];
				if ($foto ['is_address']) {
					$foto_name_in_zip = getFileNameWithAddressInName ( $foto ['foto_old_name'] );
				}
				$zip->addFile ( $foto ['foto_name'], $foto_name_in_zip );
			}
// 			debug_message ( ">>> fotos end" );
// 			debug_message ( "numfiles: " . $zip->numFiles );
// 			debug_message ( "status:" . $zip->status );
			$zip->close ();
			
			$row_to_update = new \DB\SQL\Mapper ( $this->f3->get ( 'db' ), 'foto_upload_category' );
			$row_to_update->load ( [ 
					'id=?',
					$category ['id'] 
			] );
			$row_to_update->file_path = $target_dir;
			$row_to_update->file_zip = $only_filename;
			$row_to_update->save ();
		}
		
		debug_message ( '- end CalculateFormPage->createZipFiles' );
	}
}