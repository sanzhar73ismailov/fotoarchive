<?php
define ( 'UPLOAD_REPORT_DIR', 'uploads/directories' );

/**
 * Статистика по загрузке
 *
 * @author admin
 *        
 */
class StatisticUpload {
	public $total_files = 0; // общее кол-во обработанных файлов (загруженных + не загруженных)
	public $uploaded_files = 0; // кол-во загруженных
	public $files = array (); // список обработанных файлов (FotoFile)
}
/**
 * Класс файла фото, для статистики
 *
 * @author admin
 *        
 */
class FotoFile {
	public $name; // старое название
	public $image_size; // размеры изображения (типа 2048Х1400)
	public $size; // размеры файла
	public $uploaded = 0; // загружен - 1, не загружен - 0
	public $error_descr = ''; // если не загружен, описание
}
function getExtensionByMimeType($type) {
	$ext = "";
	if ($type) {
		switch ($type) {
			case "image/jpeg" :
				$ext = 'jpg';
				break;
			case "image/png" :
				$ext = 'png';
				break;
			case "image/bmp" :
				$ext = 'bmp';
				break;
			default :
				$ext = '';
		}
	}
	return $ext;
}
function checkAuth($f3) {
	debug_message ( "Start checkAuth" );
	if (! $f3->get ( 'SESSION.user' ))
		$f3->reroute ( '/login' );
	debug_message ( "Finish checkAuth" );
}
function debug_message($message, $is_var = false) {
	if ($is_var)
		$message = var_export ( $message, true );
	
	$message = date ( '[Y-m-d H:i:s] ' ) . $message;
	
	file_put_contents ( 'data/message.txt', $message . PHP_EOL, FILE_APPEND );
}
function process_exception(Exception $e) {
	$file = 'data/error.txt';
	$text = getDetailsOfException ( $e );
	file_put_contents ( $file, $text . PHP_EOL, FILE_APPEND );
}
function getDetailsOfException(Exception $e) {
	$text = '';
	
	$text .= 'Error: ' . $e->getCode () . ' on ' . date ( 'Y-m-d H:i:s' ) . PHP_EOL;
	$text .= $e->getFile () . ':' . $e->getLine () . PHP_EOL;
	$text .= $e->getMessage () . PHP_EOL;
	$text .= var_export ( $e->getTraceAsString (), true ) . PHP_EOL;
	return $text;
}
function getSettings($f3) {
	$config = $f3->get ( 'db' )->exec ( 'SELECT * FROM settings' );
	if (count ( $config ) == 0) {
		throw new Exception ( "Настройки пустые, см. таблицу settings" );
	}
	return $config [0];
}
function deleteReport($f3, $id) {
	try {
		$delFotos = deleteFotosByReportId ( $f3, $id );
		$deletedFuc = $f3->get ( 'db' )->exec ( 'DELETE FROM foto_upload_category WHERE foto_upload_id=' . ( int ) $id );
		$delReports = $f3->get ( 'db' )->exec ( 'DELETE FROM foto_upload WHERE id=' . ( int ) $id );
		debug_message('$delFotos='.$delFotos);
		debug_message('$deletedFuc='.$deletedFuc);
		debug_message('$delReports='.$delReports);
		$delFolder = delTree ( UPLOAD_REPORT_DIR . '/' . $id );
		debug_message('$delFolder='.$delFolder);
		return true;
	} catch ( Exception $ex ) {
		process_exception ( $ex );
		return $ex->getMessage ();
	}
	return false;
}
function createDraftReport($f3, $ftp = 0) {
	$object = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'foto_upload' );
	$object->region_id = null;
	$object->ftp = $ftp;
	$object->save ();
	return $object->id;
}
function deleteFoto($f3, $id) {
	return deleteEntity ( $f3, 'foto', $id );
}

/**
 *
 * @return number of deleted records
 */
