<?php
error_reporting ( E_ALL );
require ("vendor/autoload.php");
require_once ("functions/config.php");
require_once ("functions/functions.php");

$f3 = Base::instance ();

$f3->set ( 'DEBUG', 3 );
$f3->set ( 'AUTOLOAD', 'page/' );
$f3->set ( 'TZ', 'Europe/Ulyanovsk' );
$f3->set ( 'LANGUAGE', 'ru-RU' );
$f3->set ( 'PER_PAGE', 10 );
setlocale ( LC_TIME, 'ru_RU' );
$f3->set ( 'UI', 'ui/;assets/media/' );



unset ( $config );
$config ['base_href'] = 'localhost/fotoarchive';
$f3->set ( 'config', $config );

/*
 * $db = new \DB\SQL($dsn, $user, $pwd, array( \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION ));
 * later on in the code, on a per-transaction basis:
 *
 * $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
 */

$db = new \DB\SQL ( 'mysql:host=' . DB_HOST . ';port=3306;dbname=' . DB_NAME, DB_USER, DB_PASS, array (
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION 
) );
$db->exec ( "SET time_zone = '+4:00';" );
$f3->set ( 'db', $db );

$f3->route ( 'GET /*', function ($f3) {
	 echo "<html><head><meta charset='UTF-8'></head><body>";
	 echo '<p/><p/>This is my Test Page! Привет';
	// ****************** start test with db
	
	include_once 'test_func.php';
	
	// test_login($f3);
	// existFoto($f3);
	// test_getPriceByFotoUploadCategoryId($f3);
	// testAddFoto($f3);
	// testGetSettings($f3);
	// testTemplate($f3);
	// test_getExtensionByMimeType();
	// testDelFoto($f3);
	// deleteTest($f3, 1610);
	// getFileSize();
	// testUserDry($f3);
	// testDelTree ();
	// test_createDraftReport($f3);
	// testDelReport($f3, 65);
	// scandir('212');
	//testF3ImageResize ( $f3 );
	showImg();
	// ******************* end test with db
	 echo "</body></html>";
} );

//$f3->set ( 'UI', 'ui/;assets/' );
$f3->route ( 'GET /image/@file', function ($f3, $args) {
	debug_message("start 789<<<");
	$img = new Image ( 'assets/media/' . $args ['file'] );
	$img->resize ( 360, 240 )->render ();
	debug_message(">>>end 789");
}, 86400 );

$f3->run ();

?>