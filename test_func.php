<?php
function test_login($f3) {
	$user_mapper = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'user' );
	//$password_hash = password_hash ( '123456', PASSWORD_DEFAULT );
	//var_dump ( $password_hash );
	$md5 = md5 ( '123456' );
	var_dump ( $md5 );
	$user = $user_mapper->load ( [ 
			'login=?',
			'admin' 
	] );
	
	echo "<br>dry:{<br>";
	var_dump ( $user->dry () );
	//var_dump ( $user->dry () );
	echo "<br>}";
	$password_verify = $md5 ==  $user->password;
	var_dump ( $password_verify );
}
function fff($f3) {
	$result = deleteCategoryListByReportId ( $f3, 59 );
	var_dump ( $result );
}

function existFoto($f3){
	$foto = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'foto' );
	$foto->load(array('foto_upload_id=? AND foto_old_name=?', 1044, 'foto02.jpg'));
	var_dump ( $foto->id );
	var_dump ( $foto->foto_name );
	var_dump ( $foto->dry() );
}

function test_getPriceByFotoUploadCategoryId($f3){
	//$res=getPriceByFotoUploadCategoryId($f3, 178);
	//$res = getCategoryPriceById($f3, 3);
	$res = existFotoUploadCategoryByCategoryId($f3,6);
	$res = getFotosByCategoryId($f3, 221);
	$res = isAllSentByCategoryId($f3, 132);
	if($res){
		echo 111;
	}else{
		echo '000';
	}
	var_dump($res);
}

function testAddFoto($f3){
	//foto02 - копия - копия - копия.jpg
	$fName = 'foto02 - копия - копия - копия';
	$foto_upload_id = 3;
	$fExist = fotoExists($f3, $foto_upload_id, $fName);
	var_dump($fExist);
	try{
		//addFoto ( $f3, $foto_upload_id, 'adasdas', $fName,111, 222 );

		var_dump(getFotoUploadCategoriesCountByReportId($f3,1));
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	//var_dump($fExist);
}

function testGetSettings($f3){
	$config = $f3->get ( 'db' )->exec('SELECT * FROM settings');
	var_dump(count($config));
	var_dump($config[0]);
}

function testTemplate($f3){
	$config = getSettings($f3);
	//var_dump(count($config));
	$msg = $config['email_msg_tmpl'];
	var_dump($msg);
	$msg = str_replace('<REPRESENTATIVE>', 'Иван', $msg);
	$msg = str_replace('<FILENAME>', 'file_name', $msg);
	$msg = str_replace('<FILELINK>', 'file_link', $msg);
	$msg = str_replace('<EMAIL_FROM>', 'email_from', $msg);
	$msg = str_replace('<COMPANY_NAME>', 'company_name', $msg);
	var_dump($msg);
}

function test_getExtensionByMimeType(){
	var_dump(getExtensionByMimeType("image/jpeg"));
	var_dump(getExtensionByMimeType("image/png"));
	var_dump(getExtensionByMimeType("image/bmp"));
}
function testUnlinkByMask(){
	unlink("map_files/a06.bmp");
}

function testDelFoto($f3){
	try{
		//$user=new DB\SQL\Mapper($db,'users');
		$foto = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'foto' );
		$foto->load ( array('id=?', 874));
		$res = '';
		$res = $foto->erase();
		//$foto->erase();
		var_dump($res);
		exit ("qqq" );
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
}

function deleteTest($f3, $id){
	//var_dump(deleteEntity($f3, 'foto', $id));
	var_dump(deleteFotosByReportId($f3, 40));
}

function getFileSize(){
	$f='map_files/b015a38be8539aa4.png';
	var_dump(file_exists(file_exists($f)));
	if(file_exists($f)){
		var_dump(filesize($f));
	}
}

function testUserDry($f3){
	$login = 'user1';
	$user_mapper = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'user' );
	$user = $user_mapper->load ( ['login=?',$login] );
	//var_dump($user_mapper);
	var_dump($user_mapper->dry ());
}

function testDelTree(){
	$file = "111/222.txt";
	var_dump(dirname ($file));
	//exit("exit");
	$folder = dirname ($file);
	var_dump("getcwd=".getcwd());
	$a = scandir($folder);
	
	print_r($a);
	$files = array_diff(scandir($folder), array('.','..'));
	print_r("<<<");
	var_dump($files);
	print_r(">>>");
	
	var_dump('$folder='.$folder);
	var_dump(file_exists($folder));
	delTree($folder);
	var_dump(file_exists($folder));
	/**/
}

function test_createDraftReport($f3){
	var_dump(createDraftReport($f3));
}
function testDelReport($f3, $id){
	deleteReport($f3,$id);
}
function testF3ImageResize($f3){
	//$img->resize( int $width [, int $height = NULL [, bool $crop = TRUE [, bool $enlarge = TRUE ]]] );
	$file = 'uploads/f1.jpg';
	//var_dump(file_exists($file));
	if(file_exists($file)) {
		$img = new Image('uploads/f1.jpg'); // relative to UI search path
		//$img = new Image();
		$img->resize(100);
		//$img->identicon("123 my friend");
		//$img->render('jpeg');
		$img->render();
		exit;
		//var_dump('$res='.$res);
		//$img->render('jpeg');
		//$f3->write( 'uploads/f1_small_9.jpg', $img->dump('jpeg',9) );
	
	}
}

function showImg(){
	echo "<img src='image/f123.jpg'/>";
}







