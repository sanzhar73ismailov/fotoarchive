<?php
class SettingsFormPage extends Page {
	public function get($f3) {
		debug_message ( '- start SettingsFormPage->get' );
		checkAuth ( $f3 );
		$errorMessage = "";
		try {
			$f3->set ( 'entityObj', getSettings ( $f3 ) );
		} catch ( Exception $ex ) {
			$errorMessage = $ex->getMessage ();
			process_exception ( $ex );
		}
		debug_message ( '- end SettingsFormPage->get' );
		echo \Template::instance ()->render ( 'view/settings.htm' );
	}
	public function post($f3) {
		debug_message ( '- start SettingsFormPage->post' );
		checkAuth ( $f3 );
		$errorMessage = "";
		
		try {
			$mapper = new \DB\SQL\Mapper ( $f3->get ( 'db' ), 'settings' );
			$mapper = $mapper->load ( array (
					'id=?',
					1 
			) );
			$mapper->copyFrom ( 'POST' );
			$mapper->save ();
		} catch ( Exception $ex ) {
			$errorMessage = $ex->getMessage ();
			process_exception ( $ex );
		}
		debug_message ( '- end SettingsFormPage->post' );
		$f3->reroute ( "/settings");
		//echo \Template::instance ()->render ( 'view/settings.htm' );
	}

}