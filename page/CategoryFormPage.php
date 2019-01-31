<?php
class CategoryFormPage extends Page {
	public function get($f3) {
		debug_message ( '- start CategoryFormPage->get' );
		checkAuth($f3);
		$f3->set ( 'title', 'Категории' );
		$f3->set ( 'page', 'category' ); // for menu tab activation
		                                 // debug_message($this->f3->get('PARAMS'), 1);
		                                 // $f3->set('myVar1', 1);
		                                 // $f3->set('uploadDone', "uploadDone in Get method");
		                                 // var_dump($employees);
		$id = $this->resolveId ();
		$report = getReportWithDic ( $f3, $id );
		$fotoUploadCategoryList = getFotoUploadCategoryListByReportId ( $f3, $id );
		debug_message($fotoUploadCategoryList,1);
		$f3->set ( 'report', $report );
		$f3->set ( 'fotoUploadCategories', $fotoUploadCategoryList );
		$f3->set ( 'categories', getCategoryActiveList ( $f3 ) );
		debug_message ( '- end CategoryFormPage->get' );
		echo \Template::instance ()->render ( 'view/category.htm' );
	}
	public function post($f3) {
		debug_message ( 'Start CategoryFormPage->post' );
		checkAuth($f3);
		$successMessage = "";
		
		$f3->set ( 'page', 'category' ); // for menu tab activation
		$id = $this->f3->get ( 'POST.id', 0 );
		
		$fotoUploadCategoryList = getFotoUploadCategoryListByReportId ( $f3, $id );
		
		$f3->set ( 'customers', getCustomerList ( $f3 ) );
		$report = getReportWithDic ( $f3, $id );
		if ($report ['status_id'] > Status::$CATEGORIES_ADDED) {
			$f3->set ( 'report', $report );
			$f3->set ( 'errorMessage', 'Категории сопоставлены. Необходимо удалить сопоставление' );
		} else {
			$category_ids = $f3->get ( 'POST.category_id' );
			$categoriies_added = 0;
			if (count ( $fotoUploadCategoryList ) > 0) {
				$deleted = deleteFotoUploadCategoryListByReportId ( $f3, $id );
			}
			for($i = 0; $i < count ( $category_ids ); $i ++) {
				$category_id = $category_ids [$i];
// 				debug_message ( "category_name = $category_name customer_id =  $customer_id price = $price" );
				addFotoUploadCategory ( $f3, $id, $category_id );
				$categoriies_added = 1;
			}
			if ($categoriies_added) {
				$successMessage = "Категории добавлены/обновлены.";
				$object = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'foto_upload' );
				if ($id) {
					$object->load ( [ 
							'id=?',
							$id 
					] );
				}
				$object->status_id = Status::$CATEGORIES_ADDED;
				$result_save = $object->save ();
				//successMessage .= " Статус отчета изменен на 'Присвоены категории'.";
			}
		}
		$f3->set ( 'report', getReportWithDic ( $f3, $id ));
		$f3->set ( 'categories', getCategoryActiveList ( $f3, $id ) );
		$f3->set ( 'fotoUploadCategories', getFotoUploadCategoryListByReportId ( $f3, $id ) );
		if($successMessage != ''){
			$f3->set ( 'successMessage', $successMessage);
		}
		debug_message ( 'Finish CategoryFormPage->post' );
		echo \Template::instance ()->render ( 'view/category.htm' );
	}
	protected function resolveId() {
		$id = 0;
		if ($this->f3->exists ( 'PARAMS.id' ))
			$id = $this->f3->get ( 'PARAMS.id' );
		
		return $id;
	}
}