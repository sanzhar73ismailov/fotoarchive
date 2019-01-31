<?php
class CategoryMatchFormPage extends Page {
	public function get($f3) {
		debug_message ( 'Start CategoryMatchFormPage->get' );
		checkAuth($f3);
		
		$f3->set ( 'title', 'Проверка фото' );
		$f3->set ( 'page', 'category_match' ); // for menu tab activation
		$id = $this->resolveId ();
		$this->setObjects ( $f3, $id );
		
		debug_message ( 'Finish CategoryMatchFormPage->get' );
		echo \Template::instance ()->render ( 'view/category_match.htm' );
	}
	public function post($f3) {
		debug_message ( 'Start CategoryMatchFormPage->post' );
		checkAuth($f3);
		$successMessage = "";
		$f3->set ( 'page', 'category_match' ); // for menu tab activation
		$id = $this->f3->get ( 'POST.id', 0 );
		debug_message ($this->f3->get ( 'POST' ),1);
		//var_dump($this->f3->get ( 'POST' ));
		//exit('-----------------');
		
		$report = getReportWithDic ( $f3, $id );
		if ($report ['status_id'] > Status::$CHECKED) {
			$this->setObjects ( $f3, $id );
			$f3->set ( 'errorMessage', 'Проверка прервана - отчет уже отправлен' );
		} else {
			
			$resOfDel = $f3->db->exec('DELETE FROM foto_category WHERE foto_id in (select f.id from foto f where f.foto_upload_id='. $report['id'] . ')');
			//var_dump($res);
			//exit();
			$prefix_to_find = "foto_";
			$address_id = 0;
			foreach ( $f3->get ( 'POST' ) as $name => $values ) {
				if (strpos ( $name, $prefix_to_find ) !== false) {
					
					foreach ($values as $value){
						$foto_id = str_replace ( $prefix_to_find, "", $name );
						$foto = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'foto' );
						$foto->load ( [
								'id=?',
								$foto_id
						] );
						$foto->is_address = 0;
						$foto->deleted = 0;
						$foto->address_id = 0;
						$foto->foto_category_id = 0;
						switch ($value) {
							case 'address' :
								$foto->is_address = 1;
								$address_id = $foto_id;
								break;
							case 'delete' :
								$foto->deleted = 1;
								$foto->address_id = $address_id;
								break;
							default :
								$foto->address_id = $address_id;
								
								$foto_category = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'foto_category' );
								$foto_category->foto_id = $foto_id;
								$foto_category->foto_upload_category_id = $value;
								$foto_category->price = getPriceByFotoUploadCategoryId($f3, $value);
								$foto_category->foto_upload_id = $id;
								
								$foto_category->save ();
								break;
						}
						$foto->save ();
					}
					
					$successMessage = "Проверка проведена";
				}
			}
			$report = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'foto_upload' );
			if ($id) {
				$report->load ( [ 
						'id=?',
						$id 
				] );
			}
			$report->status_id = Status::$CHECKED;
			$report->save ();
			$successMessage .= " Статус отчета изменен на 'Проверено'.";
		}
		$this->setObjects ( $f3, $id );
		if ($successMessage != '') {
			$f3->set ( 'successMessage', $successMessage );
		}
		debug_message ( 'Finish CategoryMatchFormPage->post' );
		// $f3->reroute('/reports');
		echo \Template::instance ()->render ( 'view/category_match.htm' );
	}
	protected function resolveId() {
		$id = 0;
		if ($this->f3->exists ( 'PARAMS.id' ))
			$id = $this->f3->get ( 'PARAMS.id' );
		
		return $id;
	}
	private function setObjects($f3, $id) {
		$report = getReportWithDic ( $f3, $id );
		$fotos = getFotosByReportId ( $f3, $id );
		$categoryList = getFotoUploadCategoryListByReportId ( $f3, $id );
		$fotoCategoryList = getFotoCategoriesByFotoUploadCategoryId( $f3, $id );
		debug_message($fotoCategoryList,1);
		$f3->set ( 'report', $report );
		$f3->set ( 'categories', $categoryList );
		$f3->set ( 'fotoCategories', $fotoCategoryList );
		$f3->set ( 'fotos', $fotos );
	}
}