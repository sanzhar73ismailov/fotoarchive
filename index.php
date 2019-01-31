<?php
error_reporting ( E_ALL );
require ("vendor/autoload.php");
require_once ("functions/config.php");
require_once ("functions/functions.php");
require_once ("functions/cron.php");

$f3 = Base::instance ();

$f3->set ( 'DEBUG', 3 );
$f3->set ( 'AUTOLOAD', 'page/' );
$f3->set ( 'TZ', 'Europe/Ulyanovsk' );
$f3->set ( 'LANGUAGE', 'ru-RU' );
$f3->set ( 'PER_PAGE', 6 );
setlocale ( LC_TIME, 'ru_RU' );

$db = new \DB\SQL ( 'mysql:host=' . DB_HOST . ';port=3306;dbname=' . DB_NAME, DB_USER, DB_PASS, array (
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION 
) );

$db->exec ( "SET time_zone = '+4:00';" );
$f3->set ( 'db', $db );

$config = getSettings ( $f3 );
$f3->set ( 'config', $config );

$f3->route ( 'GET|POST /login', function ($f3) {
	if ($f3->get ( 'SESSION.user' )) {
		$f3->reroute ( '/reports' );
	}
	
	if ($f3->get ( 'POST' )) {
		$user_mapper = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'user' );
		$user = $user_mapper->load ( [ 
				'login=?',
				$f3->get ( 'POST.login' ) 
		] );
		$pass_verify = (md5 ( $f3->get ( 'POST.password' ) ) == $user->password);
		if (! $user_mapper->dry () && $pass_verify) {
			
			$f3->set ( 'SESSION.user', $user->login );
			$f3->set ( 'SESSION.user_id', $user->id );
			// debug_message ( "set session user!!!" );
			$f3->reroute ( '/reports' );
		} else {
			$f3->set ( 'message', array (
					'text' => 'Could not authorize',
					'type' => 'danger' 
			) );
		}
	}
	echo View::instance ()->render ( 'view/login.php' );
	return;
} );

$f3->route ( 'GET|POST /logout', function ($f3) {
	
	$f3->clear ( 'SESSION.user' );
	$f3->reroute ( '/login' );
} );

$f3->map ( [ 
		'/dictionary',
		'/dictionary/@entity',
		'/dictionary/@entity/@id' 
], 'DictionaryPage' );

$f3->map ( [ 
		'/reports',
		'/reports/@page',
		'/reportDelete/@delid/@page'
], 'ReportsPage' );

$f3->map ( [ 
		'/report',
		'/report/@id',
		'/report/@id/@fotoLoad'
], 'ReportFormPage' );

$f3->map ( [ 
		'/category',
		'/category/@id' 
], 'CategoryFormPage' );

$f3->map ( [ 
		'/category_match',
		'/category_match/@id' 
], 'CategoryMatchFormPage' );

$f3->map ( [ 
		'/calculate',
		'/calculate/@id' 
], 'CalculateFormPage' );

$f3->map ( [ 
		'/settings' 
], 'SettingsFormPage' );

$f3->route ( 'GET /*', function ($f3) {
	checkAuth ( $f3 );
	$f3->reroute ( '/reports' );
} );
$f3->route ( 'GET /smallfoto/uploads/directories/@report/@file', function ($f3, $args) {
	$img = new Image ( 'uploads/directories/'. $args ['report'] . '/'.$args ['file'] );
	$img->resize ( 90, 60 )->render ();
}, 86400 );
$f3->route ( 'GET /previewfoto/uploads/directories/@report/@file', function ($f3, $args) {
	$img = new Image ( 'uploads/directories/'. $args ['report'] . '/'.$args ['file'] );
	$img->resize ( 190, 127 )->render ();
}, 86400 );
$f3->route ( 'GET /smallmap/@file', function ($f3, $args) {
	//$img = new Image ( 'assets/media/' . $args ['file'] );
	$img = new Image ( 'map_files/'. $args ['file'] );
	$img->resize ( 90, 60 )->render ();
}, 86400 );

$f3->route ( 'GET /cron', function ($f3) {
	CreateReportFtp::checkZips($f3);
} );

$f3->run ();

?>