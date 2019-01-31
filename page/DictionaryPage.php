<?php
class DictionaryPage extends Page {
	public function get($f3) {
		debug_message ( '- start DictionaryPage->get' );
		
		checkAuth ( $f3 );
		$entity = $f3->get ( 'PARAMS.entity', "" );
		debug_message ( '$entity=' . $entity );
		$this->showPage ( $entity, $this->resolveId () );
		
		debug_message ( '- end DictionaryPage->get' );
	}
	private function regionAjax($id, $action, $mapper) {
		debug_message ( 'Start DictionaryPage->regionAjax' );
		
		$result = [ ];
		$result ['error'] = 0;
		$result ['message'] = '';
		$result ['file_name'] = '';
		$result ['id'] = $id;
		
		try {
			if ($action == 'delete') {
				$result ['file_name'] = $mapper->file_name;
				$mapper->file_name = '';
				$mapper->save ();
				unlink ( 'map_files/' . $result ['file_name'] );
				return $result;
			} elseif ($action == 'upload') {
				$file_limit_mb = 10;
				if ($_FILES ["fileToUpload"] ["size"] > ($file_limit_mb * 1024 * 1024)) {
					$result ['error'] = 1;
					$result ['message'] = "Файл превышает лимит (10 мб)";
					return $result;
				}
				$target_dir = "map_files/";
				
				$region_name = $mapper->name;
				$ext = getExtensionByMimeType ( $_FILES ["fileToUpload"] ["type"] );
				$file_name = $region_name . uniqid () . '.' . $ext;
				$target_file = $target_dir . $file_name;
				
				$mask_prev_files = $target_dir . $region_name . "*.*";
				$array_res = array_map ( 'unlink', glob ( $mask_prev_files ) );
				
				if (move_uploaded_file ( $_FILES ["fileToUpload"] ["tmp_name"], $target_file )) {
					$result ['file_name'] = $file_name;
					$mapper->file_name = $file_name;
					$mapper->save ();
				} else {
					$result ['error'] = 1;
					$result ['message'] = "Ошибка при загрузке файла";
				}
			} else {
				$result ['error'] = 1;
				$result ['message'] = "action не известный";
			}
		} catch ( Exception $ex ) {
			process_exception ( $ex );
			$result ['error'] = 1;
			$result ['message'] = $ex->getMessage ();
		}
		debug_message ( 'End DictionaryPage->regionAjax: ' . $result, 1 );
		return $result;
	}
	public function post($f3) {
		debug_message ( '- start DictionaryPage->post' );
		checkAuth ( $f3 );
		$id = $f3->get ( 'POST.id', 0 );
		$entity = $f3->get ( 'POST.entity', "" );
		$errorMessage = "";
		
		try {
			// debug_message ( '$entity='.$entity );
			$mapper = new \DB\SQL\Mapper ( $f3->get ( 'db' ), $entity );
			if ($id) {
				$mapper = $mapper->load ( array (
						'id=?',
						$id 
				) );
			}
			if ($entity == 'region' and $f3->exists ( 'POST.action' )) {
				$result = $this->regionAjax ( $id, $f3->get ( 'POST.action' ), $mapper );
				exit ( json_encode ( $result ) );
			}
			
			if ($entity == 'category') {
				//debug_message($f3->get ( 'POST', ""), 1);
				//debug_message($mapper, 1);
				//debug_message("changed<<<");
				//debug_message($this->categoryIsChangedExceptStatus($mapper, $f3->get ( 'POST', "")), 1);
				//debug_message("equals>>>");
				//$this->categoryIsChangedExceptStatus($mapper, $f3->get ( 'POST', ""))
				
				if (existFotoUploadCategoryByCategoryId ( $f3, $id )) {
					if ($this->categoryIsChangedExceptStatus($mapper, $f3->get ( 'POST', ""))) {
						throw new Exception ( "Данную категорию нельзя менять, на нее ссылаются отчеты" );
					}
				}
				
				
			}
			
			if ($entity == 'user') {
				$mapper->login = $f3->get ( 'POST.login', "" );
				$mapper->password = md5 ( $f3->get ( 'POST.password', "123456" ) );
			} else {
				$mapper->copyFrom ( 'POST' );
			}
			$mapper->status = $f3->get ( 'POST.status', 0 );
			$mapper->save ();
			$id = $mapper->id;
		} catch ( Exception $ex ) {
			$errorMessage = $ex->getMessage ();
			process_exception ( $ex );
		}
		
		debug_message ( '- end DictionaryPage->post' );
		$this->showPage ( $entity, $id, $errorMessage );
		// $f3->reroute ( "/dictionary/$entity/" . $mapper->id );
	}
	private function showPage($entity, $id, $errorMessage = '') {
		debug_message ( '- start DictionaryPage->showPage' );
		$f3 = $this->f3;
		$f3->set ( 'title', 'Справочник ' . $this->getEntityName ( $entity ) );
		$f3->set ( 'errorMessage', $errorMessage );
		
		$object = getEntityByNameAndId ( $f3, $entity, $id );
		
		$f3->set ( 'entity', $entity ); // for menu tab activation
		$f3->set ( 'entityObj', $object );
		if ($entity == 'category') {
			//$query = 'select c.*, cst.name as customer_name from category c, customer cst where c.customer_id=cst.id order by c.id desc';
			$query = "select c.id, c.name, c.price, c.status,cst.name as customer_name, "
  ." GROUP_CONCAT(fuc.foto_upload_id order by fuc.foto_upload_id) AS report_list "
    ." from category c "
    ." inner join  customer cst on c.customer_id=cst.id "
    ." left join foto_upload_category fuc on(fuc.category_id=c.id) "
    ." group by c.id, c.name, cst.name, c.price "
   ." order by c.id desc";
			$f3->set ( 'entityList', $f3->get ( 'db' )->exec ( $query ) );
			$f3->set ( 'customers', $f3->get ( 'db' )->exec ( 'select * from customer order by name' ) );
		} else {
			$order_by = ' order by id desc';
			if ($entity == 'region')
				$order_by = ' order by id asc';
			$f3->set ( 'entityList', $f3->get ( 'db' )->exec ( 'select * from ' . $entity . $order_by ) );
		}
		
		debug_message ( '- end DictionaryPage->showPage' );
		echo \Template::instance ()->render ( 'view/dictionary.htm' );
	}
	protected function resolveId() {
		$id = 0;
		if ($this->f3->exists ( 'PARAMS.id' ))
			$id = $this->f3->get ( 'PARAMS.id' );
		
		return $id;
	}
	protected function resolveEntity() {
		if ($this->f3->exists ( 'PARAMS.entity' ))
			$id = $this->f3->get ( 'PARAMS.entity' );
		
		return false;
	}
	private function getEntityName($entity) {
		$map = [ 
				'category' => 'Категории',
				'customer' => 'Заказчики',
				'employee' => 'Сотрудники',
				'region' => 'Районы',
				'user' => 'Пользователи' 
		
		];
		return $map [$entity];
	}
	
	/**
	 * 
	 * @param unknown $category1
	 * @param unknown $category2
	 * @return true if one of fields except status was changed
	 */
	private function categoryIsChangedExceptStatus ($categoryDb, $categoryForm){
		if($categoryDb->name != $categoryForm['name'] 
			or $categoryDb->customer_id != $categoryForm['customer_id'] 
				or $categoryDb->price != $categoryForm['price']) {
				return true; 
		}
		return false;
		
	}
}