function deleteFotosByReportId($f3, $reportId) {
	$db = $f3->get ( 'db' );
	return $db->exec ( 'DELETE FROM foto WHERE foto_upload_id=' . ( int ) $reportId );
}
function deleteEntity($f3, $entity, $id) {
	if (! $id)
		return "id не задан";
	if (! $entity)
		return "таблица не указана";
	
	try {
		$foto = new \DB\SQL\Mapper ( $f3->get ( 'db' ), $entity );
		$foto->load ( array (
				'id=?',
				$id 
		) );
		return $foto->erase () == 1;
	} catch ( Exception $e ) {
		process_exception ( $e );
		return $e->getMessage ();
	}
}
function getAllReports($f3) {
	return getReports ( $f3 );
}
function getEntityByNameAndId($f3, $entity, $id) {
	if (! $id)
		return false;
	
	$entity_mapper = new \DB\SQL\Mapper ( $f3->get ( 'db' ), $entity );
	$entity = $entity_mapper->load ( [ 
			'id=?',
			$id 
	] );
	return $entity_mapper->cast ();
}
function getReports($f3, $page = 1) {
	$db = $f3->get ( 'db' );
	
	$limit = $f3->get ( 'PER_PAGE' );
	$offset = ($page - 1) * $limit;
	
	return $db->exec ( "
			select fu.*,
            s.name_ru status_name,
            e.last_name e_last_name,
            e.first_name e_first_name,
            e.patronymic_name e_patronymic_name,
            r.name r_name,
            u.login,
            count(f.id) fotos
            from foto_upload fu
            left join employee e on (fu.`employee_id`=e.`id`)
            left join region r on (fu.`region_id`=r.`id`)
            left join user u on (fu.`user_id`=u.id)
            left join status s on (fu.`status_id`=s.id)
            left join foto f on (fu.id=f.foto_upload_id)
            group by fu.id
			ORDER BY fu.id DESC 
			LIMIT ? OFFSET ?", [ 
			$limit,
			$offset 
	] );
}
function getReportersCount($f3) {
	$db = $f3->get ( 'db' );
	$contact = new \DB\SQL\Mapper ( $db, 'foto_upload' );
	
	if (empty ( $type ))
		return $contact->count ();
	
	return $contact->count ();
}
/*
 * $user=new DB\SQL\Mapper($db,'users');
 * $user->load('visits>3');
 * // Rewritten as a parameterized query
 * $user->load(array('visits>?',3));
 */
