<?php
class ReportFormPage extends Page {
	private static $IMG_SIZE_ALLOWED = 2048;
	private $uploadedFiles = [ ];
	public function get($f3) {
		debug_message ( 'Start ReportFormPage->get' );
		checkAuth ( $f3 );
		$f3->set ( 'title', 'Отчёт' );
		$f3->set ( 'page', 'report' ); // for menu tab activation
		$id = $this->resolveId ();
		if ($id) {
			$this->uploadedFiles = getFotosByReportId ( $f3, $id );
			// debug_message($this->uploadedFiles, 1);
		}
		$fotoLoad = $f3->get ( 'PARAMS.fotoLoad', "" );
		// debug_message ( $f3->get ( 'PARAMS'),1 );
		// debug_message ( $fotoLoad);
		if ($fotoLoad == "fotoLoad") {
			$object = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'foto_upload' );
			$object->load ( [ 
					'id=?',
					$id 
			] );
			if ($object->status_id == Status::$ASSIGNED_NUMBER) {
				$object->status_id = Status::$UPLOADED;
				$result_save = $object->save ();
			}
		}
		$this->setObjects ( $f3, $id );
		debug_message ( 'Finish ReportFormPage->get' );
		echo \Template::instance ()->render ( 'view/report.htm' );
	}
	public function post($f3) {
		debug_message ( 'Start ReportFormPage->post ver 123' );
		$result = [ ];
		$result ['error'] = 0;
		$result ['message'] = '';
		$result ['status_id'] = 0;
		$result ['id'] = 0;
		
		// echo json_encode($f3->get ( 'POST' ));
		
		// debug_message ( $f3->get ( 'POST' ), 1 );
		// debug_message ($f3->get ( 'POST.region' ), 1 );
		
		checkAuth ( $f3 );
		$id = $this->f3->get ( 'POST.id', 0 );
		
		if ($f3->exists ( 'POST.action' )) {
			debug_message ( '51' );
			debug_message ( $f3->get ( 'POST' ), 1 );
			if ($f3->get ( 'POST.action', '' ) == 'delete') {
				debug_message ( '53' );
				
				$result = $this->deleteFoto ( $f3, $result );
				
				exit ( json_encode ( $result ) );
			} elseif ($f3->get ( 'POST.action', '' ) == 'deleteAllFotos') {
				debug_message ( '57' );
				debug_message ( $result, 1 );
				$result = $this->deleteAllFotos ( $f3, $id, $result );
				debug_message ( $result, 1 );
				debug_message ( '57' );
				exit ( json_encode ( $result ) );
			} elseif ($f3->get ( 'POST.action', '' ) == 'getFotos') {
				exit ( json_encode ( getFotosByReportId ( $f3, $id ) ) );
			} else {
				$result ['error'] = 1;
				$result ['message'] = 'action не задан';
				exit ( json_encode ( $result ) );
			}
		}
		
		$f3->set ( 'page', 'report' ); // for menu tab activation
		                               // debug_message($this->f3->get('PARAMS'),1);
		                               // $f3->set('uploadDone', "uploadDone in Post method");
		
		if ($f3->exists ( 'POST.sendFiles' )) {
			$result = self::uploadFile ( $f3, $result, $id );
			// if(rand(1, 1000)%2==0){
			// http_response_code(500);
			// }
			exit ( json_encode ( $result ) );
		}
		
		$object = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'foto_upload' );
		if ($id) {
			$object->load ( [ 
					'id=?',
					$id 
			] );
		}
		if ($object ['status_id'] > Status::$UPLOADED) {
			// $this->setObjects ( $f3, $object->id );
			// $f3->set ( 'errorMessage', 'Категории уже созданы. Необходимо удалить категории' );
			$result ['error'] = 1;
			$result ['message'] = 'Категории уже созданы. Необходимо удалить категории';
			$result ['status_id'] = 0;
		} else {
			$object->copyFrom ( 'POST' );
			$object->user_id = $f3->get ( 'SESSION.user_id' );
			$object->save ();
			$result ['message'] = 'Данные сохранены';
			$result ['status_id'] = $object->status_id;
			$result ['id'] = $object->id;
		}
		
		echo json_encode ( $result );
		debug_message ( 'Finish ReportFormPage->post' );
	}
	protected function resolveId() {
		$id = 0;
		if ($this->f3->exists ( 'PARAMS.id' ))
			$id = $this->f3->get ( 'PARAMS.id' );
		
		return ( int ) $id;
	}
	private function isThereFiles() {
		if (empty ( $_FILES )) {
			return false;
		}
		if (empty ( $_FILES ['fileToUpload'] ['name'] [0] )) {
			return false;
		}
		return true;
	}
	private function isFitoAlreadyExists($name) {
		foreach ( $this->uploadedFiles as $file ) {
			if ($name == $file ['foto_old_name']) {
				return true;
			}
		}
		return false;
	}
	public static function uploadFile($f3, $result=array(), $foto_upload_id, $ftp_file = '') {
		//debug_message ( 'Start ReportFormPage->uploadFile' );
		define ( 'UPLOAD_DIR', UPLOAD_REPORT_DIR . '/' . $foto_upload_id );
		// debug_message ("0");
		try {
			/*
			 * проверка существования файла теперь делается на стороне клиента
			 * $fotoExist = fotoExists($f3, $foto_upload_id, $foto_old_name);
			 * debug_message ($fotoExist);
			 * if($fotoExist){
			 * debug_message ("0=Фото с таким именем уже существует");
			 * throw new Exception("Фото с таким именем уже существует");
			 * }
			 */
			if (! file_exists ( UPLOAD_DIR )) {
				if (! mkdir ( UPLOAD_DIR, 0777, true )) {
					throw new Exception ( "Невозможно создать папку " . UPLOAD_DIR );
				}
			}
			// debug_message ("1");
			$foto_old_name = '';
			$success = false;
			$newname = date ( 'Ymd-His', time () ) . '-' . uniqid () . '.jpg';
			$newname = UPLOAD_DIR . '/' . $newname;
			/*
			$dir_for_small_pic = UPLOAD_DIR . '/small';
			if(!file_exists($dir_for_small_pic)){
				mkdir($dir_for_small_pic);
			}
			*/
			$ftp = 0;
			if ($ftp_file) {
				$ftp = 1;
				$foto_old_name = basename ($ftp_file);;
				//debug_message ("in in ftp_file");
				//debug_message ('$ftp_file='.$ftp_file);
				$success = rename($ftp_file, $newname);
				//debug_message ('$success='.$success);
			} else {
				$foto_old_name = $_POST ['file_name'];
				$img = $_POST ['image'];
				$img = str_replace ( 'data:image/jpeg;base64,', '', $img );
				$img = str_replace ( ' ', '+', $img );
				$data = base64_decode ( $img );
				$success = file_put_contents ( $newname, $data );
			}
			//creta 
			$result ['fileName'] = $foto_old_name;
			
			// debug_message ("2");
			if (! $success) {
				$result ['error'] = 1;
				$result ['message'] = 'Unable to save the file.';
				debug_message ( 'Unable to save the file.' );
			} else {
				$checkSize = getimagesize ( $newname );
				$newfoto_id = addFoto ( $f3, $foto_upload_id, $newname, $foto_old_name, $checkSize [0], $checkSize [1] );
				//debug_message ('$newfoto_id='.$newfoto_id);
				// debug_message ("3");
			}
		} catch ( Exception $e ) {
			// debug_message ("Exception");
			process_exception ( $e );
			// SQLSTATE[23000]
			$result ['error'] = 1;
			$result ['message'] = $e->getMessage ();
		}
		// debug_message ("4");
		//debug_message ( 'Start ReportFormPage->uploadFile' );
		return $result;
	}
	
	/**
	 *
	 * @deprecated
	 */
	private function uploadFiles($foto_upload_id) {
		$statisticUpload = new StatisticUpload ();
		if ($this->isThereFiles ()) {
			$img_desc = $this->reArrayFiles ( $_FILES ['fileToUpload'] );
			
			foreach ( $img_desc as $img_item ) {
				$statisticUpload->total_files ++;
				$file = new FotoFile ();
				$file->uploaded = 0;
				$file->error_descr = '';
				$statisticUpload->files [] = $file;
				try {
					$file->name = $img_item ['name'];
					$file->size = ( int ) ($img_item ['size'] / 1024);
					$checkSize = getimagesize ( $img_item ["tmp_name"] );
					if (! empty ( $checkSize )) {
						$file->image_size = $checkSize [0] . " x " . $checkSize [1];
					}
					
					if ($img_item ['error']) {
						switch ($img_item ['error']) {
							case UPLOAD_ERR_OK :
								break;
							case UPLOAD_ERR_NO_FILE :
								throw new Exception ( 'No file sent' );
								break;
							case UPLOAD_ERR_INI_SIZE :
							case UPLOAD_ERR_FORM_SIZE :
								throw new Exception ( 'Exceeded filesize limit' );
								break;
							default :
								throw new Exception ( 'Unknown errors' );
						}
					}
					
					if ($this->isFitoAlreadyExists ( $file->name )) {
						throw new Exception ( 'Файл с таким именем уже существует' );
					}
					if ($checkSize [0] < self::$IMG_SIZE_ALLOWED && $checkSize [1] < self::$IMG_SIZE_ALLOWED) {
						throw new Exception ( 'Размер каждой стороны изображения должен быть больше, чем ' . self::$IMG_SIZE_ALLOWED . ' px' );
					}
					
					$target_dir = "uploads/directories";
					$target_dir .= '/' . $foto_upload_id;
					
					if (! file_exists ( $target_dir )) {
						mkdir ( $target_dir, 0777, true );
					}
					$newname = date ( 'Ymd-His', time () ) . '-' . mt_rand () . '.jpg';
					$newname = $target_dir . '/' . $newname;
					
					$move_uploaded = move_uploaded_file ( $img_item ['tmp_name'], $newname );
					if (! $move_uploaded) {
						throw new Exception ( "move_uploaded_file failed" );
					}
					addFoto ( $this->f3, $foto_upload_id, $newname, $img_item ['name'], $checkSize [0], $checkSize [1] );
					$file->uploaded = 1;
					$statisticUpload->uploaded_files ++;
				} catch ( Exception $ex ) {
					$file->error_descr = $ex->getMessage ();
				}
			}
		}
		
		return $statisticUpload;
	}
	private function reArrayFiles($file) {
		$file_ary = array ();
		$file_count = count ( $file ['name'] );
		$file_key = array_keys ( $file );
		
		for($i = 0; $i < $file_count; $i ++) {
			foreach ( $file_key as $val ) {
				$file_ary [$i] [$val] = $file [$val] [$i];
			}
		}
		return $file_ary;
	}
	private function setObjects($f3, $id) {
		// debug_message ( "Start ReportFormPage->setObjects" );
		$id = ( int ) $id;
		$object = getReportWithDic ( $f3, $id );
		$f3->set ( 'employees', getEmployeeList ( $f3 ) );
		$f3->set ( 'regions', getRegionList ( $f3 ) );
		$f3->set ( 'report', $object );
		$f3->set ( 'status', getStatus ( $f3, $object->status_id ) );
		$foto_count = getFotoCountByReportId ( $f3, $id );
		$f3->set ( 'foto_count', $foto_count );
		$f3->set ( 'fotos', $this->uploadedFiles );
		// debug_message ( "FInish ReportFormPage->setObjects" );
	}
	private function deleteFoto($f3, $result) {
		$result ['id'] = $f3->get ( 'POST.foto_id', '' );
		$res = deleteFoto ( $f3, $result ['id'] );
		if ($res === true) {
			return $result;
		}
		$result ['error'] = 1;
		if ($res === false) {
			$result ['message'] = 'Фото не существует';
		} else {
			$result ['message'] = $res;
		}
		return $result;
	}
	// deleteFotosByReportId($f3, $id);
	private function deleteAllFotos($f3, $id, $result) {
		debug_message ( 'start deleteAllFotos' );
		debug_message ( $result, 1 );
		$fotos = getFotosByReportId ( $f3, $id );
		$delRows = deleteFotosByReportId ( $f3, $id );
		$delFiles = 0;
		if ($delRows == 0) {
			// if(1){
			debug_message ( '327' );
			$result ['error'] = 1;
			$result ['message'] = 'Не удалена ни одна запись';
		} else {
			debug_message ( '331' );
			delTree ( UPLOAD_REPORT_DIR . '/' . $id );
			$result ['message'] = 'Удалены все фото';
		}
		debug_message ( $result, 1 );
		debug_message ( 'end deleteAllFotos' );
		return $result;
	}
}