function getEmployeeList($f3) {
	$db = $f3->get ( 'db' );
	return $db->exec ( "select * from employee order by last_name, first_name" );
}
function getCustomerList($f3) {
	$db = $f3->get ( 'db' );
	return $db->exec ( "select * from customer order by name" );
}
function getCategoryActiveList($f3) {
	$db = $f3->get ( 'db' );
	$query = "select c.*, cst.name as customer_name from category c, customer cst";
	$query .= " where c.customer_id=cst.id and c.status=1 order by cst.name";
	return $db->exec ( $query );
}
function getRegionList($f3) {
	$db = $f3->get ( 'db' );
	return $db->exec ( "select * from region order by id" );
}
function getReport($f3, $id) {
	if (! $id)
		return false;
	
	$db = $f3->get ( 'db' );
	return (new \DB\SQL\Mapper ( $db, 'foto_upload' ))->findone ( [ 
			'id = :id',
			':id' => $id 
	] );
}
function getReportWithDic($f3, $id) {
	$db = $f3->get ( 'db' );
	$result = $db->exec ( "
			select fu.*,
            s.name_ru status_name,
            e.last_name e_last_name,
            e.first_name e_first_name,
            e.patronymic_name e_patronymic_name,
            concat(e.last_name,' ',e.first_name,' ',e.patronymic_name) e_fio,
            r.name r_name,
            u.login,
            (select count(*) from foto where foto_upload_id=fu.id) fotos_count,
            (select sum(price) from foto_category where foto_upload_id=fu.id) price,
            (select count(*) from foto_upload_category where foto_upload_id=fu.id) categories_count
            from foto_upload fu
            left join employee e on (fu.`employee_id`=e.`id`)
            left join region r on (fu.`region_id`=r.`id`)
            left join user u on (fu.`user_id`=u.id)
            left join status s on (fu.`status_id`=s.id)
            left join foto f on (fu.id=f.foto_upload_id)
            left join foto_upload_category fuc on (fu.id=fuc.foto_upload_id)
            where fu.id=$id            
            group by fu.id" );
	
	return count ( $result ) == 1 ? $result [0] : array ();
}
function getStatus($f3, $id) {
	$db = $f3->get ( 'db' );
	return (new \DB\SQL\Mapper ( $db, 'status' ))->findone ( [ 
			'id = :id',
			':id' => $id 
	] );
}
function getFotoCountByReportId($f3, $id) {
	$db = $f3->get ( 'db' );
	return ( int ) $db->exec ( "select count(1) num from foto where foto_upload_id=" . $id ) [0] ['num'];
}
function getFotoUploadCategoriesCountByReportId($f3, $id) {
	$db = $f3->get ( 'db' );
	return ( int ) $db->exec ( "select count(1) num from foto_upload_category where foto_upload_id=" . $id ) [0] ['num'];
}
function getFotosByReportId($f3, $id) {
	$db = $f3->get ( 'db' );
	return $db->exec ( "select * from foto where foto_upload_id=" . ( int ) $id . " order by foto_old_name" );
}
/**
 * Существует ли фото с таким старым именем и номером отчета
 *
 * @param unknown $foto_upload_id
 * @param unknown $foto_old_name
 * @return boolean
 */
function fotoExists($f3, $foto_upload_id, $foto_old_name) {
	$foto = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'foto' );
	$foto->load ( array (
			'foto_upload_id=? AND foto_old_name=?',
			$foto_upload_id,
			trim ( $foto_old_name ) 
	) );
	if ($foto->dry ()) {
		return false;
	}
	return true;
}

/**
 *
 * @return список фотографий по категории вместе фото адресов
 */
function getFotosByCategoryId($f3, $foto_category_id) {
	/*
	 * select * FROM foto f
	 * where f.foto_category_id=19
	 * or f.id in (select f2.address_id from foto f2 where f2.foto_category_id=19)
	 */
	$db = $f3->get ( 'db' );
	$query = "select * from foto f \n" . " where f.id in  \n" . "   (select fc.foto_id from foto_category fc where  fc.foto_upload_category_id = " . ( int ) $foto_category_id . ") \n" . " or f.id in \n" . "   (select f2.address_id from foto f2 where f2.id in  \n" . "      (select fc2.foto_id from foto_category fc2 where  fc2.foto_upload_category_id = " . ( int ) $foto_category_id . ")\n" . " order by f.foto_name, f.id)";
	return $db->exec ( $query );
}
function getFotoCategoriesByFotoUploadCategoryId($f3, $foto_upload_category_id) {
	$db = $f3->get ( 'db' );
	$query = "select * from foto_category fc where fc.foto_id in " . " (select f.id from foto f where f.foto_upload_id=" . ( int ) $foto_upload_category_id . ")" . "order by fc.id";
	return $db->exec ( $query );
}
function getFotoUploadCategoryListByReportId($f3, $report_id) {
	if (! $report_id)
		return false;
	
	$db = $f3->get ( 'db' );
	$query = "select fuc.*, CONCAT('Заказчик \"',cst.name,'\"', ' (',c.name,' ', c.price,' руб.)')  as category_name 
			  from foto_upload_category fuc, category c, customer cst
			  where 
			  fuc.category_id=c.id
			  and c.customer_id=cst.id
              and fuc.foto_upload_id=" . ( int ) $report_id . " order by cst.name;";
	return $db->exec ( $query );
	/*
	 * return (new \DB\SQL\Mapper ( $db, 'foto_upload_category' ))->find ( [
	 * 'foto_upload_id = :foto_upload_id',
	 * ':foto_upload_id' => $report_id
	 * ], [
	 * 'order' => 'id'
	 * ] );
	 */
}
function getPriceByFotoUploadCategoryId($f3, $foto_upload_category_id) {
	if (! $foto_upload_category_id)
		return false;
	
	$db = $f3->get ( 'db' );
	$query = "select fuc.price from foto_upload_category fuc where fuc.id=" . ( int ) $foto_upload_category_id;
	$res = $db->exec ( $query ) [0] ['price'];
	return ( float ) $res;
}
function existFotoUploadCategoryByCategoryId($f3, $category_id) {
	if (! $category_id)
		return false;
	
	$db = $f3->get ( 'db' );
	$query = "select count(*) num from foto_upload_category where category_id=" . $category_id;
	return ( int ) $db->exec ( $query ) [0] ['num'];
}
function deleteFotoUploadCategoryListByReportId($f3, $report_id) {
	if (! $report_id)
		return false;
	
	$db = $f3->get ( 'db' );
	return $db->exec ( "delete from foto_upload_category where foto_upload_id=" . $report_id );
}
function getCategoryViewListByReportId($f3, $report_id) {
	if (! $report_id)
		return false;
	
	$db = $f3->get ( 'db' );
	
	return (new \DB\SQL\Mapper ( $db, 'foto_category_view' ))->find ( [ 
			'foto_upload_id = :foto_upload_id',
			':foto_upload_id' => $report_id 
	], [ 
			'order' => 'id' 
	] );
}
/**
 *
 * @deprecated - не используется
 * @param unknown $report_id
 * @param number $total_row
 *        	- включать итоговую строку (1-да, 0-нет), по умолчанию 1
 * @return boolean|unknown
 */
function getCategoryViewListByReportIdAndTotalRow($f3, $report_id, $total_row = 1) {
	if (! $report_id)
		return false;
	
	$db = $f3->get ( 'db' );
	$query = sprintf ( "select " . "fc.id, " . // 1
"fc.foto_upload_id, " . // 2
"fc.category_name, " . // 3
"fc.price , " . // 4
"fc.customer_id, " . // 5
"fc.customer_name, " . // 6
"fc.customer_email, " . // 7
"fc.foto_number, " . // 8
"fc.category_sum, " . // 9
"fc.file_path, " . // 10
"fc.file_zip, " . // 11
"fc.email_send, " . // 12
"fc.email_result, " . // 13
"fc.email_error, " . // 14
"fc.email_date," . // 15
"fc.email_send_manual " . // 16
" from foto_category_view fc where  fc.foto_upload_id=%s ", $report_id );
	if ($total_row) {
		$query .= sprintf ( " union " . "select " . "'', " . // 1
"fc.foto_upload_id, " . // 2
"'Общая сумма', " . // 3
"'',  " . // 4
"'', " . // 5
"'', " . // 6
"'', " . // 7
"'', " . // 8
"sum(category_sum), " . // 9
"'', " . // 10
"'', " . // 11
"'', " . // 12
"'', " . // 13
"'', " . // 14
"'', " . // 15
"'' " . // 16
"from foto_category_view fc where fc.foto_upload_id=%s", $report_id );
	}
	return $db->exec ( $query );
}
function getTotalRowCategoryViewListByReportId($f3, $report_id) {
	$db = $f3->get ( 'db' );
	$query = "select sum(category_sum) as summa from foto_category_view fc where fc.foto_upload_id=" . ( int ) $report_id;
	return $db->exec ( $query ) [0] ['summa'];
}
function getCategoryViewById($f3, $id) {
	if (! $id)
		return false;
	
	$db = $f3->get ( 'db' );
	return $db->exec ( "select * from `foto_category_view` fc where fc.id=" . ( int ) $id ) [0];
}

/*
 * select
 * (select count(1) from foto_category fc where fc.foto_upload_id=60) all_count,
 * (select count(1) from foto_category fc2 where fc2.foto_upload_id=60
 * and fc2.email_result=1) sent_count
 */
/**
 * Все ли категории посланы по отчету данной категории
 * Получает номер отчета, по нему находит номер отчета, смотрит все ли письма отправлены
 *
 * @param unknown $f3
 * @param unknown $id
 *        	номер номер
 * @return boolean|unknown
 */
function isAllSentByReportId($f3, $report_id) {
	debug_message ( "START isAllSentByCategoryId, \$report_id: " . $report_id );
	$resultToReturn = [ ];
	if (! $report_id)
		return false;
	
	$db = $f3->get ( 'db' );
	$query = "
select $report_id as foto_upload_id,
(select count(1) from foto_upload_category fc where fc.id in 
      (select foto_upload_category_id from foto_category where foto_upload_id=$report_id)) all_count,
(select count(1) from foto_upload_category fc2 where fc2.foto_upload_id=$report_id
and fc2.email_result=1) sent_count
;";
	// debug_message ( $query );
	$result = $db->exec ( $query ) [0];
	$resultToReturn ['report_id'] = $result ['foto_upload_id'];
	$resultToReturn ['all_sent'] = false;
	debug_message ( $result, 1 );
	if ($result ['all_count'] > 0) {
		if ($result ['all_count'] == $result ['sent_count']) {
			debug_message ( "in true" );
			$resultToReturn ['all_sent'] = true;
		}
	}
	// debug_message ( $resultToReturn, 1 );
	debug_message ( "FINISH isAllSentByCategoryId" );
	return $resultToReturn;
}
function addFoto($f3, $foto_upload_id, $foto_name, $foto_old_name, $width, $height) {
	if (! $foto_upload_id)
		return false;
	
	$db = $f3->get ( 'db' );
	$row = new \DB\SQL\Mapper ( $db, 'foto' );
	$row->foto_upload_id = $foto_upload_id;
	$row->foto_name = $foto_name;
	$row->foto_old_name = $foto_old_name;
	$row->width = $width;
	$row->height = $height;
	$row->save ();
	return $row->id;
}
function addFotoUploadCategory($f3, $foto_upload_id, $category_id) {
	if (! $foto_upload_id)
		return false;
	
	$db = $f3->get ( 'db' );
	$row = new \DB\SQL\Mapper ( $db, 'foto_upload_category' );
	$row->foto_upload_id = $foto_upload_id;
	$row->category_id = $category_id;
	$row->price = getCategoryPriceById ( $f3, $category_id );
	$row->save ();
}
function getCategoryPriceById($f3, $category_id) {
	if (! $category_id)
		return 0;
	$db = $f3->get ( 'db' );
	return ( float ) $db->exec ( "select price from category where id=" . ( int ) $category_id ) [0] ['price'];
}
function getFileNameWithAddressInName($f_name) {
	$point_pos = strrpos ( $f_name, "." );
	$name_part = substr ( $f_name, 0, $point_pos );
	$ext_part = substr ( $f_name, $point_pos + 1, strlen ( $f_name ) );
	return $name_part . "_address." . $ext_part;
}
function sendMailWithAttach($config, $email_to, $base_url, $file_path, $file_name, $category, $test = 0) {
	debug_message ( "start sendMailWithAttach $email_to, $file_path ,$file_nam" );
	require_once "SendMailSmtpClass.php"; // подключаем класс
	                                      
	// копия самому отправителю
	$email_to = $email_to . ',' . $config ['email_login'];
	// debug_message ( "sendMailWithAttach 441" );
	
	$filepath = "./" . $file_path . "/" . $file_name;
	if (DIRECTORY_SEPARATOR == '\\') {
		// if this is a Windows
		$filepath = iconv ( "UTF-8", "windows-1251", $filepath );
	}
	
	// return $link_to_file;
	// debug_message ( "sendMailWithAttach 450" );
	$mailSMTP = new SendMailSmtpClass ( $config ['email_login'], $config ['email_pass'], 'ssl://smtp.yandex.ru', $config ['email_sender'], 465 ); // создаем экземпляр класса
	$headers = "MIME-Version: 1.0\r\n";
	$subject = "Отчет " . $file_name;
	
	// debug_message ( "sendMailWithAttach 455" );
	$link_to_file_to_site = (isset ( $_SERVER ['HTTPS'] ) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$link_to_file_to_site = str_replace ( "calculate/send_email", "", $link_to_file_to_site );
	$link_to_file = $link_to_file_to_site . $file_path . '/' . $file_name;
	
	$send_map_in_letter_body = false;
	$map_img_element = "";
	
	if ($send_map_in_letter_body) {
		
		$map_file = getcwd () . DIRECTORY_SEPARATOR . map_files . DIRECTORY_SEPARATOR . $category ['region_file_name'];
		// debug_message ( '$map_file=' . $map_file );
		// debug_message ( 'file_exists($map_file)=' . file_exists ( $map_file ) );
		
		$map_img_element = '';
		
		// в случае, если отправлять карту в теле письма
		
		if ($category ['region_file_name'] and file_exists ( $map_file )) {
			$map_img_element = sprintf ( "<img alt='Карта района %s' width='500'" . " src='%smap_files/%s'>", $category ['region_name'], $link_to_file_to_site, $category ['region_file_name'] );
		} else {
			$map_img_element = 'Карта района на найдена';
		}
	}
	
	// debug_message ( "sendMailWithAttach 461" );
	// debug_message ( $map_img_element );
	// return true;
	
	$message = $config ['email_msg_tmpl'];
	$message = str_replace ( '<REPRESENTATIVE>', $category ['representative'], $message );
	$message = str_replace ( '<FILENAME>', '&#34;' . $file_name . '&#34;', $message );
	$message = str_replace ( '<FILELINK>', "<a href='" . $link_to_file . "' download>ссылка</a>", $message );
	$message = str_replace ( '<EMAIL_FROM>', $config ['email_from'], $message );
	$message = str_replace ( '<COMPANY_NAME>', $category ['name_official'], $message );
	$message = str_replace ( '<REGION_MAP>', $map_img_element, $message );
	
	// debug_message ( $message );
	
	if (! $config ['email_file_limit_mb']) {
		return "Настройки размера файла не установлены";
	}
	
	$mapFileSize = 0;
	$map_file_path = '';
	if ($category ['region_file_name']) {
		$map_file_path = 'map_files/' . $category ['region_file_name'];
		if (file_exists ( $map_file_path )) {
			$mapFileSize = filesize ( $map_file_path );
		}
	}
	
	$allAttachBig = (filesize ( $filepath ) + $mapFileSize) > ($config ['email_file_limit_mb'] * 1024 * 1024);
	/*
	 * debug_message('<<< filesize ******************');
	 * debug_message('$filesize ( $filepath )='.filesize ( $filepath ));
	 * debug_message('$mapFileSize='.$mapFileSize);
	 * debug_message('filesize ( $filepath ) + $mapFileSize)='. (filesize ( $filepath ) + $mapFileSize));
	 * debug_message('$allAttachBig='.$allAttachBig);
	 * debug_message('>>>');
	 */
	if ($allAttachBig and $mapFileSize == 0) {
		// debug_message ( "отправляем без вложения" );
		$headers .= "Content-type: text/html; charset=utf-8\r\n"; // кодировка письма
		$headers .= sprintf ( "From: %s <%s>\r\n", $config ['email_from'], $config ['email_login'] );
		// $headers111 .= $headers . sprintf ( "From: %s <%s>\r\n", $config ['email_from'], $config ['email_login'] );
		// $headers .= "From: Администратор сайта <test4dmin@yandex.ru>\r\n"; // от кого письмо
		// debug_message ( "\$headers" . $headers );
	} else {
		// debug_message ( "sendMailWithAttach 511" );
		$boundary = "--" . md5 ( uniqid ( time () ) ); // генерируем разделитель
		$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n"; // кодировка письма// разделитель указывается в заголовке в параметре boundary
		$headers .= sprintf ( "From: %s <%s>\r\n", $config ['email_from'], $config ['email_login'] );
		
		$multipart = "--$boundary\r\n";
		$multipart .= "Content-Type: text/html; charset=utf-8\r\n";
		$multipart .= "Content-Transfer-Encoding: base64\r\n";
		$multipart .= "\r\n";
		$multipart .= chunk_split ( base64_encode ( $message ) );
		
		// Если файл превышает позволенный лимит не отправляем его
		/* send archive file start */
		// debug_message ( "sendMailWithAttach 524" );
		if (! $allAttachBig) {
			// debug_message ( "sendMailWithAttach 526" );
			$fp = fopen ( $filepath, "r" );
			if (! $fp) {
				exit ( "Не удается открыть файл" );
			}
			$file = fread ( $fp, filesize ( $filepath ) ); // чтение файла
			fclose ( $fp );
			
			$message_part = "\r\n--$boundary\r\n";
			$message_part .= "Content-Type: application / zip; name=\"$file_name\"\r\n";
			$message_part .= "Content-Disposition: attachment\r\n";
			$message_part .= "Content-Transfer-Encoding: base64\r\n";
			$message_part .= "\r\n";
			ini_set ( 'memory_limit', '-1' );
			$message_part .= chunk_split ( base64_encode ( $file ) );
			$message_part .= "\r\n-$boundary-\r\n"; // второй частью прикрепляем файл, можно прикрепить два и более файла
			$multipart .= $message_part;
			/* send archive file end */
			// debug_message ( "sendMailWithAttach 544" );
		}
		/* send map file start */
		if ($mapFileSize) {
			$map_file_path = 'map_files/' . $category ['region_file_name'];
			$fp = fopen ( $map_file_path, "r" );
			if (! $fp) {
				exit ( "Не удается открыть файл" );
			}
			$file = fread ( $fp, filesize ( $filepath ) ); // чтение файла
			fclose ( $fp );
			
			$message_part = "\r\n--$boundary\r\n";
			$map_send_file_name = "Карта_района_" . $category ['region_name'] . '.' . pathinfo ( $map_file_path ) ['extension'];
			// debug_message ( '$map_send_file_name=' . $map_send_file_name );
			$message_part .= "Content-Type: application / zip; name=\"$map_send_file_name\"\r\n";
			$message_part .= "Content-Disposition: attachment\r\n";
			$message_part .= "Content-Transfer-Encoding: base64\r\n";
			$message_part .= "\r\n";
			ini_set ( 'memory_limit', '-1' );
			$message_part .= chunk_split ( base64_encode ( $file ) );
			$message_part .= "\r\n-$boundary-\r\n"; // второй частью прикрепляем файл, можно прикрепить два и более файла
			$multipart .= $message_part;
		}
		/* send map file end */
		
		$message = $multipart;
	}
	if ($test) {
		$result = true;
		if ($email_to == 'javajan@mail.ru') {
			// $result = 'Не ушло';
		}
		sleep ( 2 );
	} else {
		// $email_to = $email_to . ',' . $config ['email_login'];
		$result = $mailSMTP->send ( $email_to, $subject, $message, $headers ); // отправляем письмо
	}
	debug_message ( "Finish sendMailWithAttach \$result=$result" );
	return $result;
}
function delTree($dir) {
	debug_message("{{{ in del tree dir=" . $dir);
	
	if(!file_exists($dir)){
		debug_message("dir not exists ");
		return;
	}
	$files = array_diff ( scandir ( $dir ), array (
			'.',
			'..' 
	) );
	foreach ( $files as $file ) {
		if (is_dir ( "$dir/$file" )) {
			delTree ( "$dir/$file" );
		} else {
			unlink ( "$dir/$file" );
		}
	}
	debug_message("}}}");
	return rmdir ( $dir );
}
function is_dir_empty($dir) {
	if (! is_readable ( $dir ))
		return NULL;
	$handle = opendir ( $dir );
	while ( false !== ($entry = readdir ( $handle )) ) {
		if ($entry != "." && $entry != "..") {
			return FALSE;
		}
	}
	return TRUE;
}